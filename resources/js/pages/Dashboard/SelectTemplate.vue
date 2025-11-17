<script setup lang="ts">
import dashboard from '@/routes/dashboard';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Check, Eye, Sparkles, Star } from 'lucide-vue-next';
import { ref } from 'vue';

// Plantillas disponibles
const templates = ref([
    {
        id: 1,
        name: 'Moderna',
        description:
            'Diseño contemporáneo con secciones dinámicas y animaciones sutiles',
        category: 'Profesional',
        image: 'M',
        color: 'from-blue-500 to-cyan-500',
        popular: true,
        features: ['Responsive', 'Animaciones', 'Secciones modulares'],
    },
    {
        id: 2,
        name: 'Minimalista',
        description: 'Enfoque limpio y elegante con máximo impacto visual',
        category: 'Diseño',
        image: 'M',
        color: 'from-gray-600 to-gray-800',
        popular: false,
        features: ['Limpio', 'Enfoque en contenido', 'Tipografía elegante'],
    },
    {
        id: 3,
        name: 'Ejecutiva',
        description:
            'Profesional y corporativo, ideal para negocios y consultoría',
        category: 'Corporativo',
        image: 'E',
        color: 'from-green-500 to-emerald-600',
        popular: true,
        features: ['Corporativo', 'Estructurado', 'Profesional'],
    },
    {
        id: 4,
        name: 'Creativa',
        description: 'Perfecta para diseñadores, artistas y perfiles creativos',
        category: 'Portafolio',
        image: 'C',
        color: 'from-purple-500 to-pink-500',
        popular: false,
        features: ['Visual', 'Interactivo', 'Portfolio focus'],
    },
    {
        id: 5,
        name: 'Tecnológica',
        description:
            'Diseño tech-forward para desarrolladores y profesionales IT',
        category: 'Tech',
        image: 'T',
        color: 'from-orange-500 to-red-500',
        popular: true,
        features: ['Code-friendly', 'Tecnológico', 'Moderno'],
    },
    {
        id: 6,
        name: 'Académica',
        description: 'Ideal para investigadores, académicos y estudiantes',
        category: 'Educación',
        image: 'A',
        color: 'from-indigo-500 to-blue-600',
        popular: false,
        features: ['Estructurado', 'Académico', 'Publicaciones'],
    },
]);

// Plantilla seleccionada
const selectedTemplate = ref<number | null>(null);

// Modal de vista previa
const previewModal = ref({
    open: false,
    template: null as any,
});

// Función para seleccionar plantilla
const selectTemplate = (templateId: number) => {
    selectedTemplate.value = templateId;
};

// Función para ver preview
const openPreview = (template: any) => {
    previewModal.value = {
        open: true,
        template,
    };
};

// Función para usar plantilla seleccionada
const useSelectedTemplate = () => {
    if (selectedTemplate.value) {
        // Navegar al editor de portafolio
        console.log(
            'Navegando al editor con plantilla:',
            selectedTemplate.value,
        );
        // Aquí iría la navegación real
    }
};

// Función para cerrar modal
const closePreview = () => {
    previewModal.value.open = false;
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100/30">
        <!-- Header -->
        <header
            class="sticky top-0 z-40 border-b border-gray-200/60 bg-white/80 backdrop-blur-sm"
        >
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-4">
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

                    <!-- Progreso -->
                    <div
                        class="hidden items-center space-x-2 text-sm text-gray-600 sm:flex"
                    >
                        <div class="h-2 w-2 rounded-full bg-[#005aeb]"></div>
                        <span class="font-medium text-[#005aeb]"
                            >Paso 1 de 3</span
                        >
                        <span>Seleccionar plantilla</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <!-- Encabezado -->
            <div class="mx-auto mb-12 max-w-3xl text-center">
                <h1
                    class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl lg:text-5xl"
                >
                    Selecciona una plantilla para tu portafolio
                </h1>
                <p class="text-lg leading-relaxed text-gray-600 sm:text-xl">
                    Elige entre diseños profesionales, modernos y totalmente
                    personalizables.
                    <span class="font-medium text-[#005aeb]"
                        >Todas las plantillas son responsive</span
                    >
                    y optimizadas para IA.
                </p>
            </div>

            <!-- Grid de plantillas -->
            <div
                class="mx-auto grid max-w-7xl grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3"
            >
                <div
                    v-for="template in templates"
                    :key="template.id"
                    class="group relative"
                >
                    <!-- Tarjeta de plantilla -->
                    <div
                        :class="[
                            'transform cursor-pointer overflow-hidden rounded-2xl border-2 bg-white transition-all duration-300',
                            selectedTemplate === template.id
                                ? 'scale-[1.02] border-[#005aeb] shadow-lg shadow-[#005aeb]/20'
                                : 'border-gray-200/80 hover:border-gray-300 hover:shadow-lg',
                        ]"
                    >
                        <!-- Header de la tarjeta -->
                        <div class="border-b border-gray-100 p-4">
                            <div class="mb-2 flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <h3
                                        class="text-lg font-semibold text-gray-900"
                                    >
                                        {{ template.name }}
                                    </h3>
                                    <span
                                        v-if="template.popular"
                                        class="flex items-center space-x-1 rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-800"
                                    >
                                        <Star class="h-3 w-3" />
                                        <span>Popular</span>
                                    </span>
                                </div>
                                <span
                                    class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-500"
                                >
                                    {{ template.category }}
                                </span>
                            </div>
                            <p class="text-sm leading-relaxed text-gray-600">
                                {{ template.description }}
                            </p>
                        </div>

                        <!-- Preview de la plantilla -->
                        <div
                            class="relative h-48 cursor-pointer bg-gradient-to-br"
                            :class="template.color"
                            @click="openPreview(template)"
                        >
                            <!-- Mockup del portafolio -->
                            <div
                                class="absolute inset-4 rounded-lg border border-white/30 bg-white/20"
                            >
                                <div class="flex h-full flex-col p-4">
                                    <!-- Header del mockup -->
                                    <div
                                        class="mb-3 flex items-center justify-between"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <div
                                                class="h-3 w-3 rounded-full bg-white/40"
                                            ></div>
                                            <div
                                                class="h-2 w-8 rounded bg-white/30"
                                            ></div>
                                        </div>
                                        <div class="flex space-x-1">
                                            <div
                                                class="h-2 w-2 rounded-full bg-white/40"
                                            ></div>
                                            <div
                                                class="h-2 w-2 rounded-full bg-white/40"
                                            ></div>
                                            <div
                                                class="h-2 w-2 rounded-full bg-white/40"
                                            ></div>
                                        </div>
                                    </div>

                                    <!-- Contenido del mockup -->
                                    <div class="grid flex-1 grid-cols-3 gap-2">
                                        <div class="rounded bg-white/30"></div>
                                        <div
                                            class="col-span-2 rounded bg-white/30"
                                        ></div>
                                        <div
                                            class="col-span-3 h-4 rounded bg-white/30"
                                        ></div>
                                        <div
                                            class="col-span-2 rounded bg-white/30"
                                        ></div>
                                        <div class="rounded bg-white/30"></div>
                                    </div>

                                    <!-- Letra inicial -->
                                    <div
                                        class="absolute inset-0 flex items-center justify-center"
                                    >
                                        <span
                                            class="text-6xl font-bold text-white opacity-20"
                                        >
                                            {{ template.image }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón de vista previa -->
                            <button
                                @click.stop="openPreview(template)"
                                class="absolute top-3 right-3 rounded-lg bg-black/20 p-2 text-white opacity-0 backdrop-blur-sm transition-all duration-200 group-hover:opacity-100 hover:bg-black/30"
                            >
                                <Eye class="h-4 w-4" />
                            </button>

                            <!-- Check de selección -->
                            <div
                                v-if="selectedTemplate === template.id"
                                class="absolute top-3 left-3 flex h-6 w-6 items-center justify-center rounded-full bg-[#005aeb]"
                            >
                                <Check class="h-4 w-4 text-white" />
                            </div>
                        </div>

                        <!-- Features y acciones -->
                        <div class="p-4">
                            <!-- Features -->
                            <div class="mb-4 flex flex-wrap gap-1">
                                <span
                                    v-for="feature in template.features"
                                    :key="feature"
                                    class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-500"
                                >
                                    {{ feature }}
                                </span>
                            </div>

                            <!-- Botón de acción -->
                            <button
                                @click="selectTemplate(template.id)"
                                :class="[
                                    'flex w-full items-center justify-center space-x-2 rounded-lg px-4 py-2.5 font-semibold transition-all duration-200',
                                    selectedTemplate === template.id
                                        ? 'bg-[#005aeb] text-white shadow-sm hover:bg-[#0048c4]'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                                ]"
                            >
                                <span>{{
                                    selectedTemplate === template.id
                                        ? 'Seleccionada'
                                        : 'Usar esta plantilla'
                                }}</span>
                                <ArrowRight
                                    v-if="selectedTemplate !== template.id"
                                    class="h-4 w-4"
                                />
                                <Check v-else class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón continuar -->
            <div
                class="fixed bottom-6 left-1/2 z-30 -translate-x-1/2 transform"
            >
                <Link :href="dashboard.informcion.url()">
                    <button
                        @click="useSelectedTemplate"
                        :disabled="!selectedTemplate"
                        :class="[
                            'flex items-center space-x-3 rounded-xl px-8 py-4 text-lg font-semibold shadow-lg transition-all duration-300',
                            selectedTemplate
                                ? 'bg-[#005aeb] text-white hover:scale-105 hover:bg-[#0048c4] hover:shadow-xl'
                                : 'cursor-not-allowed bg-gray-300 text-gray-500',
                        ]"
                    >
                        <Sparkles class="h-5 w-5" />
                        <span>Continuar con la plantilla seleccionada</span>
                        <ArrowRight class="h-5 w-5" />
                    </button>
                </Link>
            </div>
        </main>

        <!-- Modal de vista previa -->
        <div
            v-if="previewModal.open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
            @click="closePreview"
        >
            <div
                class="max-h-[90vh] w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white transition-all duration-300"
                @click.stop
            >
                <!-- Header del modal -->
                <div
                    class="flex items-center justify-between border-b border-gray-200 p-6"
                >
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">
                            {{ previewModal.template?.name }}
                        </h3>
                        <p class="mt-1 text-gray-600">
                            {{ previewModal.template?.description }}
                        </p>
                    </div>
                    <button
                        @click="closePreview"
                        class="rounded-lg p-2 transition-colors duration-200 hover:bg-gray-100"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <!-- Contenido del modal -->
                <div class="overflow-auto p-6">
                    <div
                        class="relative min-h-[400px] rounded-2xl bg-gradient-to-br p-8"
                        :class="previewModal.template?.color"
                    >
                        <!-- Mockup más detallado -->
                        <div
                            class="rounded-xl border border-white/20 bg-white/10 p-6 backdrop-blur-sm"
                        >
                            <div class="mx-auto max-w-2xl">
                                <!-- Header del portafolio -->
                                <div class="mb-8 text-center">
                                    <div
                                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white/20"
                                    >
                                        <span
                                            class="text-2xl font-bold text-white"
                                            >{{
                                                previewModal.template?.image
                                            }}</span
                                        >
                                    </div>
                                    <h4
                                        class="mb-2 text-xl font-semibold text-white"
                                    >
                                        Tu Nombre
                                    </h4>
                                    <p class="text-white/80">
                                        Tu profesión o título
                                    </p>
                                </div>

                                <!-- Secciones -->
                                <div class="grid gap-4">
                                    <div
                                        class="rounded-lg border border-white/10 bg-white/10 p-4"
                                    >
                                        <div
                                            class="mb-2 h-3 w-1/3 rounded bg-white/30"
                                        ></div>
                                        <div
                                            class="mb-1 h-2 w-full rounded bg-white/20"
                                        ></div>
                                        <div
                                            class="h-2 w-2/3 rounded bg-white/20"
                                        ></div>
                                    </div>
                                    <div
                                        class="rounded-lg border border-white/10 bg-white/10 p-4"
                                    >
                                        <div
                                            class="mb-2 h-3 w-1/2 rounded bg-white/30"
                                        ></div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div
                                                class="h-16 rounded bg-white/20"
                                            ></div>
                                            <div
                                                class="h-16 rounded bg-white/20"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Características -->
                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div
                            v-for="feature in previewModal.template?.features"
                            :key="feature"
                            class="flex items-center space-x-3 rounded-lg bg-gray-50 p-3"
                        >
                            <Check class="h-4 w-4 text-green-500" />
                            <span class="text-gray-700">{{ feature }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer del modal -->
                <div
                    class="flex items-center justify-between border-t border-gray-200 p-6"
                >
                    <button
                        @click="closePreview"
                        class="rounded-lg border border-gray-300 px-6 py-2.5 text-gray-700 transition-colors duration-200 hover:bg-gray-50"
                    >
                        Cerrar vista previa
                    </button>
                    <button
                        @click="
                            selectTemplate(previewModal.template?.id);
                            closePreview();
                        "
                        class="flex items-center space-x-2 rounded-lg bg-[#005aeb] px-6 py-2.5 text-white transition-colors duration-200 hover:bg-[#0048c4]"
                    >
                        <Check class="h-4 w-4" />
                        <span>Seleccionar esta plantilla</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.container {
    max-width: 1200px;
}

/* Scroll suave */
html {
    scroll-behavior: smooth;
}

/* Mejoras de animación */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

/* Transiciones mejoradas */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Efectos de profundidad */
.shadow-lg {
    box-shadow:
        0 10px 25px -5px rgb(0 0 0 / 0.1),
        0 8px 10px -6px rgb(0 0 0 / 0.1);
}
</style>
