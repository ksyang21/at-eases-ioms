<?php

namespace App\Http\Controllers;

use App\Models\DeliveryMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DeliveryMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Inertia\Response
    {
        $breadcrumbs      = [
            [
                'label' => 'Manage Delivery Method',
                'link'  => 'delivery.index',
            ],
        ];
        $delivery_methods = DeliveryMethod::orderBy('status')->orderBy('delivery_method')->get();
        return Inertia::render('DeliveryMethod/Index', [
            'delivery_methods' => $delivery_methods,
            'breadcrumbs'      => $breadcrumbs,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => [
                'required','string', 'max:255',
                Rule::unique('delivery_methods', 'delivery_method'),
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        if ($validator->fails()) {
            return Inertia::render('DeliveryMethod/Index', [
                'errors' => $validator->errors()->all(),
            ]);
        }

        $data = $validator->getData();
        $delivery_method = DeliveryMethod::create([
            'delivery_method' => $data['name'],
            'status' => $data['status'],
        ]);

        return Inertia::location(route('delivery.index'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeliveryMethod $deliveryMethod)
    {
        $deliveryMethod->delivery_method = $request['name'];
        $deliveryMethod->status = $request['status'];
        $deliveryMethod->update();

        return Inertia::location(route('delivery.index'));
    }

    public function deactivate(DeliveryMethod $deliveryMethod) {
        $deliveryMethod->status = 'inactive';
        $deliveryMethod->save();
        return Inertia::location(route('delivery.index'));
    }

    public function activate(DeliveryMethod $deliveryMethod) {
        $deliveryMethod->status = 'active';
        $deliveryMethod->save();
        return Inertia::location(route('delivery.index'));
    }
}
