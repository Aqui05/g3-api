<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['user_id', 'product_id', 'content',];

    // Relation avec l'utilisateur (auteur du commentaire)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec le post (article auquel le commentaire est associé)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
}
