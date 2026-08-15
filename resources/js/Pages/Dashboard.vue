<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    projects: {
        type: Array,
        default: () => []
    }
});

const showCreateModal = ref(false);

const form = useForm({
    name: ''
});

const createProject = () => {
    form.post(route('projects.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            form.reset();
        }
    });
};

const deleteProject = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus proyek website ini? Seluruh draf desain Anda akan terhapus secara permanen.')) {
        router.delete(route('projects.destroy', { id }));
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Dashboard - Project Hub" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold leading-tight text-[#0e1015]">
                    Hub Proyek Website Anda
                </h2>
                <button
                    @click="showCreateModal = true"
                    class="px-4 py-2 bg-[#C2410C] hover:bg-[#C2410C]/90 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition duration-200 shadow-sm"
                >
                    + Buat Website
                </button>
            </div>
        </template>

        <div class="py-12 bg-[#FAF9F5] min-h-[calc(100vh-65px)]">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Welcome Section -->
                <div class="mb-10 px-4 sm:px-0">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0e1015] tracking-tight">
                        Selamat datang kembali, {{ $page.props.auth.user.name }}!
                    </h1>
                    <p class="text-sm text-[#57585F] mt-1">
                        Kelola draf website dan luncurkan halaman kustom Anda tanpa menulis baris kode.
                    </p>
                </div>

                <!-- Empty State -->
                <div 
                    v-if="projects.length === 0" 
                    class="bg-white border border-[#E8E6E0] rounded-[2.5rem] p-12 text-center max-w-xl mx-auto shadow-sm"
                >
                    <div class="w-16 h-16 rounded-2xl bg-[#C2410C]/5 border border-[#C2410C]/10 flex items-center justify-center text-[#C2410C] mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21a9.004 9.004 0 0 0-8.716-6.747M12 21a9.004 9.004 0 0 1 8.716-6.747M12 11c0 2.9-1.91 5.34-4.5 6.13M12 11c0-2.9 1.91-5.34 4.5-6.13M12 11h-.008v.008H12V11zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#0e1015] mb-2">Belum ada website yang dibuat</h3>
                    <p class="text-sm text-[#57585F] mb-8 leading-relaxed max-w-sm mx-auto">
                        Mulai rancang website impian Anda sekarang juga. Blok visual teroptimasi kami siap digunakan.
                    </p>
                    <button
                        @click="showCreateModal = true"
                        class="px-6 py-3 bg-[#0E1015] hover:bg-[#252830] text-white font-bold text-xs uppercase tracking-wider rounded-xl transition duration-200 shadow-md"
                    >
                        Buat Website Pertama Anda
                    </button>
                </div>

                <!-- Projects Grid -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4 sm:px-0">
                    <div 
                        v-for="project in projects" 
                        :key="project.id"
                        class="bg-white border border-[#E8E6E0] rounded-[2rem] p-6 flex flex-col justify-between hover:border-[#C2410C]/30 hover:shadow-md transition duration-300 relative group"
                    >
                        <div class="space-y-4">
                            <!-- Project Header Icon -->
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 rounded-xl bg-[#FAF9F5] border border-[#E8E6E0] flex items-center justify-center text-[#C2410C] group-hover:bg-[#C2410C]/5 transition duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747" />
                                    </svg>
                                </div>
                                <span 
                                    class="text-[9px] uppercase font-bold tracking-widest px-2.5 py-1 rounded-md border"
                                    :class="project.is_published 
                                        ? 'bg-emerald-50 border-emerald-200 text-emerald-600' 
                                        : 'bg-amber-50 border-amber-200 text-amber-600'"
                                >
                                    {{ project.is_published ? 'Published' : 'Draft' }}
                                </span>
                            </div>

                            <!-- Name & Slug Info -->
                            <div>
                                <h4 class="text-base font-bold text-[#0e1015] truncate group-hover:text-[#C2410C] transition duration-200">
                                    {{ project.name }}
                                </h4>
                                <p class="text-xs text-[#57585F] mt-1 font-mono select-all">
                                    /builder/{{ project.slug }}
                                </p>
                            </div>
                        </div>

                        <!-- Card Footer actions -->
                        <div class="pt-6 mt-6 border-t border-[#FAF9F5] flex items-center justify-between">
                            <span class="text-[10px] text-[#8C8D94] font-medium">
                                Diperbarui: {{ formatDate(project.updated_at) }}
                            </span>
                            
                            <div class="flex items-center gap-2">
                                <!-- Delete Button -->
                                <button 
                                    @click="deleteProject(project.id)"
                                    class="p-2 text-[#8C8D94] hover:text-red-600 rounded-lg hover:bg-red-50 transition duration-200"
                                    title="Hapus Website"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.34 6.6m-2.77 0-.34-6.6M9.25 10.5l.34 6.6m.36-10.74L15.01 4.5m-5.02 0L5.99 4.5m10.02 0V17c0 1.1-.9 2-2 2h-7a2 2 0 0 1-2-2V4.5m14.02 0h-14.02" />
                                    </svg>
                                </button>
                                
                                <!-- Edit Button -->
                                <a 
                                    :href="route('builder.workspace', { project_slug: project.slug })"
                                    class="px-3.5 py-1.5 bg-[#0e1015] hover:bg-[#252830] text-white font-bold text-xs uppercase tracking-wider rounded-lg transition duration-200"
                                >
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Create Project Dialog Modal -->
        <div 
            v-if="showCreateModal" 
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        >
            <!-- Backdrop -->
            <div 
                @click="showCreateModal = false"
                class="fixed inset-0 bg-[#0e1015]/30 backdrop-blur-xs transition-opacity"
            ></div>

            <!-- Modal Panel -->
            <div class="relative bg-white border border-[#E8E6E0] rounded-[2.5rem] p-8 max-w-md w-full shadow-lg z-10 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-[#0e1015] tracking-tight">Buat Proyek Website Baru</h3>
                    <p class="text-xs text-[#57585F] mt-1">Masukkan nama proyek untuk membuat ruang kerja visual baru.</p>
                </div>

                <form @submit.prevent="createProject" class="space-y-4">
                    <div class="space-y-2">
                        <label for="project-name" class="text-[10px] text-[#57585F] font-bold uppercase tracking-wider block">
                            Nama Website
                        </label>
                        <input 
                            id="project-name"
                            v-model="form.name"
                            type="text" 
                            placeholder="Contoh: Portofolio Kreatif"
                            required
                            class="w-full px-4 py-3 bg-[#FAF9F5] border border-[#E8E6E0] rounded-xl text-sm focus:outline-none focus:border-[#C2410C]/50 transition duration-200"
                            autocomplete="off"
                        >
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button 
                            type="button"
                            @click="showCreateModal = false"
                            class="px-4 py-2 text-[#57585F] hover:bg-[#FAF9F5] rounded-xl font-bold text-xs uppercase tracking-wider transition duration-200"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="px-5 py-2.5 bg-[#C2410C] hover:bg-[#C2410C]/90 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition duration-200 shadow-sm disabled:opacity-50"
                        >
                            {{ form.processing ? 'Membuat...' : 'Mulai Rancang' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
