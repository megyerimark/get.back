<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $user = $request->user();

        // Létrehozunk egy Stripe Checkout sessiont
        // 'price_123...' az a kód, amit a Stripe Dashboardon a termékednél kapsz
        /* $checkoutSession = $user->newSubscription('default', 'price_123456789')
            ->checkout([
                'success_url' => 'https://getingo.hu/billing/success',
                'cancel_url' => 'https://getingo.hu/billing/cancel',
            ]); */
            $checkoutSession = $user->newSubscription('default', 'price_teszt_kod')
    ->checkout([
        'success_url' => env('STRIPE_SUCCESS_URL'),
        'cancel_url' => env('STRIPE_CANCEL_URL'),
    ]);

        return response()->json(['url' => $checkoutSession->url]);
    }
}
