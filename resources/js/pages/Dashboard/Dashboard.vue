<script setup lang="ts">
/**
 * Vista principal del Dashboard de usuario.
 * Filosofía de Diseño: "Claridad Vibrante" - Hi-Fi Design System
 */
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    Calendar,
    Download,
    Edit,
    Eye,
    Folder,
    Home,
    Menu,
    Plus,
    Search,
    Trash2,
    Pencil,
    Sparkles,
    LayoutGrid,
    TrendingUp,
} from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { computed, ref, nextTick } from 'vue';

// Template Previews
import AcademicaPreview from '@/components/Templates/Academica.vue';
import CreativaPreview from '@/components/Templates/Creativa.vue';
import EjecutivaPreview from '@/components/Templates/Ejecutiva.vue';
import MinimalistaPreview from '@/components/Templates/Minimalista.vue';
import ModernaPreview from '@/components/Templates/Moderna.vue';
import TecnologicaPreview from '@/components/Templates/Tecnologica.vue';

// Props que vienen del backend
const props = defineProps<{
    portfolios: Array<{
        id: number;
        title: string;
        description: string;
        template_type: string;
        template_data?: any;
        createdAt: string;
        status: string;
        statusColor: string;
        is_public: boolean;
        is_completed: boolean;
        is_published_in_community?: boolean;
    }>;
}>();

// Datos del usuario
const page = usePage();
const authUser = page.props.auth.user;

const user = computed(() => ({
    first_name: authUser.first_name,
    last_name: authUser.last_name,
    email: authUser.email,
    avatar_url: authUser.avatar_url,
    full_name: authUser.first_name + ' ' + authUser.last_name,
}));

// Búsqueda
const searchQuery = ref('');
const filteredPortfolios = computed(() => {
    if (!searchQuery.value.trim()) return props.portfolios;
    const query = searchQuery.value.toLowerCase();
    return props.portfolios.filter(p => 
        p.title.toLowerCase().includes(query) ||
        p.description?.toLowerCase().includes(query) ||
        p.template_type?.toLowerCase().includes(query)
    );
});

// Métricas del dashboard
const metrics = computed(() => [
    {
        title: 'Portafolios',
        value: props.portfolios.length.toString(),
        icon: Folder,
        gradient: 'from-indigo-500 to-violet-500',
        bgLight: 'bg-indigo-50 dark:bg-indigo-500/10',
    },
    {
        title: 'Publicados',
        value: props.portfolios.filter(p => p.is_public).length.toString(),
        icon: TrendingUp,
        gradient: 'from-emerald-500 to-teal-500',
        bgLight: 'bg-emerald-50 dark:bg-emerald-500/10',
    },
    {
        title: 'En progreso',
        value: props.portfolios.filter(p => p.status === 'borrador').length.toString(),
        icon: LayoutGrid,
        gradient: 'from-amber-500 to-orange-500',
        bgLight: 'bg-amber-50 dark:bg-amber-500/10',
    },
]);

// Portafolios del usuario (desde props)
const portfolios = computed(() => filteredPortfolios.value);

// Estado vacío - mostrar cuando no hay portafolios
const hasPortfolios = computed(() => props.portfolios.length > 0);

// Función para eliminar portafolio
const deletePortfolio = (portfolioId: number) => {
    if (confirm('¿Estás seguro de eliminar este portafolio?')) {
        router.delete(`/dashboard/portfolio/${portfolioId}`, {
            preserveScroll: true,
        });
    }
};

// Función para editar portafolio
const editPortfolio = (portfolioId: number) => {
    router.visit(`/dashboard/portfolio/${portfolioId}/editor`);
};

// Función para ver portafolio
const viewPortfolio = (portfolioId: number) => {
    router.visit(`/dashboard/portfolio/${portfolioId}/view`);
};

// Inline Editing
const editingTitleId = ref<number | null>(null);
const editingTitleValue = ref('');

const startEditingTitle = async (portfolio: any) => {
    editingTitleId.value = portfolio.id;
    editingTitleValue.value = portfolio.title;
    await nextTick();
    const input = document.getElementById(`title-input-${portfolio.id}`) as HTMLInputElement;
    if (input) {
        input.focus();
        input.select();
    }
};

const cancelEditingTitle = () => {
    editingTitleId.value = null;
    editingTitleValue.value = '';
};

const saveTitle = (portfolioId: number) => {
    if (!editingTitleValue.value.trim()) {
        cancelEditingTitle();
        return;
    }

    router.patch(`/dashboard/portfolio/${portfolioId}/title`, {
        title: editingTitleValue.value.trim(),
    }, {
        preserveScroll: true,
        onSuccess: () => {
            editingTitleId.value = null;
            editingTitleValue.value = '';
        },
    });
};

const handleTitleKeydown = (event: KeyboardEvent, portfolioId: number) => {
    if (event.key === 'Enter') {
        saveTitle(portfolioId);
    } else if (event.key === 'Escape') {
        cancelEditingTitle();
    }
};

const getTemplateInitial = (templateType: string) => {
    return templateType?.charAt(0)?.toUpperCase() || 'P';
};

// Status classes
const getStatusClasses = (status: string) => {
    switch (status) {
        case 'publicado':
            return 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20';
        case 'borrador':
            return 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700';
        default:
            return 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700';
    }
};

// Component map
const previewComponents: Record<string, any> = {
    Academica: AcademicaPreview,
    Moderna: ModernaPreview,
    Minimalista: MinimalistaPreview,
    Ejecutiva: EjecutivaPreview,
    Creativa: CreativaPreview,
    Tecnologica: TecnologicaPreview,
};

// Fallback data
const previewData = {
    personal: {
        name: 'Tu Nombre',
        title: 'Tu Título Profesional',
        email: 'tu.email@ejemplo.com',
        phone: '+00 000 000 000',
        location: 'Tu Ciudad, País',
        summary: 'Aquí aparecerá tu resumen profesional una vez que completes tu perfil.',
    },
    experience: [],
    education: [],
    skills: { technical: [], soft: [] },
    projects: [],
    certifications: [],
    languages: [],
};

const getTemplateData = (portfolio: any = null) => {
    if (portfolio && portfolio.template_data) {
        const data = portfolio.template_data;
        if (data.personal && !data.personal.name && data.personal.firstName) {
            data.personal.name = `${data.personal.firstName} ${data.personal.lastName || ''}`.trim();
        }
        return data;
    }
    const fallbackData = JSON.parse(JSON.stringify(previewData));
    if (user.value) {
        fallbackData.personal.name = user.value.full_name || 'Mi Nombre';
        fallbackData.personal.email = user.value.email || '';
        fallbackData.personal.title = 'Mi Profesión';
    }
    return fallbackData;
};

const getPreviewComponent = (type: string) => {
    if (!type) return null;
    const capitalized = type.charAt(0).toUpperCase() + type.slice(1).toLowerCase();
    return previewComponents[capitalized] || previewComponents[type] || null;
};
</script>

<template>
    <DashboardLayout v-slot="{ toggleSidebar }">
        <!-- Header Glassmorphism -->
        <header class="sticky top-0 z-30 border-b border-slate-200/60 dark:border-slate-800 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md transition-colors duration-300">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex flex-1 items-center gap-4">
                    <button @click="toggleSidebar"
                        class="rounded-2xl p-2.5 text-slate-500 dark:text-slate-400 transition-all duration-200 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden">
                        <Menu class="h-5 w-5" />
                    </button>

                    <!-- Search Bar -->
                    <div class="relative max-w-md flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <Search class="h-4 w-4 text-slate-400 dark:text-slate-500" />
                        </div>
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Buscar portafolios..."
                            class="w-full rounded-2xl border-0 bg-slate-100/80 dark:bg-slate-900 py-3 pr-4 pl-11 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 ring-1 ring-transparent transition-all duration-200 focus:bg-white dark:focus:bg-slate-950 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none" 
                        />
                    </div>
                </div>

                <!-- Go to Home Button -->
                <Link href="/">
                    <button
                        class="group relative overflow-hidden rounded-2xl bg-linear-to-r from-indigo-600 to-violet-600 px-5 py-2.5 font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2">
                        <div class="absolute inset-0 bg-linear-to-r from-transparent via-white/25 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                        <Home class="h-4 w-4" />
                        <span class="relative z-10 hidden sm:inline">Ir a Inicio</span>
                    </button>
                </Link>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8 transition-colors duration-300">
            <!-- Hero Section -->
            <div class="mb-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex-1 space-y-2">
                    <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white lg:text-4xl">
                        Hola, {{ user.first_name }} 👋
                    </h1>
                    <p class="text-lg text-slate-500 dark:text-slate-400 max-w-xl">
                        Gestiona y crea portafolios profesionales que destacan.
                    </p>
                </div>

                <Link href="/dashboard/plantillas">
                    <button
                        class="group relative overflow-hidden rounded-[20px] bg-linear-to-r from-indigo-600 to-violet-600 px-7 py-4 font-bold text-white shadow-xl shadow-indigo-500/25 transition-all duration-300 hover:shadow-2xl hover:shadow-indigo-500/30 hover:-translate-y-1 flex items-center gap-3">
                        <div class="absolute inset-0 bg-linear-to-r from-transparent via-white/30 to-transparent -translate-x-full animate-shine"></div>
                        <Sparkles class="h-5 w-5" />
                        <span class="relative z-10">Crear con IA</span>
                        <Plus class="h-5 w-5 transition-transform duration-300 group-hover:rotate-90" />
                    </button>
                </Link>
            </div>

            <!-- Metrics Cards -->
            <div class="mb-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div 
                    v-for="metric in metrics" 
                    :key="metric.title"
                    class="group relative overflow-hidden rounded-[24px] border border-slate-200/60 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm dark:shadow-none transition-all duration-300 hover:shadow-lg dark:hover:bg-slate-800/50 hover:-translate-y-1"
                >
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <p class="text-3xl font-black text-slate-900 dark:text-white">
                                {{ metric.value }}
                            </p>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                {{ metric.title }}
                            </p>
                        </div>
                        <div 
                            :class="[metric.bgLight, 'flex h-14 w-14 items-center justify-center rounded-2xl transition-transform duration-300 group-hover:scale-110']"
                        >
                            <component 
                                :is="metric.icon" 
                                class="h-6 w-6 text-indigo-600 dark:text-indigo-400"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Portfolios Section -->
            <section>
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                        Mis Portafolios
                    </h2>
                    <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-sm font-medium text-slate-600 dark:text-slate-400">
                        {{ portfolios.length }} de {{ props.portfolios.length }}
                    </span>
                </div>

                <!-- Empty State -->
                <div v-if="!hasPortfolios"
                    class="rounded-[32px] border-2 border-dashed border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 p-16 text-center">
                    <div class="mx-auto max-w-md">
                        <div class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-[28px] bg-linear-to-br from-indigo-600 to-violet-600 shadow-xl shadow-indigo-500/30 float-animation">
                            <Folder class="h-12 w-12 text-white" />
                        </div>
                        <h3 class="mb-4 text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                            Tu espacio está vacío
                        </h3>
                        <p class="mb-8 text-slate-500 dark:text-slate-400 leading-relaxed">
                            Comienza creando tu primer portafolio profesional. Nivelado por IA.
                        </p>
                        <Link href="/dashboard/plantillas">
                            <button class="inline-flex items-center gap-2 rounded-2xl bg-linear-to-r from-indigo-600 to-violet-600 px-6 py-3 font-bold text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:shadow-xl hover:-translate-y-0.5">
                                <Sparkles class="h-5 w-5" />
                                Crear mi primer portafolio
                            </button>
                        </Link>
                    </div>
                </div>

                <!-- No results -->
                <div v-else-if="portfolios.length === 0 && searchQuery.trim()"
                    class="rounded-[32px] border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-12 text-center">
                    <Search class="mx-auto mb-4 h-12 w-12 text-slate-300 dark:text-slate-700" />
                    <h3 class="mb-2 text-lg font-bold text-slate-900 dark:text-white">Sin resultados</h3>
                    <p class="text-slate-500 dark:text-slate-400">No encontramos portafolios que coincidan con "{{ searchQuery }}"</p>
                </div>

                <!-- Portfolio Grid -->
                <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <div 
                        v-for="portfolio in portfolios" 
                        :key="portfolio.id"
                        class="group relative overflow-hidden rounded-[28px] border border-slate-200/60 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm dark:shadow-none transition-all duration-300 hover:shadow-xl hover:border-slate-300 dark:hover:border-slate-700"
                    >
                        <div class="relative h-48 overflow-hidden bg-slate-100 dark:bg-slate-800">
                            <div 
                                v-if="getPreviewComponent(portfolio.template_type)" 
                                class="absolute inset-0 h-[300%] w-[250%] origin-top-left scale-[0.4] pointer-events-none"
                            >
                                <component :is="getPreviewComponent(portfolio.template_type)" :data="getTemplateData(portfolio)" />
                            </div>
                            <div v-else class="absolute inset-0 flex items-center justify-center bg-linear-to-br from-indigo-600 to-violet-600 opacity-20">
                                <span class="text-5xl font-black text-white/40">{{ getTemplateInitial(portfolio.template_type) }}</span>
                            </div>
                            
                            <!-- Status -->
                            <div class="absolute top-4 right-4 focus:ring-none">
                                <span :class="[getStatusClasses(portfolio.status), 'rounded-full border px-3 py-1.5 text-xs font-semibold capitalize backdrop-blur-sm']">
                                    {{ portfolio.status }}
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="absolute inset-0 flex items-center justify-center gap-2 bg-slate-900/60 opacity-0 backdrop-blur-sm transition-all duration-300 group-hover:opacity-100">
                                <button @click="editPortfolio(portfolio.id)" class="flex h-11 w-11 items-center justify-center rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-white transition-all hover:scale-110">
                                    <Edit class="h-4 w-4" />
                                </button>
                                <button @click="viewPortfolio(portfolio.id)" class="flex h-11 w-11 items-center justify-center rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-white transition-all hover:scale-110">
                                    <Eye class="h-4 w-4" />
                                </button>
                                <a :href="`/portfolio/${portfolio.id}/download-pdf`" target="_blank" class="flex h-11 w-11 items-center justify-center rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-white transition-all hover:scale-110">
                                    <Download class="h-4 w-4" />
                                </a>
                            </div>
                        </div>

                        <div class="p-5">
                            <div class="mb-3">
                                <div v-if="editingTitleId === portfolio.id" class="flex items-center gap-2">
                                    <input
                                        :id="`title-input-${portfolio.id}`"
                                        v-model="editingTitleValue"
                                        @keydown="handleTitleKeydown($event, portfolio.id)"
                                        @blur="saveTitle(portfolio.id)"
                                        type="text"
                                        class="flex-1 rounded-xl border-0 bg-indigo-50/50 dark:bg-indigo-500/10 px-3 py-2 text-base font-semibold text-slate-900 dark:text-white ring-2 ring-indigo-500/30 focus:outline-none"
                                    />
                                </div>
                                <div v-else class="group/title flex items-center gap-2 cursor-pointer" @click="startEditingTitle(portfolio)">
                                    <h3 class="truncate text-lg font-bold text-slate-900 dark:text-white transition-colors group-hover/title:text-indigo-600">
                                        {{ portfolio.title }}
                                    </h3>
                                    <Pencil class="h-3.5 w-3.5 text-slate-400 opacity-0 group-hover/title:opacity-100" />
                                </div>
                                <p class="mt-1 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">
                                    {{ portfolio.description }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-4">
                                <div class="flex items-center gap-1.5 text-sm text-slate-400 tracking-tighter">
                                    <Calendar class="h-4 w-4" />
                                    <span>{{ portfolio.createdAt }}</span>
                                </div>
                                <button @click="deletePortfolio(portfolio.id)" class="rounded-xl p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </DashboardLayout>
</template>

<style scoped>
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
@keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-12px); } }
.float-animation { animation: float 3s ease-in-out infinite; }
@keyframes shine { 0% { transform: translateX(-100%); } 50%, 100% { transform: translateX(100%); } }
.animate-shine { animation: shine 3s ease-in-out infinite; }
</style>
