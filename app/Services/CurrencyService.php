<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://v6.exchangerate-api.com/v6';

    public function __construct()
    {
        $this->apiKey = config('services.exchangerate.key');
    }

    public function getRates(string $baseCurrency = 'USD'): array
    {
        return Cache::remember("exchange_rates_{$baseCurrency}", 3600, function () use ($baseCurrency) {
            $response = Http::get("{$this->baseUrl}/{$this->apiKey}/latest/{$baseCurrency}");
            if ($response->successful()) {
                return $response->json('conversion_rates', []);
            }
            return [];
        });
    }

    public function convert(float $amount, string $from = 'USD', string $to = 'USD'): float
    {
        if ($from === $to) return $amount;
        $rates = $this->getRates($from);
        $rate = $rates[$to] ?? 1;
        return round($amount * $rate, 2);
    }

    public function getSupportedCurrencies(): array
    {
        return [
            'USD' => '🇺🇸 USD',
            'EUR' => '🇪🇺 EUR',
            'GBP' => '🇬🇧 GBP',
            'JPY' => '🇯🇵 JPY',
            'PHP' => '🇵🇭 PHP',
            'AUD' => '🇦🇺 AUD',
            'CAD' => '🇨🇦 CAD',
            'SGD' => '🇸🇬 SGD',
        ];
    }
}