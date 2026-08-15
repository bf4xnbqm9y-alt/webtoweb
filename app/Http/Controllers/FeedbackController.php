<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class FeedbackController extends Controller
{
    /**
     * Store feedback from user and dispatch Telegram alert.
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

        // Send Realtime Notification to @contactwtw_bot (falls back to TELEGRAM_BOT_TOKEN)
        $botToken = env('TELEGRAM_CONTACT_BOT_TOKEN') ?: env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if ($botToken && $chatId) {
            $typeEmoji = [
                'saran' => '💡 <b>SARAN</b>',
                'kritik' => '⚡ <b>KRITIK</b>',
                'pertanyaan' => '❓ <b>PERTANYAAN</b>',
            ][$validated['type']] ?? '📩 <b>MASUKAN</b>';

            $message = "{$typeEmoji} BARU DARI PELANGGAN!\n\n"
                . "👤 <b>Nama:</b> <code>{$validated['name']}</code>\n"
                . "📧 <b>Email:</b> <code>{$validated['email']}</code>\n"
                . "💬 <b>Pesan:</b>\n<i>\"{$validated['message']}\"</i>\n\n"
                . "⏰ <b>Waktu:</b> <code>" . now()->toDateTimeString() . "</code>";

            try {
                Http::withoutVerifying()->timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);
            } catch (\Exception $e) {
                logger()->error("Feedback Telegram Alert Failed: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Masukan Anda berhasil disimpan! Terima kasih banyak.'
        ]);
    }
}
