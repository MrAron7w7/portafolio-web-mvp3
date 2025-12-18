<!-- ============================================
   AI AUTOCOMPLETE CONFIRMATION MODAL
   Modal de confirmación después de autocompletar
   CON DATOS DINÁMICOS DEL ANÁLISIS IA
   ============================================ -->
<script setup lang="ts">
import { ref, computed } from 'vue';
import { CheckCircle2, Briefcase, GraduationCap, Globe, Wrench, Loader2, Code2 } from 'lucide-vue-next';

// Tipos - Interfaz correcta del mapa de IA
interface AIAnalysisResult {
    personal: Array<{ summary: string }>;
    experience: Array<{
        company: string;
        position: string;
        startDate: string;
        endDate: string;
        current: boolean;
        description: string
    }>;
    skills: {
        technical: Array<{ name: string; level: number }>;
        soft: Array<{ name: string; level: number }>;
    };
    projects: Array<{
        id: number;
        name: string;
        description: string;
        image: string | null;
        link: string | null;
        technologies: string[]
    }>;
    education: Array<{
        id: number;
        degree: string;
        institution: string;
        startDate: string;
        endDate: string;
        description: string | null;
        current: boolean
    }>;
    languages: Array<{
        id: number;
        name: string;
        level: string
    }>;
}

// Props
const props = defineProps<{
    isOpen: boolean;
    isAutoCompleting?: boolean;
    analysisResult?: AIAnalysisResult | null;
}>();

// Emits
const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'continue'): void;
}>();

// Estado local
const isProcessing = ref(false);

// ============================================
// COMPUTED - CONTEOS DINÁMICOS
// ============================================

const experienceCount = computed(() => props.analysisResult?.experience?.length || 0);
const educationCount = computed(() => props.analysisResult?.education?.length || 0);
const languageCount = computed(() => props.analysisResult?.languages?.length || 0);
const skillsCount = computed(() => {
    const technical = props.analysisResult?.skills?.technical?.length || 0;
    const soft = props.analysisResult?.skills?.soft?.length || 0;
    return technical + soft;
});
const projectsCount = computed(() => props.analysisResult?.projects?.length || 0);

// Verificación de que hay datos
const hasData = computed(() => props.analysisResult !== null);

// ============================================
// MÉTODOS
// ============================================

const handleContinue = async () => {
    isProcessing.value = true;
    await new Promise(resolve => setTimeout(resolve, 500));
    emit('continue');
    isProcessing.value = false;
};

const handleClose = () => {
    if (!isProcessing.value) {
        emit('close');
    }
};
</script>

<template>
    <!-- Modal Container -->
    <Transition name="scale">
        <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center z-50 px-4 py-4">
            <!-- ✨ MODAL CON SCROLL COMPLETO ✨ -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-hidden flex flex-col"
                @click.stop>
                <!-- Header Success - STICKY -->
                <div
                    class="sticky top-0 z-10 relative overflow-hidden bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-12 text-white text-center flex-shrink-0">
                    <!-- Decorative elements -->
                    <div class="absolute top-0 right-0 w-40 h-40 bg-green-400 rounded-full -mr-20 -mt-20 opacity-20" />

                    <!-- Success Icon -->
                    <div class="relative z-10 mb-4">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4 animate-pulse-scale">
                            <CheckCircle2 class="w-8 h-8 text-white" />
                        </div>
                    </div>

                    <!-- Text -->
                    <div class="relative z-10">
                        <h1 class="text-3xl font-bold mb-2">¡Formulario Completado!</h1>
                        <p class="text-green-100 text-lg">Todos tus datos han sido autocompletos</p>
                    </div>
                </div>

                <!-- Body - CON SCROLL ✨ -->
                <div class="overflow-y-auto flex-1 px-8 pt-8">
                    <!-- Info Cards - DINÁMICAS -->
                    <!-- Info Cards - DISEÑO ULTRA COMPACTO -->
                    <div class="space-y-2 mb-8">
                        <!-- Experience -->
                        <div v-if="experienceCount > 0"
                            class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3 flex-1">
                                <div
                                    class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <Briefcase class="w-5 h-5 text-white" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ experienceCount }}
                                        {{ experienceCount === 1 ? 'Experiencia' : 'Experiencias' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Agregadas</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>

                        <!-- Education -->
                        <div v-if="educationCount > 0"
                            class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-700 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3 flex-1">
                                <div
                                    class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <GraduationCap class="w-5 h-5 text-white" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ educationCount }}
                                        {{ educationCount === 1 ? 'Estudio' : 'Estudios' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Agregados</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>

                        <!-- Languages -->
                        <div v-if="languageCount > 0"
                            class="flex items-center justify-between p-3 bg-pink-50 dark:bg-pink-900/20 rounded-lg border border-pink-200 dark:border-pink-700 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3 flex-1">
                                <div
                                    class="w-10 h-10 bg-pink-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <Globe class="w-5 h-5 text-white" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ languageCount }}
                                        {{ languageCount === 1 ? 'Idioma' : 'Idiomas' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Agregados</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>

                        <!-- Skills -->
                        <div v-if="skillsCount > 0"
                            class="flex items-center justify-between p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-700 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3 flex-1">
                                <div
                                    class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <Wrench class="w-5 h-5 text-white" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ skillsCount }} {{
                                        skillsCount === 1 ? 'Habilidad' : 'Habilidades' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Agregadas</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>

                        <!-- Projects -->
                        <div v-if="projectsCount > 0"
                            class="flex items-center justify-between p-3 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg border border-cyan-200 dark:border-cyan-700 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3 flex-1">
                                <div
                                    class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <Code2 class="w-5 h-5 text-white" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ projectsCount }}
                                        {{ projectsCount === 1 ? 'Proyecto' : 'Proyectos' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Agregados</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

       

                    <!-- Info Alert -->
                    <div
                        class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 mb-8">
                        <p class="text-sm text-green-900 dark:text-green-200">
                            <span class="font-semibold">✅ Ahora </span> continúa con la edición manual para completar los campos restantes. Avancemos paso a paso.
                        </p>
                    </div>
                </div>

                <!-- Action Buttons - STICKY AL FONDO ✨ -->
                <div
                    class="sticky bottom-0 z-10 p-8 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-slate-800 flex-shrink-0">
                    <div class="flex gap-3">
                        <button @click="handleContinue" :disabled="isProcessing || !hasData"
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg font-semibold hover:shadow-lg hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2">
                            <Loader2 v-if="isProcessing" class="w-5 h-5 animate-spin" />
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                            <span>{{ isProcessing ? 'Continuando...' : 'Continuar' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>


<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.scale-enter-active,
.scale-leave-active {
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.scale-enter-from,
.scale-leave-to {
    transform: scale(0.95);
    opacity: 0;
}

@keyframes pulse-scale {

    0%,
    100% {
        transform: scale(1);
        opacity: 1;
    }

    50% {
        transform: scale(1.1);
        opacity: 0.8;
    }
}

.animate-pulse-scale {
    animation: pulse-scale 2s ease-in-out infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>