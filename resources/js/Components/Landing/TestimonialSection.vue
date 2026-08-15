<script setup>
const testimonials = [
    {
        id: 1,
        name: 'Rian Kurnia',
        role: 'Freelance Web Developer',
        rating: 5,
        quote: 'Platform ini penyelamat deadline saya! Biasanya slicing visual ke Blade Laravel makan waktu berhari-hari. Pakai WebToWeb tinggal susun komponen, ekspor .zip, dan kodenya rapi tanpa kode sampah.',
        avatar: 'RK'
    },
    {
        id: 2,
        name: 'Amelia Putri',
        role: 'UI/UX Designer @ Creative Studio',
        rating: 5,
        quote: 'Sangat praktis membuat prototype interaktif langsung di browser. Klien saya jauh lebih mudah memahami alur navigasi halaman secara nyata dibanding hanya melihat desain statis di Figma.',
        avatar: 'AP'
    },
    {
        id: 3,
        name: 'Budi Santoso',
        role: 'Founder Agensi Digital',
        rating: 5,
        quote: 'Lisensi Developer unlimited sangat membantu agensi kami. Kami bisa men-slicing landing page klien dalam hitungan jam, mengunduh file mentahnya, lalu tinggal mengisi logic backend Laravel-nya.',
        avatar: 'BS'
    }
];
</script>

<template>
    <!-- Testimonial Section (Social Proof) -->
    <section id="testimoni" class="space-y-12">
        <div class="text-center space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#C2410C]/5 border border-[#C2410C]/20 text-[#C2410C] text-[10px] font-bold uppercase tracking-wider">
                Suara Pengguna
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#0E1015] leading-tight">
                Dicintai Oleh Developer & Kreator
            </h2>
            <p class="text-[#57585F] max-w-xl mx-auto text-sm sm:text-base leading-relaxed">
                Dengarkan langsung cerita mereka yang telah memangkas waktu pengerjaan project hingga 80% menggunakan visual builder kami.
            </p>
        </div>

        <!-- Masonry Grid Layout -->
        <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <div 
                v-for="item in testimonials" 
                :key="item.id"
                class="p-8 rounded-[2.5rem] bg-white border border-[#E8E6E0] shadow-2xs hover:shadow-xs transition duration-300 flex flex-col justify-between space-y-6"
            >
                <div class="space-y-4">
                    <!-- Rating Stars -->
                    <div class="flex gap-1">
                        <span 
                            v-for="star in item.rating" 
                            :key="star"
                            class="text-[#C2410C]"
                        >
                            ★
                        </span>
                    </div>
                    
                    <!-- Quote text -->
                    <p class="text-xs sm:text-sm text-[#57585F] leading-relaxed italic">
                        &ldquo;{{ item.quote }}&rdquo;
                    </p>
                </div>

                <!-- User profile info bar -->
                <div class="flex items-center gap-3 pt-4 border-t border-[#E8E6E0]/60">
                    <div class="w-8 h-8 rounded-lg bg-[#0E1015] flex items-center justify-center font-bold text-[10px] text-white select-none">
                        {{ item.avatar }}
                    </div>
                    <div>
                        <span class="block font-bold text-xs text-[#0E1015]">{{ item.name }}</span>
                        <span class="block text-[10px] text-[#57585F]/80">{{ item.role }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
