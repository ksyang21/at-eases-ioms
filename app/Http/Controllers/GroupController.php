<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Inertia\Response
    {
        $breadcrumbs = [
            [
                'label' => 'Groups',
                'link' => 'groups.index'
            ]
        ];

        // Get parent groups first
        $categorized_groups = Group::with('subgroups')->with('members')->where('parent_id', 0)->orderBy('total_pv', 'desc')->get();
        $groups = Group::with('members')->orderBy('total_pv', 'desc')->get();
        foreach($groups as $group_key => $group) {
            $members = $group['members'];
            if($members) {
                foreach($members as $member) {
                   $seller = $member->seller;
                }
            }
        }
        return Inertia::render('Group/Index', [
            'categorized_groups' => $categorized_groups,
            'groups' => $groups,
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
    public function show(Group $group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        //
    }
}
