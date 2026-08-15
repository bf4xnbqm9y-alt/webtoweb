<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Project;

class BuilderController extends Controller
{
    /**
     * Render the demo builder workspace.
     */
    public function demo()
    {
        return Inertia::render('Builder/Demo', [
            'isDemo' => true,
            'project' => null
        ]);
    }

    /**
     * Render the authenticated user workspace.
     */
    public function workspace($project_slug)
    {
        $project = auth()->user()->projects()->where('slug', $project_slug)->firstOrFail();

        return Inertia::render('Builder/Demo', [
            'isDemo' => false,
            'project' => $project
        ]);
    }

    /**
     * Save the updated draft_data to the project in database.
     */
    public function save(Request $request, $project_slug)
    {
        $request->validate([
            'draft_data' => 'required|array',
        ]);

        $project = auth()->user()->projects()->where('slug', $project_slug)->firstOrFail();

        $project->update([
            'draft_data' => $request->draft_data
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proyek berhasil disimpan ke cloud.'
        ]);
    }
}
