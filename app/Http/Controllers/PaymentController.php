<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
class PaymentController extends Controller
{
    public function purchaseCourse(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'brl',
                    'product_data' => [
                        'name' => $request->course_name,
                    ],
                    'unit_amount' => $request->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('showcase', ['course' => $request->course_id, 'enrolled' => true]),
            'cancel_url' => route('showcase'),
        ]);

        return response()->json(['id' => $session->id]);
    }
}
