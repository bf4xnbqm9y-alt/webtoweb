<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    message: {
        type: String,
        default: 'Anda tidak berhak mengakses dengan cara terlarang!'
    },
    url: String
});
</script>

<template>
    <Head title="Akses Ditolak | WebToWeb Security" />

    <div class="min-h-screen bg-[#FAF9F5] flex flex-col items-center justify-center p-6 text-center select-none relative overflow-hidden">
        <!-- Abstract Security Grid Background -->
        <div class="absolute inset-0 bg-[radial-gradient(#E8E6E0_1px,transparent_1px)] [background-size:16px_16px] opacity-60 z-0"></div>
        
        <!-- Big alert neon-ish halo behind icon -->
        <div class="absolute w-72 h-72 rounded-full bg-[#C2410C]/5 blur-3xl z-0"></div>

        <div class="max-w-md space-y-8 z-10">
            <!-- Icon Security Shield with Alert Exclamation -->
            <div class="w-20 h-20 rounded-3xl bg-[#C2410C]/5 border border-[#C2410C]/20 flex items-center justify-center text-[#C2410C] mx-auto shadow-sm animate-bounce">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
                </svg>
            </div>

            <!-- Titles -->
            <div class="space-y-3">
                <span class="text-[10px] font-mono font-black text-[#C2410C] bg-[#C2410C]/10 border border-[#C2410C]/20 px-3 py-1.5 rounded-full uppercase tracking-widest inline-block">
                    Status: 403 Forbidden
                </span>
                <h1 class="text-3xl font-black text-[#0E1015] tracking-tight">
                    {{ message }}
                </h1>
                <p class="text-xs sm:text-sm text-[#57585F] leading-relaxed max-w-sm mx-auto">
                    Akses paksa tanpa otorisasi terdeteksi. Rincian perangkat dan IP address Anda telah direkam serta dilaporkan langsung ke administrator sistem secara real-time.
                </p>
            </div>

            <!-- Request Log Details Card -->
            <div class="p-5 bg-white border border-[#E8E6E0] rounded-2xl text-left font-mono text-[10px] text-[#57585F] space-y-2.5 shadow-2xs">
                <div class="flex justify-between border-b border-[#E8E6E0]/60 pb-1.5 font-bold text-[#0E1015]">
                    <span>Security Log</span>
                    <span class="text-[#C2410C] animate-pulse">● RECORDING</span>
                </div>
                <div><span class="text-[#0E1015]">TARGET:</span> <span class="break-all">{{ url }}</span></div>
                <div><span class="text-[#0E1015]">ACTION:</span> BLOCKED_BY_MIDDLEWARE</div>
                <div><span class="text-[#0E1015]">REPORT:</span> TELEGRAM_BOT_DISPATCHED</div>
            </div>

            <!-- Back to Home Link -->
            <div class="pt-4">
                <Link 
                    href="/" 
                    class="inline-flex items-center justify-center px-6 py-3 bg-[#0E1015] hover:bg-[#C2410C] text-white font-bold text-xs rounded-xl shadow-sm hover:shadow-md transition duration-300 focus:outline-none"
                >
                    Kembali ke Beranda
                </Link>
            </div>
        </div>
    </div>
</template>
