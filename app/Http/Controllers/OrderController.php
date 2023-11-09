<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\Package;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\DeliveryMethod;
use App\Models\PackageProduct;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $delivery_methods = DeliveryMethod::all();
        $orders = Order::all();
        foreach ($orders as &$order) {
            $order['seller']          = $order->seller;
            $order['customer']        = $order->customer;
            $order['details']         = $order->details;
            $order['delivery_method'] = $order->deliveryMethod;
        }
        return Inertia::render('Order/Index', [
            'delivery_methods' => $delivery_methods,
            'orders'           => $orders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        // Get current user customer
        $customers = Customer::where('seller_id', Auth::id())->get();
        $all_products = Product::where('status', 'active')->get();
        foreach ($all_products as &$product) {
            $get_packages = PackageProduct::where('product_id', $product->id)->get();
            $packages     = [];
            foreach ($get_packages as $get_package) {
                $package_id          = $get_package->package_id;
                $package             = Package::where('id', $package_id)->first();
                $details             = PackageProduct::select('quantity', 'price')->where('package_id', $package_id)->first();
                $package['quantity'] = $details['quantity'];
                $package['price']    = $details['price'];
                $packages[]          = $package;
//                    'package' => $package,
//                    'details' => $details,
//                ];
            }
            $product['packages'] = $packages;
        }
        return Inertia::render('Order/Create', [
            'products' => $all_products,
            'customers' => $customers
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
        $order            = Order::find($id);
        $order['details'] = $order->details;
        foreach ($order['details'] as &$detail) {
            $detail['product'] = $detail->product;
        }
        $customer        = $order->customer;
        $seller          = $order->seller;
        $delivery_method = $order->deliveryMethod;
        return Inertia::render('Order/Details', [
            'order'           => $order,
            'seller'          => $seller,
            'customer'        => $customer,
            'delivery_method' => $delivery_method,
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
