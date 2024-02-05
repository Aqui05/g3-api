<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    //
  public function toggleFavorite(Request $request, $productId)
    {
        $user = auth()->user();

        // Vérifiez si le produit est déjà dans les favoris de l'utilisateur
        $isFavorite = $user->favoriteProducts()->where('product_id', $productId)->exists();

        // Si le produit est dans les favoris, retirez-le, sinon ajoutez-le
        if ($isFavorite) {

            $user->favoriteProducts()->detach($productId);

            $message = 'Produit retiré des favoris avec succès';

        }
        else {
            $user->favoriteProducts()->attach($productId);
            $message = 'Produit ajouté aux favoris avec succès';
        }

        return response()->json(['message' => $message]);
    }


    public function removeFavorite(Request $request, $productId)
    {
        $user = auth()->user();

        // Vérifiez si le produit est déjà dans les favoris de l'utilisateur
        $isFavorite = $user->favoriteProducts()->where('product_id', $productId)->exists();

        // Si le produit est dans les favoris, retirez-le, sinon ajoutez-le
        if ($isFavorite) {

            $user->favoriteProducts()->detach($productId);

            $message = 'Produit retiré des favoris avec succès';

        }
        else {
            $message = 'Le Produit n\'est pas dans les favoris';
        }

        return response()->json(['message' => $message]);
    }

    public function getFavorites()
    {
        $user = auth()->user();
        $favorites = $user->favoriteProducts;

        return response()->json(['favorites' => $favorites]);
    }
}
