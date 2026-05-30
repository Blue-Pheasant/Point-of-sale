<?php

namespace app\Services\Payment;

/**
 * Immutable value object describing a payment a gateway has just created
 * (roadmap T22).
 *
 * It carries the gateway's transaction reference plus the URL the customer
 * should be sent to in order to complete the payment. Keeping this a typed
 * object — rather than a loose array — lets the controller and any future
 * gateway agree on a single, predictable shape.
 *
 * @package app\Services\Payment
 */
final class PaymentResult
{
    /**
     * @param string $transactionId The gateway's reference for this payment.
     * @param string $redirectUrl The URL to send the customer to (the sandbox
     *     gateway's hosted page, or a return URL for an instant sandbox).
     */
    public function __construct(
        public readonly string $transactionId,
        public readonly string $redirectUrl,
    ) {
    }
}
