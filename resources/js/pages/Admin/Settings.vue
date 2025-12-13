<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

const page = usePage();
const props = defineProps({
    settings: Object,
    grouped_settings: Object
});

// Form con todos los settings
const form = useForm({
    settings: {},
    logo: null
});

// INICIALIZACIÓN CORRECTA
onMounted(() => {
    if (props.grouped_settings) {
        Object.keys(props.grouped_settings).forEach(group => {
            Object.keys(props.grouped_settings[group]).forEach(key => {
                form.settings[key] = props.grouped_settings[group][key].value;
            });
        });
    }
});

// Preview del logo
const logoPreview = ref(null);
const currentLogo = computed(() => {
    const logo = props.settings?.general?.find(s => s.key === 'app_logo');
    if (logo && logo.value) {
        return `/storage/${logo.value}`;
    }
    return null;
});

// Manejar cambio de logo
const handleLogoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

// Enviar formulario
const submitSettings = () => {
    form.post('/admin/settings', {
        preserveScroll: true,
        onSuccess: () => {
            logoPreview.value = null;
        }
    });
};

// Resetear configuración
const resetSettings = () => {
    if (confirm('¿Estás seguro de restaurar la configuración por defecto?')) {
        router.post('/admin/settings/reset', {}, {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            }
        });
    }
};

// Tabs activo
const activeTab = ref('general');
</script>

<template>
    <AdminLayout title="Configuración del Sistema">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Configuración</h1>
                    <p class="mt-2 text-gray-600">Personaliza la apariencia y contenido del sitio</p>
                </div>
                
                <button
                    @click="resetSettings"
                    type="button"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Restaurar por Defecto
                </button>
            </div>

            <!-- Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button
                        @click="activeTab = 'general'"
                        :class="[
                            activeTab === 'general'
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                            'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium'
                        ]"
                    >
                        General
                    </button>
                    <button
                        @click="activeTab = 'appearance'"
                        :class="[
                            activeTab === 'appearance'
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                            'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium'
                        ]"
                    >
                        Apariencia
                    </button>
                    <button
                        @click="activeTab = 'content'"
                        :class="[
                            activeTab === 'content'
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                            'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium'
                        ]"
                    >
                        Contenido
                    </button>
                </nav>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitSettings" class="space-y-8">
                
                <!-- GENERAL TAB -->
                <div v-if="activeTab === 'general'" class="rounded-lg bg-white p-6 shadow">
                    <h2 class="mb-6 text-xl font-semibold">Configuración General</h2>
                    
                    <div class="space-y-6">
                        <!-- Nombre de la App -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de la Aplicación
                            </label>
                            <input
                                v-model="form.settings.app_name"
                                type="text"
                                class="w-full rounded-lg border border-gray-400 px-2 py-1 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="PortafolioAI"
                            />
                            <p class="mt-1 text-sm text-gray-500">Este nombre aparece en el header y footer</p>
                        </div>

                        <!-- Logo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Logo
                            </label>
                            <div class="flex items-center space-x-4">
                                <div v-if="logoPreview || currentLogo" class="h-20 w-20 rounded-lg border-2 border-gray-200 overflow-hidden bg-white flex items-center justify-center">
                                    <img :src="logoPreview || currentLogo" alt="Logo" class="h-full w-full object-contain p-2" />
                                </div>
                                <div v-else class="h-20 w-20 rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center">
                                    <span class="text-3xl">📷</span>
                                </div>
                                
                                <label class="cursor-pointer rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    <input
                                        type="file"
                                        @change="handleLogoChange"
                                        accept="image/*"
                                        class="hidden"
                                    />
                                    {{ logoPreview || currentLogo ? 'Cambiar Logo' : 'Subir Logo' }}
                                </label>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">PNG, JPG hasta 2MB. Recomendado: 200x200px</p>
                        </div>
                    </div>
                </div>

                <!-- APPEARANCE TAB -->
                <div v-if="activeTab === 'appearance'" class="rounded-lg bg-white p-6 shadow">
                    <h2 class="mb-6 text-xl font-semibold">Colores</h2>
                    
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Color Primario -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Color Primario
                            </label>
                            <div class="flex items-center space-x-3">
                                <input
                                    v-model="form.settings.primary_color"
                                    type="color"
                                    class="h-12 w-12 rounded border-gray-300 cursor-pointer"
                                />
                                <input
                                    v-model="form.settings.primary_color"
                                    type="text"
                                    class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="#005aeb"
                                />
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Color principal de botones y elementos destacados</p>
                        </div>

                        <!-- Color Secundario -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Color Secundario
                            </label>
                            <div class="flex items-center space-x-3">
                                <input
                                    v-model="form.settings.secondary_color"
                                    type="color"
                                    class="h-12 w-12 rounded border-gray-300 cursor-pointer"
                                />
                                <input
                                    v-model="form.settings.secondary_color"
                                    type="text"
                                    class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="#7B2FF7"
                                />
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Color usado en gradientes y acentos</p>
                        </div>
                    </div>

                    <!-- Preview del Gradiente -->
                    <div class="mt-8">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Vista Previa</label>
                        <div class="rounded-xl border-2 border-gray-200 p-8" :style="{
                            background: `linear-gradient(135deg, ${form.settings.primary_color || '#005aeb'} 0%, ${form.settings.secondary_color || '#7B2FF7'} 100%)`
                        }">
                            <p class="text-center text-2xl font-bold text-white drop-shadow-lg">{{ form.settings.app_name || 'PortafolioAI' }}</p>
                            <p class="text-center text-white/90 mt-2">Este es el gradiente que se usará en tu sitio</p>
                        </div>
                    </div>
                </div>

                <!-- CONTENT TAB -->
                <div v-if="activeTab === 'content'" class="space-y-6">
                    
                    <!-- Hero Section -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-6 text-xl font-semibold">Sección Hero</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Título Principal (Línea 1)</label>
                                <input v-model="form.settings.hero_title_1" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" placeholder="Crea tu Portafolio" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Título Principal (Línea 2)</label>
                                <input v-model="form.settings.hero_title_2" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" placeholder="Profesional en" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Texto Destacado (con gradiente)</label>
                                <input v-model="form.settings.hero_title_highlight" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" placeholder="Minutos con IA" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                                <textarea v-model="form.settings.hero_description" rows="3" class="w-full rounded-lg border border-gray-400 px-2 py-1" placeholder="Genera un portafolio moderno..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Texto del Botón Principal</label>
                                <input v-model="form.settings.hero_cta_main" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" placeholder="Crear mi Portafolio Ahora" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Texto del Botón Secundario</label>
                                <input v-model="form.settings.hero_cta_secondary" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" placeholder="Ver Ejemplos" />
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-6 text-xl font-semibold">Estadísticas</h2>
                        
                        <div class="grid gap-6 md:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Usuarios Activos</label>
                                <input v-model.number="form.settings.stats_users_count" type="number" class="w-full rounded-lg border border-gray-400 px-2 py-1 mb-2" />
                                <input v-model="form.settings.stats_users_label" type="text" class="w-full rounded-lg border-gray-300" placeholder="Usuarios Activos" />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Portafolios Creados</label>
                                <input v-model.number="form.settings.stats_portfolios_count" type="number" class="w-full rounded-lg border border-gray-400 px-2 py-1 mb-2" />
                                <input v-model="form.settings.stats_portfolios_label" type="text" class="w-full rounded-lg border-gray-300" placeholder="Portafolios Creados" />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">% Satisfacción</label>
                                <input v-model.number="form.settings.stats_satisfaction_count" type="number" class="w-full rounded-lg border border-gray-400 px-2 py-1 mb-2" />
                                <input v-model="form.settings.stats_satisfaction_label" type="text" class="w-full rounded-lg border-gray-300" placeholder="Satisfacción" />
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Características -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-6 text-xl font-semibold">Sección "Por qué elegir"</h2>
                        
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Título de la Sección</label>
                                <input v-model="form.settings.features_title" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subtítulo</label>
                                <textarea v-model="form.settings.features_subtitle" rows="2" class="w-full rounded-lg border border-gray-400 px-2 py-1"></textarea>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <!-- Feature 1 -->
                            <div class="border-t pt-4">
                                <h3 class="mb-3 font-medium text-gray-900 flex items-center gap-2">
                                    <span class="text-2xl">{{ form.settings.feature_1_icon || '🤖' }}</span>
                                    Característica 1
                                </h3>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Título</label>
                                        <input v-model="form.settings.feature_1_title" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Icono (emoji)</label>
                                        <input v-model="form.settings.feature_1_icon" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm text-gray-700 mb-1">Descripción</label>
                                        <textarea v-model="form.settings.feature_1_description" rows="2" class="w-full rounded border border-gray-400 px-2 py-1"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Estadística</label>
                                        <input v-model="form.settings.feature_1_stat" type="text" class="w-full rounded border border-gray-400 px-2 py-1" placeholder="2min" />
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Etiqueta</label>
                                        <input v-model="form.settings.feature_1_stat_label" type="text" class="w-full rounded border border-gray-400 px-2 py-1" placeholder="Creación promedio" />
                                    </div>
                                </div>
                            </div>

                            <!-- Feature 2 -->
                            <div class="border-t pt-4">
                                <h3 class="mb-3 font-medium text-gray-900 flex items-center gap-2">
                                    <span class="text-2xl">{{ form.settings.feature_2_icon || '🎨' }}</span>
                                    Característica 2
                                </h3>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Título</label>
                                        <input v-model="form.settings.feature_2_title" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Icono (emoji)</label>
                                        <input v-model="form.settings.feature_2_icon" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm text-gray-700 mb-1">Descripción</label>
                                        <textarea v-model="form.settings.feature_2_description" rows="2" class="w-full rounded border border-gray-400 px-2 py-1"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Estadística</label>
                                        <input v-model="form.settings.feature_2_stat" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Etiqueta</label>
                                        <input v-model="form.settings.feature_2_stat_label" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                </div>
                            </div>

                            <!-- Feature 3 -->
                            <div class="border-t pt-4">
                                <h3 class="mb-3 font-medium text-gray-900 flex items-center gap-2">
                                    <span class="text-2xl">{{ form.settings.feature_3_icon || '🚀' }}</span>
                                    Característica 3
                                </h3>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Título</label>
                                        <input v-model="form.settings.feature_3_title" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Icono (emoji)</label>
                                        <input v-model="form.settings.feature_3_icon" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm text-gray-700 mb-1">Descripción</label>
                                        <textarea v-model="form.settings.feature_3_description" rows="2" class="w-full rounded border border-gray-400 px-2 py-1"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Estadística</label>
                                        <input v-model="form.settings.feature_3_stat" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Etiqueta</label>
                                        <input v-model="form.settings.feature_3_stat_label" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                </div>
                            </div>

                            <!-- Feature 4 -->
                            <div class="border-t pt-4">
                                <h3 class="mb-3 font-medium text-gray-900 flex items-center gap-2">
                                    <span class="text-2xl">{{ form.settings.feature_4_icon || '⚙️' }}</span>
                                    Característica 4
                                </h3>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Título</label>
                                        <input v-model="form.settings.feature_4_title" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Icono (emoji)</label>
                                        <input v-model="form.settings.feature_4_icon" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm text-gray-700 mb-1">Descripción</label>
                                        <textarea v-model="form.settings.feature_4_description" rows="2" class="w-full rounded border border-gray-400 px-2 py-1"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Estadística</label>
                                        <input v-model="form.settings.feature_4_stat" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Etiqueta</label>
                                        <input v-model="form.settings.feature_4_stat_label" type="text" class="w-full rounded border border-gray-400 px-2 py-1" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cómo Funciona -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-6 text-xl font-semibold">Sección "Cómo funciona"</h2>
                        
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                                <input v-model="form.settings.how_it_works_title" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subtítulo</label>
                                <input v-model="form.settings.how_it_works_subtitle" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-3">
                            <div>
                                <h3 class="mb-2 font-medium">Paso 1</h3>
                                <input v-model="form.settings.step_1_title" type="text" class="w-full rounded border-gray-300 mb-2" placeholder="Título" />
                                <textarea v-model="form.settings.step_1_description" rows="2" class="w-full rounded border border-gray-400 px-2 py-1" placeholder="Descripción"></textarea>
                            </div>
                            <div>
                                <h3 class="mb-2 font-medium">Paso 2</h3>
                                <input v-model="form.settings.step_2_title" type="text" class="w-full rounded border-gray-300 mb-2" placeholder="Título" />
                                <textarea v-model="form.settings.step_2_description" rows="2" class="w-full rounded border border-gray-400 px-2 py-1" placeholder="Descripción"></textarea>
                            </div>
                            <div>
                                <h3 class="mb-2 font-medium">Paso 3</h3>
                                <input v-model="form.settings.step_3_title" type="text" class="w-full rounded border-gray-300 mb-2" placeholder="Título" />
                                <textarea v-model="form.settings.step_3_description" rows="2" class="w-full rounded border border-gray-400 px-2 py-1" placeholder="Descripción"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonios -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-6 text-xl font-semibold">Sección Testimonios</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                                <input v-model="form.settings.testimonials_title" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subtítulo</label>
                                <input v-model="form.settings.testimonials_subtitle" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                            </div>
                        </div>
                    </div>

                    <!-- Planes -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-6 text-xl font-semibold">Sección Planes</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                                <input v-model="form.settings.pricing_title" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subtítulo</label>
                                <input v-model="form.settings.pricing_subtitle" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                            </div>
                        </div>
                    </div>

                    <!-- CTA Final -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-6 text-xl font-semibold">CTA Final</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                                <input v-model="form.settings.cta_title" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subtítulo</label>
                                <input v-model="form.settings.cta_subtitle" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Texto del Botón</label>
                                <input v-model="form.settings.cta_button" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="rounded-lg bg-white p-6 shadow">
                        <h2 class="mb-6 text-xl font-semibold">Footer</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                                <textarea v-model="form.settings.footer_description" rows="2" class="w-full rounded-lg border border-gray-400 px-2 py-1"></textarea>
                            </div>
                            
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input v-model="form.settings.footer_email" type="email" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                                    <input v-model="form.settings.footer_phone" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Ubicación</label>
                                    <input v-model="form.settings.footer_location" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Copyright</label>
                                    <input v-model="form.settings.footer_copyright" type="text" class="w-full rounded-lg border border-gray-400 px-2 py-1" />
                                </div>
                            </div>

                            <div class="border-t pt-4 mt-4">
                                <h3 class="mb-3 font-medium text-gray-900">Redes Sociales</h3>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Facebook URL</label>
                                        <input v-model="form.settings.social_facebook" type="text" class="w-full rounded border border-gray-400 px-2 py-1" placeholder="https://facebook.com/..." />
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Twitter URL</label>
                                        <input v-model="form.settings.social_twitter" type="text" class="w-full rounded border border-gray-400 px-2 py-1" placeholder="https://twitter.com/..." />
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">Instagram URL</label>
                                        <input v-model="form.settings.social_instagram" type="text" class="w-full rounded border border-gray-400 px-2 py-1" placeholder="https://instagram.com/..." />
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-1">LinkedIn URL</label>
                                        <input v-model="form.settings.social_linkedin" type="text" class="w-full rounded border border-gray-400 px-2 py-1" placeholder="https://linkedin.com/..." />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="sticky bottom-0 bg-white border-t border-gray-200 py-4 px-6 flex justify-end space-x-4 shadow-lg">
                    <button
                        type="button"
                        @click="router.visit('/admin/dashboard')"
                        class="rounded-lg border border-gray-300 bg-white px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transition-colors"
                    >
                        <svg v-if="form.processing" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<style scoped>
/* Smooth transitions */
button {
    transition: all 0.2s ease;
}

/* Sticky button shadow */
.sticky {
    z-index: 10;
}
</style>