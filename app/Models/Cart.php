<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\User;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'quantity','user_id'];
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_product')->withPivot('quantity');
    }

    public function clearCart()
{
    $this->products()->detach();
}

public function calculateQuantity($productId)
{
    return $this->getQuantity($productId); // Vous pouvez avoir une méthode getQuantity dans votre modèle Cart
}

public function getQuantity($productId)
{
    // Utilisez la relation products pour accéder aux produits dans le panier
    $product = $this->products()->where('product_id', $productId)->first();

    // Vérifiez si le produit existe dans le panier
    if ($product) {
        // Retournez la quantité associée au produit dans le panier
        return $product->pivot->quantity;
    }

    // Si le produit n'est pas trouvé dans le panier, retournez 0 ou une valeur par défaut
    return 0;
}

// Modèle Cart.php

public function refreshTotal()
{
    $this->total = $this->products()->sum('quantity * prix');
    $this->save();
}


}
