<script setup lang="ts">
import Academica from '@/components/Templates/Academica.vue';
import Creativa from '@/components/Templates/Creativa.vue';
import Ejecutiva from '@/components/Templates/Ejecutiva.vue';
import Minimalista from '@/components/Templates/Minimalista.vue';
import Moderna from '@/components/Templates/Moderna.vue';
import Tecnologica from '@/components/Templates/Tecnologica.vue';
import { router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    Briefcase,
    Check,
    Eye,
    FileText,
    Link,
    Palette,
    Save,
    Settings,
    Star,
    User,
    Zap,
} from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';
import AboutSection from './Components/AboutSection.vue';
import ConfigSection from './Components/ConfigSection.vue';
import ExperienceSection from './Components/ExperienceSection.vue';
import PersonalSection from './Components/PersonalSection.vue';
import PreviewContainer from './Components/PreviewContainer.vue';
import ProjectsSection from './Components/ProjectsSection.vue';
import SkillsSection from './Components/SkillsSection.vue';
import SocialSection from './Components/SocialSection.vue';

// Props del portfolio
const props = defineProps<{
    portfolio: any;
    templateData: any;
    sections: any[];

}>();

// Estado del wizard
const currentStep = ref(1);
const steps = ref([
    { id: 1, title: 'Información personal', completed: true, icon: User },
    { id: 2, title: 'Sobre mí', completed: false, icon: FileText },
    { id: 3, title: 'Experiencia', completed: false, icon: Briefcase },
    { id: 4, title: 'Habilidades', completed: false, icon: Star },
    { id: 5, title: 'Proyectos', completed: false, icon: Zap },
    { id: 6, title: 'Redes sociales', completed: false, icon: Link },
    { id: 7, title: 'Configuración', completed: false, icon: Settings },
]);

const templateComponents = {
    academica: Academica,
    creativa: Creativa,
    ejecutiva: Ejecutiva,
    minimalista: Minimalista,
    moderna: Moderna,
    tecnologica: Tecnologica,
};

// Componente actual basado en la plantilla seleccionada
const currentTemplate = computed(() => {
    return templateComponents[props.portfolio.template_type as keyof typeof templateComponents] || Moderna;
});

// Usar los datos del portfolio en lugar de datos estáticos
const formData = reactive({
    personal: {
        firstName: props.templateData.personal.name.split(' ')[0] || '',
        lastName:
            props.templateData.personal.name.split(' ').slice(1).join(' ') ||
            '',
        title: props.templateData.personal.title || '',
        city: props.templateData.personal.location
            ? props.templateData.personal.location.split(',')[0]
            : '',
        country: props.templateData.personal.location
            ? props.templateData.personal.location.split(',')[1]
            : '',
        phone: props.templateData.personal.phone || '',
        email: props.templateData.personal.email || '',
        photo: null as string | null,
        summary: props.templateData.personal.summary || '',
        website: props.templateData.personal.website || '',
        linkedin: props.templateData.personal.linkedin || '',
        github: props.templateData.personal.github || '',
    },
    about: {
        summary: props.templateData.personal.summary || '',
        description: props.templateData.about?.description || '',
    },
    experience:
        props.templateData.experience?.map((exp: any, index: number) => ({
            id: index + 1,
            company: exp.company || '',
            position: exp.position || '',
            period: `${exp.startDate} - ${exp.current ? 'Presente' : exp.endDate}`,
            description: exp.description || '',
            startDate: exp.startDate || '',
            endDate: exp.endDate || '',
            current: exp.current || false,
        })) || [],
    skills: {
        technical:
            props.templateData.skills?.technical?.map(
                (skill: string, index: number) => ({
                    id: index + 1,
                    name: skill,
                    level: 80,
                    category: 'Technical',
                }),
            ) || [],
        soft:
            props.templateData.skills?.soft?.map(
                (skill: string, index: number) => ({
                    id: index + 1000,
                    name: skill,
                    level: 85,
                    category: 'Soft',
                }),
            ) || [],
    },
    social: {
        linkedin: props.templateData.personal.linkedin || '',
        github: props.templateData.personal.github || '',
        website: props.templateData.personal.website || '',
    },
    projects: props.templateData.projects || [],
    education: props.templateData.education || [],
    config: {
        theme: 'moderna',
        public: props.portfolio.is_public || false,
    }
});

// Estado avanzado
const isSaving = ref(false);

// Progreso calculado
const progress = computed(() => {
    const completedSteps = steps.value.filter((step) => step.completed).length;
    return Math.round((completedSteps / steps.value.length) * 100);
});

// Funciones de navegación
const nextStep = () => {
    if (currentStep.value < steps.value.length) {
        steps.value[currentStep.value - 1].completed = true;
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const goToStep = (stepId: number) => {
    const targetStep = steps.value.find((step) => step.id === stepId);
    if (targetStep && (targetStep.completed || stepId === currentStep.value)) {
        currentStep.value = stepId;
    }
};

// Guardar cambios
const saveChanges = () => {
    isSaving.value = true;

    // Preparar datos para enviar al backend
    const templateData = {
        personal: {
            name: `${formData.personal.firstName} ${formData.personal.lastName}`.trim(),
            title: formData.personal.title,
            email: formData.personal.email,
            phone: formData.personal.phone,
            location: `${formData.personal.city}, ${formData.personal.country}`
                .trim()
                .replace(/^,\s*|\s*,$/g, ''),
            website: formData.social.website || formData.personal.website,
            linkedin: formData.social.linkedin || formData.personal.linkedin,
            github: formData.social.github || formData.personal.github,
            summary: formData.about.summary || formData.personal.summary,
        },
        about: {
            description: formData.about.description,
        },
        experience: formData.experience.map((exp: any) => ({
            company: exp.company,
            position: exp.position,
            startDate: exp.startDate,
            endDate: exp.endDate,
            current: exp.current,
            description: exp.description,
        })),
        skills: {
            technical: formData.skills.technical.map((skill: any) => skill.name),
            soft: formData.skills.soft.map((skill: any) => skill.name),
        },
        projects: formData.projects,
        education: formData.education,
        config: formData.config,
    };

    router.put(
        `/portfolio/${props.portfolio.id}`,
        {
            template_data: templateData,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                isSaving.value = false;
            },
            onError: () => {
                isSaving.value = false;
            },
        },
    );
};

// Vista previa completa
const showFullPreview = ref(false);

const openFullPreview = () => {
    showFullPreview.value = true;
};

const closeFullPreview = () => {
    showFullPreview.value = false;
};
</script>

<template>
    <div class="min-h-screen bg-gray-50/50">
        <!-- Header -->
        <header class="border-b border-gray-200/60 bg-white">
            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#005aeb]">
                            <span class="text-sm font-bold text-white">P</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900">PortafolioAI</span>
                    </div>

                    <!-- Progreso general -->
                    <div class="hidden items-center space-x-4 md:flex">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-[#005aeb]">{{ progress }}%</span>
                            completado
                        </div>
                        <div class="h-2 w-32 rounded-full bg-gray-200">
                            <div class="h-2 rounded-full bg-[#005aeb] transition-all duration-500"
                                :style="{ width: progress + '%' }"></div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center space-x-3">
                        <button @click="openFullPreview"
                            class="hidden items-center space-x-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-50 sm:flex">
                            <Eye class="h-4 w-4" />
                            <span>Vista previa completa</span>
                        </button>
                        <button @click="saveChanges" :disabled="isSaving"
                            class="flex items-center space-x-2 rounded-lg bg-[#005aeb] px-4 py-2 text-white transition-colors duration-200 hover:bg-[#0048c4] disabled:opacity-50">
                            <Save class="h-4 w-4" />
                            <span>{{
                                isSaving ? 'Guardando...' : 'Guardar'
                            }}</span>
                        </button>
                        <a :href="`/portfolio/${portfolio.id}/download-pdf`" target="_blank"
                            class="flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition shadow-sm text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                <path d="M12 18v-6" />
                                <path d="m9 15 3 3 3-3" />
                            </svg>

                            Exportar PDF
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido principal -->
        <div class="container mx-auto px-6 py-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <!-- Columna Izquierda - Stepper -->
                <div class="lg:col-span-3">
                    <div class="sticky top-8 rounded-2xl border border-gray-200/60 bg-white p-6 shadow-xs">
                        <!-- Lista de pasos -->
                        <nav class="space-y-2">
                            <div v-for="(step, index) in steps" :key="step.id"
                                class="group flex cursor-pointer items-start space-x-4" @click="goToStep(step.id)">
                                <!-- Línea vertical -->
                                <div class="flex flex-col items-center">
                                    <div :class="[
                                        'flex h-8 w-8 items-center justify-center rounded-full border-2 text-sm font-semibold transition-all duration-200',
                                        step.completed
                                            ? 'border-[#005aeb] bg-[#005aeb] text-white'
                                            : currentStep === step.id
                                                ? 'border-[#005aeb] bg-white text-[#005aeb]'
                                                : 'border-gray-300 bg-white text-gray-400',
                                    ]">
                                        <Check v-if="step.completed" class="h-4 w-4" />
                                        <component :is="step.icon" v-else-if="currentStep === step.id"
                                            class="h-4 w-4" />
                                        <span v-else>{{ step.id }}</span>
                                    </div>
                                    <!-- Línea conectora -->
                                    <div v-if="index < steps.length - 1" :class="[
                                        'mt-2 h-8 w-0.5 transition-colors duration-200',
                                        steps[index + 1].completed
                                            ? 'bg-[#005aeb]'
                                            : 'bg-gray-200',
                                    ]"></div>
                                </div>

                                <!-- Texto del paso -->
                                <div class="min-w-0 flex-1 pb-6">
                                    <p :class="[
                                        'font-medium transition-colors duration-200',
                                        currentStep === step.id
                                            ? 'text-[#005aeb]'
                                            : step.completed
                                                ? 'text-gray-900'
                                                : 'text-gray-500',
                                    ]">
                                        {{ step.title }}
                                    </p>
                                    <p v-if="currentStep === step.id" class="mt-1 text-sm text-[#005aeb]">
                                        Paso actual
                                    </p>
                                </div>
                            </div>
                        </nav>

                        <!-- Progreso y enlaces -->
                        <div class="mt-4 space-y-4 border-t border-gray-200 pt-6">
                            <!-- Barra de progreso -->
                            <div>
                                <div class="mb-2 flex justify-between text-sm text-gray-600">
                                    <span>Progreso total</span>
                                    <span class="font-medium text-[#005aeb]">{{ progress }}%</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-gray-200">
                                    <div class="h-2 rounded-full bg-[#005aeb] transition-all duration-500"
                                        :style="{ width: progress + '%' }"></div>
                                </div>
                            </div>

                            <!-- Información del portfolio -->
                            <div class="rounded-lg bg-gray-50 p-4">
                                <p class="text-sm font-medium text-gray-900">
                                    Plantilla: {{ portfolio.template_type }}
                                </p>
                                <p class="mt-1 text-xs text-gray-600">
                                    ID: {{ portfolio.id }}
                                </p>
                            </div>

                            <!-- Enlaces legales -->
                            <div class="space-y-2 text-xs text-gray-500">
                                <div class="flex space-x-4">
                                    <a href="#"
                                        class="transition-colors duration-200 hover:text-gray-700">Condiciones</a>
                                    <a href="#"
                                        class="transition-colors duration-200 hover:text-gray-700">Privacidad</a>
                                    <a href="#" class="transition-colors duration-200 hover:text-gray-700">Contacto</a>
                                </div>
                                <p class="text-gray-400">
                                    &copy; 2024 PortafolioAI. Todos los derechos
                                    reservados.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Central - Formulario -->
                <div class="lg:col-span-5">
                    <div
                        class="rounded-2xl border border-gray-200/60 bg-white p-8 shadow-xs"
                    >
                        <!-- Componentes Dinámicos -->
                        <PersonalSection
                            v-if="currentStep === 1"
                            v-model="formData.personal"
                        />
                        <AboutSection
                            v-if="currentStep === 2"
                            v-model="formData.about"
                        />
                        <ExperienceSection
                            v-if="currentStep === 3"
                            v-model="formData.experience"
                        />
                        <SkillsSection
                            v-if="currentStep === 4"
                            v-model="formData.skills"
                        />
                        <ProjectsSection
                            v-if="currentStep === 5"
                            v-model="formData.projects"
                        />
                        <SocialSection
                            v-if="currentStep === 6"
                            v-model="formData.social"
                        />
                        <ConfigSection
                            v-if="currentStep === 7"
                            v-model="formData.config"
                        />

                        <!-- Navegación entre pasos -->
                        <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-8">
                            <button @click="prevStep"
                                class="flex items-center space-x-2 rounded-lg border border-gray-300 px-6 py-3 text-gray-700 transition-colors duration-200 hover:bg-gray-50"
                                :disabled="currentStep === 1">
                                <ArrowLeft class="h-4 w-4" />
                                <span>Anterior</span>
                            </button>

                            <button
                                @click="nextStep"
                                class="flex items-center space-x-2 rounded-lg bg-[#005aeb] px-6 py-3 text-white transition-colors duration-200 hover:bg-[#0048c4]"
                            >
                                <span>{{ currentStep === 7 ? 'Finalizar' : 'Siguiente paso' }}</span>
                                <ArrowRight class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Vista Previa -->
                <div class="lg:col-span-4">
                    <div
                        class="sticky top-8 h-[calc(100vh-8rem)] overflow-hidden rounded-2xl border border-gray-200/60 bg-white shadow-xs"
                    >
                        <PreviewContainer>
                            <component
                                :is="currentTemplate"
                                :data="{
                                    personal: {
                                        name: `${formData.personal.firstName} ${formData.personal.lastName}`.trim(),
                                        title: formData.personal.title,
                                        email: formData.personal.email,
                                        phone: formData.personal.phone,
                                        location:
                                            `${formData.personal.city}, ${formData.personal.country}`
                                                .trim()
                                                .replace(
                                                    /^,\s*|\s*,$/g,
                                                    '',
                                                ),
                                        website: formData.social.website || formData.personal.website,
                                        linkedin:
                                            formData.social.linkedin || formData.personal.linkedin,
                                        github: formData.social.github || formData.personal.github,
                                        summary: formData.about.summary || formData.personal.summary,
                                    },
                                    experience: formData.experience.map(
                                        (exp: any) => ({
                                            company: exp.company,
                                            position: exp.position,
                                            startDate: exp.startDate,
                                            endDate: exp.endDate,
                                            current: exp.current,
                                            description: exp.description,
                                        }),
                                    ),
                                    skills: {
                                        technical:
                                            formData.skills.technical.map(
                                                (skill: any) => skill.name,
                                            ),
                                        soft: formData.skills.soft.map(
                                            (skill: any) => skill.name,
                                        ),
                                    },
                                    projects: formData.projects,
                                    education: formData.education,
                                    certifications: [],
                                    languages: [],
                                }"
                            />
                        </PreviewContainer>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de vista previa completa -->
    <div v-if="showFullPreview"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        @click="closeFullPreview">
        <div class="max-h-[90vh] w-full max-w-6xl transform overflow-hidden rounded-2xl bg-white transition-all duration-300"
            @click.stop>
            <!-- Header del modal -->
            <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 p-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">
                        Vista Previa: {{ portfolio.template_type }}
                    </h3>
                    <p class="mt-1 text-gray-600">
                        Vista previa completa de tu portafolio
                    </p>
                </div>
                <button @click="closeFullPreview"
                    class="rounded-lg p-2 transition-colors duration-200 hover:bg-gray-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Contenido del modal -->
            <div class="max-h-[80vh] overflow-auto">
                <component
                    :is="currentTemplate"
                    :data="{
                        personal: {
                            name: `${formData.personal.firstName} ${formData.personal.lastName}`.trim(),
                            title: formData.personal.title,
                            email: formData.personal.email,
                            phone: formData.personal.phone,
                            location:
                                `${formData.personal.city}, ${formData.personal.country}`
                                    .trim()
                                    .replace(/^,\s*|\s*,$/g, ''),
                            website: formData.social.website || formData.personal.website,
                            linkedin: formData.social.linkedin || formData.personal.linkedin,
                            github: formData.social.github || formData.personal.github,
                            summary: formData.about.summary || formData.personal.summary,
                        },
                        experience: formData.experience.map((exp: any) => ({
                            company: exp.company,
                            position: exp.position,
                            startDate: exp.startDate,
                            endDate: exp.endDate,
                            current: exp.current,
                            description: exp.description,
                        })),
                        skills: {
                            technical: formData.skills.technical.map(
                                (skill: any) => skill.name,
                            ),
                            soft: formData.skills.soft.map(
                                (skill: any) => skill.name,
                            ),
                        },
                        projects: formData.projects,
                        education: formData.education,
                        certifications: [],
                        languages: [],
                    }"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Estilos personalizados para mejor UX */
.sticky {
    position: sticky;
}

/* Mejoras en los inputs */
input:focus,
select:focus {
    outline: none;
    /* ring: 2px; */
}

/* Transiciones suaves */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Shadow muy sutiles */
.shadow-xs {
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
}
</style>
