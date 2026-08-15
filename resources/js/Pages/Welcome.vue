<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ScrollToPlugin } from 'gsap/ScrollToPlugin';

// Import Modular Landing Components
import HeroPlayground from '@/Components/Landing/HeroPlayground.vue';
import FeatureCarousel from '@/Components/Landing/FeatureCarousel.vue';
import TemplateShowcase from '@/Components/Landing/TemplateShowcase.vue';
import TestimonialSection from '@/Components/Landing/TestimonialSection.vue';
import PricingSection from '@/Components/Landing/PricingSection.vue';
import ContactSection from '@/Components/Landing/ContactSection.vue';
import FaqSection from '@/Components/Landing/FaqSection.vue';

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

// --- DYNAMIC SPOTLIGHT TRACKING (Ultra Lightweight CSS-only, Mobile friendly) ---
const globalMouseX = ref(0);
const globalMouseY = ref(0);
const showScrollTop = ref(false);
const isMobileMenuOpen = ref(false);
const isScrolled = ref(false);

const handleMouseMoveGlobal = (event) => {
    globalMouseX.value = event.pageX;
    globalMouseY.value = event.pageY;
};

const handleScrollGlobal = () => {
    showScrollTop.value = window.scrollY > 300;
    isScrolled.value = window.scrollY > 10;
};

const scrollToSection = (id, event) => {
    event.preventDefault();
    isMobileMenuOpen.value = false;
    gsap.to(window, {
        duration: 0.8,
        scrollTo: { y: id, offsetY: 100 },
        ease: 'power3.out'
    });
};

const scrollToTop = () => {
    gsap.to(window, {
        duration: 0.8,
        scrollTo: { y: 0 },
        ease: 'power3.out'
    });
};

onMounted(() => {
    // 1. Initial GSAP Entrance Animations (Runs globally over DOM classes)
    const tl = gsap.timeline();
    tl.from('.animate-hero-badge', { y: 15, opacity: 0, duration: 0.6, ease: 'power2.out' })
      .from('.animate-title-line-1', { y: 25, opacity: 0, duration: 0.8, ease: 'power3.out' }, '-=0.35')
      .from('.animate-title-word', { y: 15, opacity: 0, duration: 0.5, ease: 'back.out(1.2)', stagger: 0.08 }, '-=0.45')
      
    // 2. Scroll Trigger Reveal Animations (Fade up staggered on scroll)
    const sectionsToReveal = ['#fitur', '#template', '#testimoni', '#harga', '#faq', '#kontak'];
    sectionsToReveal.forEach((selector) => {
        gsap.from(selector, {
            scrollTrigger: {
                trigger: selector,
                start: 'top 85%',
                toggleActions: 'play none none none'
            },
            y: 35,
            opacity: 0,
            duration: 0.8,
            ease: 'power3.out'
        });
    });

    window.addEventListener('mousemove', handleMouseMoveGlobal);
    window.addEventListener('scroll', handleScrollGlobal);
});

onBeforeUnmount(() => {
    window.removeEventListener('mousemove', handleMouseMoveGlobal);
    window.removeEventListener('scroll', handleScrollGlobal);
});
</script>

<template>
    <Head title="Web-to-Web Builder - Studio Rancang Visual Website" />

    <!-- Studio Warm Light Backdrop Decor (Lightweight Gradient GPU) -->
    <div class="min-h-screen bg-[#FAF9F5] text-[#111317] selection:bg-[#C2410C]/10 selection:text-[#C2410C] overflow-x-hidden relative">
        
        <!-- Glow spotlight behind content (Dynamic CSS Variables spotlight) -->
        <div 
            class="pointer-events-none absolute inset-0 z-0 opacity-[0.3] hidden sm:block"
            :style="`background: radial-gradient(500px circle at ${globalMouseX}px ${globalMouseY}px, rgba(194, 65, 12, 0.06), transparent 80%);`"
        ></div>

        <!-- Crisp Dotted Grid Overlay -->
        <div class="absolute inset-0 z-0 pointer-events-none opacity-[0.4]" 
             style="background-image: radial-gradient(rgba(17, 19, 23, 0.05) 1px, transparent 1px); background-size: 24px 24px;">
        </div>

        <!-- Dynamic Gradient Blur Mask Layer (Background only - Fades blur intensity using mask-image to avoid sharp cuts) -->
        <div 
            class="fixed top-0 left-0 right-0 z-40 h-[110px] pointer-events-none backdrop-blur-md bg-gradient-to-b from-[#FAF9F5] via-[#FAF9F5]/75 to-transparent transition-all duration-300"
            :class="isScrolled ? 'opacity-100' : 'opacity-0'"
            style="-webkit-mask-image: linear-gradient(to bottom, black 30%, transparent 100%); mask-image: linear-gradient(to bottom, black 30%, transparent 100%);"
        ></div>

        <!-- Foreground Navigation Capsule -->
        <header 
            class="fixed top-0 left-0 right-0 z-50 w-full transition-all duration-300 pointer-events-none"
            :class="isScrolled ? 'py-3' : 'py-5'"
        >
            <div class="max-w-5xl mx-auto px-6 pointer-events-auto">
                <div class="flex items-center justify-between px-8 py-4 bg-white border border-[#E8E6E0] shadow-sm rounded-3xl">
                    <!-- Brand Logo -->
                    <div class="flex items-center gap-3 select-none">
                        <div class="w-8 h-8 rounded-lg bg-[#0E1015] flex items-center justify-center font-bold text-sm text-white">
                            W
                        </div>
                        <span class="text-md font-bold tracking-tight text-[#0E1015]">Web<span class="text-[#C2410C] font-normal">To</span>Web</span>
                    </div>

                    <!-- Desktop Center Links -->
                    <nav class="hidden md:flex items-center gap-8 text-[11px] font-bold uppercase tracking-wider text-[#57585F]">
                        <a href="#fitur" @click="scrollToSection('#fitur', $event)" class="hover:text-[#0E1015] transition duration-200">Fitur</a>
                        <a href="#template" @click="scrollToSection('#template', $event)" class="hover:text-[#0E1015] transition duration-200">Template</a>
                        <a href="#harga" @click="scrollToSection('#harga', $event)" class="hover:text-[#0E1015] transition duration-200">Harga</a>
                        <a href="#faq" @click="scrollToSection('#faq', $event)" class="hover:text-[#0E1015] transition duration-200">FAQ</a>
                        <a href="#kontak" @click="scrollToSection('#kontak', $event)" class="hover:text-[#0E1015] transition duration-200">Kontak</a>
                    </nav>

                    <!-- Desktop Auth buttons -->
                    <div v-if="canLogin" class="hidden md:flex items-center gap-5">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="text-[10px] font-bold uppercase tracking-wider text-[#111317] bg-[#F5F4F0] hover:bg-[#EAE8E2] px-5 py-2.5 rounded-lg border border-[#E8E6E0] transition duration-200"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="text-[10px] font-bold uppercase tracking-wider text-[#57585F] hover:text-[#111317] transition duration-200"
                            >
                                Masuk
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="text-[10px] font-bold uppercase tracking-wider text-white bg-[#0E1015] hover:bg-[#252830] shadow-sm px-5 py-2.5 rounded-xl transition duration-200"
                            >
                                Daftar Gratis
                            </Link>
                        </template>
                    </div>

                    <!-- Mobile Hamburger Button -->
                    <button 
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="md:hidden p-2 text-[#57585F] hover:text-[#0E1015] focus:outline-none transition duration-200"
                    >
                        <svg v-if="!isMobileMenuOpen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Dropdown Panel (Elegant off-white capsule drawer) -->
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 -translate-y-4 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0 scale-100"
                leave-to-class="opacity-0 -translate-y-4 scale-95"
            >
                <div 
                    v-if="isMobileMenuOpen"
                    class="md:hidden absolute top-full left-6 right-6 mt-3 p-6 bg-white border border-[#E8E6E0] rounded-[2rem] shadow-lg z-50 flex flex-col gap-5 text-center pointer-events-auto"
                >
                    <!-- Mobile Navigation Links -->
                    <nav class="flex flex-col gap-4 text-xs font-bold uppercase tracking-wider text-[#57585F]">
                        <a href="#fitur" @click="scrollToSection('#fitur', $event)" class="py-2 hover:text-[#0E1015] border-b border-[#FAF9F5] transition">Fitur</a>
                        <a href="#template" @click="scrollToSection('#template', $event)" class="py-2 hover:text-[#0E1015] border-b border-[#FAF9F5] transition">Template</a>
                        <a href="#harga" @click="scrollToSection('#harga', $event)" class="py-2 hover:text-[#0E1015] border-b border-[#FAF9F5] transition">Harga</a>
                        <a href="#faq" @click="scrollToSection('#faq', $event)" class="py-2 hover:text-[#0E1015] border-b border-[#FAF9F5] transition">FAQ</a>
                        <a href="#kontak" @click="scrollToSection('#kontak', $event)" class="py-2 hover:text-[#0E1015] transition">Kontak</a>
                    </nav>

                    <div class="w-full h-px bg-[#E8E6E0] my-1"></div>

                    <!-- Mobile Auth Actions -->
                    <div v-if="canLogin" class="flex flex-col gap-3">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="w-full py-3.5 text-center font-bold text-xs uppercase tracking-wider text-[#111317] bg-[#FAF9F5] border border-[#E8E6E0] rounded-xl"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="w-full py-3 text-center font-bold text-xs uppercase tracking-wider text-[#57585F] hover:text-[#111317] transition"
                            >
                                Masuk
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="w-full py-3.5 text-center font-bold text-xs uppercase tracking-wider text-white bg-[#0E1015] rounded-xl shadow-xs"
                            >
                                Daftar Gratis
                            </Link>
                        </template>
                    </div>
                </div>
            </Transition>
        </header>

        <div class="relative max-w-7xl mx-auto px-6 lg:px-8 z-10">
            <main class="mt-36 space-y-44 pb-24">
                
                <!-- 1. Hero & Simulator Playground (Interactive Mockup) -->
                <div id="hero">
                    <HeroPlayground />
                </div>

                <!-- 2. Features Bento Showcase (Horizontal Scroll) -->
                <div id="fitur">
                    <FeatureCarousel />
                </div>

                <!-- 3. Premium Templates Showcase (Dynamic Grid) -->
                <div id="template">
                    <TemplateShowcase />
                </div>

                <!-- Testimonials Section (Social Proof) -->
                <div id="testimoni">
                    <TestimonialSection />
                </div>

                <!-- 4. Pricing Plans Comparison (SaaS Tiering) -->
                <div id="harga">
                    <PricingSection />
                </div>

                <!-- 5. FAQ (Tanya Jawab) Accordion Grid -->
                <div id="faq">
                    <FaqSection />
                </div>

                <!-- 6. Contact, Criticism & Suggestions Form -->
                <div id="kontak">
                    <ContactSection />
                </div>

            </main>

            <!-- Footer (Clean, elegant editorial layout) -->
            <footer class="pt-16 pb-8 border-t border-[#E8E6E0]/60 space-y-12">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 text-xs text-[#57585F]">
                    <!-- Brand Column -->
                    <div class="md:col-span-5 space-y-4">
                        <div class="flex items-center gap-3 select-none">
                            <div class="w-7 h-7 rounded-lg bg-[#0E1015] flex items-center justify-center font-bold text-xs text-white">
                                W
                            </div>
                            <span class="text-sm font-bold tracking-tight text-[#0E1015]">Web<span class="text-[#C2410C] font-normal">To</span>Web</span>
                        </div>
                        <p class="max-w-xs leading-relaxed text-[11px]">
                            Platform studio rancang visual website no-code premium. Ekspor source code bersih (Laravel, React, Vue, HTML) siap pakai dalam hitungan detik.
                        </p>
                        <!-- Social links -->
                        <div class="flex items-center gap-3 pt-2">
                            <a href="mailto:aaaapiiiiss14@gmail.com" class="w-7 h-7 rounded-full bg-white border border-[#E8E6E0] hover:border-[#C2410C]/35 flex items-center justify-center text-[#57585F] hover:text-[#C2410C] transition duration-200" title="Email">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0l-7.5-4.615m19.5 0v-2.43" /></svg>
                            </a>
                            <a href="https://wa.me/6282381047450" target="_blank" class="w-7 h-7 rounded-full bg-white border border-[#E8E6E0] hover:border-[#C2410C]/35 flex items-center justify-center text-[#57585F] hover:text-[#C2410C] transition duration-200" title="WhatsApp">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-3.5 h-3.5" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.977h.004c4.368 0 7.927-3.559 7.93-7.93a7.9 7.9 0 0 0-2.327-5.615zM7.994 14.52a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/></svg>
                            </a>
                            <a href="https://t.me/contactwtw_bot" target="_blank" class="w-7 h-7 rounded-full bg-white border border-[#E8E6E0] hover:border-[#C2410C]/35 flex items-center justify-center text-[#57585F] hover:text-[#C2410C] transition duration-200" title="Telegram">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5"><path d="M20.665 3.717l-17.73 6.837c-1.21.486-1.203 1.161-.222 1.462l4.552 1.42 10.532-6.645c.498-.303.953-.14.577.191l-8.536 7.705-.332 4.981c.488 0 .703-.223.976-.487l2.344-2.279 4.875 3.6c.898.495 1.543.24 1.767-.832l3.193-15.04c.328-1.314-.5-1.909-1.36-.615z"/></svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Quick Links Column -->
                    <div class="md:col-span-3 space-y-3">
                        <span class="block font-bold text-[#0E1015] uppercase tracking-wider text-[10px]">Navigasi</span>
                        <ul class="space-y-2 text-[11px] font-bold text-[#57585F]/80">
                            <li><a href="#fitur" @click="scrollToSection('#fitur', $event)" class="hover:text-[#0E1015] transition">Fitur</a></li>
                            <li><a href="#template" @click="scrollToSection('#template', $event)" class="hover:text-[#0E1015] transition">Template</a></li>
                            <li><a href="#harga" @click="scrollToSection('#harga', $event)" class="hover:text-[#0E1015] transition">Harga</a></li>
                            <li><a href="#faq" @click="scrollToSection('#faq', $event)" class="hover:text-[#0E1015] transition">FAQ</a></li>
                        </ul>
                    </div>
                    
                    <!-- Legal Column -->
                    <div class="md:col-span-4 space-y-3">
                        <span class="block font-bold text-[#0E1015] uppercase tracking-wider text-[10px]">Syarat & Kebijakan</span>
                        <ul class="space-y-2 text-[11px] font-bold text-[#57585F]/80">
                            <li><a href="#" class="hover:text-[#0E1015] transition">Ketentuan Layanan</a></li>
                            <li><a href="#" class="hover:text-[#0E1015] transition">Kebijakan Privasi</a></li>
                            <li><a href="#" class="hover:text-[#0E1015] transition">Kebijakan Lisensi</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Bottom bar -->
                <div class="pt-8 border-t border-[#E8E6E0]/60 flex flex-col sm:flex-row items-center justify-between gap-4 text-[10px] text-[#57585F]">
                    <span>&copy; 2026 WebToWeb Builder. Hak Cipta Dilindungi.</span>
                    <span class="font-mono text-[#57585F]/70">Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})</span>
                </div>
            </footer>

        </div>

        <!-- Floating Back-to-Top Button -->
        <button 
            @click="scrollToTop"
            class="fixed bottom-8 right-8 z-50 w-11 h-11 rounded-full bg-white border border-[#E8E6E0] text-[#111317] flex items-center justify-center shadow-md hover:shadow-lg transition-all duration-300 transform ease-out hover:-translate-y-1 hover:border-[#C2410C] focus:outline-none"
            :class="showScrollTop ? 'opacity-100 scale-100 translate-y-0 pointer-events-auto' : 'opacity-0 scale-90 translate-y-2 pointer-events-none'"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
            </svg>
        </button>

    </div>
</template>
