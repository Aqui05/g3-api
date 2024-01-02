<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PromotionController extends Controller
{

    public function addPromotion(Request $request, Product $product)
{

    $user = Auth::user();

    // Vérifiez que l'utilisateur est un vendeur
        if ($user->role !== 'seller') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Vérifier si le produit appartient à ce vendeur
    if ($user->id !== $product->user_id) {
        return response()->json(['message' => 'Vous n\'êtes pas autorisé à ajouter une promotion à ce produit.'], 403);
    }

    $request->validate([
        'discount_percent' => 'required|numeric|between:1,100',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
    ]);

    // Créer une nouvelle promotion pour le produit
    $promotion = new Promotion([
        'discount_percent' => $request->discount_percent,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
    ]);

    // Associer la promotion au produit
    $product->promotions()->save($promotion);

    return response()->json(['message' => 'Promotion added successfully']);
}

public function createDiscountCode(Request $request, Product $product)
{
    $user = Auth::user();

    // Vérifiez que l'utilisateur est un vendeur
        if ($user->role !== 'seller') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Vérifier si le produit appartient à ce vendeur
    if ($user->id !== $product->user_id) {
        return response()->json(['message' => 'Vous n\'êtes pas autorisé à ajouter un code promo à ce produit.'], 403);
    }

    $request->validate([
        'code' => 'required|unique:discount_codes',
        'discount_percent' => 'required|numeric|between:1,100',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
    ]);

    // Créer un nouveau code de réduction pour le produit
    $discountCode = new DiscountCode([
        'code' => $request->code,
        'discount_percent' => $request->discount_percent,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
    ]);

    // Associer le code de réduction au produit
    $product->discountCodes()->save($discountCode);

    return response()->json(['message' => 'Code de réduction créé avec succès']);
}



public function getProductsWithValidPromotion()
{
    $today = now();

    // Récupérez les produits avec une promotion encore valide
    $products = Product::whereHas('promotions', function ($query) use ($today) {
        $query->where('start_date', '<=', $today)
              ->where('end_date', '>=', $today);
    })->get();

    return response()->json(['products' => $products]);
}






}
