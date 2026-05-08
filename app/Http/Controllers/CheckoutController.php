<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\PayPalService;
use Illuminate\Http\RedirectResponse;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService    $cartService,
        private readonly OrderService   $orderService,
        private readonly PaymentService $paymentService,
        private readonly PayPalService  $payPalService,
    ) {}

    public function index()
    {
        $cart = $this->cartService->getOrCreateCart();
        $cart->load('items.product.media', 'items.variant');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                             ->with('error', 'Your cart is empty.');
        }

        return view('checkout.index', compact('cart'));
    }

    public function store(PlaceOrderRequest $request): RedirectResponse
    {
        \Log::info('Checkout store called', [
            'payment_method' => $request->payment_method,
            'cart_items'     => $this->cartService->getOrCreateCart()->items()->count(),
        ]);

        $order = $this->orderService->createFromCart(
            addressData: $request->only('shipping', 'billing'),
            couponCode:  $request->coupon_code,
        );

        \Log::info('Order created: ' . $order->order_number);

        if ($request->payment_method === 'stripe') {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

                $intent = \Stripe\PaymentIntent::create([
                    'amount'               => (int) ($order->total_amount * 100),
                    'currency'             => 'usd',
                    'payment_method'       => $request->stripe_payment_method,
                    'confirmation_method'  => 'manual',
                    'confirm'              => true,
                    'return_url'           => route('orders.show', $order),
                    'metadata'             => [
                        'order_id'     => $order->id,
                        'order_number' => $order->order_number,
                    ],
                ]);

                if ($intent->status === 'succeeded') {
                    $order->update([
                        'payment_status'    => 'paid',
                        'payment_method'    => 'stripe',
                        'payment_intent_id' => $intent->id,
                    ]);
                    $this->orderService->updateStatus($order, 'confirmed', 'Payment successful via Stripe.');
                    return redirect()->route('orders.show', $order)
                                     ->with('success', '🎉 Order placed and payment successful!');
                }

                if ($intent->status === 'requires_action') {
                    $order->update([
                        'payment_intent_id' => $intent->id,
                        'payment_method'    => 'stripe',
                    ]);
                    return redirect()->route('orders.show', $order)
                                     ->with('warning', 'Additional verification required.');
                }

            } catch (\Stripe\Exception\CardException $e) {
                $order->update(['status' => 'cancelled', 'payment_status' => 'failed']);
                return redirect()->route('checkout.index')
                                 ->with('error', 'Card declined: ' . $e->getMessage());
            } catch (\Exception $e) {
                $order->update(['status' => 'cancelled', 'payment_status' => 'failed']);
                return redirect()->route('checkout.index')
                                 ->with('error', 'Payment failed: ' . $e->getMessage());
            }
        }

        if ($request->payment_method === 'paypal') {
            \Log::info('Redirecting to PayPal for order: ' . $order->order_number);
            $this->orderService->updateStatus($order, 'pending', 'Awaiting PayPal payment.');
            return redirect()->route('paypal.redirect', $order);
        }

        // Cash on delivery
        $this->orderService->updateStatus($order, 'confirmed', 'Cash on delivery order confirmed.');
        return redirect()->route('orders.show', $order)
                         ->with('success', 'Order placed successfully!');
    }
}