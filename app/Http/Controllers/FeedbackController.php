<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{
    /**
     * Store feedback from user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'type' => 'required|string|in:saran,kritik,pertanyaan',
            'message' => 'required|string',
        ]);

        Feedback::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Masukan Anda berhasil disimpan! Terima kasih banyak.'
        ]);
    }
}
