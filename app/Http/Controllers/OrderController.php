<?php

namespace App\Http\Controllers;

use App\Models\InventoryLog;
use App\Models\OrderDetails;
use App\Models\Postage;
use App\Models\User;
use Illuminate\Support\Carbon;
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
    public function index(): \Inertia\Response
    {
        $breadcrumbs      = [
            [
                'label' => 'Orders',
                'link'  => 'orders.index',
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

        // Get today orders
        $today_orders       = Order::whereDate('created_at', Carbon::today())->get();
        $today_order_amount = $today_orders->sum('total_price');

        $earliest_order_date = Order::min('created_at');
        $latest_order_date   = Order::max('created_at');

        return Inertia::render('Order/Index', [
            'delivery_methods'    => $delivery_methods,
            'orders'              => $orders,
            'breadcrumbs'         => $breadcrumbs,
            'today_orders'        => $today_orders,
            'today_order_amount'  => $today_order_amount,
            'earliest_order_date' => $earliest_order_date,
            'latest_order_date'   => $latest_order_date,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        $breadcrumbs = [
            [
                'label' => 'Orders',
                'link'  => 'orders.index',
            ],
            [
                'label' => 'New Order',
                'link'  => 'order.create',
            ],
        ];

        // Get current user customer
        $customers = Customer::where('seller_id', Auth::id())->get();
        foreach ($customers as $customer) {
            $customer->address = sprintf('%s, %s, %s %s, %s',
                $customer->unit_no,
                $customer->street,
                $customer->postcode,
                $customer->city,
                $customer->state,
            );
        }

        // Get products with packages
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

        // Get all sellers
        $sellers = User::with('groupDetails')->orderBy('name', 'asc')->get();

        // Get postages to calculate delivery fee
        $postages = Postage::all();

        return Inertia::render('Order/Create', [
            'products'    => $all_products,
            'customers'   => $customers,
            'sellers'     => $sellers,
            'postages'    => $postages,
            'breadcrumbs' => $breadcrumbs,
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
    public function show(string $id): \Inertia\Response
    {
        $order            = Order::find($id);
        $order['details'] = $order->details;
        foreach ($order['details'] as &$detail) {
            $detail['product'] = $detail->product;
        }
        $customer = $order->customer;
        $seller   = $order->seller;
        if ($order->delivery_method_id !== NULL) {
            $delivery_method = $order->deliveryMethod;
        } else {
            $delivery_method = NULL;
        }

        $breadcrumbs = [
            [
                'label' => 'Orders',
                'link'  => 'orders.index',
            ],
            [
                'label'  => sprintf('Details (#%s)', $order->order_no),
                'link'   => 'order.show',
                'params' => $order->id,
            ],
        ];

        return Inertia::render('Order/Details', [
            'order'           => $order,
            'seller'          => $seller,
            'customer'        => $customer,
            'delivery_method' => $delivery_method,
            'breadcrumbs'     => $breadcrumbs,
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

    public function approveOrder(Order $order): \Symfony\Component\HttpFoundation\Response
    {
        $order->status = 'approved';
        $order->update();
        return Inertia::location(route('orders.index'));
    }

    public function cancelOrder(Order $order): \Symfony\Component\HttpFoundation\Response
    {
        $order->status = 'cancelled';
        $order->update();

        $order_details = OrderDetails::where('order_id', $order->id)->get();
        foreach ($order_details as $detail) {
            $product_id = $detail->product_id;
            $quantity   = $detail->quantity;

            InventoryLog::create([
                'product_id'   => $product_id,
                'quantity'     => $quantity,
                'stock_status' => 'stock in',
                'description'  => '#' . $order->order_no,
            ]);

            $product_item                 = Product::find($product_id);
            $product_item->stock_quantity = $product_item->stock_quantity + $quantity;
            $product_item->update();
        }

        return Inertia::location(route('orders.index'));
    }

    public function inTransitOrder(Request $request, Order $order): \Symfony\Component\HttpFoundation\Response
    {
        $order->status = 'in transit';
        $order->update();
        return Inertia::location(route('orders.index'));
    }

    public function completeOrder(Order $order): \Symfony\Component\HttpFoundation\Response
    {
        $order->status = 'completed';
        $order->update();
        return Inertia::location(route('orders.index'));
    }

    public function returnOrder(Order $order): \Symfony\Component\HttpFoundation\Response
    {
        $order->status = 'pending return';
        $order->update();

        return Inertia::location(route('orders.index'));
    }

    public function rejectOrder(Order $order): \Symfony\Component\HttpFoundation\Response
    {
        $order->status = 'rejected';
        $order->update();

        $order_details = OrderDetails::where('order_id', $order->id)->get();
        foreach ($order_details as $detail) {
            $product_id = $detail->product_id;
            $quantity   = $detail->quantity;

            InventoryLog::create([
                'product_id'   => $product_id,
                'quantity'     => $quantity,
                'stock_status' => 'stock in',
                'description'  => '#' . $order->order_no,
            ]);

            $product_item                 = Product::find($product_id);
            $product_item->stock_quantity = $product_item->stock_quantity + $quantity;
            $product_item->update();
        }

        return Inertia::location(route('orders.index'));
    }

    public function approveReturn(Order $order)
    {
        $order->status = 'return';
        $order->update();

        $order_details = OrderDetails::where('order_id', $order->id)->get();
        foreach ($order_details as $detail) {
            $product_id = $detail->product_id;
            $quantity   = $detail->quantity;

            InventoryLog::create([
                'product_id'   => $product_id,
                'quantity'     => $quantity,
                'stock_status' => 'stock in',
                'description'  => '#' . $order->order_no,
            ]);

            $product_item                 = Product::find($product_id);
            $product_item->stock_quantity = $product_item->stock_quantity + $quantity;
            $product_item->update();
        }

        return Inertia::location(route('orders.index'));
    }
}
