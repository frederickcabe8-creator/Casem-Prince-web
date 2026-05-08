<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\PayPalService;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    public function __construct(
        private readonly PayPalService $payPalService,
        private readonly OrderService  $orderService,
    ) {}

    public function redirect(Order $order)
    {
        try {
            $paypalOrder = $this->payPalService->createOrder($order);

            $order->update(['payment_intent_id' => $paypalOrder['id']]);

            $approvalUrl = collect($paypalOrder['links'])
                ->firstWhere('rel', 'approve')['href'];

            return redirect($approvalUrl);

        } catch (\Exception $e) {
            return redirect()->route('orders.show', $order)
                             ->with('error', 'PayPal error: ' . $e->getMessage());
        }
    }

    public function success(Request $request, Order $order)
    {
        try {
            \Log::info('PayPal success callback', $request->all());

            $paypalOrderId = $request->token;
            $capture = $this->payPalService->captureOrder($paypalOrderId);

            \Log::info('PayPal capture response', $capture ?? []);

            $status = $capture['status'] ?? null;

            if ($status === 'COMPLETED') {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'paypal',
                ]);

                $this->orderService->updateStatus(
                    $order, 'confirmed', 'Payment successful via PayPal.'
                );

                return redirect()->route('orders.show', $order)
                                 ->with('success', '🎉 Order placed and PayPal payment successful!');
            }

            \Log::warning('PayPal unexpected response: ' . json_encode($capture));

            return redirect()->route('orders.show', $order)
                             ->with('error', 'PayPal payment not completed. Response: ' . json_encode($capture));

        } catch (\Exception $e) {
            \Log::error('PayPal success error: ' . $e->getMessage());
            return redirect()->route('orders.show', $order)
                             ->with('error', 'PayPal error: ' . $e->getMessage());
        }
    }

    public function cancel(Order $order)
    {
        return redirect()->route('checkout.index')
                         ->with('error', 'PayPal payment was cancelled.');
    }
}