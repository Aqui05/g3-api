<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;


    public $timestamps = false;

    protected $fillable = ['user_id', 'amount', 'status','phone_number','currency','order_id','payment_method'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
