<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function switch(Request $request, CurrencyService $currencyService)
    {
        $currency = $request->input('currency', 'USD');
        $supported = array_keys($currencyService->getSupportedCurrencies());

        if (in_array($currency, $supported)) {
            session(['currency' => $currency]);
        }

        return back();
    }
}