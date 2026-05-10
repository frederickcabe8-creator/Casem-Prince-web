<?php

use App\Services\CurrencyService;

if (!function_exists('formatPrice')) {
    function formatPrice(float $amount): string
    {
        $currency = session('currency', 'USD');
        $service = app(CurrencyService::class);
        $converted = $service->convert($amount, 'USD', $currency);

        $symbols = [
            'USD' => '$', 'EUR' => '€', 'GBP' => '£',
            'JPY' => '¥', 'PHP' => '₱', 'AUD' => 'A$',
            'CAD' => 'C$', 'SGD' => 'S$',
        ];

        $symbol = $symbols[$currency] ?? $currency . ' ';
        return $symbol . number_format($converted, 2);
    }
}