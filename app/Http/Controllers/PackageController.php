<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product)
    {
        $get_packages = PackageProduct::where('product_id', $product->id)->get();
        $packages     = [];
        foreach ($get_packages as $get_package) {
            $package_id = $get_package->package_id;
            $package    = Package::where('id', $package_id)->first();
            $details    = PackageProduct::where('package_id', $package_id)->first();
            $packages[] = [
                'package' => $package,
                'details' => $details,
            ];
        }
        return Inertia::render('Package/Index', [
            'packages' => $packages,
            'product'  => $product,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Product $product): \Inertia\Response
    {
        $get_packages = PackageProduct::where('product_id', $product->id)->get();
        $packages     = [];
        foreach ($get_packages as $get_package) {
            $package_id = $get_package->package_id;
            $package    = Package::where('id', $package_id)->first();
            $details    = PackageProduct::where('package_id', $package_id)->first();
            $packages[] = [
                'package' => $package,
                'details' => $details,
            ];
        }
        return Inertia::render('Package/Create', [
            'product'  => $product,
            'packages' => $packages,
            //            'all_products' => $all_products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => [
                'required',
                'string',
                'max:255',
            ],
            'product_id' => 'required|numeric|min:0',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return Inertia::render('Package/Create', [
                'errors' => $validator->errors()->all(),
            ]);
        }
        $data = $validator->getData();
        $product_id = $data['product_id'];

        $package = Package::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'status' => 'active'
        ]);

        PackageProduct::create([
            'package_id' => $package->id,
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'price' => $data['price']
        ]);

        return Redirect::route('package.index', $product_id)->with('success', 'New package added!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        //
    }
}
