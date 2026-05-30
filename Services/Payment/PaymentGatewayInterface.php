<?php

namespace app\Services\Payment;

/**
 * Contract every payment gateway driver must satisfy (roadmap T22).
 *
 * The business layer depends only on this interface, so a new gateway
 * (VNPay, Momo, Stripe, ...) can be supported by writing one more driver — no
 * change to the controller or order logic. The shipped {@see SandboxGateway}
 * implements it with signed, self-contained sandbox payments.
 *
 * @package app\Services\Payment
 */
interface PaymentGatewayInterface
{
    /**
     * Creates a payment for an order and returns where to send the customer.
     *
     * @param string $orderId The order being paid for.
     * @param int $amount The amount to charge, in VND.
     * @return PaymentResult The transaction reference and redirect URL.
     */
    public function createPayment(string $orderId, int $amount): PaymentResult;

    /**
     * Verifies an incoming gateway callback and normalises its outcome.
     *
     * Implementations MUST validate the callback's signature against the
     * gateway secret before trusting any field, so a forged callback can never
     * mark an order as paid.
     *
     * @param array<string, mixed> $payload The raw callback parameters.
     * @return CallbackResult The verified, normalised result.
     */
    public function verifyCallback(array $payload): CallbackResult;
}
