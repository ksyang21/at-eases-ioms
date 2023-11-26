<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postage extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = [
        'area',
        'postcode',
        'delivery_fee'
    ];
}
