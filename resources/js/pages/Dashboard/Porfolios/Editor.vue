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
    Plus,
    Save,
    Settings,
    Star,
    Trash2,
    Upload,
    User,
    Zap,
} from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';

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
        description: '',
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
});

// Estado avanzado
const advancedOptions = ref(false);
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

// Funciones de formulario
const handlePhotoUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            formData.personal.photo = e.target?.result as string;
        };
        reader.readAsDataURL(input.files[0]);
    }
};

// Funciones para manejar arrays
const addExperience = () => {
    formData.experience.push({
        id: Date.now(),
        company: '',
        position: '',
        period: '',
        description: '',
        startDate: '',
        endDate: '',
        current: false,
    });
};

const removeExperience = (index: number) => {
    formData.experience.splice(index, 1);
};

const addSkill = (type: 'technical' | 'soft') => {
    const newId =
        type === 'technical'
            ? formData.skills.technical.length + 1
            : formData.skills.soft.length + 1000;
    if (type === 'technical') {
        formData.skills.technical.push({
            id: newId,
            name: '',
            level: 80,
            category: 'Technical',
        });
    } else {
        formData.skills.soft.push({
            id: newId,
            name: '',
            level: 85,
            category: 'Soft',
        });
    }
};

const removeSkill = (type: 'technical' | 'soft', index: number) => {
    if (type === 'technical') {
        formData.skills.technical.splice(index, 1);
    } else {
        formData.skills.soft.splice(index, 1);
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
            website: formData.personal.website,
            linkedin: formData.personal.linkedin,
            github: formData.personal.github,
            summary: formData.personal.summary,
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
                    <div class="rounded-2xl border border-gray-200/60 bg-white p-8 shadow-xs">
                        <!-- Paso 1: Información Personal -->
                        <div v-if="currentStep === 1">
                            <div class="mb-8">
                                <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">
                                    Información Personal
                                </h1>
                                <p class="text-lg text-gray-600">
                                    Completa tu información básica para comenzar
                                    tu portafolio profesional.
                                </p>
                            </div>

                            <!-- Foto de perfil -->
                            <div class="mb-8 flex items-start space-x-6">
                                <div class="relative">
                                    <div
                                        class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-[#005aeb] to-[#7B2FF7] text-xl font-semibold text-white">
                                        <span v-if="!formData.personal.photo">
                                            {{
                                                formData.personal.firstName.charAt(
                                                    0,
                                                )
                                            }}{{
                                                formData.personal.lastName.charAt(
                                                    0,
                                                )
                                            }}
                                        </span>
                                        <img v-else :src="formData.personal.photo"
                                            class="h-20 w-20 rounded-full object-cover" />
                                    </div>
                                    <label
                                        class="absolute -right-2 -bottom-2 cursor-pointer rounded-full border border-gray-300 bg-white p-1.5 shadow-sm transition-colors duration-200 hover:bg-gray-50">
                                        <Upload class="h-4 w-4 text-gray-600" />
                                        <input type="file" class="hidden" accept="image/*"
                                            @change="handlePhotoUpload" />
                                    </label>
                                </div>
                                <div>
                                    <h3 class="mb-1 font-semibold text-gray-900">
                                        Foto de perfil
                                    </h3>
                                    <p class="mb-3 text-sm text-gray-600">
                                        Recomendado: 400x400 px, formato JPG o
                                        PNG
                                    </p>
                                    <button class="text-sm font-medium text-[#005aeb] hover:text-[#0048c4]">
                                        Eliminar foto
                                    </button>
                                </div>
                            </div>

                            <!-- Formulario -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Nombre *
                                    </label>
                                    <input v-model="formData.personal.firstName" type="text"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="Tu nombre" />
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Apellido *
                                    </label>
                                    <input v-model="formData.personal.lastName" type="text"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="Tu apellido" />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Título profesional *
                                    </label>
                                    <input v-model="formData.personal.title" type="text"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="Ej: Diseñador UX/UI & Desarrollador Frontend" />
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Ciudad
                                    </label>
                                    <input v-model="formData.personal.city" type="text"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="Tu ciudad" />
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        País
                                    </label>
                                    <input v-model="formData.personal.country" type="text"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="Tu país" />
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Teléfono
                                    </label>
                                    <input v-model="formData.personal.phone" type="tel"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="+34 612 345 678" />
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Email *
                                    </label>
                                    <input v-model="formData.personal.email" type="email"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="tu@email.com" />
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        LinkedIn
                                    </label>
                                    <input v-model="formData.personal.linkedin" type="url"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="https://linkedin.com/in/tu-usuario" />
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        GitHub
                                    </label>
                                    <input v-model="formData.personal.github" type="url"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="https://github.com/tu-usuario" />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Sitio Web
                                    </label>
                                    <input v-model="formData.personal.website" type="url"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="https://tu-sitio-web.com" />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-gray-700">
                                        Resumen profesional *
                                    </label>
                                    <textarea v-model="formData.personal.summary" rows="4"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="Breve descripción sobre tu experiencia y habilidades..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Paso 3: Experiencia -->
                        <div v-if="currentStep === 3">
                            <div class="mb-8">
                                <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">
                                    Experiencia Laboral
                                </h1>
                                <p class="text-lg text-gray-600">
                                    Agrega tu historial profesional y
                                    experiencias relevantes.
                                </p>
                            </div>

                            <div class="space-y-6">
                                <div v-for="(exp, index) in formData.experience" :key="exp.id"
                                    class="rounded-lg border border-gray-200 p-6">
                                    <div class="mb-4 flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            Experiencia {{ index + 1 }}
                                        </h3>
                                        <button @click="removeExperience(index)"
                                            class="text-red-600 hover:text-red-800">
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                                Empresa *
                                            </label>
                                            <input v-model="exp.company" type="text"
                                                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                                                placeholder="Nombre de la empresa" />
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                                Cargo *
                                            </label>
                                            <input v-model="exp.position" type="text"
                                                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                                                placeholder="Tu posición" />
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                                Fecha de inicio
                                            </label>
                                            <input v-model="exp.startDate" type="month"
                                                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]" />
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                                Fecha de fin
                                            </label>
                                            <input v-model="exp.endDate" type="month" :disabled="exp.current"
                                                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb] disabled:opacity-50" />
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="flex items-center space-x-2">
                                                <input v-model="exp.current" type="checkbox"
                                                    class="rounded border-gray-300" />
                                                <span class="text-sm text-gray-700">Trabajo actual</span>
                                            </label>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                                Descripción
                                            </label>
                                            <textarea v-model="exp.description" rows="3"
                                                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                                                placeholder="Describe tus responsabilidades y logros..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <button @click="addExperience"
                                    class="flex w-full items-center space-x-2 rounded-lg border-2 border-dashed border-gray-300 p-6 text-gray-600 transition-colors hover:border-[#005aeb] hover:text-[#005aeb]">
                                    <Plus class="h-5 w-5" />
                                    <span>Agregar nueva experiencia</span>
                                </button>
                            </div>
                        </div>

                        <!-- Navegación entre pasos -->
                        <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-8">
                            <button @click="prevStep"
                                class="flex items-center space-x-2 rounded-lg border border-gray-300 px-6 py-3 text-gray-700 transition-colors duration-200 hover:bg-gray-50"
                                :disabled="currentStep === 1">
                                <ArrowLeft class="h-4 w-4" />
                                <span>Anterior</span>
                            </button>

                            <button @click="nextStep"
                                class="flex items-center space-x-2 rounded-lg bg-[#005aeb] px-6 py-3 text-white transition-colors duration-200 hover:bg-[#0048c4]">
                                <span>Siguiente paso</span>
                                <ArrowRight class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Vista Previa -->
                <div class="lg:col-span-4">
                    <div class="sticky top-8 rounded-2xl border border-gray-200/60 bg-white p-6 shadow-xs">
                        <!-- Header de la vista previa -->
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">
                                Vista previa
                            </h3>
                            <div class="flex items-center space-x-2">
                                <button class="p-2 text-gray-400 transition-colors duration-200 hover:text-gray-600">
                                    <Palette class="h-4 w-4" />
                                </button>
                                <button
                                    class="flex items-center space-x-2 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 transition-colors duration-200 hover:bg-gray-50">
                                    <Eye class="h-4 w-4" />
                                    <span>Vista previa</span>
                                </button>
                            </div>
                        </div>

                        <!-- Contenedor del preview -->
                        <!-- Contenedor del preview -->
                        <div class="overflow-hidden rounded-xl border-2 border-gray-200 bg-white">
                            <!-- Mini navegador -->
                            <div class="flex items-center space-x-2 border-b border-gray-200 bg-gray-100 px-4 py-2">
                                <div class="flex space-x-1">
                                    <div class="h-3 w-3 rounded-full bg-red-400"></div>
                                    <div class="h-3 w-3 rounded-full bg-yellow-400"></div>
                                    <div class="h-3 w-3 rounded-full bg-green-400"></div>
                                </div>
                                <div class="flex-1 truncate rounded bg-white px-3 py-1 text-xs text-gray-500">
                                    {{ portfolio.slug }}.portafolioai.com
                                </div>
                            </div>

                            <!-- Vista previa de la plantilla seleccionada -->
                            <div class="max-h-[600px] overflow-auto">
                                <component :is="currentTemplate" :data="{
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
                                        website: formData.personal.website,
                                        linkedin:
                                            formData.personal.linkedin,
                                        github: formData.personal.github,
                                        summary: formData.personal.summary,
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
                                }" />
                            </div>
                        </div>

                        <!-- Información de la plantilla -->
                        <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#005aeb]">
                                    <span class="text-xs font-bold text-white">{{
                                        portfolio.template_type
                                            .charAt(0)
                                            .toUpperCase()
                                    }}</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        Plantilla "{{
                                            portfolio.template_type
                                        }}"
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        Totalmente responsive y personalizable
                                    </p>
                                </div>
                            </div>
                        </div>
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
                <component :is="currentTemplate" :data="{
                    personal: {
                        name: `${formData.personal.firstName} ${formData.personal.lastName}`.trim(),
                        title: formData.personal.title,
                        email: formData.personal.email,
                        phone: formData.personal.phone,
                        location:
                            `${formData.personal.city}, ${formData.personal.country}`
                                .trim()
                                .replace(/^,\s*|\s*,$/g, ''),
                        website: formData.personal.website,
                        linkedin: formData.personal.linkedin,
                        github: formData.personal.github,
                        summary: formData.personal.summary,
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
                }" />
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
