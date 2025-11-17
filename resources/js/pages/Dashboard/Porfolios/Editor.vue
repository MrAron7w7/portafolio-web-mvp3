<script setup lang="ts">
import {
    ArrowLeft,
    ArrowRight,
    Briefcase,
    Check,
    ChevronRight,
    Eye,
    FileText,
    Github,
    Instagram,
    Link,
    Linkedin,
    Mail,
    MapPin,
    Palette,
    Phone,
    Settings,
    Star,
    Upload,
    User,
    Zap,
} from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';

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

// Datos del formulario
const formData = reactive({
    personal: {
        firstName: 'María',
        lastName: 'González',
        title: 'Diseñadora UX/UI & Desarrolladora Frontend',
        city: 'Madrid',
        country: 'España',
        phone: '+34 612 345 678',
        email: 'maria.gonzalez@email.com',
        photo: null as string | null,
    },
    about: {
        summary:
            'Creo experiencias digitales excepcionales que combinan diseño hermoso con funcionalidad impecable. Especializada en diseño de interfaces y desarrollo frontend moderno.',
        description: '',
    },
    experience: [
        {
            id: 1,
            company: 'TechCorp',
            position: 'Senior UX Designer',
            period: '2022 - Presente',
            description:
                'Lideré el rediseño de la plataforma principal que resultó en un aumento del 40% en la retención de usuarios.',
        },
    ],
    skills: [
        { id: 1, name: 'Figma', level: 90, category: 'Design' },
        { id: 2, name: 'Vue.js', level: 85, category: 'Development' },
        { id: 3, name: 'UI/UX Design', level: 95, category: 'Design' },
    ],
    social: {
        linkedin: 'maria-gonzalez',
        github: 'mariagonzalez',
        instagram: 'maria.design',
    },
});

// Estado avanzado
const advancedOptions = ref(false);

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
    // Solo permitir navegación a pasos completados o el actual
    const targetStep = steps.value.find((step) => step.id === stepId);
    if (targetStep && (targetStep.completed || stepId === currentStep.value)) {
        currentStep.value = stepId;
    }
};

// Funciones de formulario
const handlePhotoUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        // Simular upload de imagen
        const reader = new FileReader();
        reader.onload = (e) => {
            formData.personal.photo = e.target?.result as string;
        };
        reader.readAsDataURL(input.files[0]);
    }
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
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#005aeb]"
                        >
                            <span class="text-sm font-bold text-white">P</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900"
                            >PortafolioAI</span
                        >
                    </div>

                    <!-- Progreso general -->
                    <div class="hidden items-center space-x-4 md:flex">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-[#005aeb]"
                                >{{ progress }}%</span
                            >
                            completado
                        </div>
                        <div class="h-2 w-32 rounded-full bg-gray-200">
                            <div
                                class="h-2 rounded-full bg-[#005aeb] transition-all duration-500"
                                :style="{ width: progress + '%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center space-x-3">
                        <button
                            class="hidden items-center space-x-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-50 sm:flex"
                        >
                            <Eye class="h-4 w-4" />
                            <span>Vista previa</span>
                        </button>
                        <button
                            class="flex items-center space-x-2 rounded-lg bg-[#005aeb] px-4 py-2 text-white transition-colors duration-200 hover:bg-[#0048c4]"
                        >
                            <span>Guardar y continuar</span>
                            <ChevronRight class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido principal -->
        <div class="container mx-auto px-6 py-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <!-- Columna Izquierda - Stepper -->
                <div class="lg:col-span-3">
                    <div
                        class="sticky top-8 rounded-2xl border border-gray-200/60 bg-white p-6 shadow-xs"
                    >
                        <!-- Lista de pasos -->
                        <nav class="space-y-2">
                            <div
                                v-for="(step, index) in steps"
                                :key="step.id"
                                class="group flex cursor-pointer items-start space-x-4"
                                @click="goToStep(step.id)"
                            >
                                <!-- Línea vertical -->
                                <div class="flex flex-col items-center">
                                    <div
                                        :class="[
                                            'flex h-8 w-8 items-center justify-center rounded-full border-2 text-sm font-semibold transition-all duration-200',
                                            step.completed
                                                ? 'border-[#005aeb] bg-[#005aeb] text-white'
                                                : currentStep === step.id
                                                  ? 'border-[#005aeb] bg-white text-[#005aeb]'
                                                  : 'border-gray-300 bg-white text-gray-400',
                                        ]"
                                    >
                                        <Check
                                            v-if="step.completed"
                                            class="h-4 w-4"
                                        />
                                        <component
                                            :is="step.icon"
                                            v-else-if="currentStep === step.id"
                                            class="h-4 w-4"
                                        />
                                        <span v-else>{{ step.id }}</span>
                                    </div>
                                    <!-- Línea conectora -->
                                    <div
                                        v-if="index < steps.length - 1"
                                        :class="[
                                            'mt-2 h-8 w-0.5 transition-colors duration-200',
                                            steps[index + 1].completed
                                                ? 'bg-[#005aeb]'
                                                : 'bg-gray-200',
                                        ]"
                                    ></div>
                                </div>

                                <!-- Texto del paso -->
                                <div class="min-w-0 flex-1 pb-6">
                                    <p
                                        :class="[
                                            'font-medium transition-colors duration-200',
                                            currentStep === step.id
                                                ? 'text-[#005aeb]'
                                                : step.completed
                                                  ? 'text-gray-900'
                                                  : 'text-gray-500',
                                        ]"
                                    >
                                        {{ step.title }}
                                    </p>
                                    <p
                                        v-if="currentStep === step.id"
                                        class="mt-1 text-sm text-[#005aeb]"
                                    >
                                        Paso actual
                                    </p>
                                </div>
                            </div>
                        </nav>

                        <!-- Progreso y enlaces -->
                        <div
                            class="mt-4 space-y-4 border-t border-gray-200 pt-6"
                        >
                            <!-- Barra de progreso -->
                            <div>
                                <div
                                    class="mb-2 flex justify-between text-sm text-gray-600"
                                >
                                    <span>Progreso total</span>
                                    <span class="font-medium text-[#005aeb]"
                                        >{{ progress }}%</span
                                    >
                                </div>
                                <div
                                    class="h-2 w-full rounded-full bg-gray-200"
                                >
                                    <div
                                        class="h-2 rounded-full bg-[#005aeb] transition-all duration-500"
                                        :style="{ width: progress + '%' }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Enlaces legales -->
                            <div class="space-y-2 text-xs text-gray-500">
                                <div class="flex space-x-4">
                                    <a
                                        href="#"
                                        class="transition-colors duration-200 hover:text-gray-700"
                                        >Condiciones</a
                                    >
                                    <a
                                        href="#"
                                        class="transition-colors duration-200 hover:text-gray-700"
                                        >Privacidad</a
                                    >
                                    <a
                                        href="#"
                                        class="transition-colors duration-200 hover:text-gray-700"
                                        >Contacto</a
                                    >
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
                        <!-- Paso 1: Información Personal -->
                        <div v-if="currentStep === 1">
                            <div class="mb-8">
                                <h1
                                    class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl"
                                >
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
                                        class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-[#005aeb] to-[#7B2FF7] text-xl font-semibold text-white"
                                    >
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
                                        <img
                                            v-else
                                            :src="formData.personal.photo"
                                            class="h-20 w-20 rounded-full object-cover"
                                        />
                                    </div>
                                    <label
                                        class="absolute -right-2 -bottom-2 cursor-pointer rounded-full border border-gray-300 bg-white p-1.5 shadow-sm transition-colors duration-200 hover:bg-gray-50"
                                    >
                                        <Upload class="h-4 w-4 text-gray-600" />
                                        <input
                                            type="file"
                                            class="hidden"
                                            accept="image/*"
                                            @change="handlePhotoUpload"
                                        />
                                    </label>
                                </div>
                                <div>
                                    <h3
                                        class="mb-1 font-semibold text-gray-900"
                                    >
                                        Foto de perfil
                                    </h3>
                                    <p class="mb-3 text-sm text-gray-600">
                                        Recomendado: 400x400 px, formato JPG o
                                        PNG
                                    </p>
                                    <button
                                        class="text-sm font-medium text-[#005aeb] hover:text-[#0048c4]"
                                    >
                                        Eliminar foto
                                    </button>
                                </div>
                            </div>

                            <!-- Formulario -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Nombre *
                                    </label>
                                    <input
                                        v-model="formData.personal.firstName"
                                        type="text"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="Tu nombre"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Apellido *
                                    </label>
                                    <input
                                        v-model="formData.personal.lastName"
                                        type="text"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="Tu apellido"
                                    />
                                </div>

                                <div class="md:col-span-2">
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Título profesional *
                                    </label>
                                    <input
                                        v-model="formData.personal.title"
                                        type="text"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="Ej: Diseñador UX/UI & Desarrollador Frontend"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Ciudad
                                    </label>
                                    <input
                                        v-model="formData.personal.city"
                                        type="text"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="Tu ciudad"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        País
                                    </label>
                                    <input
                                        v-model="formData.personal.country"
                                        type="text"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="Tu país"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Teléfono
                                    </label>
                                    <input
                                        v-model="formData.personal.phone"
                                        type="tel"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="+34 612 345 678"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Email *
                                    </label>
                                    <input
                                        v-model="formData.personal.email"
                                        type="email"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                                        placeholder="tu@email.com"
                                    />
                                </div>
                            </div>

                            <!-- Opciones avanzadas -->
                            <div class="mt-8 border-t border-gray-200 pt-6">
                                <div
                                    class="mb-4 flex items-center justify-between"
                                >
                                    <div>
                                        <h4 class="font-semibold text-gray-900">
                                            Opciones avanzadas
                                        </h4>
                                        <p class="text-sm text-gray-600">
                                            Configuraciones adicionales para tu
                                            portafolio
                                        </p>
                                    </div>
                                    <button
                                        @click="
                                            advancedOptions = !advancedOptions
                                        "
                                        :class="[
                                            'relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200',
                                            advancedOptions
                                                ? 'bg-[#005aeb]'
                                                : 'bg-gray-200',
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                'inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200',
                                                advancedOptions
                                                    ? 'translate-x-6'
                                                    : 'translate-x-1',
                                            ]"
                                        />
                                    </button>
                                </div>

                                <div
                                    v-if="advancedOptions"
                                    class="grid grid-cols-1 gap-4 rounded-lg bg-gray-50 p-4 md:grid-cols-2"
                                >
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium text-gray-700"
                                            >Idioma principal</label
                                        >
                                        <select
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#005aeb]"
                                        >
                                            <option>Español</option>
                                            <option>English</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium text-gray-700"
                                            >Zona horaria</label
                                        >
                                        <select
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#005aeb]"
                                        >
                                            <option>Europe/Madrid</option>
                                            <option>UTC</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navegación entre pasos -->
                        <div
                            class="mt-8 flex items-center justify-between border-t border-gray-200 pt-8"
                        >
                            <button
                                @click="prevStep"
                                class="flex items-center space-x-2 rounded-lg border border-gray-300 px-6 py-3 text-gray-700 transition-colors duration-200 hover:bg-gray-50"
                                :disabled="currentStep === 1"
                            >
                                <ArrowLeft class="h-4 w-4" />
                                <span>Anterior</span>
                            </button>

                            <button
                                @click="nextStep"
                                class="flex items-center space-x-2 rounded-lg bg-[#005aeb] px-6 py-3 text-white transition-colors duration-200 hover:bg-[#0048c4]"
                            >
                                <span>Siguiente paso</span>
                                <ArrowRight class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Vista Previa -->
                <div class="lg:col-span-4">
                    <div
                        class="sticky top-8 rounded-2xl border border-gray-200/60 bg-white p-6 shadow-xs"
                    >
                        <!-- Header de la vista previa -->
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">
                                Vista previa
                            </h3>
                            <div class="flex items-center space-x-2">
                                <button
                                    class="p-2 text-gray-400 transition-colors duration-200 hover:text-gray-600"
                                >
                                    <Palette class="h-4 w-4" />
                                </button>
                                <button
                                    class="flex items-center space-x-2 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 transition-colors duration-200 hover:bg-gray-50"
                                >
                                    <Eye class="h-4 w-4" />
                                    <span>Vista previa</span>
                                </button>
                            </div>
                        </div>

                        <!-- Contenedor del preview -->
                        <div
                            class="overflow-hidden rounded-xl border-2 border-gray-200 bg-white"
                        >
                            <!-- Mini navegador -->
                            <div
                                class="flex items-center space-x-2 border-b border-gray-200 bg-gray-100 px-4 py-2"
                            >
                                <div class="flex space-x-1">
                                    <div
                                        class="h-3 w-3 rounded-full bg-red-400"
                                    ></div>
                                    <div
                                        class="h-3 w-3 rounded-full bg-yellow-400"
                                    ></div>
                                    <div
                                        class="h-3 w-3 rounded-full bg-green-400"
                                    ></div>
                                </div>
                                <div
                                    class="flex-1 truncate rounded bg-white px-3 py-1 text-xs text-gray-500"
                                >
                                    maria-gonzalez.portafolioai.com
                                </div>
                            </div>

                            <!-- Contenido del preview -->
                            <div class="p-6">
                                <!-- Hero Section -->
                                <div class="text-center">
                                    <div
                                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-[#005aeb] to-[#7B2FF7] text-lg font-semibold text-white"
                                    >
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
                                        <img
                                            v-else
                                            :src="formData.personal.photo"
                                            class="h-16 w-16 rounded-full object-cover"
                                        />
                                    </div>
                                    <h2
                                        class="mb-2 text-xl font-bold text-gray-900"
                                    >
                                        {{ formData.personal.firstName }}
                                        {{ formData.personal.lastName }}
                                    </h2>
                                    <p
                                        class="mb-4 text-sm font-medium text-[#005aeb]"
                                    >
                                        {{ formData.personal.title }}
                                    </p>

                                    <!-- Información de contacto -->
                                    <div
                                        class="mb-6 grid grid-cols-1 gap-2 text-xs text-gray-600"
                                    >
                                        <div
                                            class="flex items-center justify-center space-x-2"
                                        >
                                            <MapPin class="h-3 w-3" />
                                            <span
                                                >{{ formData.personal.city }},
                                                {{
                                                    formData.personal.country
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="flex items-center justify-center space-x-2"
                                        >
                                            <Phone class="h-3 w-3" />
                                            <span>{{
                                                formData.personal.phone
                                            }}</span>
                                        </div>
                                        <div
                                            class="flex items-center justify-center space-x-2"
                                        >
                                            <Mail class="h-3 w-3" />
                                            <span>{{
                                                formData.personal.email
                                            }}</span>
                                        </div>
                                    </div>

                                    <!-- Redes sociales -->
                                    <div class="flex justify-center space-x-3">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100"
                                        >
                                            <Linkedin
                                                class="h-3 w-3 text-gray-600"
                                            />
                                        </div>
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100"
                                        >
                                            <Github
                                                class="h-3 w-3 text-gray-600"
                                            />
                                        </div>
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100"
                                        >
                                            <Instagram
                                                class="h-3 w-3 text-gray-600"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección de experiencia (mini preview) -->
                                <div class="mt-6 border-t border-gray-100 pt-6">
                                    <h3
                                        class="mb-3 text-sm font-semibold text-gray-900"
                                    >
                                        Experiencia
                                    </h3>
                                    <div class="space-y-3">
                                        <div
                                            v-for="exp in formData.experience.slice(
                                                0,
                                                2,
                                            )"
                                            :key="exp.id"
                                            class="text-xs"
                                        >
                                            <p
                                                class="font-medium text-gray-900"
                                            >
                                                {{ exp.position }}
                                            </p>
                                            <p class="text-[#005aeb]">
                                                {{ exp.company }}
                                            </p>
                                            <p class="text-gray-500">
                                                {{ exp.period }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de la plantilla -->
                        <div
                            class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4"
                        >
                            <div class="flex items-center space-x-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#005aeb]"
                                >
                                    <span class="text-xs font-bold text-white"
                                        >M</span
                                    >
                                </div>
                                <div class="flex-1">
                                    <p
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        Plantilla "Moderna"
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        Totalmente responsive y personalizable
                                    </p>
                                </div>
                                <button
                                    class="text-xs font-medium text-[#005aeb] hover:text-[#0048c4]"
                                >
                                    Cambiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
    ring: 2px;
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

/* Mejora en el toggle switch */
.toggle-checkbox:checked {
    right: 0;
    border-color: #005aeb;
}

.toggle-checkbox:checked + .toggle-label {
    background-color: #005aeb;
}
</style>
