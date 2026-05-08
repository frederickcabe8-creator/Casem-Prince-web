<?php

namespace App\Services;

use App\Models\Order;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(Order $order): array
    {
        try {
            $intent = PaymentIntent::create([
                'amount'   => (int) ($order->total_amount * 100),
                'currency' => strtolower($order->currency),
                'metadata' => [
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);

            $order->update(['payment_intent_id' => $intent->id]);

            return [
                'client_secret'     => $intent->client_secret,
                'payment_intent_id' => $intent->id,
            ];
        } catch (ApiErrorException $e) {
            throw new \RuntimeException('Payment initialization failed: ' . $e->getMessage());
        }
    }

    public function confirmPayment(string $paymentIntentId): PaymentIntent
    {
        return PaymentIntent::retrieve($paymentIntentId);
    }

    public function refund(Order $order, ?int $amountCents = null): void
    {
        try {
            \Stripe\Refund::create(array_filter([
                'payment_intent' => $order->payment_intent_id,
                'amount'         => $amountCents,
            ]));
        } catch (ApiErrorException $e) {
            throw new \RuntimeException('Refund failed: ' . $e->getMessage());
        }
    }
}