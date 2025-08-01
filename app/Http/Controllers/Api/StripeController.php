<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function createCheckoutSession(Request $request): JsonResponse
    {
        $plan = $request->input('plan');
        $prices = [
            'basic' => env('STRIPE_PRICE_BASIC'),
            'pro' => env('STRIPE_PRICE_PRO'),
            'premium' => env('STRIPE_PRICE_PREMIUM'),
        ];

        if (!isset($prices[$plan])) {
            return response()->json(['message' => 'Invalid plan'], 422);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'line_items' => [
                [
                    'price' => $prices[$plan],
                    'quantity' => 1,
                ],
            ],
            'success_url' => config('app.url') . '/complete?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => config('app.url') . '/plans',
        ]);

        return response()->json(['url' => $session->url]);
    }

    public function getSession(string $id): JsonResponse
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = StripeSession::retrieve($id);
        $paid = $session->payment_status === 'paid';

        return response()->json(['paid' => $paid]);
    }
}
