@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                        Shipping Address
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" name="shipping[first_name]" value="{{ old('shipping.first_name') }}" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="shipping[last_name]" value="{{ old('shipping.last_name') }}" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                            <input type="text" name="shipping[address_line_1]" value="{{ old('shipping.address_line_1') }}" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 <span class="text-gray-400">(optional)</span></label>
                            <input type="text" name="shipping[address_line_2]" value="{{ old('shipping.address_line_2') }}" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="shipping[city]" value="{{ old('shipping.city') }}" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State / Province</label>
                            <input type="text" name="shipping[state]" value="{{ old('shipping.state') }}" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                            <input type="text" name="shipping[postal_code]" value="{{ old('shipping.postal_code') }}" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <select name="shipping[country]" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                                <option value="">Select country</option>
                                <option value="PH" {{ old('shipping.country') === 'PH' ? 'selected' : '' }}>Philippines</option>
                                <option value="US" {{ old('shipping.country') === 'US' ? 'selected' : '' }}>United States</option>
                                <option value="GB" {{ old('shipping.country') === 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                <option value="AU" {{ old('shipping.country') === 'AU' ? 'selected' : '' }}>Australia</option>
                                <option value="CA" {{ old('shipping.country') === 'CA' ? 'selected' : '' }}>Canada</option>
                                <option value="SG" {{ old('shipping.country') === 'SG' ? 'selected' : '' }}>Singapore</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                        Coupon Code
                    </h2>
                    <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="Enter coupon code (e.g. WELCOME10)" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
                        Payment Method
                    </h2>
                    <div class="space-y-3" id="payment-options">

                        <label class="flex items-center gap-4 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-indigo-400 transition">
                            <input type="radio" name="payment_method" value="cod" id="pay-cod" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }} class="text-indigo-600">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Cash on Delivery</p>
                                <p class="text-xs text-gray-400">Pay when your order arrives</p>
                            </div>
                        </label>

                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <label class="flex items-center gap-4 p-4 cursor-pointer hover:border-indigo-400 transition">
                                <input type="radio" name="payment_method" value="stripe" id="pay-stripe" {{ old('payment_method') === 'stripe' ? 'checked' : '' }} class="text-indigo-600">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">💳 Credit / Debit Card</p>
                                    <p class="text-xs text-gray-400">Secured by Stripe</p>
                                </div>
                            </label>
                            <div id="stripe-card-section" class="px-4 pb-4 hidden">
                                <div id="card-element" class="p-3 border border-gray-200 rounded-lg bg-white text-sm"></div>
                                <div id="card-errors" class="text-red-500 text-xs mt-2"></div>
                                <p class="text-xs text-gray-400 mt-2">
                                    🔒 Test card: <span class="font-mono font-medium">4242 4242 4242 4242</span> · Any future date · Any CVC
                                </p>
                            </div>
                        </div>

                        <label class="flex items-center gap-4 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-indigo-400 transition">
                            <input type="radio" name="payment_method" value="paypal" id="pay-paypal" {{ old('payment_method') === 'paypal' ? 'checked' : '' }} class="text-indigo-600">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    <span class="text-blue-600 font-bold">Pay</span><span class="text-blue-900 font-bold">Pal</span>
                                </p>
                                <p class="text-xs text-gray-400">Pay securely with your PayPal account</p>
                            </div>
                        </label>

                    </div>
                </div>

            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 mb-5">Order Summary</h2>
                    <div class="space-y-3 mb-5">
                        @foreach ($cart->items as $item)
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                                    @if ($item->product->getFirstMediaUrl('thumbnail'))
                                        <img src="{{ $item->product->getFirstMediaUrl('thumbnail') }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-indigo-50"></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-gray-700 line-clamp-1">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-400">Qty: {{ $item->quantity }}</p>
                                </div>
                                <span class="text-xs font-semibold text-gray-700">${{ number_format($item->unit_price * $item->quantity, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-gray-100 pt-4 space-y-3 text-sm">
                        @php
                            $subtotal = $cart->subtotal;
                            $shipping = $subtotal >= 100 ? 0 : 9.99;
                            $tax      = round($subtotal * 0.10, 2);
                            $total    = $subtotal + $shipping + $tax;
                        @endphp
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="{{ $shipping == 0 ? 'text-green-600' : '' }}">{{ $shipping == 0 ? 'FREE' : '$'.number_format($shipping, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600"><span>Tax (10%)</span><span>${{ number_format($tax, 2) }}</span></div>
                        <div class="border-t border-gray-100 pt-3 flex justify-between font-bold text-gray-900"><span>Total</span><span>${{ number_format($total, 2) }}</span></div>
                    </div>
                    <button type="submit" id="submit-btn" class="mt-6 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition shadow-sm">
                        Place Order →
                    </button>
                    <p class="mt-3 text-xs text-center text-gray-400">By placing your order you agree to our terms of service.</p>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    let stripe, cardElement;
    try {
        stripe = Stripe('{{ config('services.stripe.key') }}');
        const elements = stripe.elements();
        cardElement = elements.create('card', {
            style: {
                base: { fontSize: '14px', color: '#374151', fontFamily: 'ui-sans-serif, system-ui, sans-serif', '::placeholder': { color: '#9CA3AF' } },
                invalid: { color: '#EF4444' }
            }
        });
        cardElement.mount('#card-element');
        cardElement.on('change', function(event) {
            document.getElementById('card-errors').textContent = event.error ? event.error.message : '';
        });
    } catch(e) {
        console.error('Stripe init error:', e);
    }

    document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const stripeSection = document.getElementById('stripe-card-section');
            stripeSection.classList.toggle('hidden', this.value !== 'stripe');
        });
    });

    if (document.getElementById('pay-stripe').checked) {
        document.getElementById('stripe-card-section').classList.remove('hidden');
    }

    const form = document.getElementById('checkout-form');
    const submitBtn = document.getElementById('submit-btn');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        console.log('Payment method selected:', paymentMethod);

        if (paymentMethod === 'cod' || paymentMethod === 'paypal') {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Redirecting...';
            form.submit();
            return;
        }

        if (paymentMethod === 'stripe') {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';
            const { paymentMethod: pm, error } = await stripe.createPaymentMethod({ type: 'card', card: cardElement });
            if (error) {
                document.getElementById('card-errors').textContent = error.message;
                submitBtn.disabled = false;
                submitBtn.textContent = 'Place Order →';
                return;
            }
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'stripe_payment_method';
            input.value = pm.id;
            form.appendChild(input);
            form.submit();
        }
    });
</script>
@endpush