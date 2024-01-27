<?php

namespace App\Models;

use App\Events\ProductQuantityChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categorie;
use App\Models\User;
use App\Models\Promotion;
use App\Models\Order;
use Illuminate\Support\Carbon;

class Product extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable =['name','description','prix','user_id','quantity','categorie_id', 'rating','subcategory_id'];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class,'categorie_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity');
    }

    public function comments()
    {
        return $this->hasMany(Product::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }


    //Vérifier si un produit a une promotion active
    public function hasActivePromotion()
    {
        return $this->promotions()->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->exists();
    }


    // Code de réduction
    public function discountCodes()
    {
        return $this->hasMany(DiscountCode::class);
    }


     public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

      public function setQuantityAttribute($value)
    {
        $originalQuantity = $this->attributes['quantity'] ?? 0;

        if ($value != $originalQuantity) {
            event(new ProductQuantityChanged($this, $originalQuantity, $value));
        }

        $this->attributes['quantity'] = $value;
    }




}
