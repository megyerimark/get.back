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
            $priceId = env('STRIPE_PRICE_ID');
            $checkoutSession = $user->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => 'http://localhost:4200/agent?success=true',
                'cancel_url' => 'http://localhost:4200/agent?canceled=true',
            ]);

        // 4. Visszaadjuk a kigenerált Stripe URL-t az Angularnak
        return response()->json(['url' => $checkoutSession->url]);
    }

}

