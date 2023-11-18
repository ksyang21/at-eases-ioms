<?php

namespace App\Http\Controllers;

use App\Models\SalesCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SalesCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumbs = [
            [
                'label' => 'Campaigns',
                'link'  => 'campaigns.index',
            ],
        ];

        $campaigns = SalesCampaign::all();

        $ongoing_campaigns = [];
        foreach ($campaigns as $campaign) {
            if (strtotime($campaign['period_start']) < time() && strtotime($campaign['period_end']) > time()) {
                $ongoing_campaigns[] = $campaign;
            }
        }

        return Inertia::render('Campaign/Index', [
            'breadcrumbs'       => $breadcrumbs,
            'campaigns'         => $campaigns,
            'ongoing_campaigns' => $ongoing_campaigns,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        $breadcrumbs = [
            [
                'label' => 'Campaigns',
                'link'  => 'campaigns.index',
            ],
            [
                'label' => 'New Campaign',
                'link'  => 'campaign.create',
            ],
        ];
        return Inertia::render('Campaign/Create', [
            'breadcrumbs' => $breadcrumbs,
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
            'target_amount' => 'numeric|min:0|required',
            'period_start' => 'date|required',
            'period_end' => 'date|required',
        ]);

        if ($validator->fails()) {
            $breadcrumbs = [
                [
                    'label' => 'Campaigns',
                    'link'  => 'campaigns.index',
                ],
                [
                    'label' => 'New Campaign',
                    'link'  => 'campaign.create',
                ],
            ];
            return Inertia::render('Campaign/Create', [
                'errors' => $validator->errors()->all(),
                'breadcrumbs' => $breadcrumbs,
            ]);
        }

        $data = $validator->getData();

        SalesCampaign::create([
            'name' => $data['name'],
            'period_start' => date('Y-m-d 00:00:00', strtotime($data['period_start'])),
            'period_end' => date('Y-m-d 23:59:59', strtotime($data['period_end'])),
            'sales_target_amount' => $data['target_amount'],
            'current_amount' => 0.00,
            'total_product_sold' => 0,
        ]);

        return redirect()->route('campaigns.index')->with('success', 'Campaign created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesCampaign $salesCampaign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesCampaign $salesCampaign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesCampaign $salesCampaign)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesCampaign $salesCampaign)
    {
        //
    }
}
