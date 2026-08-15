<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WorkspaceProtect extends Middleware
{
    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        $ip = $request->ip();
        $url = $request->fullUrl();
        $userAgent = $request->userAgent();
        $time = now()->toDateTimeString();

        // Send Realtime Security Alert to Telegram Bot
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if ($botToken && $chatId) {
            $message = "⚠️ <b>UPAYA AKSES ILEGAL TERDETEKSI</b> ⚠️\n\n"
                . "Akses tanpa login terdeteksi pada rute terproteksi!\n\n"
                . "🌐 <b>URL:</b> <code>{$url}</code>\n"
                . "🖥️ <b>IP Address:</b> <code>{$ip}</code>\n"
                . "📱 <b>Device/UA:</b> <code>{$userAgent}</code>\n"
                . "⏰ <b>Waktu:</b> <code>{$time}</code>\n\n"
                . "❌ <i>Akses diblokir dan dialihkan ke Halaman Login.</i>";

            try {
                Http::withoutVerifying()->timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);
            } catch (\Exception $e) {
                logger()->error("Telegram Exception: " . $e->getMessage());
            }
        }

        // Call parent to perform default redirect/exception behavior
        parent::unauthenticated($request, $guards);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
