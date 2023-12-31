<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMethod extends Model
{
    use HasFactory;

    public    $timestamps = TRUE;
    protected $fillable   = [
        'delivery_method',
        'status',
    ];
}
