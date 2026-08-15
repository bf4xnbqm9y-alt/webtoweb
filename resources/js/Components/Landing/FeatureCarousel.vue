<script setup>
import { ref, onMounted } from 'vue';

// --- CAROUSEL DATA (Clean data-driven mapping) ---
const cardsData = [
    {
        id: 'card-1',
        title: 'Visual Component Builder',
        desc: 'Merancang tata letak halaman semudah menyusun blok komponen visual. Lakukan inline-editing teks secara langsung dan ubah style melalui panel kontrol visual kustom.',
        icon: 'builder',
        bg: 'bg-white border-[#E8E6E0] hover:border-[#C2410C]/30 text-[#0E1015] select-none',
        hasCustomFooter: 'builder'
    },
    {
        id: 'card-2',
        title: 'Live Public Preview',
        desc: 'Dapatkan public link instan untuk menguji pratinjau website Anda langsung di tab baru secara utuh tanpa toolbar editor.',
        icon: 'preview',
        bg: 'bg-white border-[#E8E6E0] hover:border-[#C2410C]/30 text-[#0E1015]',
        hasCustomFooter: 'preview'
    },
    {
        id: 'card-3',
        title: 'Super Fast Loading',
        desc: 'Aset website yang di-generate dirancang bersih tanpa script berlebih, menjamin kecepatan akses optimal demi kenyamanan pengunjung.',
        icon: 'loading',
        bg: 'bg-white border-[#E8E6E0] hover:border-[#C2410C]/30 text-[#0E1015]',
        hasCustomFooter: 'loading'
    },
    {
        id: 'card-4',
        title: 'Clean Code Exporter',
        desc: 'Ketika mendownload, system kami akan memetakan desain Anda ke dalam struktur project terstandarisasi. Tidak ada kode acak—semuanya valid dan siap dipasang di localhost.',
        icon: 'exporter',
        bg: 'bg-white border-[#E8E6E0] hover:border-[#C2410C]/30 text-[#0E1015]',
        hasCustomFooter: 'exporter'
    }
];

// Display the cards array directly (Clean native scroll list - 4 cards)
const displayCards = cardsData;

const carouselRef = ref(null);
const activeCardIndex = ref(0); // Default to first card (index 0)

const handleCarouselScroll = () => {
    if (!carouselRef.value) return;
    const container = carouselRef.value;
    const cards = container.querySelectorAll('.bento-card');
    if (cards.length === 0) return;
    
    // Determine closest card to viewport center for blur-focus highlight
    const containerRect = container.getBoundingClientRect();
    const containerCenter = containerRect.left + containerRect.width / 2;
    
    let closestIndex = 0;
    let minDistance = Infinity;
    
    cards.forEach((card, idx) => {
        const cardRect = card.getBoundingClientRect();
        const cardCenter = cardRect.left + cardRect.width / 2;
        const distance = Math.abs(containerCenter - cardCenter);
        if (distance < minDistance) {
            minDistance = distance;
            closestIndex = idx;
        }
    });
    
    activeCardIndex.value = closestIndex;
};


onMounted(() => {
    setTimeout(() => {
        handleCarouselScroll();
    }, 150);
});
</script>

<template>
    <!-- Features Bento Grid (Warm Minimalist Style) -->
    <section id="fitur" class="space-y-16">
        <div class="text-center space-y-4">
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#0E1015] leading-tight">
                Platform Rancang Web Berperforma Tinggi
            </h2>
            <p class="text-[#57585F] max-w-xl mx-auto text-sm sm:text-base leading-relaxed">
                Bebaskan diri dari setup environment coding. Kami merancang arsitektur bersih yang dioptimasi untuk kecepatan pemuatan halaman dan kemudahan ekspor.
            </p>
        </div>

        <!-- Horizontal Scroll Showcase (With Desktop Navigation Controls) -->
        <div class="relative group/carousel max-w-5xl mx-auto">
            <!-- Scrollable container wrapper -->
            <div 
                ref="carouselRef" 
                @scroll="handleCarouselScroll" 
                class="no-scrollbar flex gap-6 overflow-x-auto snap-x snap-mandatory pb-6 px-[calc(50%-140px)] sm:px-[calc(50%-200px)] scroll-smooth" 
                style="scrollbar-width: none; -ms-overflow-style: none;"
            >
                <div 
                    v-for="(card, idx) in displayCards"
                    :key="idx"
                    class="bento-card w-[280px] sm:w-[400px] flex-shrink-0 snap-center p-8 rounded-[2rem] border flex flex-col justify-between transition-all duration-500 transform ease-out group shadow-sm relative overflow-hidden"
                    :class="[
                        card.bg,
                        activeCardIndex === idx 
                            ? 'opacity-100 scale-100 blur-none shadow-md z-10' 
                            : 'opacity-25 scale-[0.96] blur-[2px] pointer-events-none'
                    ]"
                >
                    <div class="space-y-4 z-10">
                        <!-- Icon container -->
                        <div class="w-12 h-12 rounded-xl bg-white border border-[#E8E6E0] flex items-center justify-center text-[#C2410C] group-hover:bg-[#0E1015] group-hover:text-white transition duration-300 shadow-xs">
                            <!-- Icon 1: Visual Builder -->
                            <svg v-if="card.icon === 'builder'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V13.5m-7.5-7.5h7.5m-7.5 0v7.5m7.5-7.5-7.5 7.5" />
                            </svg>
                            
                            <!-- Icon 2: Live Public Preview -->
                            <svg v-else-if="card.icon === 'preview'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747" />
                            </svg>
                            
                            <!-- Icon 3: Super Fast Loading -->
                            <svg v-else-if="card.icon === 'loading'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                            
                            <!-- Icon 4: Clean Code Exporter -->
                            <svg v-else-if="card.icon === 'exporter'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3 6-6M9 21h6a2.25 2.25 0 0 0 2.25-2.25V5.25A2.25 2.25 0 0 0 15 3H9a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 9 21z" />
                            </svg>
                        </div>
                        
                        <h3 class="text-lg font-bold">{{ card.title }}</h3>
                        <p class="text-sm text-[#57585F] leading-relaxed">{{ card.desc }}</p>
                    </div>
                    
                    <!-- Dynamic custom footer mockups inside card -->
                    <div v-if="card.hasCustomFooter === 'builder'" class="mt-8 p-4 bg-[#FAF9F5] rounded-2xl border border-[#E8E6E0] flex flex-wrap gap-2 text-[10px] sm:text-xs font-mono text-[#57585F] select-none z-10">
                        <div class="px-2 py-1.5 bg-[#C2410C]/10 text-[#C2410C] rounded-lg border border-[#C2410C]/20 font-bold">&lt;Navbar /&gt;</div>
                        <div class="px-2 py-1.5 bg-white rounded-lg border border-[#E8E6E0]">&lt;BannerHero /&gt;</div>
                        <div class="px-2 py-1.5 bg-white rounded-lg border border-[#E8E6E0]">&lt;CardGrid /&gt;</div>
                    </div>

                    <div v-else-if="card.hasCustomFooter === 'preview'" class="mt-8 p-3 bg-[#FAF9F5] rounded-xl border border-[#E8E6E0] flex items-center justify-between text-[10px] text-[#C2410C] font-mono z-10">
                        <span>/preview/my-site-1</span>
                        <span class="w-2 h-2 rounded-full bg-[#C2410C] animate-pulse"></span>
                    </div>

                    <div v-else-if="card.hasCustomFooter === 'loading'" class="mt-8 flex items-center gap-3 z-10">
                        <span class="text-3xl font-black text-[#0E1015]">99%</span>
                        <span class="text-[9px] uppercase tracking-widest font-black text-[#C2410C] bg-[#C2410C]/10 border border-[#C2410C]/20 px-2 py-1 rounded">Core Web Vitals</span>
                    </div>

                    <div v-else-if="card.hasCustomFooter === 'exporter'" class="mt-8 p-4 bg-[#FAF9F5] rounded-2xl border border-[#E8E6E0] text-[8px] sm:text-[10px] font-mono text-[#57585F] space-y-1 z-10">
                        <div><span class="text-[#C2410C]">app/</span> Http/Controllers/ContactController.php</div>
                        <div><span class="text-[#0E1015] font-bold">resources/</span> views/welcome.blade.php</div>
                        <div><span class="text-indigo-600">routes/</span> web.php</div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
</style>
