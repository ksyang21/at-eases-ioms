<?php

namespace App\Http\Controllers;

use App\Models\Postage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
	    $validator = Validator::make($request->all(), [
		    'area' => [
			    'required',
			    Rule::unique('postages')->where(function ($query) use ($request) {
				    return $query->where('postcode', $request->postcode);
			    }),
		    ],
		    'postcode' => 'required|unique:postages',
		    'state' => 'required|string',
		    'delivery_fee' => 'required|numeric|min:0'
	    ]);

		if($validator->fails()) {
			$breadcrumbs = [
				[
					'label' => 'Manage Postage',
					'link' => 'postages.index'
				]
			];
			return Inertia::render('Postage/Index', [
				'postages' => Postage::all(),
				'breadcrumbs' => $breadcrumbs,
				'errors' => $validator->errors()
			]);
		}

		$data = $validator->validated();
		Postage::create([
			'area' => $data['area'],
			'postcode' => $data['postcode'],
			'state' => $data['state'],
			'delivery_fee' => $data['delivery_fee']
		]);

		return Inertia::location(route('postages.index'));
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
    public function destroy(Postage $postage): \Symfony\Component\HttpFoundation\Response
    {
        $postage->delete();

		return Inertia::location(route('postages.index'));
    }
}
