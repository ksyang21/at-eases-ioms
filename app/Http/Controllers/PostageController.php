<?php

namespace App\Http\Controllers;

use App\Models\Postage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumbs = [
            [
                'label' => 'Manage Postage',
                'link' => 'postages.index'
            ]
        ];
        $postages = Postage::all();
        return Inertia::render('Postage/Index', [
            'postages' => $postages,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(Postage $postage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Postage $postage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Postage $postage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Postage $postage)
    {
        //
    }
}
