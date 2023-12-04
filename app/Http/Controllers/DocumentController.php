<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Inertia\Response
    {
        $breadcrumbs = [
            [
                'label' => 'Documents',
                'link'  => 'documents.index',
            ],
        ];
        $documents   = Document::with('uploader')->get();

        return Inertia::render('Document/Index', [
            'breadcrumbs' => $breadcrumbs,
            'documents'   => $documents,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        $breadcrumbs = [
            [
                'label' => 'Documents',
                'link'  => 'documents.index',
            ],
            [
                'label' => 'Upload Document',
                'link'  => 'document.create',
            ],
        ];

        return Inertia::render('Document/Create', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $file_path = NULL;
        if ($request->hasFile('document')) {
            $file      = $request->file('document');
            $extension = $file->getClientOriginalExtension();
            $directory = match ($extension) {
                'mp4', 'avi', 'mov' => 'document/video',
                'jpg', 'jpeg', 'png', 'gif' => 'document/image',
                default => 'document/documents',
            };
            $file_path = $request->file('document')->store($directory, 'public');
        }


        Document::create([
            'name'        => $request->name,
            'description' => $request->description,
            'filename'    => $file_path,
            'status'      => 'active',
            'uploaded_by' => Auth::user()->id,
        ]);

        return Redirect::route('documents.index')->with('success', sprintf('%s added!', $request->name));
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $document->delete();

        return Inertia::location(route('documents.index'));
    }

    public function downloadDocument(Document $document): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $file     = Storage::disk('public')->path($document->filename);
        $filename = explode('/', $document->filename);
        $filename = $filename[count($filename) - 1];
        return response()->download($file, $filename);
    }
}
