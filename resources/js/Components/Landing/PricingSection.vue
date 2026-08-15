<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const activeTier = ref('personal'); // 'personal' or 'developer'
</script>

<template>
    <!-- Pricing Section (SaaS Tiering) -->
    <section id="harga" class="space-y-12">
        <div class="text-center space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#C2410C]/5 border border-[#C2410C]/20 text-[#C2410C] text-[10px] font-bold uppercase tracking-wider">
                Skema Investasi
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#0E1015]">
                Pilih Skema Sesuai Kebutuhan Anda
            </h2>
            <p class="text-[#57585F] max-w-xl mx-auto text-sm sm:text-base leading-relaxed">
                Eksplorasi editor visual kami secara gratis, dapatkan file source code lengkap saat Anda siap.
            </p>
        </div>

        <!-- Dynamic License Toggle Switch -->
        <div class="flex items-center justify-center gap-3 py-2">
            <button 
                @click="activeTier = 'personal'"
                class="text-xs font-bold transition duration-200 focus:outline-none"
                :class="activeTier === 'personal' ? 'text-[#0E1015]' : 'text-[#57585F]/60 hover:text-[#57585F]'"
            >
                Lisensi Personal
            </button>
            <button 
                @click="activeTier = activeTier === 'personal' ? 'developer' : 'personal'"
                class="w-12 h-6 rounded-full bg-[#E8E6E0] p-1 flex items-center transition duration-300 focus:outline-none"
                :class="activeTier === 'developer' ? 'bg-[#C2410C]' : 'bg-[#E8E6E0]'"
                title="Beralih tipe lisensi"
            >
                <div 
                    class="w-4 h-4 rounded-full bg-white shadow-sm transform transition duration-300"
                    :class="activeTier === 'developer' ? 'translate-x-6' : 'translate-x-0'"
                ></div>
            </button>
            <button 
                @click="activeTier = 'developer'"
                class="text-xs font-bold flex items-center gap-1.5 transition duration-200 focus:outline-none"
                :class="activeTier === 'developer' ? 'text-[#0E1015]' : 'text-[#57585F]/60 hover:text-[#57585F]'"
            >
                Lisensi Developer
                <span class="px-1.5 py-0.5 rounded-md bg-[#C2410C]/5 border border-[#C2410C]/20 text-[8px] font-black uppercase text-[#C2410C] tracking-widest animate-pulse">Hemat</span>
            </button>
        </div>

        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Plan Free -->
            <div class="p-8 rounded-[2.5rem] bg-white border border-[#E8E6E0] flex flex-col justify-between space-y-8 shadow-2xs hover:shadow-xs transition duration-300">
                <div class="space-y-4">
                    <h3 class="text-base font-bold text-[#57585F] uppercase tracking-wider text-[10px]">Paket Gratis (Starter)</h3>
                    <div class="text-4xl font-extrabold text-[#0E1015] tracking-tight">Rp 0 <span class="text-xs font-normal text-slate-400">/ selamanya</span></div>
                    <p class="text-xs text-[#57585F] leading-relaxed">Sangat cocok untuk mengeksplorasi visual builder, menyusun layout, dan menguji pratinjau website.</p>
                    <div class="w-full h-px bg-[#E8E6E0] my-4"></div>
                    <ul class="space-y-3 text-xs text-[#57585F]">
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-[#C2410C] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Akses Visual Editor Penuh
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-[#C2410C] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Halaman Pratinjau Live (Public Link)
                        </li>
                        <li class="flex items-center gap-2.5 text-slate-300">
                            <svg class="w-4 h-4 text-slate-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Ekspor Source Code (Laravel/React/Vue/HTML)
                        </li>
                    </ul>
                </div>
                <Link
                    :href="route('register')"
                    class="w-full text-center py-3.5 bg-[#FAF9F5] hover:bg-[#EAE8E2] border border-[#E8E6E0] text-[#111317] font-bold text-xs rounded-xl transition duration-200"
                >
                    Daftar Akun Gratis
                </Link>
            </div>

            <!-- Plan Premium (Reactively animated pricing card) -->
            <div class="p-8 rounded-[2.5rem] bg-white border-2 border-[#C2410C] flex flex-col justify-between space-y-8 relative overflow-hidden shadow-sm hover:shadow-md transition duration-300">
                <!-- Popular Badge -->
                <div class="absolute top-0 right-0 bg-[#C2410C] text-white font-mono font-bold text-[8px] uppercase tracking-widest px-4 py-1.5 rounded-bl-xl">Best Choice</div>
                
                <div class="space-y-4">
                    <h3 class="text-base font-bold text-[#C2410C] uppercase tracking-wider text-[10px]">
                        {{ activeTier === 'personal' ? 'Paket Ekspor (Pro)' : 'Paket Agensi (Dev)' }}
                    </h3>
                    
                    <!-- Reactive Price Tag -->
                    <div class="transition-all duration-300">
                        <div v-if="activeTier === 'personal'" class="text-4xl font-extrabold text-[#0E1015] tracking-tight">
                            Rp 99.000 <span class="text-xs font-normal text-slate-400">/ sekali bayar</span>
                        </div>
                        <div v-else class="text-4xl font-extrabold text-[#0E1015] tracking-tight">
                            Rp 249.000 <span class="text-xs font-normal text-slate-400">/ sekali bayar</span>
                        </div>
                    </div>

                    <p class="text-xs text-[#57585F] leading-relaxed transition-all duration-300">
                        {{ activeTier === 'personal' 
                            ? 'Unduh source code utuh dan bersih siap pakai di hosting sendiri untuk 1 domain/project.' 
                            : 'Unduh source code tanpa batasan domain dengan lisensi developer lengkap untuk agensi & freelance.' 
                        }}
                    </p>
                    <div class="w-full h-px bg-[#E8E6E0] my-4"></div>
                    
                    <!-- Reactive Benefits List -->
                    <ul class="space-y-3 text-xs text-[#111317] transition-all duration-300">
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-[#C2410C] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Semua Fitur Paket Gratis
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-[#C2410C] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span v-if="activeTier === 'personal'">Ekspor Project .zip Mandiri (1 Project)</span>
                            <span v-else><strong>Ekspor Project .zip UNLIMITED Project</strong></span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-[#C2410C] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Pilihan Stack: Laravel, React, Vue, HTML Statis
                        </li>
                        <li class="list-item-expand flex items-center gap-2.5" :class="{ 'show': activeTier === 'developer' }">
                            <svg class="w-4 h-4 text-[#C2410C] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <strong>Support Prioritas (Gmail/WA/Telegram)</strong>
                        </li>
                        <li class="list-item-expand flex items-center gap-2.5" :class="{ 'show': activeTier === 'developer' }">
                            <svg class="w-4 h-4 text-[#C2410C] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Free Update Komponen Selamanya
                        </li>
                    </ul>
                </div>
                <Link
                    :href="route('register')"
                    class="w-full text-center py-3.5 bg-[#C2410C] hover:bg-[#A1340A] text-white font-bold text-xs rounded-xl shadow-sm hover:shadow-md transition duration-200"
                >
                    {{ activeTier === 'personal' ? 'Beli Lisensi Pro' : 'Beli Lisensi Developer' }}
                </Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.list-item-expand {
    max-height: 0px;
    opacity: 0;
    margin-top: 0px;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease, margin 0.4s ease;
}
.list-item-expand.show {
    max-height: 40px;
    opacity: 1;
    margin-top: 12px; /* space-y-3 equivalent */
}
</style>
