<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Inertia\Response
    {
        $breadcrumbs = [
            [
                'label' => 'Dealers',
                'link'  => 'sellers.index',
            ],
        ];
        $sellers     = User::all();
        return Inertia::render('Seller/Index', [
            'sellers'     => $sellers,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $breadcrumbs = [
            [
                'label' => 'Dealers',
                'link'  => 'sellers.index',
            ],
            [
                'label' => 'New Dealer',
                'link'  => 'seller.create',
            ],
        ];
        $sellers     = User::all();

        return Inertia::render('Seller/Create', [
            'breadcrumbs' => $breadcrumbs,
            'sellers'     => $sellers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Inertia\Response|\Illuminate\Http\RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'email'      =>
                'required|string|lowercase|email|max:255|unique:'.User::class,
        ]);

        if ($validator->fails()) {
            $breadcrumbs = [
                [
                    'label' => 'Sellers',
                    'link'  => 'sellers.index',
                ],
                [
                    'label' => 'New Seller',
                    'link'  => 'seller.create',
                ],
            ];
            $sellers     = User::all();
            return Inertia::render('Seller/Create', [
                'errors' => $validator->errors()->all(),
                'breadcrumbs' => $breadcrumbs,
                'sellers'     => $sellers,
            ]);
        }

        $data = $validator->getData();
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make('VertexSeller'),
        ]);

        return redirect()->route('sellers.index')->with('success', 'New seller added!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deactivateSeller(User $user) {

    }
}
