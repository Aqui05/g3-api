<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Promotion extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable =['discount_percent','start_date','end_date','product_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
