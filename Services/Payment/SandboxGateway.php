<?php

namespace app\Services\Payment;

use app\Core\Uuid;

/**
 * A self-contained sandbox payment gateway (roadmap T22).
 *
 * It mimics a real hosted-checkout gateway (VNPay/Momo/Stripe-style) without
 * any network call, so the full create → callback → reconcile flow can be
 * exercised locally and in tests. Authenticity is enforced exactly as a real
 * gateway would: every callback carries an HMAC-SHA256 signature over its
 * fields, keyed by a secret read from the environment, and verified in
 * constant time with {@see hash_equals()}. A forged or tampered callback fails
 * verification and can never mark an order as paid.
 *
 * The secret is NEVER hard-coded — it comes from `PAYMENT_SANDBOX_SECRET`.
 *
 * @package app\Services\Payment
 */
final class SandboxGateway implements PaymentGatewayInterface
{
    /** Callback `status` value the gateway sends for a successful payment. */
    public const STATUS_SUCCESS = 'success';

    /** The secret used to sign and verify callbacks. */
    private string $secret;

    /** The base path the customer is returned to after the sandbox checkout. */
    private string $returnPath;

    /**
     * @param string|null $secret Override for the signing secret (mainly for
     *     tests); falls back to the `PAYMENT_SANDBOX_SECRET` env var.
     * @param string $returnPath The return URL path for the hosted checkout.
     */
    public function __construct(?string $secret = null, string $returnPath = '/payment/callback')
    {
        $secret ??= (string) ($_ENV['PAYMENT_SANDBOX_SECRET'] ?? '');
        if ($secret === '') {
            throw new \RuntimeException(
                'PAYMENT_SANDBOX_SECRET is not configured; cannot sign payments.'
            );
        }

        $this->secret = $secret;
        $this->returnPath = $returnPath;
    }

    /**
     * Creates a sandbox payment and returns the (signed) hosted-checkout URL.
     *
     * The amount is included in the signature so it cannot be altered in
     * transit. The returned URL is where a real gateway would host its payment
     * page; here it is the application's own sandbox checkout endpoint.
     */
    public function createPayment(string $orderId, int $amount): PaymentResult
    {
        $transactionId = 'sbx_' . Uuid::v4();
        $signature = $this->sign([
            'order_id'       => $orderId,
            'transaction_id' => $transactionId,
            'amount'         => (string) $amount,
            'status'         => self::STATUS_SUCCESS,
        ]);

        $query = http_build_query([
            'order_id'       => $orderId,
            'transaction_id' => $transactionId,
            'amount'         => $amount,
            'status'         => self::STATUS_SUCCESS,
            'signature'      => $signature,
        ]);

        return new PaymentResult($transactionId, $this->returnPath . '?' . $query);
    }

    /**
     * Verifies a sandbox callback's signature, then reports its outcome.
     *
     * The signature is recomputed over the same fields used at creation and
     * compared in constant time; only an authentic callback is trusted, and
     * "paid" is reported solely when the status field is the success marker.
     */
    public function verifyCallback(array $payload): CallbackResult
    {
        $orderId       = (string) ($payload['order_id'] ?? '');
        $transactionId = (string) ($payload['transaction_id'] ?? '');
        $amount        = (string) ($payload['amount'] ?? '');
        $status        = (string) ($payload['status'] ?? '');
        $provided      = (string) ($payload['signature'] ?? '');

        $expected = $this->sign([
            'order_id'       => $orderId,
            'transaction_id' => $transactionId,
            'amount'         => $amount,
            'status'         => $status,
        ]);

        $verified = $provided !== '' && hash_equals($expected, $provided);
        $paid = $verified && $status === self::STATUS_SUCCESS;

        return new CallbackResult($verified, $paid, $orderId, $transactionId);
    }

    /**
     * Computes the HMAC-SHA256 signature over the given fields.
     *
     * Fields are sorted by key and joined deterministically so the signer and
     * verifier always hash the same canonical string regardless of input order.
     *
     * @param array<string, string> $fields The fields to sign.
     */
    private function sign(array $fields): string
    {
        ksort($fields);

        $canonical = [];
        foreach ($fields as $key => $value) {
            $canonical[] = $key . '=' . $value;
        }

        return hash_hmac('sha256', implode('&', $canonical), $this->secret);
    }
}
