<?php

declare(strict_types=1);

namespace Tests\Unit;

use app\Services\Payment\SandboxGateway;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@see SandboxGateway} (roadmap T22), focused on the security
 * property that matters most: only a callback bearing a valid HMAC signature
 * is ever treated as authentic.
 */
final class SandboxGatewayTest extends TestCase
{
    private const SECRET = 'test-secret-key';

    private SandboxGateway $gateway;

    protected function setUp(): void
    {
        $this->gateway = new SandboxGateway(self::SECRET);
    }

    public function testConstructorRejectsAnEmptySecret(): void
    {
        $this->expectException(\RuntimeException::class);
        new SandboxGateway('');
    }

    public function testCreatePaymentReturnsTransactionIdAndRedirectUrl(): void
    {
        $result = $this->gateway->createPayment('order-1', 50000);

        $this->assertStringStartsWith('sbx_', $result->transactionId);
        $this->assertStringContainsString('order_id=order-1', $result->redirectUrl);
        $this->assertStringContainsString('signature=', $result->redirectUrl);
    }

    public function testCreatedPaymentRoundTripsThroughVerifyCallback(): void
    {
        $result = $this->gateway->createPayment('order-1', 50000);
        parse_str((string) parse_url($result->redirectUrl, PHP_URL_QUERY), $payload);

        $callback = $this->gateway->verifyCallback($payload);

        $this->assertTrue($callback->verified);
        $this->assertTrue($callback->paid);
        $this->assertTrue($callback->isSuccessful());
        $this->assertSame('order-1', $callback->orderId);
        $this->assertSame($result->transactionId, $callback->transactionId);
    }

    public function testTamperedAmountFailsVerification(): void
    {
        $result = $this->gateway->createPayment('order-1', 50000);
        parse_str((string) parse_url($result->redirectUrl, PHP_URL_QUERY), $payload);

        // An attacker lowers the amount but keeps the original signature.
        $payload['amount'] = '1';

        $callback = $this->gateway->verifyCallback($payload);

        $this->assertFalse($callback->verified);
        $this->assertFalse($callback->isSuccessful());
    }

    public function testForgedSignatureFailsVerification(): void
    {
        $callback = $this->gateway->verifyCallback([
            'order_id'       => 'order-1',
            'transaction_id' => 'sbx_forged',
            'amount'         => '50000',
            'status'         => SandboxGateway::STATUS_SUCCESS,
            'signature'      => 'deadbeef',
        ]);

        $this->assertFalse($callback->verified);
    }

    public function testMissingSignatureFailsVerification(): void
    {
        $callback = $this->gateway->verifyCallback([
            'order_id'       => 'order-1',
            'transaction_id' => 'sbx_x',
            'amount'         => '50000',
            'status'         => SandboxGateway::STATUS_SUCCESS,
        ]);

        $this->assertFalse($callback->verified);
    }

    public function testSignatureFromADifferentSecretIsRejected(): void
    {
        $result = $this->gateway->createPayment('order-1', 50000);
        parse_str((string) parse_url($result->redirectUrl, PHP_URL_QUERY), $payload);

        // A gateway with a different secret must not accept the foreign signature.
        $other = new SandboxGateway('a-different-secret');

        $this->assertFalse($other->verifyCallback($payload)->verified);
    }
}
