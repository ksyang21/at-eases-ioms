<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\InventoryLog;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class InventoryLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product): \Inertia\Response
    {
        $product_id     = $product->id;
        $inventory_logs = InventoryLog::where('product_id', $product_id)->orderBy('created_at', 'desc')->get();
        return Inertia::render('InventoryLog/Index', [
            'product'        => $product,
            'inventory_logs' => $inventory_logs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product)
    {
        $product_id     = $product->id;
        $inventory_logs = InventoryLog::where('product_id', $product_id)->orderBy('created_at', 'desc')->get();
        return Inertia::render('InventoryLog/Create', [
            'product'        => $product,
            'inventory_logs' => $inventory_logs,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'stock_status' => [
                'required',
                Rule::in(['stock in', 'stock out']),
            ],
            'quantity' => 'required|numeric|min:0'
        ]);
        if ($validator->fails()) {
            return Inertia::render('InventoryLog/Create', [
                'errors' => $validator->errors()->all(),
            ]);
        }
        $data = $validator->getData();
        $product_id = $product->id;
        $inventory_logs = InventoryLog::create([
            'product_id' => $product_id,
            'stock_status' => $data['stock_status'],
            'quantity' => $data['quantity']
        ]);

        // Update product quantity
        $product_quantity = intval($product->stock_quantity);
        if($data['stock_status'] === 'stock in') {
            $product_quantity = $product_quantity + intval($data['quantity']);
        } else {
            $product_quantity = $product_quantity - intval($data['quantity']);
        }
        $product->update([
            'stock_quantity' => $product_quantity
        ]);

        return Redirect::route('inventoryLogs.index', $product_id)->with('success', 'Record added!');
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryLog $inventoryLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryLog $inventoryLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryLog $inventoryLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryLog $inventoryLog)
    {
        //
    }
}
