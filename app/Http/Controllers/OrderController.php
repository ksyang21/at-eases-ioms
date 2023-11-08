<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\DeliveryMethod;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $delivery_methods = DeliveryMethod::all();

        $orders = Order::all();
        foreach($orders as &$order) {
            $order['seller'] = $order->seller;
            $order['customer'] = $order->customer;
            $order['details'] = $order->details;
            $order['delivery_method'] = $order->deliveryMethod;
        }

        return Inertia::render('Order/Index', [
            'delivery_methods' => $delivery_methods,
            'orders' => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        $all_products = Product::where('status', 'active')->get();

        return Inertia::render('Order/Create', [
            'products' => $all_products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::find($id);
        $order['details'] = $order->details;
        foreach($order['details'] as &$detail) {
            $detail['product'] = $detail->product;
        }
        $customer = $order->customer;
        $seller = $order->seller;
        $delivery_method = $order->deliveryMethod;
        return Inertia::render('Order/Details', [
            'order' => $order,
            'seller' => $seller,
            'customer' => $customer,
            'delivery_method' => $delivery_method
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
