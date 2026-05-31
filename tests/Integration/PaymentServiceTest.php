<?php

declare(strict_types=1);

namespace Tests\Integration;

use app\Models\Order;
use app\Services\Payment\PaymentService;
use app\Services\Payment\SandboxGateway;

/**
 * Integration tests for {@see PaymentService} against the real (SQLite)
 * database (roadmap T22): starting a payment records the transaction, a valid
 * callback marks the order paid + done, and a forged callback changes nothing.
 */
final class PaymentServiceTest extends IntegrationTestCase
{
    private const SECRET = 'test-secret-key';

    private PaymentService $service;
    private SandboxGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedUser('user-1');
        $this->gateway = new SandboxGateway(self::SECRET);
        $this->service = new PaymentService($this->gateway);
    }

    private function seedOrder(string $id): void
    {
        self::$pdo->prepare(
            'INSERT INTO orders (id, user_id, status, payment_method, delivery_name, delivery_phone, delivery_address)
             VALUES (:id, "user-1", :status, "sandbox", "Test", "0900000000", "1 Test St")'
        )->execute(['id' => $id, 'status' => Order::STATUS_PROCESSING]);
    }

    private function paymentStatus(string $orderId): string
    {
        $stmt = self::$pdo->prepare('SELECT payment_status FROM orders WHERE id = :id');
        $stmt->execute(['id' => $orderId]);

        return (string) $stmt->fetchColumn();
    }

    public function testStartPaymentRecordsPendingStatusAndTransactionId(): void
    {
        $this->seedOrder('o-1');

        $result = $this->service->startPayment('o-1', 50000);

        $this->assertSame(PaymentService::STATUS_PENDING, $this->paymentStatus('o-1'));

        $stmt = self::$pdo->prepare('SELECT transaction_id FROM orders WHERE id = :id');
        $stmt->execute(['id' => 'o-1']);
        $this->assertSame($result->transactionId, $stmt->fetchColumn());
    }

    public function testValidCallbackMarksOrderPaidAndDone(): void
    {
        $this->seedOrder('o-1');
        $result = $this->service->startPayment('o-1', 50000);
        parse_str((string) parse_url($result->redirectUrl, PHP_URL_QUERY), $payload);

        $callback = $this->service->handleCallback($payload);

        $this->assertTrue($callback->isSuccessful());
        $this->assertSame(PaymentService::STATUS_PAID, $this->paymentStatus('o-1'));

        $stmt = self::$pdo->prepare('SELECT status FROM orders WHERE id = :id');
        $stmt->execute(['id' => 'o-1']);
        $this->assertSame(Order::STATUS_DONE, $stmt->fetchColumn());
    }

    public function testForgedCallbackLeavesOrderUntouched(): void
    {
        $this->seedOrder('o-1');
        $this->service->startPayment('o-1', 50000);

        $callback = $this->service->handleCallback([
            'order_id'       => 'o-1',
            'transaction_id' => 'sbx_forged',
            'amount'         => '1',
            'status'         => SandboxGateway::STATUS_SUCCESS,
            'signature'      => 'deadbeef',
        ]);

        $this->assertFalse($callback->verified);
        // Status must still be the pending one written at startPayment.
        $this->assertSame(PaymentService::STATUS_PENDING, $this->paymentStatus('o-1'));

        $stmt = self::$pdo->prepare('SELECT status FROM orders WHERE id = :id');
        $stmt->execute(['id' => 'o-1']);
        $this->assertSame(Order::STATUS_PROCESSING, $stmt->fetchColumn());
    }
}
