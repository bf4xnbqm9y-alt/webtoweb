<script setup>
import { ref } from 'vue';
import axios from 'axios';

const name = ref('');
const email = ref('');
const feedbackType = ref('saran'); // 'kritik', 'saran', 'pertanyaan'
const message = ref('');
const isSubmitted = ref(false);
const isLoading = ref(false);

const submitFeedback = (e) => {
    e.preventDefault();
    if (!name.value || !email.value || !message.value) {
        alert('Mohon isi semua bidang formulir.');
        return;
    }
    
    isLoading.value = true;
    
    axios.post('/feedback', {
        name: name.value,
        email: email.value,
        type: feedbackType.value,
        message: message.value
    })
    .then(response => {
        isLoading.value = false;
        isSubmitted.value = true;
        
        // Reset form fields
        name.value = '';
        email.value = '';
        feedbackType.value = 'saran';
        message.value = '';
    })
    .catch(error => {
        isLoading.value = false;
        const msg = error.response?.data?.message || 'Terjadi kesalahan saat mengirim masukan. Silakan coba lagi.';
        alert(msg);
    });
};
</script>

<template>
    <!-- Contact & Feedback Section -->
    <section id="kontak" class="py-12 border-t border-[#E8E6E0]/60 space-y-16">
        <div class="grid lg:grid-cols-12 gap-12 items-start max-w-5xl mx-auto">
            
            <!-- Left Info Panel (Editorial Text) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#C2410C]/5 border border-[#C2410C]/20 text-[#C2410C] text-[10px] font-bold uppercase tracking-wider">
                    Kritik & Saran Pelanggan
                </div>
                
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#0E1015] leading-tight">
                    Suara Anda Adalah Kompas Kami
                </h2>
                
                <p class="text-sm text-[#57585F] leading-relaxed">
                    Bantu kami merancang arsitektur visual website yang lebih tangguh. Kami sangat terbuka menerima segala kritik tajam, saran fitur baru, atau pertanyaan teknis dari Anda.
                </p>
                
                <div class="pt-6 space-y-4 text-xs text-[#57585F]">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white border border-[#E8E6E0] flex items-center justify-center text-[#C2410C] shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0l-7.5-4.615m19.5 0v-2.43" />
                            </svg>
                        </div>
                        <div>
                            <span class="block font-bold text-[#0E1015]">Hubungi Tim Support</span>
                            <a href="mailto:aaaapiiiiss14@gmail.com" class="font-mono text-[#C2410C] hover:underline">aaaapiiiiss14@gmail.com</a>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white border border-[#E8E6E0] flex items-center justify-center text-[#C2410C] shadow-xs">
                            <!-- WhatsApp Icon SVG (Aligned Style) -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4" viewBox="0 0 16 16">
                              <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.977h.004c4.368 0 7.927-3.559 7.93-7.93a7.9 7.9 0 0 0-2.327-5.615zM7.994 14.52a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                            </svg>
                        </div>
                        <div>
                            <span class="block font-bold text-[#0E1015]">WhatsApp Support</span>
                            <a href="https://wa.me/6282381047450" target="_blank" class="font-mono text-[#C2410C] hover:underline">+62 823-8104-7450</a>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white border border-[#E8E6E0] flex items-center justify-center text-[#C2410C] shadow-xs">
                            <!-- Telegram Icon SVG (Aligned Style) -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                              <path d="M20.665 3.717l-17.73 6.837c-1.21.486-1.203 1.161-.222 1.462l4.552 1.42 10.532-6.645c.498-.303.953-.14.577.191l-8.536 7.705-.332 4.981c.488 0 .703-.223.976-.487l2.344-2.279 4.875 3.6c.898.495 1.543.24 1.767-.832l3.193-15.04c.328-1.314-.5-1.909-1.36-.615z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="block font-bold text-[#0E1015]">Telegram Support</span>
                            <a href="https://t.me/contactwtw_bot" target="_blank" class="font-mono text-[#C2410C] hover:underline">@contactwtw_bot</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Form Panel (Glassmorphic Card) -->
            <div class="lg:col-span-7 bg-white border border-[#E8E6E0] rounded-[2.5rem] p-8 sm:p-10 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#C2410C]/2 rounded-full filter blur-2xl"></div>
                
                <!-- Success State Display -->
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 translate-y-4"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="isSubmitted" class="space-y-6 py-8 text-center">
                        <div class="w-16 h-16 bg-[#C2410C]/5 border border-[#C2410C]/20 rounded-full flex items-center justify-center mx-auto text-[#C2410C]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-lg font-bold text-[#0E1015]">Pesan Berhasil Terkirim</h3>
                            <p class="text-xs text-[#57585F] leading-relaxed max-w-sm mx-auto">
                                Terima kasih banyak! Kritik, saran, atau masukan berharga Anda telah kami terima dan akan menjadi acuan penting untuk peningkatan platform ini.
                            </p>
                        </div>
                        <button 
                            @click="isSubmitted = false" 
                            class="px-6 py-2.5 bg-[#0E1015] hover:bg-[#252830] text-white text-[10px] uppercase tracking-wider font-bold rounded-xl transition duration-200"
                        >
                            Kirim Pesan Lain
                        </button>
                    </div>

                    <!-- Input Form State -->
                    <form v-else @submit="submitFeedback" class="space-y-6">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <!-- Name input -->
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-[#57585F] block">Nama Lengkap</label>
                                <input 
                                    v-model="name"
                                    type="text" 
                                    required
                                    placeholder="Masukkan nama Anda"
                                    class="w-full bg-[#FAF9F5]/50 border border-[#E8E6E0] rounded-xl px-4 py-3 text-xs text-[#111317] placeholder-[#A3A29E] focus:outline-none focus:border-[#C2410C]/40 focus:bg-white focus:ring-1 focus:ring-[#C2410C]/20 transition"
                                />
                            </div>

                            <!-- Email input -->
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-[#57585F] block">Alamat Email</label>
                                <input 
                                    v-model="email"
                                    type="email" 
                                    required
                                    placeholder="nama@email.com"
                                    class="w-full bg-[#FAF9F5]/50 border border-[#E8E6E0] rounded-xl px-4 py-3 text-xs text-[#111317] placeholder-[#A3A29E] focus:outline-none focus:border-[#C2410C]/40 focus:bg-white focus:ring-1 focus:ring-[#C2410C]/20 transition"
                                />
                            </div>
                        </div>

                        <!-- Message Type Select (Pills style) -->
                        <div class="space-y-2.5">
                            <label class="text-[9px] font-black uppercase tracking-widest text-[#57585F] block">Tipe Masukan</label>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="type in ['saran', 'kritik', 'pertanyaan']"
                                    :key="type"
                                    type="button"
                                    @click="feedbackType = type"
                                    class="px-4 py-2 border text-[10px] font-bold rounded-xl uppercase tracking-wider transition-all duration-200"
                                    :class="feedbackType === type 
                                        ? 'bg-[#C2410C] border-transparent text-white shadow-xs' 
                                        : 'bg-[#FAF9F5]/60 border-[#E8E6E0] text-[#57585F] hover:bg-white hover:text-[#111317]'"
                                >
                                    {{ type }}
                                </button>
                            </div>
                        </div>

                        <!-- Feedback input -->
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-[#57585F] block">Isi Pesan / Masukan</label>
                            <textarea 
                                v-model="message"
                                rows="4" 
                                required
                                placeholder="Tulis kritik konstruktif, saran fitur baru, atau pertanyaan Anda di sini..."
                                class="w-full bg-[#FAF9F5]/50 border border-[#E8E6E0] rounded-xl px-4 py-3 text-xs text-[#111317] placeholder-[#A3A29E] focus:outline-none focus:border-[#C2410C]/40 focus:bg-white focus:ring-1 focus:ring-[#C2410C]/20 transition resize-none leading-relaxed"
                            ></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            :disabled="isLoading"
                            class="w-full py-4 bg-[#0E1015] hover:bg-[#252830] text-white text-xs font-bold uppercase tracking-wider rounded-xl transition duration-200 shadow-sm hover:shadow flex items-center justify-center gap-2 disabled:opacity-70"
                        >
                            <!-- Spinner while sending -->
                            <svg v-if="isLoading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ isLoading ? 'Mengirim Masukan...' : 'Kirim Kritik & Saran' }}</span>
                        </button>
                    </form>
                </Transition>
            </div>
        </div>
    </section>
</template>
