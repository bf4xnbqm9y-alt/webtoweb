<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard with their projects list.
     */
    public function index()
    {
        return Inertia::render('Dashboard', [
            'projects' => auth()->user()->projects()->latest()->get()
        ]);
    }

    /**
     * Store a newly created project in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Generate a unique slug for the user's project
        $baseSlug = Str::slug($request->name);
        if (empty($baseSlug)) {
            $baseSlug = 'project';
        }
        
        $slug = $baseSlug;
        $count = 1;
        while (Project::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count++;
        }

        // Initialize with default template component blocks (Navbar & Footer)
        $defaultDraft = [
            [
                'id' => 'sec-' . time() . '-1',
                'type' => 'Navbar',
                'content' => [
                    'brand' => ucwords($request->name),
                    'link1' => 'Fitur',
                    'link2' => 'Harga'
                ]
            ],
            [
                'id' => 'sec-' . time() . '-2',
                'type' => 'Footer',
                'content' => [
                    'copyright' => '© ' . date('Y') . ' ' . ucwords($request->name) . '. All rights reserved.'
                ]
            ]
        ];

        $project = auth()->user()->projects()->create([
            'name' => $request->name,
            'slug' => $slug,
            'draft_data' => $defaultDraft,
            'is_published' => false
        ]);

        return redirect()->route('builder.workspace', ['project_slug' => $project->slug]);
    }

    /**
     * Remove the specified project from database.
     */
    public function destroy($id)
    {
        $project = auth()->user()->projects()->findOrFail($id);
        $project->delete();

        return redirect()->back();
    }
}
