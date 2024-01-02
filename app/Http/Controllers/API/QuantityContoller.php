<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Categorie;

class QuantityContoller extends Controller
{

    /*public function verifyStock(Request $request)
    {
        $productId = $request->input('product_id');
        $quantiteDemandee = $request->input('quantite_demandee');

        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        if ($product->quantity >= $quantiteDemandee) {
            return response()->json(['message' => 'Stock disponible'], 200);
        } else {
            return response()->json(['message' => 'Stock insuffisant'], 400);
        }
    }*/

    /*public function updateStock(Request $request)
    {
        $productId = $request->input('product_id');
        $quantiteVendue = $request->input('quantite_vendue');

        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        if ($product->quantity >= $quantiteVendue) {
            // Mettez à jour le stock du produit
            $product->quantity -= $quantiteVendue;
            $product->save();

            return response()->json(['message' => 'Stock mis à jour avec succès'], 200);
        } else {
            return response()->json(['message' => 'Stock insuffisant'], 400);
        }
    }*/


    public function getQuantityDispo($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        return response()->json(['quantity' => $product->quantity], 200);
    }
}

