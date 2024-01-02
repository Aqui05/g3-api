<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    //
    public function index ()
    {
        return view ('index');
    }

    public function checkout ()
    {
        \Stripe\Stripe::setApiKey(config('stripe.sk'));
        $session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'cfa',
                        'product_data' =>[
                            'name' => 'PAYMENT',
                        ],
                        'unit_amount' => 500,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'PAYMENT',
            'success_url' => route('#'),
            'cancel_url' => route('index'),
        ]);
        return redirect()->away($session->url);
    }

    public function success ()
    {
        return view ('index');
    }
}
