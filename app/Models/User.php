<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Categorie;

class User extends Authenticatable implements JWTSubject,MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'company',
        'provider',
        'provider_id',
        'provider_token',
        'monthly_sales',
        'numeroIDCard_Passport',
        'Description_boutique',
        'imgIDCard_Passport',
        'Emplacement_boutique',
        'photo_coverage',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class,'user_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getCartProducts()
{
    return $this->cart->products;
}

public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function ratingForProduct(Product $product)
    {
        return $this->ratings()->where('product_id', $product->id)->first();
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function favoriteProducts()
{
    return $this->belongsToMany(Product::class, 'user_favorite_product', 'user_id', 'product_id')
        ->withTimestamps();
}












public static function generateUsername($username)
{
    if($username === null)
    {
        $username = Str::lower(Str::random(8));
    }
    if(User::where('username', $username)->exists())
    {
        $newUsername = $username.Str::lower(Str::random(3));
        $username = self::generateUsername($newUsername);
    }
    return $username;
}

}
