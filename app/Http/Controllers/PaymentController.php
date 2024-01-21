<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kouyatekarim\Momoapi\Products\Collection;

class PaymentController extends Controller
{

    public function payer($orderId,Request $request)
    {
        $order = Order::find($orderId);
        $user = Auth::user();

        $payement = new Payment([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'amount' => $order->total,
            'phone_number' => $request->input('phone_number'),
        ]);
        $payement->save();

        return response()->json([$payement,'message' => 'Payment effectuated successfully']);
    }


public function callback(Request $request)
{
    return response()->json(['Message']);
}

}
