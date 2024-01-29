<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Models\rating;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    //
    public function addComment(Request $request ,$productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validator =Validator::make(
            $request->all(),[
                'content'=>'required|string',
            ]
            );
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(),400);
            }

            $comment = new Comment([
            'content' => $request->content,
            'user_id' => auth()->user()->id,
            'product_id' => $productId,
        ]);

        $comment->save();

        return response()->json($comment);
    }

    public function updateComment(Request $request, $commentId)
    {
        $comment = Comment::findorFail($commentId);

        if (Auth::id() !== $comment->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content'=>'required|string',
        ]);

        $comment->update([
            'content' => $request->content,
        ]);

        return response()->json(['comment' => $comment], 200);
    }


    public function deleteComment(Request $request,$commentId)
    {
        $comment = Comment::find($commentId);

        if(is_null($comment)){
            return response()->json(['message'=>'Produit introuvable'],404);
        }

        // Vérifiez que l'utilisateur est le propriétaire du comment
        if (Auth::user()->id !== $comment->user_id) {
            return response()->json(['message' => 'Vous n\'avez pas la permission de supprimer ce commentaire.'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'Commentaire spprimé avec succès']);
    }


    public function viewComment($productId)
    {
        $comments = Comment::where('product_id', $productId)->get();

        foreach ($comments as $comment) {
            echo "Comment: {$comment->content} by {$comment->user->name}";
        }
    }

    public function addReply(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $reply = new Reply([
            'content' => $request->content,
            'user_id' => auth()->user()->id,
        ]);

        $comment->replies()->save($reply);

        return response()->json(['message' => 'Reply added successfully',$reply]);
    }



    public function rateProduct(Request $request, $productId)
{
    $user = auth()->user();

    $request->validate([
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        if ($user) {    // Vérifiez si l'utilisateur a déjà noté ce produit
    $existingRating = $user->ratingForProduct(Product::find($productId));

    if ($existingRating) {
        // Si l'utilisateur a déjà noté, mettez à jour la note existante
        $existingRating->update(['rating' => $request->input('rating')]);
    } else {
        // Sinon, créez une nouvelle note
        $user->ratings()->create([
            'product_id' => $productId,
            'rating' => $request->input('rating'),
        ]);
    }

    $product = Product::find($productId);

    $product->update(['rating' => $product->averageRating()]);
}

return response()->json(['message' => 'Note enregistrée avec succès']);

}


}
