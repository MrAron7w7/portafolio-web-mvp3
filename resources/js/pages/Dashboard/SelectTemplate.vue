<script setup lang="ts">
/**
 * Vista de selección de plantillas.
 * Filosofía de Diseño: "Claridad Vibrante" - Vitrina de Productos de Lujo
 * - Tipografía de Impacto (font-black, tracking-tighter)
 * - Visualización Bento Style con mockups
 * - Barra de Acción Flotante
 * - Feedback de Selección con ring luminoso
 */
import { router } from '@inertiajs/vue3';
import { ArrowRight, Check, Eye, Sparkles, Star, X, Menu, LayoutTemplate, ArrowLeft } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

// Template component imports
import AcademicaPreview from '@/components/Templates/Academica.vue';
import CreativaPreview from '@/components/Templates/Creativa.vue';
import EjecutivaPreview from '@/components/Templates/Ejecutiva.vue';
import MinimalistaPreview from '@/components/Templates/Minimalista.vue';
import ModernaPreview from '@/components/Templates/Moderna.vue';
import TecnologicaPreview from '@/components/Templates/Tecnologica.vue';

// Portfolio routes
// import portfolio from '@/routes/dashboard/portfolio';

const props = defineProps<{
    templates: Array<{
        id: string;
        name: string;
        description: string;
        category: string;
        color: string;
        popular: boolean;
        features: string[];
        preview_component: string;
    }>;
}>();

// Selected template state
const selectedTemplate = ref<string | null>(null);
const isCreating = ref(false);

// Preview modal state
const previewModal = ref({
    open: false,
    template: null as any,
});

// Component map
const previewComponents: Record<string, any> = {
    Academica: AcademicaPreview,
    Moderna: ModernaPreview,
    Minimalista: MinimalistaPreview,
    Ejecutiva: EjecutivaPreview,
    Creativa: CreativaPreview,
    Tecnologica: TecnologicaPreview,
};

// Preview data
const previewData = {
    personal: {
        name: 'Juan Pérez',
        title: 'Desarrollador Full Stack',
        email: 'juan@ejemplo.com',
        phone: '+51 999 888 777',
        location: 'Lima, Perú',
        linkedin: 'https://linkedin.com/in/juanperez',
        github: 'https://github.com/juanperez',
        summary: 'Desarrollador apasionado con 5+ años de experiencia en crear soluciones web innovadoras y escalables.',
    },
    experience: [
        {
            company: 'Tech Solutions SAC',
            position: 'Senior Developer',
            startDate: '2020-01',
            endDate: '',
            current: true,
            description: 'Liderando equipo de desarrollo de aplicaciones empresariales con Laravel y Vue.js.',
        },
        {
            company: 'Startup Innovadora',
            position: 'Full Stack Developer',
            startDate: '2018-06',
            endDate: '2019-12',
            current: false,
            description: 'Desarrollo de MVP y features para plataforma SaaS.',
        },
    ],
    education: [
        {
            institution: 'Universidad Nacional',
            degree: 'Ingeniería de Sistemas',
            field: 'Ciencias de la Computación',
            startDate: '2014-03',
            endDate: '2018-12',
        },
    ],
    skills: {
        technical: [
            { name: 'Laravel', level: 90 },
            { name: 'Vue.js', level: 85 },
            { name: 'PHP', level: 88 },
            { name: 'JavaScript', level: 85 },
            { name: 'MySQL', level: 80 },
            { name: 'Tailwind CSS', level: 82 },
        ],
        soft: [
            { name: 'Liderazgo', level: 90 },
            { name: 'Trabajo en equipo', level: 95 },
            { name: 'Comunicación', level: 88 },
            { name: 'Resolución de problemas', level: 92 },
        ],
    },
    projects: [
        {
            name: 'Sistema de Gestión',
            description: 'Plataforma completa para gestión empresarial',
            technologies: ['Laravel', 'Vue.js', 'MySQL'],
        },
    ],
    certifications: [],
    languages: [
        { name: 'Español', level: 'Nativo' },
        { name: 'Inglés', level: 'Avanzado' },
    ],
};

// Get template data
const getTemplateData = (componentName: string) => {
    return JSON.parse(JSON.stringify(previewData));
};

// Actions
const selectTemplate = (templateId: string) => {
    selectedTemplate.value = templateId;
};

const openPreview = (template: any) => {
    previewModal.value = {
        open: true,
        template,
    };
    document.body.classList.add('overflow-hidden');
};

const closePreview = () => {
    previewModal.value.open = false;
    document.body.classList.remove('overflow-hidden');
};

const createPortfolio = () => {
    if (!selectedTemplate.value) return;
    isCreating.value = true;

    router.post(
        '/dashboard/portfolio/crear',
        { template_type: selectedTemplate.value },
        {
            onSuccess: () => {},
            onError: () => {
                isCreating.value = false;
                console.log('Error al crear el portafolio');
            },
        },
    );
};

const goBack = () => {
    router.visit('/dashboard');
};

// Computed
const selectedTemplateData = computed(() => {
    return props.templates.find(t => t.id === selectedTemplate.value);
});
</script>

<template>
    <DashboardLayout v-slot="{ toggleSidebar }">
        <div class="min-h-screen bg-slate-50">
            <!-- Header Glassmorphism -->
            <header class="sticky top-0 z-40 border-b border-slate-200/60 bg-white/80 backdrop-blur-md">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between py-4">
                        <!-- Left side -->
                        <div class="flex items-center gap-4">
                            <button 
                                @click="toggleSidebar" 
                                class="rounded-2xl p-2.5 text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-all lg:hidden"
                            >
                                <Menu class="h-5 w-5" />
                            </button>
                            
                            <!-- Back button -->
                            <button 
                                @click="goBack"
                                class="hidden sm:flex items-center gap-2 rounded-2xl px-4 py-2 text-slate-600 hover:bg-slate-100 transition-all"
                            >
                                <ArrowLeft class="h-4 w-4" />
                                <span class="text-sm font-medium">Volver</span>
                            </button>

                            <!-- Logo -->
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-indigo-600 to-violet-600 shadow-lg shadow-indigo-500/25">
                                    <LayoutTemplate class="h-5 w-5 text-white" />
                                </div>
                                <span class="text-xl font-black tracking-tight bg-linear-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent">
                                    PortafolioAI
                                </span>
                            </div>
                        </div>

                        <!-- Steps Indicator -->
                        <div class="hidden items-center gap-4 sm:flex">
                            <div class="flex items-center gap-2">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 to-violet-600 text-xs font-bold text-white shadow-lg shadow-indigo-500/25">
                                    1
                                </span>
                                <span class="text-sm font-semibold text-slate-900">Plantilla</span>
                            </div>
                            <div class="h-px w-8 bg-slate-300"></div>
                            <div class="flex items-center gap-2 opacity-50">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-xs font-medium text-slate-500">
                                    2
                                </span>
                                <span class="text-sm text-slate-500">Personalizar</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 pb-32">
                <!-- Hero Section -->
                <div class="mb-16 text-center">
                    <h1 class="mb-4 text-4xl font-black tracking-tighter text-slate-900 sm:text-5xl lg:text-6xl">
                        Elige tu identidad
                        <span class="bg-linear-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent"> digital</span>
                    </h1>
                    <p class="mx-auto max-w-2xl text-lg text-slate-500 leading-relaxed">
                        Selecciona el diseño perfecto para tu perfil. Todas las plantillas son 
                        <span class="font-semibold text-indigo-600">100% responsivas</span> y optimizadas para impresionar.
                    </p>
                </div>

                <!-- Templates Grid - Minimalist -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <div
                        v-for="template in templates"
                        :key="template.id"
                        @click="selectTemplate(template.id)"
                        class="group relative cursor-pointer overflow-hidden rounded-2xl border bg-white transition-all duration-200 hover:-translate-y-1"
                        :class="[
                            selectedTemplate === template.id 
                                ? 'border-indigo-500 ring-4 ring-indigo-100 shadow-lg' 
                                : 'border-slate-200 shadow-sm hover:shadow-md hover:border-slate-300'
                        ]"
                    >
                        <!-- Preview Image -->
                        <div 
                            class="relative aspect-[16/10] overflow-hidden bg-slate-50"
                            @click.stop="openPreview(template)"
                        >
                            <div class="absolute inset-0 h-[220%] w-[220%] origin-top-left scale-[0.45] pointer-events-none">
                                <component
                                    v-if="previewComponents[template.preview_component]"
                                    :is="previewComponents[template.preview_component]"
                                    :data="getTemplateData(template.preview_component)"
                                />
                                <div v-else class="flex h-full w-full items-center justify-center bg-slate-100">
                                    <LayoutTemplate class="h-16 w-16 text-slate-300" />
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer - Clean -->
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-semibold text-slate-900">{{ template.name }}</h3>
                                    <span 
                                        v-if="template.popular" 
                                        class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700"
                                    >
                                        Popular
                                    </span>
                                </div>
                                <!-- Selection Check -->
                                <div 
                                    class="flex h-5 w-5 items-center justify-center rounded-full border transition-all"
                                    :class="[
                                        selectedTemplate === template.id 
                                            ? 'border-indigo-500 bg-indigo-500' 
                                            : 'border-slate-300'
                                    ]"
                                >
                                    <Check 
                                        v-if="selectedTemplate === template.id" 
                                        class="h-3 w-3 text-white" 
                                    />
                                </div>
                            </div>

                            <!-- Select Button -->
                            <button 
                                @click.stop="selectTemplate(template.id)"
                                class="w-full rounded-xl py-2.5 text-sm font-medium transition-all"
                                :class="[
                                    selectedTemplate === template.id 
                                        ? 'bg-indigo-600 text-white' 
                                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                ]"
                            >
                                {{ selectedTemplate === template.id ? 'Seleccionada' : 'Seleccionar' }}
                            </button>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Floating Action Bar -->
            <Transition
                enter-active-class="transform transition duration-300 ease-out"
                enter-from-class="translate-y-full opacity-0"
                enter-to-class="translate-y-0 opacity-100"
                leave-active-class="transform transition duration-200 ease-in"
                leave-from-class="translate-y-0 opacity-100"
                leave-to-class="translate-y-full opacity-0"
            >
                <div 
                    v-if="selectedTemplate" 
                    class="fixed bottom-0 left-0 right-0 z-[999] border-t border-slate-200/60 bg-white/90 p-4 backdrop-blur-lg shadow-[0_-10px_40px_rgba(0,0,0,0.1)] sm:p-6"
                >
                    <div class="mx-auto flex max-w-4xl items-center justify-between gap-4">
                        <!-- Selected Template Info -->
                        <div class="hidden flex-col md:flex">
                            <span class="text-xs font-medium uppercase tracking-wider text-slate-400">
                                Plantilla seleccionada
                            </span>
                            <span class="text-lg font-bold text-slate-900">
                                {{ selectedTemplateData?.name }}
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex w-full items-center gap-3 md:w-auto">
                            <button 
                                @click="selectedTemplate = null"
                                class="rounded-2xl px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-all hidden sm:block"
                            >
                                Cancelar
                            </button>
                            <button 
                            <button 
                                @click="createPortfolio"
                                :disabled="isCreating"
                                class="w-full flex items-center justify-center gap-2 rounded-xl bg-linear-to-r from-indigo-600 to-violet-600 py-4 font-bold text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/30 hover:-translate-y-0.5 disabled:opacity-70 disabled:cursor-not-allowed"
                            >
                                <!-- Shine effect -->
                                <div class="absolute inset-0 bg-linear-to-r from-transparent via-white/25 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                                <Sparkles v-if="!isCreating" class="h-5 w-5" />
                                <div v-if="isCreating" class="h-5 w-5 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                                <span v-if="isCreating" class="relative z-10">Creando...</span>
                                <span v-else class="relative z-10">Continuar</span>
                                <ArrowRight v-if="!isCreating" class="h-5 w-5 transition-transform duration-300 group-hover:translate-x-1" />
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>

            <!-- Preview Modal -->
            <Teleport to="body">
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div 
                        v-if="previewModal.open" 
                        class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6"
                        @click="closePreview"
                    >
                        <!-- Backdrop -->
                        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closePreview"></div>
                        
                        <!-- Modal Content -->
                        <div 
                            class="relative z-10 flex h-full max-h-[90vh] w-full max-w-7xl flex-col overflow-hidden rounded-[28px] bg-white shadow-2xl"
                            @click.stop
                        >
                            <!-- Modal Header -->
                            <div class="flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
                                <div>
                                    <h2 class="text-xl font-bold text-slate-900">{{ previewModal.template?.name }}</h2>
                                    <p class="text-sm text-slate-500">{{ previewModal.template?.category }}</p>
                                </div>
                                <button 
                                    @click="closePreview" 
                                    class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <X class="h-5 w-5" />
                                </button>
                            </div>

                            <!-- Preview Container -->
                            <div class="flex-1 overflow-auto bg-slate-100 p-4 md:p-8 custom-scrollbar">
                                <div class="relative w-full overflow-hidden rounded-t-[28px] md:scale-100 md:rounded-[28px] aspect-16/10 bg-white shadow-2xl">
                                    <component
                                        v-if="previewModal.template"
                                        :is="previewComponents[previewModal.template.preview_component]"
                                        :data="getTemplateData(previewModal.template.preview_component)"
                                    />
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="flex justify-end gap-3 border-t border-slate-200 bg-white px-6 py-4">
                                <button 
                                    @click="closePreview" 
                                    class="rounded-2xl px-5 py-2.5 font-medium text-slate-600 transition-all hover:bg-slate-100"
                                >
                                    Cerrar
                                </button>
                                <button 
                                    @click="selectTemplate(previewModal.template?.id); closePreview();"
                                    class="rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-2.5 font-bold text-white shadow-lg shadow-indigo-500/25 transition-all hover:shadow-xl hover:-translate-y-0.5"
                                >
                                    Elegir plantilla
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </Teleport>
        </div>
    </DashboardLayout>
</template>

<style scoped>
/* Line clamp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}
</style>
