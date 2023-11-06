<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Inertia\Response
    {
        $products = Product::orderBy('status')->orderBy('name')->get();

        return Inertia::render('Inventory/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Inventory/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Inertia\Response|\Illuminate\Http\RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name'),
            ],
            'description' => 'nullable|string',
            'pv'          => 'numeric|min:0|required',
            'price'       => 'numeric|min:0|required',
            'image'       => 'nullable|image|mimes:jpeg,png|max:2048',
            'status'      => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        if ($validator->fails()) {
            return Inertia::render('Inventory/Create', [
                'errors' => $validator->errors()->all(),
            ]);
        }

        $data = $validator->getData();

        $image_path = null;
        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('image', 'public');
        }

        $product = Product::create([
            'name'        => $data['name'],
            'description' => $data['description'],
            'pv'          => $data['pv'],
            'price'       => $data['price'],
            'image'       => $image_path,
            'status'      => $data['status'],
        ]);

        return Redirect::route('products.index')->with('success', sprintf('%s added!', $data['name']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return Inertia::render('Inventory/Edit', [
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): \Inertia\Response|\Illuminate\Http\RedirectResponse
    {
        dd($request->request);
        $validator = Validator::make($request->all(), [
            'name'        => [
                'required',
                'string',
                'max:255',
            ],
            'description' => 'nullable|string',
            'pv'          => 'numeric|min:0|required',
            'price'       => 'numeric|min:0|required',
            'image'       => 'nullable|image|mimes:jpeg,png|max:2048',
            'status'      => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);
        if ($validator->fails()) {
            return Inertia::render('Inventory/Edit', [
                'errors' => $validator->errors()->all(),
            ]);
        }

        $data = $validator->getData();

        $image_path = null;
        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('image', 'public');
        }

        $product->update([
            'name'        => $data['name'],
            'description' => $data['description'],
            'pv'          => $data['pv'],
            'price'       => $data['price'],
            'image'       => $image_path,
            'status'      => $data['status'],
        ]);

        return Redirect::route('products.index')->with('success', sprintf('%s updated!', $data['name']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return Redirect::route('products.index')->with('success', sprintf('%s added!', $product->name));
    }
}
