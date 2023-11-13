<?php

namespace App\Http\Controllers;

use App\Models\SalesCampaign;
use Illuminate\Http\Request;
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
                'link' => 'campaigns.index'
            ]
        ];

        $campaigns = SalesCampaign::all();

        $ongoing_campaigns = [];
        foreach($campaigns as $campaign) {
            if(strtotime($campaign['period_start']) < time() && strtotime($campaign['period_end']) > time()) {
                $ongoing_campaigns[] = $campaign;
            }
        }

        return Inertia::render('Campaign/Index', [
            'breadcrumbs' => $breadcrumbs,
            'campaigns' => $campaigns,
            'ongoing_campaigns' => $ongoing_campaigns
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
