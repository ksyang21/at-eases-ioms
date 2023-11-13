<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesCampaign extends Model
{
    use HasFactory;

    public $timestamps = TRUE;

    protected $fillable = [
        'name',
        'period_start',
        'period_end',
        'sales_target_amount',
        'current_amount',
        'total_product_sold',
        'type'
    ];
}
