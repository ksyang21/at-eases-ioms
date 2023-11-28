<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class CustomerController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(): \Inertia\Response
	{
		$breadcrumbs = [
			[
				'label' => 'Customers',
				'link'  => 'customers.index',
			],
		];

		$customers = Customer::all();
		foreach ($customers as &$customer) {
			$customer['seller'] = $customer->seller;
			$customer->address  = sprintf('%s, %s, %s %s, %s',
				$customer->unit_no,
				$customer->street,
				$customer->postcode,
				$customer->city,
				$customer->state,
			);
		}

		return Inertia::render('Customer/Index', [
			'breadcrumbs' => $breadcrumbs,
			'customers'   => $customers,
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): \Inertia\Response
	{
		$breadcrumbs = [
			[
				'label' => 'Customers',
				'link'  => 'customers.index',
			],
			[
				'label' => 'New Customer',
				'link'  => 'customer.create',
			],
		];

		$all_sellers = User::all();

		return Inertia::render('Customer/Create', [
			'breadcrumbs' => $breadcrumbs,
			'sellers'     => $all_sellers,
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request): \Inertia\Response|\Illuminate\Http\RedirectResponse
	{
		$validator = Validator::make($request->all(), [
			'name'    => 'required|string|max:255',
			'seller'  => 'required|numeric|min:1',
			'address' => 'required|string',
			'phone'   => 'required|string',
			'email'   => 'required|email|string',
		]);

		if ($validator->fails()) {
			$breadcrumbs = [
				[
					'label' => 'Customers',
					'link'  => 'customers.index',
				],
				[
					'label' => 'New Customer',
					'link'  => 'customer.create',
				],
			];
			return Inertia::render('Customer/Create', [
				'breadcrumbs' => $breadcrumbs,
				'errors'      => $validator->errors()->all(),
			]);
		}

		$data = $validator->getData();

		Customer::create([
			'name'      => $data['name'],
			'seller_id' => $data['seller'],
			'address'   => $data['address'],
			'phone'     => $data['phone'],
			'email'     => $data['email'],
		]);

		return redirect()->route('customers.index')->with('success', 'Customer added!');
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Customer $customer)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Customer $customer)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Customer $customer)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Customer $customer)
	{
		//
	}
}
