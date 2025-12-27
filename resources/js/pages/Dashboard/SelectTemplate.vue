<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ArrowRight, Check, Eye, Sparkles, Star, X, Menu, LayoutTemplate } from 'lucide-vue-next';
import { ref } from 'vue';
import DashboardLayout from '@/layouts/DashboardLayout.vue';

// 1. IMPORTACIONES CORREGIDAS
import CreativaPreview from '@/components/Templates/Creativa.vue';
import EjecutivaPreview from '@/components/Templates/Ejecutiva.vue';
import MinimalistaPreview from '@/components/Templates/Minimalista.vue';
import ModernaPreview from '@/components/Templates/Moderna.vue';
import TecnologicaPreview from '@/components/Templates/Tecnologica.vue';

// Portfolio routes
import portfolio from '@/routes/dashboard/portfolio';

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

// Plantilla seleccionada
const selectedTemplate = ref<string | null>(null);
const isCreating = ref(false);

// Modal de vista previa
const previewModal = ref({
    open: false,
    template: null as any,
});

// Map de componentes de preview
const previewComponents: Record<string, any> = {
    Moderna: ModernaPreview,
    Minimalista: MinimalistaPreview,
    Ejecutiva: EjecutivaPreview,
    Creativa: CreativaPreview,
    Tecnologica: TecnologicaPreview,
};

// Datos de ejemplo para preview
const previewData = {
    personal: {
        name: 'Juan Pérez',
        title: 'Desarrollador Full Stack',
        email: 'juan@ejemplo.com',
        phone: '+51 999 888 777',
        location: 'Lima, Perú',
        linkedin: 'https://linkedin.com/in/juanperez',
        github: 'https://github.com/juanperez',
        summary:
            'Desarrollador apasionado con 5+ años de experiencia en crear soluciones web innovadoras y escalables.',
    },
    experience: [
        {
            company: 'Tech Solutions SAC',
            position: 'Senior Developer',
            startDate: '2020-01',
            endDate: '',
            current: true,
            description:
                'Liderando equipo de desarrollo de aplicaciones empresariales con Laravel y Vue.js.',
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
            'Laravel',
            'Vue.js',
            'PHP',
            'JavaScript',
            'MySQL',
            'Tailwind CSS',
        ],
        soft: [
            'Liderazgo',
            'Trabajo en equipo',
            'Comunicación',
            'Resolución de problemas',
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

// Helper para adaptar los datos según la plantilla
const getTemplateData = (componentName: string) => {
    // Clonamos los datos base
    const data = JSON.parse(JSON.stringify(previewData));
    
    // Estandarizamos skills para que siempre sean objetos { name: string }
    // Así todas las plantillas funcionan igual que Moderna
    if (data.skills.technical?.length && typeof data.skills.technical[0] === 'string') {
        data.skills.technical = data.skills.technical.map((s: string) => ({ name: s, level: 85 }));
    }
    
    if (data.skills.soft?.length && typeof data.skills.soft[0] === 'string') {
        data.skills.soft = data.skills.soft.map((s: string) => ({ name: s, level: 90 }));
    }

    return data;
};

const selectTemplate = (templateId: string) => {
    selectedTemplate.value = templateId;
};

const openPreview = (template: any) => {
    previewModal.value = {
        open: true,
        template,
    };
};

const closePreview = () => {
    previewModal.value.open = false;
};

const createPortfolio = () => {
    if (!selectedTemplate.value) return;

    isCreating.value = true;

    router.post(
        portfolio.create().url,
        {
            template_type: selectedTemplate.value,
        },
        {
            onSuccess: () => {
                // Backend redirect
            },
            onError: () => {
                isCreating.value = false;
                console.log('Error al crear el portafolio');
            },
        },
    );
};
</script>

<template>
    <DashboardLayout v-slot="{ toggleSidebar }">
    <div class="min-h-screen bg-[#F8FAFC]">
        <!-- Header -->
        <header class="sticky top-0 z-40 border-b border-gray-200 bg-white/80 backdrop-blur-md">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center space-x-3">
                        <button @click="toggleSidebar" class="rounded-lg p-2 hover:bg-gray-100 lg:hidden mr-2">
                            <Menu class="h-5 w-5 text-gray-600" />
                        </button>
                        <div class="flex items-center gap-2">
                            <div class="bg-indigo-600 p-1.5 rounded-lg">
                                <LayoutTemplate class="h-5 w-5 text-white" />
                            </div>
                            <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600">
                                PortafolioAI
                            </span>
                        </div>
                    </div>
                    <!-- Steps -->
                    <div class="hidden items-center gap-3 text-sm font-medium sm:flex">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-600 text-white text-xs">1</span>
                        <span class="text-gray-900">Seleccionar Plantilla</span>
                        <div class="w-8 h-px bg-gray-300"></div>
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-200 text-gray-500 text-xs">2</span>
                        <span class="text-gray-500">Personalizar</span>
                    </div>
                </div>
            </div>
        </header>

        <main class="container mx-auto px-4 py-12 sm:px-6 lg:px-8 max-w-[1400px]">
            <!-- Hero Text -->
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h1 class="text-4xl font-bold text-gray-900 tracking-tight sm:text-5xl mb-4">
                    Elige tu identidad digital
                </h1>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Selecciona el diseño que mejor se adapte a tu perfil. 
                    Todas las plantillas son <span class="text-indigo-600 font-semibold">100% responsivas</span> y optimizadas.
                </p>
            </div>

            <!-- Templates Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                <div
                    v-for="template in templates"
                    :key="template.id"
                    class="group relative flex flex-col bg-white rounded-3xl transition-all duration-300 hover:-translate-y-1 hover:shadow-xl border"
                    :class="selectedTemplate === template.id ? 'border-2 border-indigo-600 ring-4 ring-indigo-50 shadow-lg' : 'border-gray-200 shadow-sm'"
                >
                    <!-- Card Header -->
                    <div class="p-6 pb-4">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xl font-bold text-gray-900">{{ template.name }}</h3>
                                    <span v-if="template.popular" class="flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-bold uppercase tracking-wide">
                                        <Star class="w-3 h-3 fill-current" /> Popular
                                    </span>
                                </div>
                                <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">{{ template.category }}</span>
                            </div>
                            <!-- Category Badge styled specifically -->
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-600 border border-gray-200 shadow-sm">
                                {{ template.category }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed line-clamp-2 h-10">{{ template.description }}</p>
                    </div>

                    <!-- Real Component Preview -->
                    <div class="relative w-full aspect-[16/10] bg-gray-100 overflow-hidden border-y border-gray-100 group-hover:border-gray-200 transition-colors"
                         @click="openPreview(template)">
                        
                        <!-- Scaled Template Component -->
                        <div class="absolute inset-0 w-[215%] h-[215%] origin-top-left transform scale-[0.5] pointer-events-none select-none bg-white">
                             <component
                                :is="previewComponents[template.preview_component]"
                                :data="getTemplateData(template.preview_component)"
                            />
                        </div>

                        <!-- Hover Actions Overlay -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center backdrop-blur-[2px] cursor-pointer z-10">
                            <button @click.stop="openPreview(template)" class="bg-white text-gray-900 px-6 py-2.5 rounded-full font-bold text-sm transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 shadow-lg flex items-center gap-2 hover:bg-gray-50">
                                <Eye class="w-4 h-4" /> Vista Previa
                            </button>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="p-6 flex flex-col gap-4 mt-auto">
                        <!-- Tags -->
                        <div class="flex flex-wrap gap-2">
                            <span v-for="feature in template.features.slice(0, 3)" :key="feature" 
                                  class="text-[11px] font-medium px-2.5 py-1 rounded bg-gray-100 text-gray-600 border border-gray-200">
                                {{ feature }}
                            </span>
                        </div>

                        <!-- Select Button -->
                        <button @click="selectTemplate(template.id)"
                                class="w-full py-3 rounded-xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 group/btn"
                                :class="selectedTemplate === template.id ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-gray-50 text-gray-900 hover:bg-indigo-50 hover:text-indigo-700 border border-gray-200 hover:border-indigo-200'">
                            <span v-if="selectedTemplate === template.id">Seleccionada</span>
                            <span v-else>Usar esta plantilla</span>
                            <component :is="selectedTemplate === template.id ? Check : ArrowRight" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Sticky Action Bar if Template Selected -->
            <transition enter-active-class="transform transition duration-300 ease-out"
                        enter-from-class="translate-y-full opacity-0"
                        enter-to-class="translate-y-0 opacity-100"
                        leave-active-class="transform transition duration-200 ease-in"
                        leave-from-class="translate-y-0 opacity-100"
                        leave-to-class="translate-y-full opacity-0">
                <div v-if="selectedTemplate" class="fixed bottom-0 left-0 right-0 p-6 bg-white/90 backdrop-blur-lg border-t border-gray-200 z-50 shadow-[0_-8px_30px_rgba(0,0,0,0.1)]">
                    <div class="container mx-auto max-w-4xl flex items-center justify-between gap-4">
                        <div class="hidden md:flex flex-col">
                            <span class="text-sm font-medium text-gray-500">Plantilla seleccionada</span>
                            <span class="font-bold text-gray-900 text-lg">{{ templates.find(t => t.id === selectedTemplate)?.name }}</span>
                        </div>
                        <button @click="createPortfolio"
                                :disabled="isCreating"
                                class="w-full md:w-auto flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-xl font-bold text-base shadow-xl shadow-indigo-600/20 transition-all hover:scale-[1.02] active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed">
                            <Sparkles v-if="!isCreating" class="w-5 h-5" />
                            <span v-if="isCreating">Creando tu portafolio...</span>
                            <span v-else>Continuar con la plantilla seleccionada</span>
                            <ArrowRight v-if="!isCreating" class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </transition>
        </main>

        <!-- Preview Modal (Keep same structure but cleaner styles) -->
        <div v-if="previewModal.open" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" @click="closePreview">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
            
            <!-- Modal Content -->
            <div class="relative w-full max-w-7xl h-full max-h-[90vh] bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col transform transition-all" @click.stop>
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-white z-10">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ previewModal.template?.name }}</h2>
                        <p class="text-sm text-gray-500">{{ previewModal.template?.category }} preview</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button @click="closePreview" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <X class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <!-- Preview Iframe/Container -->
                <div class="flex-1 overflow-auto bg-gray-50 p-4 md:p-8 custom-scrollbar">
                     <div class="mx-auto max-w-screen-xl bg-white shadow-2xl rounded-xl overflow-hidden min-h-[500px] border border-gray-200">
                        <component
                            v-if="previewModal.template"
                            :is="previewComponents[previewModal.template.preview_component]"
                            :data="getTemplateData(previewModal.template.preview_component)"
                        />
                     </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-white z-10 flex justify-end gap-3">
                    <button @click="closePreview" class="px-5 py-2.5 font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button @click="selectTemplate(previewModal.template?.id); closePreview();" 
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold shadow-lg shadow-indigo-600/20 transition-all">
                        Elegir esta plantilla
                    </button>
                </div>
            </div>
        </div>
    </div>
    </DashboardLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
