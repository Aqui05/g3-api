<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Order extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable =['total','status','user_id'];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }

    public function delivery()
{
    return $this->hasOne(Delivery::class);
}

public function payment()
{
    return $this->hasOne(Payment::class);
}

public function user()
    {
        return $this->belongsTo(User::class);
    }
}
