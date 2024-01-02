<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable =['code','discount_percent','start_date','end_date'];
}
