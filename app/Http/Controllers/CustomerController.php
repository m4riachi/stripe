<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;

class CustomerController extends Controller
{
    protected $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function createCustomer(Request $request)
    {
        $email = $request->query('email');

        if (!$email) {
            return response()->json(['error' => 'Email is required'], 400);
        }

        $customer = $this->stripe->customers->create([
            'email' => $email,
        ]);

        $setupIntent = $this->stripe->setupIntents->create([
            'customer' => $customer->id,
        ]);

        return view('payment', [
            'setupIntent' => $setupIntent,
            'customer' => $customer
        ]);
    }

    // Charge the customer
    public function chargeCustomer(Request $request)
    {
        $customerId = $request->query('customer_id');
        $paymentMethodId = $request->query('payment_method_id');
        $amount = $request->query('amount');

        if (!$customerId || !$paymentMethodId || !$amount) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $paymentIntent = $this->stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => 'usd',
            'customer' => $customerId,
            'payment_method' => $paymentMethodId,
            'off_session' => true,
            'confirm' => true,
        ]);

        return response()->json($paymentIntent);
    }
}
