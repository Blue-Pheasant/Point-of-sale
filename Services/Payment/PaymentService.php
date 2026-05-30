<?php

namespace app\Services\Payment;

use app\Common\QueryBuilder;
use app\Models\Order;

/**
 * Orchestrates payments without knowing which gateway is in use (roadmap T22).
 *
 * It depends only on {@see PaymentGatewayInterface}, so swapping the gateway is
 * a constructor change — the order/business logic here never changes. Its job
 * is to turn gateway results into persisted order state (`payment_status` and
 * `transaction_id`), keeping every write param-bound through the
 * {@see QueryBuilder}.
 *
 * @package app\Services\Payment
 */
class PaymentService
{
    /** Payment lifecycle values stored in `orders.payment_status`. */
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID    = 'paid';
    public const STATUS_FAILED  = 'failed';

    /**
     * @param PaymentGatewayInterface $gateway The driver to use. The container
     *     binds this to the {@see SandboxGateway}; swapping gateways is a single
     *     binding change in {@see \app\Core\Application}, with no change here.
     */
    public function __construct(private PaymentGatewayInterface $gateway)
    {
    }

    /**
     * Starts a payment for an order: asks the gateway to create it, records the
     * transaction reference against the order, and returns where to send the
     * customer.
     *
     * @param string $orderId The order being paid for.
     * @param int $amount The amount to charge, in VND.
     * @return PaymentResult The transaction reference and redirect URL.
     */
    public function startPayment(string $orderId, int $amount): PaymentResult
    {
        $result = $this->gateway->createPayment($orderId, $amount);

        QueryBuilder::table('orders')
            ->where('id', $orderId)
            ->update([
                'payment_status' => self::STATUS_PENDING,
                'transaction_id' => $result->transactionId,
            ]);

        return $result;
    }

    /**
     * Handles a gateway callback: verifies it, then updates the order's payment
     * status (and marks the order done on success). A callback that fails
     * signature verification is rejected and the order is left untouched.
     *
     * @param array<string, mixed> $payload The raw callback parameters.
     * @return CallbackResult The verified, normalised outcome.
     */
    public function handleCallback(array $payload): CallbackResult
    {
        $result = $this->gateway->verifyCallback($payload);

        if (!$result->verified) {
            // Forged or tampered callback — change nothing.
            return $result;
        }

        $update = [
            'payment_status' => $result->paid ? self::STATUS_PAID : self::STATUS_FAILED,
            'transaction_id' => $result->transactionId,
        ];

        // A successful payment also advances the order out of "processing".
        if ($result->paid) {
            $update['status'] = Order::STATUS_DONE;
        }

        QueryBuilder::table('orders')
            ->where('id', $result->orderId)
            ->update($update);

        return $result;
    }
}
