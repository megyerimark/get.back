<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $user = $request->user();

        
            $priceId = env('STRIPE_PRICE_ID');
            $checkoutSession = $user->newSubscription('default', $priceId)
            ->trialDays(7)
            ->checkout([
                'success_url' => 'http://localhost:4200/agent?success=true',
                'cancel_url' => 'http://localhost:4200/agent?canceled=true',
            ]);
        return response()->json(['url' => $checkoutSession->url]);
    }

    // Létrehozunk egy Stripe Checkout sessiont
        // 'price_123...' az a kód, amit a Stripe Dashboardon a termékednél kapsz
        /* $checkoutSession = $user->newSubscription('default', 'price_123456789')
            ->checkout([
                'success_url' => 'https://getingo.hu/billing/success',
                'cancel_url' => 'https://getingo.hu/billing/cancel',
            ]); */

}

