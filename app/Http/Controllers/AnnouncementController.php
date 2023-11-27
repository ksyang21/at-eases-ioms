<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Inertia\Response
    {
        $breadcrumbs = [
            [
                'label' => 'Manage Announcement',
                'link' => 'announcements.index'
            ]
        ];

        $active_announcements = [];
//        $announcements = Announcement::join('announcement_categories', 'announcements.category_id', '=', 'announcement_categories.id')
//            ->orderBy('announcements.status')
//            ->orderBy('announcement_categories.category')
//            ->with('category')
//            ->get();
//        foreach($announcements as $announcement) {
//            if($announcement->status === 'active') {
//                $active_announcements[] = $announcement;
//            }
//        }
        $announcements = Announcement::orderBy('status')->get();
        foreach($announcements as &$announcement) {
            $announcement['category'] = $announcement->category;
            if($announcement->status === 'active') {
                $active_announcements[] = $announcement;
            }
        }
        $announcements = $announcements->toArray();

        usort($announcements, function($a1, $a2) {
            $status_sort = strcmp($a1['status'], $a2['status'] );
            return $status_sort !== 0 ? strcmp($a1['status'], $a2['status'] ) : strcmp($a1['category']['category'], $a2['category']['category']);
        });

        $categories = AnnouncementCategory::orderBy('category')->get();

        return Inertia::render('Announcement/Index', [
           'breadcrumbs' => $breadcrumbs,
           'announcements' => $announcements,
           'activeAnnouncements' => $active_announcements,
           'categories' => $categories
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
    public function show(Announcement $announcement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return Inertia::location(route('announcements.index'));
    }

    public function activateAnnouncement(Announcement $announcement) {
        $announcement->status = 'active';
        $announcement->update();

        return Inertia::location(route('announcements.index'));
    }

    public function deactivateAnnouncement(Announcement $announcement) {
        $announcement->status = 'inactive';
        $b = $announcement->update();

        return Inertia::location(route('announcements.index'));
    }
}
