<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LowStockNotification extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable =['user_id','quantity','product_id'];

}
