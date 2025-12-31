<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { 
    Image, 
    Palette, 
    Save, 
    Settings, 
    Type,
    Upload,
    RefreshCw
} from 'lucide-vue-next';
import { ref } from 'vue';

// Datos de ejemplo (mock)
const settings = ref({
    appName: 'PortafolioIA',
    appTagline: 'Crea tu portafolio profesional con IA',
    logoUrl: '',
    primaryColor: '#005aeb',
    secondaryColor: '#7B2FF7',
    accentColor: '#10b981',
});

const isSaving = ref(false);

// Preview del logo
const logoPreview = ref<string | null>(null);

const handleLogoUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const saveSettings = () => {
    isSaving.value = true;
    // Simular guardado
    setTimeout(() => {
        isSaving.value = false;
        alert('Configuración guardada (mock)');
    }, 1000);
};

const resetToDefaults = () => {
    settings.value = {
        appName: 'PortafolioIA',
        appTagline: 'Crea tu portafolio profesional con IA',
        logoUrl: '',
        primaryColor: '#005aeb',
        secondaryColor: '#7B2FF7',
        accentColor: '#10b981',
    };
    logoPreview.value = null;
};
</script>

<template>
    <Head title="Configuración del Sistema" />

    <AdminLayout title="Configuración del Sistema">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulario de configuración -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Información básica -->
                <div class="rounded-2xl bg-white dark:bg-slate-900 shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                            <Type class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Información de la App</h2>
                            <p class="text-sm text-gray-500 dark:text-slate-400">Personaliza el nombre y descripción</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                Nombre de la aplicación
                            </label>
                            <input
                                v-model="settings.appName"
                                type="text"
                                class="w-full rounded-lg border border-gray-300 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-[#005aeb] focus:outline-none focus:ring-1 focus:ring-[#005aeb]"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                Eslogan / Tagline
                            </label>
                            <input
                                v-model="settings.appTagline"
                                type="text"
                                class="w-full rounded-lg border border-gray-300 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-[#005aeb] focus:outline-none focus:ring-1 focus:ring-[#005aeb]"
                            />
                        </div>
                    </div>
                </div>

                <!-- Logo -->
                <div class="rounded-2xl bg-white dark:bg-slate-900 shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/20">
                            <Image class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Logo</h2>
                            <p class="text-sm text-gray-500 dark:text-slate-400">Sube el logo de tu aplicación</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <!-- Preview del logo -->
                        <div class="flex h-24 w-24 items-center justify-center rounded-xl border-2 border-dashed border-gray-300 dark:border-slate-800 bg-gray-50 dark:bg-slate-800/50">
                            <img v-if="logoPreview" :src="logoPreview" class="h-20 w-20 object-contain" />
                            <Image v-else class="h-10 w-10 text-gray-300 dark:text-slate-700" />
                        </div>

                        <div class="flex-1">
                            <label class="relative cursor-pointer">
                                <input
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleLogoUpload"
                                />
                                <div class="inline-flex items-center space-x-2 rounded-lg border border-gray-300 dark:border-slate-800 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-slate-300 transition hover:bg-gray-50 dark:hover:bg-slate-800">
                                    <Upload class="h-4 w-4" />
                                    <span>Subir logo</span>
                                </div>
                            </label>
                            <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">PNG, JPG o SVG. Máximo 2MB.</p>
                        </div>
                    </div>
                </div>

                <!-- Colores -->
                <div class="rounded-2xl bg-white dark:bg-slate-900 shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-linear-to-r from-pink-100 dark:from-pink-900/20 to-purple-100 dark:to-purple-900/20">
                            <Palette class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Colores</h2>
                            <p class="text-sm text-gray-500 dark:text-slate-400">Define la paleta de colores principal</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                Color Primario
                            </label>
                            <div class="flex items-center space-x-3">
                                <input
                                    v-model="settings.primaryColor"
                                    type="color"
                                    class="h-10 w-14 cursor-pointer rounded-lg border border-gray-300 dark:border-slate-800 bg-white dark:bg-slate-900"
                                />
                                <input
                                    v-model="settings.primaryColor"
                                    type="text"
                                    class="flex-1 rounded-lg border border-gray-300 dark:border-slate-800 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-gray-900 dark:text-white font-mono"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                Color Secundario
                            </label>
                            <div class="flex items-center space-x-3">
                                <input
                                    v-model="settings.secondaryColor"
                                    type="color"
                                    class="h-10 w-14 cursor-pointer rounded-lg border border-gray-300 dark:border-slate-800 bg-white dark:bg-slate-900"
                                />
                                <input
                                    v-model="settings.secondaryColor"
                                    type="text"
                                    class="flex-1 rounded-lg border border-gray-300 dark:border-slate-800 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-gray-900 dark:text-white font-mono"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                Color Acento
                            </label>
                            <div class="flex items-center space-x-3">
                                <input
                                    v-model="settings.accentColor"
                                    type="color"
                                    class="h-10 w-14 cursor-pointer rounded-lg border border-gray-300 dark:border-slate-800 bg-white dark:bg-slate-900"
                                />
                                <input
                                    v-model="settings.accentColor"
                                    type="text"
                                    class="flex-1 rounded-lg border border-gray-300 dark:border-slate-800 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-gray-900 dark:text-white font-mono"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview y acciones -->
            <div class="space-y-6">
                <!-- Preview -->
                <div class="rounded-2xl bg-white dark:bg-slate-900 shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Vista Previa</h3>
                    
                    <div class="rounded-xl border border-gray-200 dark:border-slate-800 p-4 bg-gray-50 dark:bg-slate-800/50">
                        <div class="flex items-center space-x-3 mb-4">
                            <div 
                                class="flex h-10 w-10 items-center justify-center rounded-lg text-white font-bold"
                                :style="{ background: `linear-gradient(135deg, ${settings.primaryColor}, ${settings.secondaryColor})` }"
                            >
                                {{ settings.appName.charAt(0) }}
                            </div>
                            <div>
                                <p class="font-bold" :style="{ color: settings.primaryColor }">
                                    {{ settings.appName }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-slate-400">{{ settings.appTagline }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div 
                                class="h-8 rounded-lg text-white text-sm flex items-center justify-center"
                                :style="{ backgroundColor: settings.primaryColor }"
                            >
                                Botón Primario
                            </div>
                            <div 
                                class="h-8 rounded-lg text-white text-sm flex items-center justify-center"
                                :style="{ backgroundColor: settings.secondaryColor }"
                            >
                                Botón Secundario
                            </div>
                            <div 
                                class="h-8 rounded-lg text-white text-sm flex items-center justify-center"
                                :style="{ backgroundColor: settings.accentColor }"
                            >
                                Acento
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="space-y-3">
                    <button
                        @click="saveSettings"
                        :disabled="isSaving"
                        class="w-full inline-flex items-center justify-center space-x-2 rounded-xl bg-[#005aeb] px-4 py-3 text-sm font-medium text-white transition hover:bg-[#0048c4] disabled:opacity-50"
                    >
                        <Save v-if="!isSaving" class="h-4 w-4" />
                        <RefreshCw v-else class="h-4 w-4 animate-spin" />
                        <span>{{ isSaving ? 'Guardando...' : 'Guardar Cambios' }}</span>
                    </button>

                    <button
                        @click="resetToDefaults"
                        class="w-full inline-flex items-center justify-center space-x-2 rounded-xl border border-gray-300 dark:border-slate-800 px-4 py-3 text-sm font-medium text-gray-700 dark:text-slate-300 transition hover:bg-gray-50 dark:hover:bg-slate-800"
                    >
                        <RefreshCw class="h-4 w-4" />
                        <span>Restablecer Valores</span>
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
