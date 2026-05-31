<?php

namespace app\Services\Payment;

/**
 * Normalised outcome of verifying a gateway callback (roadmap T22).
 *
 * Every gateway reports success/failure and signature validity in its own way;
 * a driver translates that into this common shape so the controller never has
 * to know which gateway it is talking to.
 *
 * @package app\Services\Payment
 */
final class CallbackResult
{
    /**
     * @param bool $verified Whether the callback's signature is authentic.
     * @param bool $paid Whether the gateway reports the payment as successful.
     * @param string $orderId The order the callback refers to.
     * @param string $transactionId The gateway transaction reference.
     */
    public function __construct(
        public readonly bool $verified,
        public readonly bool $paid,
        public readonly string $orderId,
        public readonly string $transactionId,
    ) {
    }

    /**
     * Whether the callback is both authentic and reports a successful payment.
     */
    public function isSuccessful(): bool
    {
        return $this->verified && $this->paid;
    }
}
