<?php

namespace app\Controllers;

use app\Auth\AuthUser;
use app\Core\Application;
use app\Core\Controller;
use app\Core\Request;
use app\Middlewares\AuthMiddleware;
use app\Services\OrderService;
use app\Services\Payment\PaymentService;

/**
 * Handles the payment flow against the configured gateway (roadmap T22).
 *
 * `pay` starts a payment for one of the signed-in user's orders and redirects
 * the customer to the gateway; `callback` receives the gateway's signed
 * callback and lets {@see PaymentService} reconcile the order. The controller
 * stays gateway-agnostic — it only talks to {@see PaymentService}.
 *
 * @package app\Controllers
 */
class PaymentController extends Controller
{
    private PaymentService $paymentService;
    private OrderService $orderService;

    public function __construct(PaymentService $paymentService, OrderService $orderService)
    {
        $this->paymentService = $paymentService;
        $this->orderService = $orderService;
        // Starting a payment requires a signed-in owner; the callback is
        // authenticated by the gateway signature, so it is intentionally open.
        $this->registerMiddleware(AuthMiddleware::class, ['pay']);
    }

    /**
     * Starts a payment for the given order and redirects to the gateway.
     *
     * The order must belong to the signed-in user. The amount is taken from the
     * order's completed-line total so the client cannot dictate the charge.
     *
     * @param Request $request The request (carries the `id` route/query param).
     * @return void
     */
    public function pay(Request $request): void
    {
        $orderId = (string) $request->getParam('id');
        $order = $this->orderService->getOrderById($orderId);

        if ($order === null || $order->getUserId() !== AuthUser::authUser()->id) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng.');
            $this->back();
            return;
        }

        $amount = $this->orderService->getOrderTotal($orderId);
        $result = $this->paymentService->startPayment($orderId, $amount);

        Application::$app->response->redirect($result->redirectUrl);
    }

    /**
     * Receives the gateway callback and reconciles the order.
     *
     * {@see PaymentService} verifies the signature before changing anything;
     * an unverified callback yields a 400 and leaves the order untouched.
     *
     * @param Request $request The callback request (query or JSON body).
     * @return void
     */
    public function callback(Request $request): void
    {
        $payload = $request->getBody();
        $result = $this->paymentService->handleCallback($payload);

        if (!$result->verified) {
            Application::$app->response->json([
                'status'  => 'error',
                'message' => 'Invalid payment signature.',
            ], 400);
            return;
        }

        Application::$app->response->json([
            'status'         => $result->paid ? 'paid' : 'failed',
            'order_id'       => $result->orderId,
            'transaction_id' => $result->transactionId,
        ]);
    }
}
