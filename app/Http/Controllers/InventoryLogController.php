<?php

namespace App\Http\Controllers;

use App\Models\InventoryLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product): \Inertia\Response
    {
        $product_id = $product->id;
        $inventory_logs = InventoryLog::where('product_id', $product_id)->orderBy('created_at', 'desc')->get();

        return Inertia::render('InventoryLog/Index', [
            'product' => $product,
            'inventory_logs' => $inventory_logs
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product)
    {
        $product_id = $product->id;
        $inventory_logs = InventoryLog::where('product_id', $product_id)->orderBy('created_at', 'desc')->get();

        return Inertia::render('InventoryLog/Create', [
            'product' => $product,
            'inventory_logs' => $inventory_logs
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
