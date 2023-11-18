<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'seller_id',
        'name',
        'address',
        'phone',
        'email'
    ];

    public function seller(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }
}
