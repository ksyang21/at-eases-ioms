<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageProduct extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
      'package_id',
      'product_id',
      'quantity',
      'price',
    ];
}
