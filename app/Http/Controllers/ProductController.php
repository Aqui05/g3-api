<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\subcategory;
use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function addCategory(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'description' => 'string',
            'type' => 'string',
            ]);


        $categorie = new Categorie([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
        ]);

        $categorie->save();

        return response()->json(['message' => 'Categorie ajouté avec succès','categorie'=>$categorie]);

    }

    public function addSubcategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);


        // Ajoutez le produit associé à l'utilisateur actuel
        $subcategory = new Subcategory([
            'name' => $request->name,
        ]);
        $category = Categorie::find($request->input('category_id'));

        $subcategory->categorie()->associate($category);

        $subcategory->save();

        return response()->json(['message' => 'Sous catégorie ajouté avec succès','subcategory'=>$subcategory]);

    }

    //
    public function addProduct(Request $request)
    {
        // Vérifiez que l'utilisateur est un vendeur
        if (Auth::user()->role !== 'seller') {
            return response()->json(['message' => 'Vous n\'avez pas la permission d\'ajouter des produits.'], 403);
        }

        // Validate les data du formulaire si nécessaire
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'prix' => 'required|numeric',
            'quantity' =>'required',
            'categorie_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'photo_path' => 'string|url'
        ]);


        // Ajoutez le produit associé à l'utilisateur actuel
        $product = new Product([
            'name' => $request->name,
            'description' => $request->description,
            'prix' => $request->prix,
            'quantity'=> $request->quantity,
            'photo_path' => $request->photo_path,
            'user_id' => Auth::id(),
        ]);
        $category = Categorie::find($request->input('categorie_id'));
        $subcategory = subcategory::find($request->input('subcategory_id'));

        $product->categorie()->associate($category);
        $product->subcategory()->associate($subcategory);

        $product->save();

        return response()->json(['message' => 'Produit ajouté avec succès','Produit'=>$product]);
    }

    public function getProducts()
    {
        // Vérifiez que l'utilisateur est un vendeur
        if (Auth::user()->role !== 'seller') {
            return response()->json(['message' => 'Vous n\'êtes pas vendeur.'], 403);
        }

        //utilisateur actuel
        $seller = Auth::user();
        // Récupérez la liste des produits associés à cet utilisateur
        $products = Product::where('user_id', $seller->id)->get();
        return response()->json(['products' => $products]);
    }

    public function getAllProducts()
    {
        return response()->json([Product::all(),200]);
    }



    public function getProductById($id)
    {
        $product = Product::find($id);
        if(is_null($product)){
            return response()->json(['message'=>'Produit introuvable'],404);
        }
        return response()->json(Product::find($id),200);
    }

    public function updateProduct(Request $request, $id)
    {
            // Récupérez le produit à mettre à jour
        $product = Product::findorFail($id);

        // Vérifiez si l'utilisateur actuel est le propriétaire du produit
        if (Auth::id() !== $product->user_id) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à mettre à jour ce produit.'], 403);
        }

        // Validez les données de la requête
        $request->validate([
            'name' => 'required|string',
            'prix' => 'required|numeric',
            'categorie_id' => 'required|exists:categories,id',
            'description' => 'string',
            'quantity' =>'required',
            'subcategory_id' => 'required|exists:subcategories,id',
            'photo_path' => 'string'
        ]);

        // Mettez à jour les données du produit
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'prix' => $request->prix,
            'quantity' => $request->quantity,
            'photo_path' => $request->photo_path,
            'categorie_id' => $request->categorie_id,
            'subcategory_id' => $request->subcategory_id,
        ]);

        return response()->json(['produit' => $product], 200);
    }

    public function deleteProduct(Request $request,$id)
    {
        $product = Product::find($id);

        if(is_null($product)){
            return response()->json(['message'=>'Produit introuvable'],404);
        }

        // Vérifiez que l'utilisateur est le propriétaire du produit
        if (Auth::user()->id !== $product->user_id) {
            return response()->json(['message' => 'Vous n\'avez pas la permission de supprimer ce produit.'], 403);
        }

        $product->delete();
        return response()->json(['message' => 'Produit spprimé avec succès']);
    }


    public function searchProduct(Request $request)
    {
        // Validez les données du formulaire si nécessaire
        $request->validate([
            'query' => 'required|string',
        ]);

        // Effectuez la recherche des produits
        $results = Product::where('name', 'like', '%' . $request->input('query') . '%')
            ->orWhere('description', 'like', '%' . $request->input('query') . '%')
            ->get();

        return response()->json(['results' => $results]);
    }
}


