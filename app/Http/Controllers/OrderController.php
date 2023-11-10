<?php

namespace App\Http\Controllers;

use App\Models\InventoryLog;
use App\Models\OrderDetails;
use Illuminate\Support\Facades\Redirect;
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
        $breadcrumbs      = [
            [
                'label' => 'All Order',
                'link'  => 'orders',
            ],
        ];
        $delivery_methods = DeliveryMethod::all();
        $orders           = Order::all();
        foreach ($orders as &$order) {
            $order['seller']   = $order->seller;
            $order['customer'] = $order->customer;
            $order['details']  = $order->details;
            if ($order->delivery_method_id !== NULL) {
                $order['delivery_method'] = $order->deliveryMethod;
            } else {
                $order['delivery_method'] = NULL;
            }
        }
        return Inertia::render('Order/Index', [
            'delivery_methods' => $delivery_methods,
            'orders'           => $orders,
            'breadcrumbs'      => $breadcrumbs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        // Get current user customer
        $customers    = Customer::where('seller_id', Auth::id())->get();
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
            }
            $product['packages'] = $packages;
        }
        return Inertia::render('Order/Create', [
            'products'  => $all_products,
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $products    = $request->request->all()['products'];
        $customer_id = $request->request->all()['customer_id'];
        $seller_id   = Auth::id();

        // get latest order to generate order no
        $latest_order = Order::latest()->first();
        if ($latest_order) {
            $new_order_id = intval($latest_order->id) + 1;
        } else {
            $new_order_id = 1;
        }

        $order = Order::create([
            'order_no'    => sprintf('OD%d', 1000 + $new_order_id),
            'status'      => 'pending',
            'seller_id'   => $seller_id,
            'customer_id' => $customer_id,
        ]);

        $order_total_price = 0.00;
        foreach ($products as $product) {
            $product_item = Product::where('id', $product['product_id'])->first();

            if ($product['quantity'] <= $product_item['stock_quantity']) {
                // Get most suitable package
                $packages            = PackageProduct::where('product_id', $product['product_id'])->get();
                $sorted_packages     = $packages->sortByDesc('quantity');
                $remaining_quantity  = $product['quantity'];
                $total_product_price = 0;
                foreach ($sorted_packages as $pack) {
                    $number_of_package = floor($remaining_quantity / $pack->quantity);

                    if ($number_of_package > 0) {
                        $total_product_price += $number_of_package * $pack->price;
                        $remaining_quantity  -= $number_of_package * $pack->quantity;
                    }

                    if ($remaining_quantity === 0) {
                        break;
                    }
                }

                if ($remaining_quantity > 0) {
                    $original_price      = $product_item->price; // Replace with the actual original price
                    $total_product_price += $remaining_quantity * $original_price;
                }

                $order_total_price += $total_product_price;

                $order_details = OrderDetails::create([
                    'order_id'   => $order->id,
                    'product_id' => $product['product_id'],
                    'quantity'   => $product['quantity'],
                    'price'      => $total_product_price,
                ]);

                InventoryLog::create([
                    'product_id'   => $product['product_id'],
                    'quantity'     => $product['quantity'],
                    'stock_status' => 'stock out',
                    'description'  => sprintf('#OD%d', 1000 + $new_order_id),
                ]);

                $product_item->stock_quantity = $product_item->stock_quantity - $product['quantity'];
                $product_item->update();
            }
        }

        $order->total_price = $order_total_price;
        $order->update();

        return Redirect::route('orders.index')->with('success', 'Order created!');
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

    public function approveOrder(Order $order): \Illuminate\Http\RedirectResponse
    {
        $order->status = 'approved';
        $order->update();
        return redirect()->route('orders.index')->with('success', sprintf('#%s approved!', $order->order_no));
    }

    public function cancelOrder(Order $order): \Illuminate\Http\RedirectResponse
    {
        $order->status = 'cancelled';
        $order->update();
        return redirect()->route('orders.index')->with('success', sprintf('#%s cancelled!', $order->order_no));
    }

    public function rejectOrder(Order $order): \Illuminate\Http\RedirectResponse
    {
        $order->status = 'rejected';
        $order->update();
        return redirect()->route('orders.index')->with('success', sprintf('#%s rejected', $order->order_no));
    }

    public function inTransitOrder(Request $request, Order $order)
    {
        $order->status = 'in transit';
        $order->update();
        return redirect()->route('orders.index')->with('success', sprintf('#%s cancelled!', $order->order_no));
    }

    public function completeOrder(Order $order) {
        $order->status= 'completed';
        $order->update();
        return redirect()->route('orders.index')->with('success', sprintf('#%s completed!', $order->order_no));
    }

    public function returnOrder(Order $order) {
        $order->status= 'return';
        $order->update();
        return redirect()->route('orders.index')->with('success', sprintf('#%s completed!', $order->order_no));
    }
}
