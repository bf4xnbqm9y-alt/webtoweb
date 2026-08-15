<script setup>
import { ref } from 'vue';

const activeIndex = ref(null);

const faqs = [
    {
        question: 'Apakah source code hasil ekspor benar-benar bersih?',
        answer: 'Ya. Kode yang diekspor terstruktur rapi sesuai standar industri. Tidak ada kode sampah atau dependensi visual builder tersembunyi—semuanya murni Tailwind CSS dan framework yang Anda pilih.'
    },
    {
        question: 'Teknologi (Stack) apa saja yang didukung?',
        answer: 'Saat ini kami mendukung ekspor ke Laravel (Blade + Tailwind), React (Vite + Tailwind), Vue (Vite + Tailwind), serta HTML & CSS Statis murni siap pakai.'
    },
    {
        question: 'Bagaimana skema pembayaran lisensi Pro & Developer?',
        answer: 'Kami menerapkan skema sekali bayar (lifetime purchase) untuk semua lisensi premium. Lisensi Personal Pro ideal untuk ekspor 1 project mandiri, sedangkan Lisensi Developer memberikan hak ekspor tanpa batas project selamanya tanpa biaya langganan bulanan.'
    },
    {
        question: 'Bagaimana cara meng-online-kan website hasil ekspor?',
        answer: 'Untuk HTML statis, Anda bisa mengunggahnya ke Netlify, Vercel, atau GitHub Pages secara gratis. Untuk Laravel/React/Vue, Anda bisa memasangnya di cPanel, VPS, atau cloud hosting andalan Anda.'
    }
];

const toggleFaq = (index) => {
    if (activeIndex.value === index) {
        activeIndex.value = null;
    } else {
        activeIndex.value = index;
    }
};
</script>

<template>
    <!-- FAQ Section -->
    <section id="faq" class="py-12 border-t border-[#E8E6E0]/60 space-y-12">
        <div class="text-center space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#C2410C]/5 border border-[#C2410C]/20 text-[#C2410C] text-[10px] font-bold uppercase tracking-wider">
                FAQ (Tanya Jawab)
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#0E1015] leading-tight">
                Pertanyaan yang Sering Diajukan
            </h2>
            <p class="text-[#57585F] max-w-xl mx-auto text-sm sm:text-base leading-relaxed">
                Temukan jawaban cepat atas pertanyaan umum seputar platform visual builder dan sistem ekspor kode kami.
            </p>
        </div>

        <div class="max-w-3xl mx-auto space-y-4">
            <div 
                v-for="(faq, index) in faqs" 
                :key="index"
                class="rounded-2xl border border-[#E8E6E0] bg-white overflow-hidden transition-all duration-300 shadow-2xs"
                :class="activeIndex === index ? 'border-[#C2410C]/30 shadow-xs' : 'hover:border-[#C2410C]/20'"
            >
                <!-- Question Header -->
                <button
                    @click="toggleFaq(index)"
                    class="w-full px-6 py-5 flex items-center justify-between text-left focus:outline-none transition duration-200 select-none"
                >
                    <span class="text-xs sm:text-sm font-bold text-[#0E1015] pr-4">
                        {{ faq.question }}
                    </span>
                    <span 
                        class="w-6 h-6 flex-shrink-0 rounded-full bg-[#FAF9F5] border border-[#E8E6E0] flex items-center justify-center text-[#57585F] transition-transform duration-300"
                        :class="activeIndex === index ? 'rotate-180 border-[#C2410C]/20 text-[#C2410C]' : ''"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </span>
                </button>

                <!-- Answer Content -->
                <div 
                    class="transition-all duration-300 ease-in-out overflow-hidden"
                    :style="activeIndex === index ? 'max-height: 200px;' : 'max-height: 0px;'"
                >
                    <div class="px-6 pb-6 pt-1 text-xs text-[#57585F] leading-relaxed border-t border-[#FAF9F5]">
                        {{ faq.answer }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
