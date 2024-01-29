<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subcategory extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'name'];


    /*public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }*/

    public function categorie()
{
    return $this->belongsTo(Categorie::class, 'category_id');
}


    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
