<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\MonthlySalesSummary;
use App\Models\Cart;
use App\Models\DiscountCode;
use App\Models\Promotion;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function createOrder(Request $request,$productId)
    {
        $user = Auth::user();
        $product = Product::findOrFail($productId);

        $quantity = $request->input('quantity');
        $userEnteredCode = $request->input('code');

        // Vérifiez si la quantité demandée est disponible
        if ($quantity > $product->quantity) {
            return response()->json(['message' => 'Product quantity not available'], 400);
        }
        //Verification du code
        $code = DiscountCode::where('code', $userEnteredCode)->first();


            $order = new Order([
            'status' => 'pending',
            'user_id' => $user->id
        ]);
        $order->save();

    // Ajouter le produit à la commande avec la quantité
    $order->products()->attach($product->id, ['quantity' => $quantity]);


// Calculer le total
$total = $order->products()->sum(DB::raw('prix * order_product.quantity'));

if ($code) {
    // Le code promotionnel est valide, appliquez la réduction ou la logique associée
    $discountPercent = $code->discount_percent;
    $total = $total - ($total * $discountPercent / 100);
} else {
    // Le code promotionnel n'est pas valide, affichez un message d'erreur
    return redirect()->back()->with('error', 'Code promotionnel invalide.');
}

$currentDate = now();
$promotion = Promotion::where('start_date', '<=', $currentDate)
                     ->where('end_date', '>=', $currentDate)
                     ->first();

if ($promotion) {
    // Appliquer la réduction ou la logique associée à la promotion
    $discountPercent = $promotion->discount_percent;
    $total = $total - ($total * $discountPercent / 100);
}

// Mettre à jour le total de la commande
$order->update(['total' => $total]);


        $order->products()->attach($product->id, ['quantity' => $quantity]);
        //Mise a jour de la quantity in the BD
        $product->decrement('quantity', $quantity);

         $order->delivery()->create([
        'address' => $request->input('address'),
        'city' => $request->input('city'),
        'state' => $request->input('state'),
        'postal_code' => $request->input('postal_code'),
    ]);


        return response()->json(['message' => 'Order created successfully', 'order' => $order]);
    }



    // list of all order (HISTORY)
    public function getUserOrders()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->get();

        return response()->json(['orders' => $orders]);
    }

    public function getOrdersForSeller()
    {
        $user = Auth::user();

        if (Auth::user()->role !== 'seller') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Récupérez les commandes associées aux produits du vendeur
        $orders = Order::whereHas('products', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();


        return response()->json(['orders' => $orders]);
    }

    public function createOrderFromCart(Request $request)
    {
        $user = auth()->user();
        $cart = $user->cart;

        $userEnteredCode = $request->input('code');
    $order = new Order([
        'status' => 'pending',
        'user_id' => $user->id
    ]);
    $order->save();

    $code = DiscountCode::where('code', $userEnteredCode)->first();

        foreach ($cart->products as $product) {
    $quantity = $product->pivot->quantity;

    // Ajoutez le produit à la commande avec la quantité
    $order->products()->attach($product->id, ['quantity' => $quantity]);
}

        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();

        $totalPrice = 0;

        foreach ($cartItems as $cartItem) {
            $totalPrice += $cartItem->quantity * $cartItem->product->prix;
        }

        if ($code) {
    // Le code promotionnel est valide, appliquez la réduction ou la logique associée
    $discountPercent = $code->discount_percent;
    $totalPrice = $totalPrice - ($totalPrice * $discountPercent / 100);
} else {
    // Le code promotionnel n'est pas valide, affichez un message d'erreur
    return redirect()->back()->with('error', 'Code promotionnel invalide.');
}

        $order->update(['total' => $totalPrice]);

        // Supprimer le panier après avoir créé la commande
    $cart->products()->detach();


    return response()->json(['order'=>$order,'produit'=> $cartItems,'total'=>$totalPrice]);
    }



    public function getOrderProducts($orderId)
    {
        $order = Order::findOrFail($orderId);

        $products = $order->products;

        return response()->json(['products' => $products]);
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        $user = Auth::user();

        $order = Order::where('id', $orderId)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->status = $request->input('status');
        $order->save();

        return response()->json(['message' => 'Order status updated successfully']);
    }












public function calculateMonthlySales()
{
    $currentMonth = now()->month;
    $currentYear = now()->year;

    // Réinitialisez monthly_sales au début de chaque mois
    User::where('role', 'seller')->update(['monthly_sales' => 0.00]);

    // Calcul des ventes mensuelles par seller
    $monthlySales = Order::whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->selectRaw('user_id, SUM(total) as total_sales')
        ->groupBy('user_id')
        ->get();

    // Mettez à jour les valeurs monthly_sales dans la table des utilisateurs
    foreach ($monthlySales as $sales) {
        $user = User::find($sales->user_id);

        // Mise à jour de la valeur monthly_sales dans la table des utilisateurs
        $user->update(['monthly_sales' => $sales->total_sales]);

        // Envoi de l'e-mail au vendeur
        Mail::to($user->email)->send(new MonthlySalesSummary($user, $sales->total_sales));
    }

    return response()->json(['message' => 'Monthly sales calculated successfully']);
}

}

