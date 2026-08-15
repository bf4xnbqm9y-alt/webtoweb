<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { gsap } from 'gsap';

const props = defineProps({
    isDemo: {
        type: Boolean,
        default: true
    },
    project: {
        type: Object,
        default: null
    }
});

// ---------------------------------------------------------------
// HELPER: unique id generator (collision-safe untuk array)
// ---------------------------------------------------------------
const makeId = () => 'sec-' + crypto.randomUUID();

// ---------------------------------------------------------------
// KATALOG KOMPONENT (default content per tipe section)
// Shape mengikuti skema JSON layout di blueprint.md
// ---------------------------------------------------------------
const CATALOG = [
    { type: 'Navbar', label: 'Navbar', desc: 'Navigasi atas dengan brand & menu' },
    { type: 'Hero', label: 'Hero Section', desc: 'Headline besar + CTA utama' },
    { type: 'Features', label: 'Grid Fitur', desc: 'Kartu keunggulan layanan' },
    { type: 'Testimonial', label: 'Testimonial', desc: 'Kutipan bukti sosial' },
    { type: 'Contact', label: 'Kontak', desc: 'Formulir & info kontak' },
    { type: 'Footer', label: 'Footer', desc: 'Kaki halaman & copyright' },
];

const createDefaultSection = (type) => {
    const base = { id: makeId(), type };
    switch (type) {
        case 'Navbar':
            return { ...base, content: { brand_name: 'Nama Brand', links: [{ label: 'Menu', url: '#menu' }] } };
        case 'Hero':
            return { ...base, content: { headline: 'Judul Utama Menarik', subheadline: 'Subjudul singkat yang menjelaskan value proposition.', btn_text: 'Mulai Sekarang', btn_color: 'obsidian' } };
        case 'Features':
            return { ...base, content: { heading: 'Keunggulan Kami', items: [{ title: 'Fitur 1', desc: 'Deskripsi singkat fitur pertama.' }, { title: 'Fitur 2', desc: 'Deskripsi singkat fitur kedua.' }, { title: 'Fitur 3', desc: 'Deskripsi singkat fitur ketiga.' }] } };
        case 'Testimonial':
            return { ...base, content: { quote: '“Produk ini benar-benar mengubah cara kami bekerja.”', name: 'Nama Pelanggan', role: 'Jabatan, Perusahaan' } };
        case 'Contact':
            return { ...base, content: { heading: 'Hubungi Kami', email: 'halo@example.com', whatsapp: '+62 812-3456-7890' } };
        case 'Footer':
            return { ...base, content: { copyright: '© 2026 Nama Brand. Hak Cipta Dilindungi.' } };
        default:
            return base;
    }
};

// ---------------------------------------------------------------
// STATE UTAMA BUILDER
// ---------------------------------------------------------------
const projectName = ref(props.isDemo ? 'Proyek Tanpa Nama (Demo)' : (props.project?.name || 'Proyek Tanpa Nama'));
const sections = ref(
    (!props.isDemo && props.project?.draft_data) 
        ? props.project.draft_data 
        : [
            createDefaultSection('Navbar'),
            createDefaultSection('Hero'),
            createDefaultSection('Features'),
            createDefaultSection('Testimonial'),
            createDefaultSection('Contact'),
            createDefaultSection('Footer'),
        ]
);

// Guard terhadap undefined: activeSection bernilai null jika tak ada yg terpilih
const activeSectionId = ref(sections.value[0].id);
const activeSection = computed(
    () => sections.value.find((s) => s.id === activeSectionId.value) ?? null,
);

const isPreviewMode = ref(false);
const isMobilePreview = ref(false);

const showExportModal = ref(false);
const selectedStack = ref('laravel');
const isExporting = ref(false);
const exportDone = ref(false);

// ---------------------------------------------------------------
// CLOUD AUTO-SAVE SYSTEM (Fase 3)
// ---------------------------------------------------------------
const saveStatus = ref('tersimpan'); // 'tersimpan', 'menyimpan', 'belum disimpan', 'gagal'
let saveTimeout = null;

const saveDraftToCloud = () => {
    if (props.isDemo) return;
    saveStatus.value = 'menyimpan';

    axios.post(route('builder.save', { project_slug: props.project.slug }), {
        draft_data: sections.value
    })
    .then(() => {
        saveStatus.value = 'tersimpan';
    })
    .catch((err) => {
        console.error('Failed to auto-save:', err);
        saveStatus.value = 'gagal';
    });
};

watch(sections, () => {
    if (props.isDemo) return;
    saveStatus.value = 'belum disimpan';

    if (saveTimeout) clearTimeout(saveTimeout);
    saveTimeout = setTimeout(() => {
        saveDraftToCloud();
    }, 1000); // Debounce auto-save by 1 second to reduce database writes
}, { deep: true });

// ---------------------------------------------------------------
// SECTION CRUD
// ---------------------------------------------------------------
const addSection = (type) => {
    const newSection = createDefaultSection(type);

    // Sisipkan sebelum Footer jika ada, agar Footer selalu di bawah.
    const footerIndex = sections.value.findIndex((sec) => sec.type === 'Footer');
    if (footerIndex !== -1) {
        sections.value.splice(footerIndex, 0, newSection);
    } else {
        sections.value.push(newSection);
    }

    activeSectionId.value = newSection.id;
};

const removeSection = (id) => {
    const index = sections.value.findIndex((sec) => sec.id === id);
    if (index === -1) return;

    // Proteksi: Navbar & Footer tidak bisa dihapus.
    if (sections.value[index].type === 'Navbar' || sections.value[index].type === 'Footer') return;

    sections.value.splice(index, 1);
    // Pindahkan seleksi ke section terdekat yang masih ada.
    const next = sections.value[Math.min(index, sections.value.length - 1)];
    activeSectionId.value = next?.id ?? null;
};

const moveSection = (id, direction) => {
    const index = sections.value.findIndex((sec) => sec.id === id);
    const target = index + direction;
    if (index === -1 || target < 0 || target >= sections.value.length) return;

    // Navbar tidak bisa turun dari posisi teratas; Footer tidak bisa naik dari posisi terbawah.
    const sec = sections.value[index];
    const neighbor = sections.value[target];
    if (sec.type === 'Navbar' || neighbor.type === 'Navbar') return;
    if (sec.type === 'Footer' || neighbor.type === 'Footer') return;

    [sections.value[index], sections.value[target]] = [sections.value[target], sections.value[index]];
};

const clearCanvas = () => {
    if (!window.confirm('Hapus semua komponen dari kanvas? Navbar & Footer akan dipertahankan.')) return;
    sections.value = [
        createDefaultSection('Navbar'),
        createDefaultSection('Footer'),
    ];
    activeSectionId.value = sections.value[0].id;
};

// --- Sub-item ops (Navbar links & Features items) ---
const addNavLink = () => {
    const section = activeSection.value;
    if (section?.type !== 'Navbar') return;
    section.content.links.push({ label: 'Menu Baru', url: '#menu' });
};

const removeNavLink = (index) => {
    const section = activeSection.value;
    if (section?.type !== 'Navbar') return;
    if (section.content.links.length <= 1) return; // minimal 1 link agar navbar wajar
    section.content.links.splice(index, 1);
};

const addFeatureItem = () => {
    const section = activeSection.value;
    if (section?.type !== 'Features') return;
    section.content.items.push({ title: `Fitur ${section.content.items.length + 1}`, desc: 'Deskripsi singkat fitur baru.' });
};

const removeFeatureItem = (index) => {
    const section = activeSection.value;
    if (section?.type !== 'Features') return;
    if (section.content.items.length <= 1) return;
    section.content.items.splice(index, 1);
};

const togglePreviewMode = () => {
    isPreviewMode.value = !isPreviewMode.value;
    if (!isPreviewMode.value) isMobilePreview.value = false;
};

// ---------------------------------------------------------------
// EXPORT MODAL (Simulasi generate ZIP 1.5 detik)
// ---------------------------------------------------------------
const STACKS = [
    { key: 'laravel', label: 'Laravel', icon: '⚓', desc: 'Blade + Tailwind' },
    { key: 'react', label: 'React', icon: '⚛️', desc: 'Vite + Tailwind' },
    { key: 'vue', label: 'Vue', icon: '💚', desc: 'Vite + Tailwind' },
    { key: 'html', label: 'HTML', icon: '🌐', desc: 'Statis murni' },
];

let exportTimer = null;
let closeTimer = null;

const openExportModal = () => {
    showExportModal.value = true;
    isExporting.value = false;
    exportDone.value = false;
    selectedStack.value = 'laravel';
};

const closeExportModal = () => {
    if (isExporting.value) return; // tidak bisa ditutup saat proses berjalan
    clearTimers();
    showExportModal.value = false;
    exportDone.value = false;
};

const runExport = () => {
    if (isExporting.value) return;
    isExporting.value = true;
    exportDone.value = false;

    exportTimer = setTimeout(() => {
        isExporting.value = false;
        exportDone.value = true;
        // Auto-close beberapa saat setelah sukses.
        closeTimer = setTimeout(() => {
            showExportModal.value = false;
            exportDone.value = false;
        }, 900);
    }, 1500);
};

const clearTimers = () => {
    if (exportTimer) clearTimeout(exportTimer);
    if (closeTimer) clearTimeout(closeTimer);
    exportTimer = null;
    closeTimer = null;
};

// ---------------------------------------------------------------
// MICRO-INTERACTIONS (GSAP Entrance Stagger)
// ---------------------------------------------------------------
onMounted(() => {
    const tl = gsap.timeline();
    tl.from('.animate-toolbar', { y: 20, opacity: 0, duration: 0.6, ease: 'power2.out' })
      .from('.animate-catalog', { y: 24, opacity: 0, duration: 0.6, ease: 'power2.out' }, '-=0.3')
      .from('.animate-canvas', { y: 24, opacity: 0, duration: 0.6, ease: 'power2.out' }, '-=0.4')
      .from('.animate-inspector', { y: 24, opacity: 0, duration: 0.6, ease: 'power2.out' }, '-=0.4');
});

onBeforeUnmount(() => {
    clearTimers();
});
</script>

<template>
    <Head title="Builder Workspace - WebToWeb" />

    <!-- Warm Alabaster Studio Theme -->
    <div class="min-h-screen bg-[#FAF9F5] text-[#111317] font-sans antialiased selection:bg-[#C2410C] selection:text-white relative overflow-x-hidden">
        <!-- Crisp Dotted Grid Overlay (light, konsisten dengan landing) -->
        <div class="absolute inset-0 z-0 pointer-events-none opacity-[0.35]"
             style="background-image: radial-gradient(rgba(17, 19, 23, 0.05) 1px, transparent 1px); background-size: 24px 24px;">
        </div>

        <div class="relative z-10 min-h-screen flex flex-col">
            <!-- ============ HEADER TOOLBAR ============ -->
            <header class="animate-toolbar sticky top-0 z-40 border-b border-[#E8E6E0] bg-white/90 backdrop-blur-md">
                <div class="flex items-center justify-between px-4 sm:px-6 py-3">
                    <!-- Kiri: Logo + Nama Proyek -->
                    <div class="flex items-center gap-3 min-w-0">
                        <Link href="/" class="flex items-center gap-2 select-none hover:opacity-85 transition">
                            <div class="w-8 h-8 rounded-lg bg-[#0E1015] flex items-center justify-center font-bold text-sm text-white">W</div>
                            <span class="hidden sm:inline text-sm font-bold tracking-tight text-[#0E1015]">Web<span class="text-[#C2410C] font-normal">To</span>Web</span>
                        </Link>
                        <div class="h-6 w-px bg-[#E8E6E0] hidden sm:block"></div>
                        <div class="flex items-center gap-2 min-w-0">
                            <input
                                v-model="projectName"
                                type="text"
                                class="w-full max-w-[140px] sm:max-w-[200px] bg-[#F5F4F0] hover:bg-[#EAE8E2] focus:bg-white border border-transparent focus:border-[#C2410C]/50 rounded-lg px-2.5 py-1.5 text-xs font-bold text-[#111317] focus:outline-none transition duration-200 truncate"
                                placeholder="Nama proyek..."
                            />
                            <!-- Mode Indicator Badge -->
                            <span v-if="isDemo" class="flex-shrink-0 text-[8px] font-bold uppercase bg-amber-500/10 text-amber-600 border border-amber-500/20 px-2 py-0.5 rounded-full select-none">
                                Demo
                            </span>
                            <span v-else class="flex-shrink-0 text-[8px] font-bold uppercase bg-green-500/10 text-green-600 border border-green-500/20 px-2 py-0.5 rounded-full select-none">
                                Workspace
                            </span>
                        </div>
                    </div>

                    <!-- Kanan: Aksi -->
                    <div class="flex items-center gap-2">
                        <!-- Cloud status -->
                        <div class="hidden md:flex items-center gap-1.5 text-[9px] font-mono text-[#57585F] select-none mr-2">
                            <template v-if="isDemo">
                                <Link href="/register" class="flex items-center gap-1 px-2.5 py-1 bg-[#C2410C]/5 hover:bg-[#C2410C]/10 border border-[#C2410C]/20 text-[#C2410C] rounded-lg transition duration-200 font-sans font-bold">
                                    <span>☁️ Simpan ke Cloud</span>
                                </Link>
                            </template>
                            <template v-else>
                                <span v-if="saveStatus === 'tersimpan'" class="flex items-center gap-1 text-emerald-600 font-sans font-bold transition duration-200">
                                    <span>☁️ Tersimpan di Cloud</span>
                                </span>
                                <span v-else-if="saveStatus === 'menyimpan'" class="flex items-center gap-1 text-amber-500 font-sans font-bold animate-pulse transition duration-200">
                                    <span>⏳ Menyimpan draf...</span>
                                </span>
                                <span v-else-if="saveStatus === 'belum disimpan'" class="flex items-center gap-1 text-slate-500 font-sans font-bold transition duration-200">
                                    <span>☁️ Perubahan terdeteksi</span>
                                </span>
                                <span v-else-if="saveStatus === 'gagal'" class="flex items-center gap-1 text-red-600 font-sans font-bold transition duration-200">
                                    <span>❌ Gagal menyimpan</span>
                                </span>
                            </template>
                        </div>

                        <button
                            @click="togglePreviewMode"
                            :class="isPreviewMode
                                ? 'bg-[#C2410C] text-white border-[#C2410C] hover:bg-[#A1340A]'
                                : 'bg-[#F5F4F0] hover:bg-[#EAE8E2] text-[#111317] border-[#E8E6E0]'"
                            class="hidden sm:flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider px-4 py-2.5 rounded-xl border transition duration-200"
                        >
                            <svg v-if="!isPreviewMode" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            <span>{{ isPreviewMode ? 'Kembali Edit' : 'Mode Pratinjau' }}</span>
                        </button>

                        <button
                            @click="clearCanvas"
                            class="hidden md:flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-[#57585F] hover:text-rose-600 bg-transparent hover:bg-rose-50 border border-[#E8E6E0] hover:border-rose-200 px-4 py-2.5 rounded-xl transition duration-200"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            Hapus Semua
                        </button>

                        <button
                            @click="openExportModal"
                            class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-white bg-[#C2410C] hover:bg-[#A1340A] px-4 py-2.5 rounded-xl shadow-sm hover:shadow-md transition duration-200"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            Ekspor Project
                        </button>
                    </div>
                </div>
            </header>

            <!-- ============ WORKSPACE 3-KOLOM ============ -->
            <main class="flex-1 flex gap-4 sm:gap-5 p-3 sm:p-5 min-h-0 overflow-hidden">
                <!-- SIDEBAR KIRI: Katalog Komponen -->
                <aside
                    class="animate-catalog w-56 sm:w-64 flex-shrink-0 bg-white border border-[#E8E6E0] rounded-2xl flex flex-col overflow-hidden"
                    :class="isPreviewMode ? 'hidden' : 'flex'"
                >
                    <div class="px-4 py-3.5 border-b border-[#E8E6E0]">
                        <div class="text-[10px] font-black uppercase tracking-widest text-[#57585F]">Katalog Komponen</div>
                        <div class="text-[9px] text-[#57585F]/70 mt-0.5">Klik untuk menambah ke kanvas</div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-3 space-y-2 no-scrollbar">
                        <button
                            v-for="item in CATALOG"
                            :key="item.type"
                            @click="addSection(item.type)"
                            class="w-full text-left p-3 bg-white hover:bg-[#FAF9F5] hover:border-[#C2410C]/30 border border-[#E8E6E0] rounded-xl transition duration-200 group"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-[#111317]">{{ item.label }}</span>
                                <span class="text-[9px] text-[#C2410C] font-bold font-mono opacity-0 group-hover:opacity-100 transition">+ ADD</span>
                            </div>
                            <div class="text-[9px] text-[#57585F] leading-snug mt-0.5">{{ item.desc }}</div>
                        </button>

                        <div class="pt-2 px-1 text-[8px] text-[#57585F]/70 leading-relaxed">
                            *Navbar & Footer dilindungi agar tetap di atas & bawah.
                        </div>
                    </div>
                </aside>

                <!-- KANVAS TENGAH: Viewport Frame -->
                <section class="animate-canvas flex-1 min-w-0 bg-white border border-[#E8E6E0] rounded-2xl flex flex-col overflow-hidden shadow-sm">
                    <!-- Frame browser (url bar) -->
                    <div class="flex items-center gap-2 px-4 py-2.5 border-b border-[#E8E6E0] bg-[#FAF9F5] select-none">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#E2E0D8]"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-[#E2E0D8]"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-[#E2E0D8]"></span>
                        </div>
                        <div class="flex-1 max-w-xs mx-auto bg-white border border-[#E8E6E0] rounded-full px-3 py-1 text-[9px] text-[#57585F] font-mono truncate">
                            {{ isPreviewMode ? 'mode-pratinjau://' : 'editor://' }}{{ projectName.toLowerCase().replace(/\s+/g, '-') || 'proyek' }}
                        </div>
                        <!-- Toggle device width saat preview -->
                        <div v-if="isPreviewMode" class="flex items-center gap-1 border border-[#E8E6E0] rounded-full bg-white p-0.5">
                            <button
                                @click="isMobilePreview = false"
                                :class="!isMobilePreview ? 'bg-[#0E1015] text-white' : 'text-[#57585F]'"
                                class="px-2 py-0.5 rounded-full text-[8px] font-bold uppercase transition duration-200"
                            >Desktop</button>
                            <button
                                @click="isMobilePreview = true"
                                :class="isMobilePreview ? 'bg-[#0E1015] text-white' : 'text-[#57585F]'"
                                class="px-2 py-0.5 rounded-full text-[8px] font-bold uppercase transition duration-200"
                            >Mobile</button>
                        </div>
                    </div>

                    <!-- Scrollable page canvas -->
                    <div class="flex-1 overflow-y-auto bg-[#F5F4F0] p-3 sm:p-6">
                        <div
                            class="mx-auto bg-white border border-[#E8E6E0] shadow-sm transition-all duration-300"
                            :class="isMobilePreview ? 'max-w-[390px] rounded-xl' : 'max-w-4xl rounded-[1.5rem]'"
                                     <!-- Render sections (data-driven) -->
                            <div 
                                v-for="(sec, idx) in sections" 
                                :key="sec.id" 
                                @click="activeSectionId = sec.id"
                                class="relative transition-all duration-300"
                                :class="[
                                    isPreviewMode ? '' : 'cursor-pointer border border-transparent',
                                    !isPreviewMode && activeSectionId === sec.id ? 'ring-2 ring-[#C2410C] ring-offset-1 z-10 bg-[#C2410C]/[0.01]' : '',
                                    !isPreviewMode && activeSectionId !== sec.id ? 'hover:border-[#C2410C]/40 hover:bg-[#FAF9F5]/30' : ''
                                ]"
                            >

                                <!-- ===== NAVBAR ===== -->
                                <div
                                    v-if="sec.type === 'Navbar'"
                                    class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-[#E8E6E0] bg-white select-none transition"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-md bg-[#0E1015] flex items-center justify-center font-bold text-xs text-white">
                                            {{ sec.content.brand_name.charAt(0) }}
                                        </div>
                                        <span class="font-extrabold text-xs sm:text-sm tracking-tight text-[#0E1015]">{{ sec.content.brand_name }}</span>
                                    </div>
                                    <div class="hidden sm:flex gap-6 text-[10px] uppercase font-bold tracking-wider text-[#57585F]">
                                        <span v-for="link in sec.content.links" :key="link.label" class="hover:text-[#0E1015] transition">{{ link.label }}</span>
                                    </div>
                                    <span class="text-[8px] font-bold uppercase tracking-widest text-white bg-[#0E1015] px-3.5 py-1.5 rounded-lg">Sign In</span>
                                </div>

                                <!-- ===== HERO ===== -->
                                <div
                                    v-else-if="sec.type === 'Hero'"
                                    class="px-6 sm:px-12 py-12 sm:py-20 text-center bg-[#FAF9F5]/50 border-b border-[#E8E6E0] select-none transition"
                                >
                                    <span class="inline-flex items-center gap-1.5 text-[8px] font-bold uppercase tracking-widest text-[#C2410C] bg-[#C2410C]/5 border border-[#C2410C]/20 px-2.5 py-1 rounded mb-4">
                                        <span class="w-1 h-1 rounded-full bg-[#C2410C]"></span>
                                        Hero Segment
                                    </span>
                                    <h2 class="text-xl sm:text-3xl font-extrabold text-[#0E1015] tracking-tight leading-tight max-w-xl mx-auto">{{ sec.content.headline }}</h2>
                                    <p class="text-xs sm:text-sm text-[#57585F] max-w-md mx-auto mt-4 leading-relaxed">{{ sec.content.subheadline }}</p>
                                    <div class="mt-6">
                                        <div
                                            class="inline-flex items-center justify-center text-[10px] font-bold uppercase tracking-wider text-white px-6 py-3 rounded-xl shadow-xs transition duration-200"
                                            :class="{
                                                'bg-[#0E1015] hover:bg-[#252830]': sec.content.btn_color === 'obsidian',
                                                'bg-[#C2410C] hover:bg-[#A1340A]': sec.content.btn_color === 'terracotta',
                                                'bg-amber-500 hover:bg-amber-600 text-[#0E1015]': sec.content.btn_color === 'amber',
                                            }"
                                        >
                                            {{ sec.content.btn_text }}
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== FEATURES (Grid) ===== -->
                                <div
                                    v-else-if="sec.type === 'Features'"
                                    class="px-6 sm:px-12 py-10 sm:py-14 bg-white border-b border-[#E8E6E0] select-none transition"
                                >
                                    <h3 class="text-sm sm:text-base font-extrabold text-[#0E1015] uppercase tracking-widest text-center mb-8">{{ sec.content.heading }}</h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                                        <div v-for="(item, i) in sec.content.items" :key="i" class="p-5 bg-[#FAF9F5] border border-[#E8E6E0] rounded-[1.5rem] hover:border-[#C2410C]/20 transition duration-300">
                                            <div class="w-7 h-7 rounded-lg bg-white border border-[#E8E6E0] flex items-center justify-center text-[#C2410C] mb-3 shadow-2xs">
                                                <!-- Dynamic mini feature icons -->
                                                <svg v-if="i%3===0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                                                <svg v-else-if="i%3===1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V13.5m-7.5-7.5h7.5m-7.5 0v7.5m7.5-7.5-7.5 7.5" /></svg>
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747" /></svg>
                                            </div>
                                            <div class="text-xs font-extrabold text-[#111317]">{{ item.title }}</div>
                                            <div class="text-[10px] text-[#57585F] leading-relaxed mt-1.5">{{ item.desc }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== TESTIMONIAL ===== -->
                                <div
                                    v-else-if="sec.type === 'Testimonial'"
                                    class="px-6 sm:px-12 py-10 sm:py-14 text-center bg-[#FAF9F5]/40 border-b border-[#E8E6E0] select-none transition"
                                >
                                    <div class="w-8 h-8 rounded-full bg-white border border-[#E8E6E0] flex items-center justify-center mx-auto text-[#C2410C] shadow-2xs mb-4">
                                        “
                                    </div>
                                    <p class="text-sm sm:text-base text-[#0E1015] font-medium italic max-w-xl mx-auto leading-relaxed">{{ sec.content.quote }}</p>
                                    <div class="mt-5 flex items-center justify-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-[#0E1015] text-white flex items-center justify-center text-[10px] font-black tracking-wider uppercase">
                                            {{ sec.content.name.charAt(0) }}
                                        </div>
                                        <div class="text-left">
                                            <div class="text-xs font-bold text-[#111317] leading-none">{{ sec.content.name }}</div>
                                            <span class="text-[9px] text-[#57585F]">{{ sec.content.role }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== CONTACT ===== -->
                                <div
                                    v-else-if="sec.type === 'Contact'"
                                    class="px-6 sm:px-12 py-10 sm:py-14 bg-white border-b border-[#E8E6E0] select-none transition"
                                >
                                    <h3 class="text-sm sm:text-base font-extrabold text-[#0E1015] uppercase tracking-widest text-center mb-6">{{ sec.content.heading }}</h3>
                                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                                        <!-- Mail Tag -->
                                        <div class="flex items-center gap-2 px-4 py-2 bg-[#FAF9F5] border border-[#E8E6E0] rounded-xl text-[10px] text-[#57585F] font-mono shadow-2xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-[#C2410C]">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0l-7.5-4.615m19.5 0v-2.43" />
                                            </svg>
                                            <span>{{ sec.content.email }}</span>
                                        </div>
                                        <!-- WhatsApp Tag -->
                                        <div class="flex items-center gap-2 px-4 py-2 bg-[#FAF9F5] border border-[#E8E6E0] rounded-xl text-[10px] text-[#57585F] font-mono shadow-2xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="w-3.5 h-3.5 text-[#C2410C]" viewBox="0 0 16 16">
                                                <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.977h.004c4.368 0 7.927-3.559 7.93-7.93a7.9 7.9 0 0 0-2.327-5.615zM7.994 14.52a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                                            </svg>
                                            <span>{{ sec.content.whatsapp }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== FOOTER ===== -->
                                <div
                                    v-else-if="sec.type === 'Footer'"
                                    class="px-6 sm:px-12 py-6 text-center bg-white border-t border-[#E8E6E0]/60 select-none transition"
                                >
                                    <div class="text-[10px] text-[#57585F] font-mono">{{ sec.content.copyright }}</div>
                                </div>

                                <!-- Floating action bar (muncul saat section aktif) -->
                                <div
                                    v-if="!isPreviewMode && activeSectionId === sec.id"
                                    class="absolute -top-3.5 right-4 z-20 flex items-center gap-0.5 bg-white border border-[#E8E6E0] rounded-lg shadow-md p-1"
                                >
                                    <button
                                        @click.stop="moveSection(sec.id, -1)"
                                        :disabled="idx === 0 || sections[idx - 1].type === 'Navbar'"
                                        class="w-6 h-6 flex items-center justify-center rounded-md text-[10px] font-bold text-[#57585F] hover:bg-[#FAF9F5] hover:text-[#0E1015] transition duration-150 disabled:opacity-30 disabled:pointer-events-none"
                                        title="Naikkan"
                                    >↑</button>
                                    <button
                                        @click.stop="moveSection(sec.id, 1)"
                                        :disabled="idx === sections.length - 1 || sections[idx + 1].type === 'Footer'"
                                        class="w-6 h-6 flex items-center justify-center rounded-md text-[10px] font-bold text-[#57585F] hover:bg-[#FAF9F5] hover:text-[#0E1015] transition duration-150 disabled:opacity-30 disabled:pointer-events-none"
                                        title="Turunkan"
                                    >↓</button>
                                    <button
                                        @click.stop="removeSection(sec.id)"
                                        :disabled="sec.type === 'Navbar' || sec.type === 'Footer'"
                                        class="w-6 h-6 flex items-center justify-center rounded-md text-[#57585F] hover:bg-rose-50 hover:text-rose-600 transition duration-150 disabled:opacity-30 disabled:pointer-events-none"
                                        :title="sec.type === 'Navbar' || sec.type === 'Footer' ? 'Diproteksi, tidak bisa dihapus' : 'Hapus'"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Empty state -->
                            <div v-if="sections.length === 0" class="py-24 text-center text-xs text-[#57585F]">
                                Kanvas kosong. Tambahkan komponen dari katalog di kiri.
                            </div>
                        </div>
                    </div>
                </section>

                <!-- INSPECTOR KANAN: Settings Panel -->
                <aside
                    class="animate-inspector w-64 sm:w-72 flex-shrink-0 bg-white border border-[#E8E6E0] rounded-2xl flex flex-col overflow-hidden"
                    :class="isPreviewMode ? 'hidden' : 'flex'"
                >
                    <div class="px-4 py-3.5 border-b border-[#E8E6E0]">
                        <div class="text-[10px] font-black uppercase tracking-widest text-[#57585F]">Settings</div>
                        <div class="text-[9px] text-[#57585F]/70 mt-0.5">Sesuaikan isi komponen terpilih</div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 space-y-5 no-scrollbar">
                        <template v-if="activeSection">
                            <!-- Label jenis -->
                            <div class="text-[10px] font-black text-[#C2410C] uppercase tracking-widest">
                                {{ activeSection.type }}
                            </div>

                            <!-- ===== INSPECTOR: NAVBAR ===== -->
                            <div v-if="activeSection.type === 'Navbar'" class="space-y-4">
                                <div class="space-y-1.5">
                                    <label class="block text-[8px] text-[#57585F] uppercase font-black tracking-wider">Teks Logo / Brand</label>
                                    <input v-model="activeSection.content.brand_name" type="text" class="input-field" />
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[8px] text-[#57585F] uppercase font-black tracking-wider">Menu Links</span>
                                        <button @click="addNavLink" class="text-[8px] font-black uppercase tracking-wider text-[#C2410C] hover:text-[#A1340A] transition">+ Tambah</button>
                                    </div>
                                    <div v-for="(link, i) in activeSection.content.links" :key="i" class="space-y-1.5 p-2.5 bg-[#FAF9F5] border border-[#E8E6E0] rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[8px] text-[#57585F] uppercase font-black tracking-wider">Link {{ i + 1 }}</span>
                                            <button @click="removeNavLink(i)" class="text-[9px] text-rose-500 hover:text-rose-700 transition">✕</button>
                                        </div>
                                        <input v-model="link.label" type="text" placeholder="Label" class="input-field" />
                                        <input v-model="link.url" type="text" placeholder="#menu" class="input-field" />
                                    </div>
                                </div>
                            </div>

                            <!-- ===== INSPECTOR: HERO ===== -->
                            <div v-else-if="activeSection.type === 'Hero'" class="space-y-4">
                                <div class="space-y-1.5">
                                    <label class="block text-[8px] text-[#57585F] uppercase font-black tracking-wider">Headline</label>
                                    <textarea v-model="activeSection.content.headline" rows="2" class="input-field resize-none"></textarea>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[8px] text-[#57585F] uppercase font-black tracking-wider">Subheadline</label>
                                    <textarea v-model="activeSection.content.subheadline" rows="3" class="input-field resize-none"></textarea>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[8px] text-[#57585F] uppercase font-black tracking-wider">Teks Tombol</label>
                                    <input v-model="activeSection.content.btn_text" type="text" class="input-field" />
                                </div>
                                <div class="space-y-2">
                                    <span class="block text-[8px] text-[#57585F] uppercase font-black tracking-wider">Warna Tombol</span>
                                    <div class="flex gap-2">
                                        <button @click="activeSection.content.btn_color = 'obsidian'" class="w-7 h-7 rounded-lg bg-[#0E1015] transition" :class="activeSection.content.btn_color === 'obsidian' ? 'ring-2 ring-[#C2410C] ring-offset-2 ring-offset-white' : 'opacity-70 hover:opacity-100'"></button>
                                        <button @click="activeSection.content.btn_color = 'terracotta'" class="w-7 h-7 rounded-lg bg-[#C2410C] transition" :class="activeSection.content.btn_color === 'terracotta' ? 'ring-2 ring-[#C2410C] ring-offset-2 ring-offset-white' : 'opacity-70 hover:opacity-100'"></button>
                                        <button @click="activeSection.content.btn_color = 'amber'" class="w-7 h-7 rounded-lg bg-amber-500 transition" :class="activeSection.content.btn_color === 'amber' ? 'ring-2 ring-[#C2410C] ring-offset-2 ring-offset-white' : 'opacity-70 hover:opacity-100'"></button>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== INSPECTOR: FEATURES ===== -->
                            <div v-else-if="activeSection.type === 'Features'" class="space-y-4">
                                <div class="space-y-1.5">
                                    <label class="block text-[8px] text-[#57585F] uppercase font-black tracking-wider">Heading Section</label>
                                    <input v-model="activeSection.content.heading" type="text" class="input-field" />
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[8px] text-[#57585F] uppercase font-black tracking-wider">Item Fitur</span>
                                        <button @click="addFeatureItem" class="text-[8px] font-black uppercase tracking-wider text-[#C2410C] hover:text-[#A1340A] transition">+ Tambah</button>
                                    </div>
                                    <div v-for="(item, i) in activeSection.content.items" :key="i" class="space-y-1.5 p-2.5 bg-[#FAF9F5] border border-[#E8E6E0] rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[8px] text-[#57585F] uppercase font-black tracking-wider">Fitur {{ i + 1 }}</span>
                                            <button @click="removeFeatureItem(i)" class="text-[9px] text-rose-500 hover:text-rose-700 transition">✕</button>
                                        </div>
                                        <input v-model="item.title" type="text" placeholder="Judul" class="input-field" />
                                        <textarea v-model="item.desc" rows="2" placeholder="Deskripsi" class="input-field resize-none"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== INSPECTOR: TESTIMONIAL ===== -->
                            <div v-else-if="activeSection.type === 'Testimonial'" class="space-y-4">
                                <div class="space-y-1.5">
                                    <label class="block text-[8px] text-[#57585F] uppercase font-black tracking-wider">Kutipan</label>
                                    <textarea v-model="activeSection.content.quote" rows="3" class="input-field resize-none"></textarea>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[8px] text-[#57585F] uppercase font-black tracking-wider">Nama</label>
                                    <input v-model="activeSection.content.name" type="text" class="input-field" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[8px] text-[#57585F] uppercase font-black tracking-wider">Jabatan / Perusahaan</label>
                                    <input v-model="activeSection.content.role" type="text" class="input-field" />
                                </div>
                            </div>

                            <!-- ===== INSPECTOR: CONTACT ===== -->
                            <div v-else-if="activeSection.type === 'Contact'" class="space-y-4">
                                <div class="space-y-1.5">
                                    <label class="block text-[8px] text-[#57585F] uppercase font-black tracking-wider">Heading</label>
                                    <input v-model="activeSection.content.heading" type="text" class="input-field" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[8px] text-[#57585F] uppercase font-black tracking-wider">Email</label>
                                    <input v-model="activeSection.content.email" type="email" class="input-field" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[8px] text-[#57585F] uppercase font-black tracking-wider">WhatsApp</label>
                                    <input v-model="activeSection.content.whatsapp" type="text" class="input-field" />
                                </div>
                            </div>

                            <!-- ===== INSPECTOR: FOOTER ===== -->
                            <div v-else-if="activeSection.type === 'Footer'" class="space-y-4">
                                <div class="space-y-1.5">
                                    <label class="block text-[8px] text-[#57585F] uppercase font-black tracking-wider">Teks Copyright</label>
                                    <input v-model="activeSection.content.copyright" type="text" class="input-field" />
                                </div>
                            </div>
                        </template>

                        <!-- Fallback: tidak ada section terpilih -->
                        <div v-else class="text-[10px] text-[#57585F] italic">
                            Pilih salah satu komponen di kanvas untuk mulai mengedit.
                        </div>
                    </div>
                </aside>
            </main>
        </div>

        <!-- ============ MODAL EKSPOR PROJECT ============ -->
        <Transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showExportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-[#111317]/40 backdrop-blur-sm" @click="closeExportModal"></div>

                <!-- Panel -->
                <div class="relative w-full max-w-lg bg-white border border-[#E8E6E0] rounded-3xl shadow-2xl overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-[#E8E6E0]">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-[#0E1015] flex items-center justify-center text-white">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            </span>
                            <div>
                                <div class="text-sm font-bold text-[#111317]">Ekspor Project</div>
                                <div class="text-[9px] text-[#57585F]">Unduh source code website Anda</div>
                            </div>
                        </div>
                        <button
                            @click="closeExportModal"
                            :disabled="isExporting"
                            class="w-7 h-7 flex items-center justify-center rounded-lg text-[#57585F] hover:bg-[#F5F4F0] transition duration-150"
                            :class="isExporting ? 'opacity-40 pointer-events-none' : ''"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-5">
                        <div class="text-[10px] font-black uppercase tracking-widest text-[#57585F] mb-3">Pilih Teknologi Target</div>
                        <div class="grid grid-cols-2 gap-3">
                            <button
                                v-for="stack in STACKS"
                                :key="stack.key"
                                @click="selectedStack = stack.key"
                                :disabled="isExporting"
                                class="text-left p-3.5 rounded-2xl border transition duration-200"
                                :class="selectedStack === stack.key
                                    ? 'bg-[#C2410C]/5 border-[#C2410C] ring-1 ring-[#C2410C]/30'
                                    : 'bg-white border-[#E8E6E0] hover:border-[#C2410C]/30'"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="text-lg">{{ stack.icon }}</span>
                                    <div>
                                        <div class="text-xs font-bold text-[#111317]">{{ stack.label }}</div>
                                        <div class="text-[9px] text-[#57585F]">{{ stack.desc }}</div>
                                    </div>
                                </div>
                            </button>
                        </div>

                        <!-- Progress / Status -->
                        <div class="mt-5">
                            <!-- Exporting state -->
                            <div v-if="isExporting" class="space-y-2.5">
                                <div class="flex items-center gap-2.5 text-[10px] text-[#57585F] font-bold uppercase tracking-wider">
                                    <span class="w-3.5 h-3.5 rounded-full border-2 border-[#C2410C] border-t-transparent animate-spin"></span>
                                    Menyiapkan project {{ STACKS.find((s) => s.key === selectedStack)?.label }}...
                                </div>
                                <div class="h-1.5 w-full bg-[#F5F4F0] rounded-full overflow-hidden">
                                    <div class="h-full bg-[#C2410C] rounded-full transition-all duration-[1500ms] ease-out w-full"></div>
                                </div>
                            </div>

                            <!-- Success state -->
                            <div v-else-if="exportDone" class="flex items-center justify-center gap-2.5 py-1.5 text-[11px] font-bold text-[#0E1015]">
                                <span class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                Project berhasil diekspor sebagai ZIP!
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-[#E8E6E0] bg-[#FAF9F5]">
                        <button
                            v-if="!exportDone"
                            @click="runExport"
                            :disabled="isExporting"
                            class="w-full flex items-center justify-center gap-2 text-[10px] font-bold uppercase tracking-wider text-white bg-[#C2410C] hover:bg-[#A1340A] py-3 rounded-xl shadow-sm hover:shadow-md transition duration-200"
                            :class="isExporting ? 'opacity-60 pointer-events-none' : ''"
                        >
                            <span v-if="!isExporting">⬇ Mulai Ekspor ZIP</span>
                            <span v-else>Memproses...</span>
                        </button>
                        <div v-else class="w-full text-center text-[10px] font-bold uppercase tracking-wider text-emerald-600 py-1.5">
                            ✓ Berhasil Diekspor
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* Utility: sembunyikan scrollbar agar kanvas bersih & elegan */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    scrollbar-width: none;
    -ms-overflow-style: none;
}

/* Style input default untuk seluruh inspector (warm alabaster theme) */
.input-field {
    width: 100%;
    background-color: #fff;
    border: 1px solid #E8E6E0;
    border-radius: 0.5rem;
    padding: 0.375rem 0.625rem;
    font-size: 12px;
    color: #111317;
    transition: border-color 0.2s ease;
}
.input-field:focus {
    outline: none;
    border-color: rgba(194, 65, 12, 0.5);
    box-shadow: 0 0 0 3px rgba(194, 65, 12, 0.08);
}
</style>
