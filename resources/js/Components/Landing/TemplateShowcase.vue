<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const activeCategory = ref('semua');

const templates = [
    { id: 1, title: 'Portofolio Kreator', category: 'portofolio', isPro: false, image: 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800&auto=format&fit=crop&q=60', desc: 'Desain minimalis dan bersih untuk memamerkan karya visual.' },
    { id: 2, title: 'Landing Page Bisnis', category: 'bisnis', isPro: true, image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=60', desc: 'Optimasi konversi klien dengan struktur section terperinci.' },
    { id: 3, title: 'Toko Online / Katalog', category: 'toko', isPro: true, image: 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=800&auto=format&fit=crop&q=60', desc: 'Etalase produk modern lengkap dengan checkout siap pakai.' },
    { id: 4, title: 'Form Registrasi & Event', category: 'form', isPro: false, image: 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=800&auto=format&fit=crop&q=60', desc: 'Formulir interaktif modern untuk pendaftaran event.' },
];
</script>

<template>
    <!-- Templates Section -->
    <section id="template" class="space-y-12">
        <div class="text-center space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#C2410C]/5 border border-[#C2410C]/20 text-[#C2410C] text-[10px] font-bold uppercase tracking-wider">
                Desain Siap Pakai
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#0E1015]">
                Mulai dengan Fondasi Desain Premium
            </h2>
            <p class="text-[#57585F] max-w-xl mx-auto text-sm sm:text-base leading-relaxed">
                Pilih template dasar di bawah ini untuk melihat contoh kerangka yang dapat Anda sesuaikan langsung di kanvas editor.
            </p>
        </div>

        <!-- Category Tabs -->
        <div class="flex flex-wrap items-center justify-center gap-2">
            <button
                v-for="cat in ['semua', 'portofolio', 'bisnis', 'toko', 'form']"
                :key="cat"
                @click="activeCategory = cat"
                class="px-4 py-2 text-xs font-bold rounded-xl capitalize transition duration-200"
                :class="activeCategory === cat ? 'bg-[#0E1015] text-white shadow-sm' : 'bg-white text-[#57585F] border border-[#E8E6E0] hover:text-[#0E1015] hover:bg-[#FAF9F5]'"
            >
                {{ cat }}
            </button>
        </div>

        <!-- Template Grid -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                v-for="item in templates.filter(t => activeCategory === 'semua' || t.category === activeCategory)"
                :key="item.id"
                class="group rounded-[2rem] bg-white border border-[#E8E6E0] overflow-hidden hover:border-[#C2410C]/30 hover:shadow-md transition duration-300 flex flex-col justify-between"
            >
                <div class="relative overflow-hidden aspect-[4/3] border-b border-[#E8E6E0]">
                    <!-- Pro/Free Badge -->
                    <div 
                        class="absolute top-3 left-3 z-10 px-2.5 py-1 rounded-lg text-[8px] font-black uppercase tracking-wider shadow-2xs select-none"
                        :class="item.isPro ? 'bg-[#C2410C] text-white' : 'bg-white text-[#57585F] border border-[#E8E6E0]'"
                    >
                        {{ item.isPro ? 'Pro' : 'Free' }}
                    </div>

                    <img
                        :src="item.image"
                        :alt="item.title"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                    />
                    <!-- Glass Overlay on Hover -->
                    <div class="absolute inset-0 bg-[#FAF9F5]/80 backdrop-blur-xs opacity-0 group-hover:opacity-100 flex items-center justify-center transition duration-300">
                        <Link
                            :href="route('builder.demo')"
                            class="px-5 py-2.5 bg-[#0E1015] hover:bg-[#252830] text-white text-xs font-bold rounded-xl transition duration-200 shadow-sm"
                        >
                            Buka Demo Editor
                        </Link>
                    </div>
                </div>
                <div class="p-6 space-y-2">
                    <span class="text-[9px] text-[#C2410C] uppercase tracking-widest font-black">{{ item.category }}</span>
                    <h4 class="text-sm font-bold text-[#0E1015]">{{ item.title }}</h4>
                    <p class="text-xs text-[#57585F] leading-relaxed">{{ item.desc }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
