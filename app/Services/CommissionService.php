<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Customer;
use App\Models\GroupMember;
use App\Models\Order;
use App\Models\Postage;

class CommissionService
{
    public function calculateCommission(Order $order)
    {
        /**
         * Check if order commission has already been calculated before
         * For example, when order had been returned but completed again (IDK what would happen, just in case lol)
         */
        $comm_calculated = Commission::where('order_id', $order->id)->first();
        if (!$comm_calculated) {
            // Get required data
            $details = $order->details;
            $seller  = $order->seller;
            $group   = $seller->groupDetails;
            $members = $group->group->members;

            $total_sales_price = $order->total_price;
            $customer_id       = $order->customer_id;

            // Calculate tier (based on current month PV)
            // Total price = PV added
            $current_month_pv = Order::whereIn('seller_id', $members->pluck('seller_id'))->where('status', 'completed')->sum('total_price');
            $tier_bonus       = $this->getTierBonusOnPv($current_month_pv);

            // Calculate total cost
            $total_costs            = 0;
            $total_product_quantity = 0;
            foreach ($details as $detail) {
                $product                = $detail->product;
                $cost                   = $product->cost * $detail->quantity;
                $total_product_quantity += $detail->quantity;
                $total_costs            += floatval($cost);
            }

            // Get customer info to calculate postage
            $customer = Customer::find($customer_id);
            $postcode = $customer->postcode;
            $postage  = Postage::where('postcode', $postcode)->first();

            $commission = $total_sales_price + ($total_product_quantity * $tier_bonus) - $total_costs - $postage->delivery_fee;

            Commission::create([
                'seller_id'  => $seller->id,
                'order_id'   => $order->id,
                'commission' => $commission,
                'status'     => 'active',
            ]);
        }
    }

    public function getTierBonusOnPv(int $pv): float
    {
        $tier_bonus = 0.0;
        if ($pv < 170000) {
            $tier_bonus = 4.0;
        } elseif ($pv < 1150000) {
            $tier_bonus = 3.0;
        } else {
            $tier_bonus = 2.5;
        }

        return $tier_bonus;
    }
}
