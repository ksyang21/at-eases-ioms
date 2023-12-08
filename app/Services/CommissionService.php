<?php
namespace App\Services;

use App\Models\Order;

class CommissionService
{
    public function calculateCommission(Order $order) {
        // Get required data
        $details = $order->details;
        $seller = $order->seller;
        $group = $seller->groupDetails;

    }
}
