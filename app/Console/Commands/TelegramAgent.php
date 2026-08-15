<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TelegramAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Ben, the Telegram Remote Code Agent to run prompts and write code directly from Telegram chat.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $botToken = env('TELEGRAM_CONTACT_BOT_TOKEN') ?: env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        $geminiKey = env('GEMINI_API_KEY');

        if (!$botToken || !$chatId) {
            $this->error('Telegram Bot Token or Chat ID is not configured in .env!');
            return 1;
        }

        if (!$geminiKey) {
            $this->warn('GEMINI_API_KEY is not configured in .env. Gemini AI answers will be simulated.');
        }

        $this->info("Ben is listening to Telegram bot prompts...");
        $this->info("Authorized Chat ID: {$chatId}");

        $telegramUrl = "https://api.telegram.org/bot{$botToken}";

        // Send a startup message to user
        $this->sendTelegramMessage($telegramUrl, $chatId, "⚡ <b>Ben (Remote Agent) Aktif!</b>\nSaya sekarang siap menerima instruksi coding Anda dari chat ini.\n\n<i>Ketik perintah Anda (misal: 'buatkan controller baru untuk galeri')</i>");

        $offset = Cache::get('tg_agent_offset', 0);

        while (true) {
            try {
                // Poll updates from Telegram (timeout 30 seconds for long polling)
                $response = Http::withoutVerifying()->timeout(35)->get("{$telegramUrl}/getUpdates", [
                    'offset' => $offset,
                    'timeout' => 30
                ]);

                if ($response->successful()) {
                    $updates = $response->json('result', []);

                    foreach ($updates as $update) {
                        $offset = $update['update_id'] + 1;
                        Cache::put('tg_agent_offset', $offset);

                        $message = $update['message'] ?? null;
                        if (!$message) continue;

                        $senderId = $message['chat']['id'] ?? null;
                        $text = $message['text'] ?? '';

                        // Only allow the authorized Chat ID
                        if ($senderId != $chatId) {
                            $this->sendTelegramMessage($telegramUrl, $senderId, "❌ Akses ditolak. Anda tidak terdaftar sebagai pemilik sistem.");
                            continue;
                        }

                        if (empty($text) || $text === '/start') continue;

                        $this->info("Received prompt: {$text}");
                        $this->sendTelegramAction($telegramUrl, $chatId, 'typing');

                        // Process the prompt
                        $this->processPrompt($text, $telegramUrl, $chatId, $geminiKey);
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error in polling loop: " . $e->getMessage());
                sleep(2);
            }
        }
    }

    /**
     * Process the user prompt using Gemini API and execute blocks.
     */
    private function processPrompt($prompt, $telegramUrl, $chatId, $geminiKey)
    {
        if (empty($geminiKey)) {
            // Simulated backup response
            $this->sendTelegramMessage($telegramUrl, $chatId, "⚠️ <b>GEMINI_API_KEY belum dikonfigurasi di .env</b>. Silakan dapatkan API Key gratis dari Google AI Studio dan tambahkan ke berkas .env Anda.");
            return;
        }

        // System instructions to structure the output of the model
        $systemInstruction = "You are Ben, the remote agentic assistant for the WebToWeb Laravel project. You run locally on the user's Windows PC inside workspace 'd:\laragon\www\webtoweb'.\n\n"
            . "You can edit/write files and execute terminal commands on their system by outputting specific tagged blocks:\n\n"
            . "1. To WRITE a new file or OVERWRITE an existing file:\n"
            . "[WRITE:relative/path/to/file.ext]\n"
            . "file content here\n"
            . "[END_WRITE]\n\n"
            . "2. To RUN a terminal command (e.g. php artisan make:controller, git status):\n"
            . "[RUN]\n"
            . "shell command here\n"
            . "[END_RUN]\n\n"
            . "3. To SEND a response or talk back to the user:\n"
            . "[TALK]\n"
            . "Your markdown/HTML formatted response explaining what you did.\n"
            . "[END_TALK]\n\n"
            . "Rules:\n"
            . "- You can use multiple WRITE, RUN, and TALK blocks in one response.\n"
            . "- Do NOT run infinite commands or dev servers. Normal artisan/git commands are fine.\n"
            . "- Keep the explanations concise and professional.";

        try {
            $apiResponse = Http::withoutVerifying()->timeout(20)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => $systemInstruction]
                        ]
                    ]
                ]
            );

            if ($apiResponse->successful()) {
                $responseText = $apiResponse->json('candidates.0.content.parts.0.text', '');
                $this->executeBlocks($responseText, $telegramUrl, $chatId);
            } else {
                $this->sendTelegramMessage($telegramUrl, $chatId, "❌ Gagal memproses prompt ke Gemini: " . $apiResponse->body());
            }
        } catch (\Exception $e) {
            $this->sendTelegramMessage($telegramUrl, $chatId, "❌ Terjadi error: " . $e->getMessage());
        }
    }

    /**
     * Parse and execute [WRITE], [RUN], and [TALK] blocks from Gemini response.
     */
    private function executeBlocks($text, $telegramUrl, $chatId)
    {
        // Parse [TALK] blocks
        preg_match_all('/\[TALK\](.*?)\[END_TALK\]/s', $text, $talkMatches);
        foreach ($talkMatches[1] as $talkContent) {
            $this->sendTelegramMessage($telegramUrl, $chatId, trim($talkContent));
        }

        // Parse [WRITE:path] blocks
        preg_match_all('/\[WRITE:(.*?)\](.*?)\[END_WRITE\]/s', $text, $writeMatches);
        for ($i = 0; $i < count($writeMatches[0]); $i++) {
            $filePath = trim($writeMatches[1][$i]);
            $fileContent = $writeMatches[2][$i];

            $absolutePath = base_path($filePath);
            $dir = dirname($absolutePath);

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($absolutePath, trim($fileContent));
            $this->sendTelegramMessage($telegramUrl, $chatId, "📁 <b>File Ditulis:</b> <code>{$filePath}</code>");
        }

        // Parse [RUN] blocks
        preg_match_all('/\[RUN\](.*?)\[END_RUN\]/s', $text, $runMatches);
        foreach ($runMatches[1] as $runCommand) {
            $command = trim($runCommand);
            $this->sendTelegramMessage($telegramUrl, $chatId, "⚙️ <b>Menjalankan Perintah:</b> <code>{$command}</code>");
            
            // Execute command locally
            $output = [];
            $exitCode = 0;
            
            // Escape double quotes for shell execution in Laragon CWD
            chdir(base_path());
            exec($command . " 2>&1", $output, $exitCode);
            $outputStr = implode("\n", $output);

            $statusEmoji = $exitCode === 0 ? '✅' : '❌';
            $responseMsg = "{$statusEmoji} <b>Hasil Perintah (Exit: {$exitCode}):</b>\n<pre>" . htmlspecialchars(substr($outputStr, 0, 3000)) . "</pre>";
            if (strlen($outputStr) > 3000) {
                $responseMsg .= "\n<i>(Output terpotong karena terlalu panjang)</i>";
            }
            
            $this->sendTelegramMessage($telegramUrl, $chatId, $responseMsg);
        }
    }

    /**
     * Send message to Telegram API.
     */
    private function sendTelegramMessage($url, $chatId, $text)
    {
        try {
            Http::withoutVerifying()->post("{$url}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);
        } catch (\Exception $e) {
            Log::error("Telegram Agent send failed: " . $e->getMessage());
        }
    }

    /**
     * Send chat action to Telegram API.
     */
    private function sendTelegramAction($url, $chatId, $action)
    {
        try {
            Http::withoutVerifying()->post("{$url}/sendChatAction", [
                'chat_id' => $chatId,
                'action' => $action
            ]);
        } catch (\Exception $e) {
            // Ignore
        }
    }
}
