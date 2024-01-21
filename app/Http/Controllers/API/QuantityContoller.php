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


    public function getQuantityDispo($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        return response()->json(['quantity' => $product->quantity], 200);
    }
}

