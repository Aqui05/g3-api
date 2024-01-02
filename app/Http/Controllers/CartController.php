<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Product;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;

class CartController extends Controller
{
    //
    public function index()
    {
    // Récupérer l'utilisateur authentifié
    $user = auth()->user();

    // Vérifier si l'utilisateur est connecté
    if ($user) {
        // Récupérer le panier de l'utilisateur
        $cart = Cart::where('user_id', $user->id)->first();

        // Vérifier si le panier existe
        if ($cart) {
            // Récupérer les produits du panier
            $cartProducts = $cart->products;

            // Retourner les produits du panier
            return response()->json(['cartProducts' => $cartProducts]);
        } else {
            // Retourner une réponse indiquant que le panier est vide
            return response()->json(['message' => 'Le panier est vide'], 200);
        }
    } else {
        // Retourner une réponse non autorisée si l'utilisateur n'est pas connecté
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    }

    public function addItem(Request $request, $productId)
    {
        $user = Auth::user();

        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $quantityToAdd = $request->input('quantity', 1);

         // Vérifiez si la quantité demandée est disponible
        if ($quantityToAdd > $product->quantity) {
            return response()->json(['message' => 'Product quantity not available'], 400);
        }

        $existingCartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existingCartItem) {
            $existingCartItem->quantity += $quantityToAdd;
            $existingCartItem->save();
        } else {
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'quantity' => $quantityToAdd,
            ]);
        }
        $product->decrement('quantity', $quantityToAdd);

        $cart = Auth::user()->cart; // Récupérez le panier de l'utilisateur connecté
        $product = Product::find($productId); // Récupérez le produit que vous souhaitez ajouter

        if ($cart && $product) {
            $existingProduct = $cart->products()->where('product_id', $productId)->first();

            if ($existingProduct) {
                // Le produit est déjà dans le panier, augmenter la quantité
                $existingProduct->pivot->quantity += $quantityToAdd;
                $existingProduct->pivot->save();
            }
            else {
                // Le produit n'est pas dans le panier, l'ajouter
                $cart->products()->attach($productId, ['quantity' => $quantityToAdd]);
            }
        }

        return response()->json(['message' => 'Product added to cart successfully']);
    }

    public function updateItem(Request $request, $productId)
    {

        $product = Product::find($productId);

        $user = auth()->user();
        $cart = $user->cart;


        if (!$product || !$cart) {
        // Gérer le cas où le produit ou le panier n'est pas trouvé
            return response()->json(['message' => 'Produit ou panier non trouvé.'], 404);
        }
        $newQuantity = $request->input('quantity');


        $bd_product=$product->quantity  +  $cart->quantity;
        // Vérifiez si la quantité demandée est disponible
        if ($newQuantity > $bd_product) {
            return response()->json(['message' => 'Product quantity not available'], 400);
        }
        $bd_quantity = $cart->quantity - $newQuantity;

        if ($bd_quantity <= 0) {
        // La nouvelle quantité est supérieure ou égale à la quantité actuelle dans le panier
        $product->increment('quantity', $bd_quantity);
        }
        else {
            // La nouvelle quantité est inférieure à la quantité actuelle dans le panier
            $product->decrement('quantity', $newQuantity - $cart->quantity);
        }


        $cart->update(['quantity' => $newQuantity]);


        $cart->products()->updateExistingPivot($productId, ['quantity' => $newQuantity]);

        return response()->json(['message' => 'Quantité mise à jour avec succès.' ,'quantity'=>$newQuantity]);

    }

    public function removeItem($productId)
    {
        $user = auth()->user();
        $cart = $user->cart;

        // Vérifiez si le produit est dans le panier de l'utilisateur
        if ($cart->products->contains($productId)) {

            // Supprimez le produit du panier
            $cart->products()->detach($productId);

            // Mettez à jour la quantité dans la base de données (par exemple, rétablissez la quantité du produit)
            $product = Product::find($productId);

            $product->increment('quantity', $cart->quantity);

            $cart->delete();

            return response()->json(['message' => 'Le produit a été supprimé du panier avec succès.']);
        }

        return response()->json(['message' => 'Le produit n\'est pas dans le panier.']);
    }

    public function getTotalPrice()
    {
        $user = Auth::user();
        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();

        $totalPrice = 0;

        foreach ($cartItems as $cartItem) {
            $totalPrice += $cartItem->quantity * $cartItem->product->prix;
        }

        return response()->json(['total_price' => $totalPrice]);
    }
}
