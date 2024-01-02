<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\subcategory;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Categorie;

class CategoryContoller extends Controller
{

    public function filter_category($categorie)
    {
        $categorieId = Categorie::where('name', $categorie)->firstOrFail()->id;
        $products = Product::where('categorie_id', $categorieId)->get();
        return response()->json(['produits' => $products]);
    }

    public function filter_subcategory($subcategory)
    {
        $subCategoryId = subcategory::where('name', $subcategory)->firstOrFail()->id;
        $products = Product::where('categorie_id', $subCategoryId)->get();
        return response()->json(['produits' => $products]);
    }


    public function filterProducts(Request $request)
    {
        $query = Product::query();

        // Filtrage par sous-catégorie
        if ($request->has('subcategory')) {
            $query->where('subcategory_id', $request->input('subcategory'));
        }

        // Filtrage par prix minimum
        if ($request->has('min_price')) {
            $query->where('prix', '>=', $request->input('min_price'));
        }

        // Filtrage par prix maximum
        if ($request->has('max_price')) {
            $query->where('prix', '<=', $request->input('max_price'));
        }

        // Filtrage par date d'ajout (les produits ajoutés après la date spécifiée)
        if ($request->has('added_after')) {
            $query->where('created_at', '>=', $request->input('added_after'));
        }

        // Filtrage par note
        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->input('min_rating'));
        }

        // Ordre alphabétique des noms
        if ($request->has('alphabetical_order')) {
            $query->orderBy('name', $request->input('alphabetical_order'));
        }

        // Récupérer les résultats
        $products = $query->get();

        return response()->json(['products' => $products]);
    }
}
