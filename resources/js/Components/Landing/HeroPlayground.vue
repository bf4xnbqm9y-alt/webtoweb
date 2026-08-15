<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';

// --- PLAYGROUND MOCKUP STATE (Vue Reactive State for Landing Page Mini-Builder) ---
const mockupSections = ref([
    { id: 'mock-nav', type: 'Navbar', content: { brand: 'MyBrand' } },
    { id: 'mock-hero', type: 'Hero', content: { title: 'Rancang Website Impian', btnColor: 'obsidian' } },
    { id: 'mock-footer', type: 'Footer', content: { copyright: '© 2026 Brand' } }
]);

const activeMockupIndex = ref(1); // Default active is Hero

const addMockupSection = (type) => {
    let newSection = null;
    const id = 'mock-' + Date.now();
    
    if (type === 'Grid') {
        newSection = {
            id,
            type: 'Grid',
            content: { title1: 'Fitur 1', title2: 'Fitur 2', title3: 'Fitur 3' }
        };
    } else if (type === 'Hero') {
        newSection = {
            id,
            type: 'Hero',
            content: { title: 'Layanan Utama Kami', btnColor: 'terracotta' }
        };
    } else if (type === 'Navbar') {
        newSection = {
            id,
            type: 'Navbar',
            content: { brand: 'KustomLogo' }
        };
    }

    if (newSection) {
        const footerIndex = mockupSections.value.findIndex(sec => sec.type === 'Footer');
        mockupSections.value.splice(footerIndex, 0, newSection);
        activeMockupIndex.value = footerIndex; // Set active to newly added section
    }
};

const deleteMockupSection = (index, event) => {
    event.stopPropagation(); // Prevent selection trigger
    if (mockupSections.value[index].type === 'Navbar' || mockupSections.value[index].type === 'Footer') {
        return; // Protect navbar & footer from deletion
    }
    mockupSections.value.splice(index, 1);
    activeMockupIndex.value = 0; // Reset active to Navbar
};
// --- DRAFT AUTO-SAVE MITIGATION (Risiko 3 Proof of Concept) ---
watch(mockupSections, (newVal) => {
    try {
        localStorage.setItem('wtw_playground_draft', JSON.stringify(newVal));
    } catch (e) {
        console.error(e);
    }
}, { deep: true });

onMounted(() => {
    try {
        const savedDraft = localStorage.getItem('wtw_playground_draft');
        if (savedDraft) {
            mockupSections.value = JSON.parse(savedDraft);
        }
    } catch (e) {
        console.error(e);
    }
});
</script>

<template>
    <!-- Hero & Playground Section -->
    <section class="text-center max-w-4xl mx-auto space-y-8">
        <!-- Badge -->
        <div class="animate-hero-badge inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#C2410C]/5 border border-[#C2410C]/20 text-[#C2410C] text-[10px] font-bold uppercase tracking-wider">
            <span class="w-1.5 h-1.5 rounded-full bg-[#C2410C]"></span>
            No-Code Visual Development Environment
        </div>
        
        <!-- Headline (Editorial Layout with Staggered Word Anim) -->
        <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-[#0E1015] leading-[1.05] select-none">
            <!-- Line 1: Simple Smooth Fade Up -->
            <span class="inline-block animate-title-line-1">Rancang & Publikasikan Website</span>
            <br class="hidden sm:inline">
            <!-- Line 2: Word-by-word Stagger -->
            <span class="inline-block">
                <span 
                    v-for="(word, idx) in 'Tanpa Perlu Menulis Kode'.split(' ')" 
                    :key="idx" 
                    class="inline-block animate-title-word mr-[0.22em] last:mr-0 text-[#C2410C] font-black"
                >
                    {{ word }}
                </span>
            </span>
        </h1>

        <!-- Paragraph -->
        <p class="animate-hero-text text-sm sm:text-md text-[#57585F] max-w-2xl mx-auto leading-relaxed">
            Cobalah editor interaktif di bawah ini secara langsung! Pilih komponen di sebelah kiri, edit kontennya di sebelah kanan, dan lihat perubahannya secara real-time di kanvas tengah.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
            <Link
                :href="route('register')"
                class="animate-hero-btn w-full sm:w-auto text-center font-bold text-xs uppercase tracking-wider text-white bg-[#0E1015] hover:bg-[#252830] shadow-md px-8 py-4.5 rounded-xl transition duration-200"
            >
                Mulai Buat Website Gratis
            </Link>
            <Link
                :href="route('builder.demo')"
                class="animate-hero-btn w-full sm:w-auto text-center font-bold text-xs uppercase tracking-wider text-[#111317] bg-white hover:bg-[#FAF9F5] border border-[#E8E6E0] px-8 py-4.5 rounded-xl transition duration-200"
            >
                Buka Full Editor Demo
            </Link>
        </div>

        <!-- INTERACTIVE PLAYGROUND MOCKUP -->
        <div class="animate-hero-mockup pt-16 max-w-5xl mx-auto relative group">
            
            <!-- Main Card Container (Flat & Stable for easy interaction) -->
            <div 
                class="relative p-2.5 rounded-[2rem] bg-white border border-[#E8E6E0] shadow-lg"
            >
                <!-- Visual Title bar -->
                <div class="flex items-center justify-between px-5 py-3 border-b border-[#E8E6E0] bg-[#FAF9F5] select-none rounded-t-[1.8rem]">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#E2E0D8]"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#E2E0D8]"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#E2E0D8]"></span>
                    </div>
                    <span class="text-[9px] text-[#57585F] font-mono tracking-widest uppercase">Cobalah Interaksi Playground Ini</span>
                    <div class="flex gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    </div>
                </div>
                
                <!-- Detailed Interactive Workspace Simulation -->
                <div class="bg-white rounded-b-[1.8rem] overflow-hidden h-[340px] md:h-auto md:aspect-[16/9] flex text-left text-xs">
                    
                    <!-- LEFT PANEL: Component Catalog -->
                    <div class="hidden md:flex md:w-1/4 border-r border-[#E8E6E0] p-4 space-y-4 bg-[#FAF9F5]/40 select-none flex-col">
                        <div class="text-[9px] text-[#57585F] font-bold uppercase tracking-wider">Klik Untuk Tambah</div>
                        <div class="space-y-2">
                            <button 
                                @click="addMockupSection('Navbar')"
                                class="w-full text-left p-2.5 bg-white hover:bg-[#FAF9F5] rounded-xl border border-[#E8E6E0] hover:border-[#C2410C]/30 flex items-center justify-between text-[10px] text-[#111317] transition"
                            >
                                <span>Navbar Section</span>
                                <span class="text-[8px] text-[#C2410C] font-bold font-mono">+ ADD</span>
                            </button>
                            <button 
                                @click="addMockupSection('Hero')"
                                class="w-full text-left p-2.5 bg-white hover:bg-[#FAF9F5] rounded-xl border border-[#E8E6E0] hover:border-[#C2410C]/30 flex items-center justify-between text-[10px] text-[#111317] transition"
                            >
                                <span>Hero Banner</span>
                                <span class="text-[8px] text-[#C2410C] font-bold font-mono">+ ADD</span>
                            </button>
                            <button 
                                @click="addMockupSection('Grid')"
                                class="w-full text-left p-2.5 bg-white hover:bg-[#FAF9F5] rounded-xl border border-[#E8E6E0] hover:border-[#C2410C]/30 flex items-center justify-between text-[10px] text-[#111317] transition"
                            >
                                <span>Grid Features</span>
                                <span class="text-[8px] text-[#C2410C] font-bold font-mono">+ ADD</span>
                            </button>
                        </div>
                        <div class="text-[8px] text-[#57585F] leading-normal pt-2">
                            *Catatan: Navbar & Footer diproteksi agar tetap berada di atas dan di bawah.
                        </div>
                    </div>
                    
                    <!-- CENTER CANVAS -->
                    <div class="w-full md:flex-1 p-5 bg-[#FAF9F5]/20 flex flex-col gap-3 md:border-r border-[#E8E6E0] overflow-y-auto max-h-full scrollbar-thin">
                        <template v-for="(sec, idx) in mockupSections" :key="sec.id">
                            
                            <!-- Render Navbar Mockup -->
                            <div 
                                v-if="sec.type === 'Navbar'" 
                                @click="activeMockupIndex = idx"
                                class="w-full py-2.5 px-4 rounded-xl border flex items-center justify-between text-[9px] cursor-pointer transition select-none"
                                :class="activeMockupIndex === idx ? 'bg-white border-[#C2410C] ring-2 ring-[#C2410C] ring-offset-1 z-10 shadow-sm' : 'bg-white border-[#E8E6E0] hover:border-slate-300'"
                            >
                                <span class="font-bold text-[#0E1015]">{{ sec.content.brand }}</span>
                                <div class="flex gap-3 text-slate-400 font-bold">
                                    <span>Fitur</span>
                                    <span>Harga</span>
                                </div>
                            </div>

                            <!-- Render Hero Mockup -->
                            <div 
                                v-else-if="sec.type === 'Hero'" 
                                @click="activeMockupIndex = idx"
                                class="w-full py-5 px-4 border rounded-2xl text-center space-y-3 relative cursor-pointer transition"
                                :class="activeMockupIndex === idx ? 'bg-white border-[#C2410C] ring-2 ring-[#C2410C] ring-offset-1 z-10 shadow-sm' : 'bg-white border-[#E8E6E0] hover:border-slate-300'"
                            >
                                <button 
                                    @click="deleteMockupSection(idx, $event)"
                                    class="absolute top-2 right-2 text-slate-400 hover:text-rose-600 px-1.5 py-0.5 rounded text-[8px] font-bold select-none"
                                >
                                    ✕
                                </button>
                                <div class="text-[10px] font-extrabold text-[#0E1015] max-w-[80%] mx-auto leading-tight">{{ sec.content.title }}</div>
                                
                                <div 
                                    class="h-5 px-3 rounded-lg mx-auto flex items-center justify-center text-[7px] font-bold w-16 select-none shadow-sm"
                                    :class="{
                                        'bg-[#0E1015] text-white': sec.content.btnColor === 'obsidian',
                                        'bg-[#C2410C] text-white': sec.content.btnColor === 'terracotta',
                                        'bg-amber-500 text-[#0E1015]': sec.content.btnColor === 'amber'
                                    }"
                                >
                                    Button
                                </div>
                            </div>

                            <!-- Render Grid Mockup -->
                            <div 
                                v-else-if="sec.type === 'Grid'" 
                                @click="activeMockupIndex = idx"
                                class="w-full p-4 border rounded-2xl grid grid-cols-3 gap-2 relative cursor-pointer transition text-center"
                                :class="activeMockupIndex === idx ? 'bg-white border-[#C2410C] ring-2 ring-[#C2410C] ring-offset-1 z-10 shadow-sm' : 'bg-white border-[#E8E6E0] hover:border-slate-300'"
                            >
                                <button 
                                    @click="deleteMockupSection(idx, $event)"
                                    class="absolute top-1.5 right-1.5 text-slate-400 hover:text-rose-600 px-1 py-0.5 rounded text-[7px] font-bold select-none"
                                >
                                    ✕
                                </button>
                                <div class="p-2 bg-[#FAF9F5] border border-[#E8E6E0] rounded-lg">
                                    <div class="w-1.5 h-1.5 rounded-full bg-[#0E1015] mx-auto mb-1"></div>
                                    <div class="text-[7px] font-bold text-[#111317] truncate">{{ sec.content.title1 }}</div>
                                </div>
                                <div class="p-2 bg-[#FAF9F5] border border-[#E8E6E0] rounded-lg">
                                    <div class="w-1.5 h-1.5 rounded-full bg-[#C2410C] mx-auto mb-1"></div>
                                    <div class="text-[7px] font-bold text-[#111317] truncate">{{ sec.content.title2 }}</div>
                                </div>
                                <div class="p-2 bg-[#FAF9F5] border border-[#E8E6E0] rounded-lg">
                                    <div class="w-1.5 h-1.5 rounded-full bg-amber-500 mx-auto mb-1"></div>
                                    <div class="text-[7px] font-bold text-[#111317] truncate">{{ sec.content.title3 }}</div>
                                </div>
                            </div>

                            <!-- Render Footer Mockup -->
                            <div 
                                v-else-if="sec.type === 'Footer'" 
                                @click="activeMockupIndex = idx"
                                class="w-full py-2.5 bg-[#FAF9F5] border text-center text-[7px] text-[#57585F] rounded-xl cursor-pointer transition select-none"
                                :class="activeMockupIndex === idx ? 'bg-[#FAF9F5] border-[#C2410C] ring-2 ring-[#C2410C] ring-offset-1 z-10 shadow-sm' : 'border-[#E8E6E0] hover:border-slate-300'"
                            >
                                {{ sec.content.copyright }}
                            </div>
                        </template>
                    </div>
                    
                    <!-- RIGHT PANEL: Properties Inspector -->
                    <div class="hidden md:flex md:w-1/4 p-4 space-y-4 bg-[#FAF9F5]/40 flex-col">
                        <div class="text-[9px] text-[#57585F] font-bold uppercase tracking-wider select-none">Settings</div>
                        
                        <div class="space-y-4" v-if="mockupSections[activeMockupIndex]">
                            <div class="text-[9px] font-black text-[#C2410C] uppercase tracking-widest select-none">
                                {{ mockupSections[activeMockupIndex].type }}
                            </div>

                            <!-- Properties inputs -->
                            <div v-if="mockupSections[activeMockupIndex].type === 'Navbar'" class="space-y-2">
                                <div class="space-y-1">
                                    <span class="text-[8px] text-[#57585F] uppercase font-black tracking-wider block">Teks Logo</span>
                                    <input 
                                        v-model="mockupSections[activeMockupIndex].content.brand" 
                                        type="text" 
                                        class="w-full bg-white border border-[#E8E6E0] rounded-lg px-2.5 py-1.5 text-[10px] text-[#111317] focus:outline-none focus:border-[#C2410C]/50"
                                    />
                                </div>
                            </div>

                            <div v-else-if="mockupSections[activeMockupIndex].type === 'Hero'" class="space-y-3">
                                <div class="space-y-1">
                                    <span class="text-[8px] text-[#57585F] uppercase font-black tracking-wider block">Teks Judul</span>
                                    <textarea 
                                        v-model="mockupSections[activeMockupIndex].content.title" 
                                        rows="2"
                                        class="w-full bg-white border border-[#E8E6E0] rounded-lg px-2.5 py-1.5 text-[10px] text-[#111317] focus:outline-none focus:border-[#C2410C]/50 resize-none"
                                    ></textarea>
                                </div>
                                <div class="space-y-1 select-none">
                                    <span class="text-[8px] text-[#57585F] uppercase font-black tracking-wider block">Warna Tombol</span>
                                    <div class="flex gap-2">
                                        <button 
                                            @click="mockupSections[activeMockupIndex].content.btnColor = 'obsidian'"
                                            class="w-5 h-5 rounded bg-[#0E1015] border transition"
                                            :class="mockupSections[activeMockupIndex].content.btnColor === 'obsidian' ? 'border-[#C2410C] ring-2 ring-[#C2410C]/30' : 'border-transparent'"
                                        ></button>
                                        <button 
                                            @click="mockupSections[activeMockupIndex].content.btnColor = 'terracotta'"
                                            class="w-5 h-5 rounded bg-[#C2410C] border transition"
                                            :class="mockupSections[activeMockupIndex].content.btnColor === 'terracotta' ? 'border-[#0E1015] ring-2 ring-[#0E1015]/20' : 'border-transparent'"
                                        ></button>
                                        <button 
                                            @click="mockupSections[activeMockupIndex].content.btnColor = 'amber'"
                                            class="w-5 h-5 rounded bg-amber-500 border transition"
                                            :class="mockupSections[activeMockupIndex].content.btnColor === 'amber' ? 'border-[#0E1015] ring-2 ring-[#0E1015]/20' : 'border-transparent'"
                                        ></button>
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="mockupSections[activeMockupIndex].type === 'Grid'" class="space-y-2">
                                <div class="space-y-1.5">
                                    <span class="text-[8px] text-[#57585F] uppercase font-black tracking-wider block">Fitur 1</span>
                                    <input 
                                        v-model="mockupSections[activeMockupIndex].content.title1" 
                                        type="text" 
                                        class="w-full bg-white border border-[#E8E6E0] rounded-lg px-2.5 py-1 text-[9px] text-[#111317] focus:outline-none"
                                    />
                                </div>
                                <div class="space-y-1.5">
                                    <span class="text-[8px] text-[#57585F] uppercase font-black tracking-wider block">Fitur 2</span>
                                    <input 
                                        v-model="mockupSections[activeMockupIndex].content.title2" 
                                        type="text" 
                                        class="w-full bg-white border border-[#E8E6E0] rounded-lg px-2.5 py-1 text-[9px] text-[#111317] focus:outline-none"
                                    />
                                </div>
                                <div class="space-y-1.5">
                                    <span class="text-[8px] text-[#57585F] uppercase font-black tracking-wider block">Fitur 3</span>
                                    <input 
                                        v-model="mockupSections[activeMockupIndex].content.title3" 
                                        type="text" 
                                        class="w-full bg-white border border-[#E8E6E0] rounded-lg px-2.5 py-1 text-[9px] text-[#111317] focus:outline-none"
                                    />
                                </div>
                            </div>

                            <div v-else-if="mockupSections[activeMockupIndex].type === 'Footer'" class="space-y-2">
                                <div class="space-y-1">
                                    <span class="text-[8px] text-[#57585F] uppercase font-black tracking-wider block">Copyright</span>
                                    <input 
                                        v-model="mockupSections[activeMockupIndex].content.copyright" 
                                        type="text" 
                                        class="w-full bg-white border border-[#E8E6E0] rounded-lg px-2.5 py-1.5 text-[10px] text-[#111317] focus:outline-none"
                                    />
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-[10px] text-[#57585F] italic select-none">
                            Pilih salah satu komponen di kanvas untuk mulai mengedit.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
@keyframes cursor-blink {
    50% { opacity: 0; }
}
.animate-pulse-cursor {
    animation: cursor-blink 0.8s step-end infinite;
}
</style>
