<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    private string $baseUrl;
    private string $clientId;
    private string $secret;

    public function __construct()
    {
        $this->baseUrl  = config('services.paypal.base_url');
        $this->clientId = config('services.paypal.client_id');
        $this->secret   = config('services.paypal.secret');
    }

    private function getAccessToken(): string
    {
        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        Log::info('PayPal token status: ' . $response->status());

        return $response->json('access_token');
    }

    public function createOrder(Order $order): array
    {
        $token = $this->getAccessToken();

        Log::info('PayPal creating order for: ' . $order->order_number . ' amount: ' . $order->total_amount);

        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $order->order_number,
                        'amount'       => [
                            'currency_code' => 'USD',
                            'value'         => number_format($order->total_amount, 2, '.', ''),
                        ],
                        'description' => 'Order ' . $order->order_number,
                    ],
                ],
                'application_context' => [
                    'return_url' => route('paypal.success', $order),
                    'cancel_url' => route('paypal.cancel', $order),
                    'brand_name' => config('app.name'),
                    'user_action' => 'PAY_NOW',
                ],
            ]);

        Log::info('PayPal createOrder status: ' . $response->status());
        Log::info('PayPal createOrder body: ' . $response->body());

        return $response->json();
    }

    public function captureOrder(string $paypalOrderId): array
    {
        $token = $this->getAccessToken();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->baseUrl}/v2/checkout/orders/{$paypalOrderId}/capture");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        Log::info('PayPal capture curl result: ' . $result);

        return json_decode($result, true) ?? [];
    }
}