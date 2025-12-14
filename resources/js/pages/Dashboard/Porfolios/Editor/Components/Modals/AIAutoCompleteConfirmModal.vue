<!-- ============================================
   AI AUTOCOMPLETE CONFIRMATION MODAL
   Modal de confirmación después de autocompletar
   CON DATOS DINÁMICOS DEL ANÁLISIS IA
   ============================================ -->
   <script setup lang="ts">
    import { ref, computed } from 'vue';
    import { CheckCircle2, Briefcase, GraduationCap, Globe, Wrench, Loader2 } from 'lucide-vue-next';
    
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
            <div 
                v-if="isOpen" 
                class="fixed inset-0 flex items-center justify-center z-50 px-4"
            >
                <div 
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden"
                    @click.stop
                >
                    <!-- Header Success -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-12 text-white text-center">
                        <!-- Decorative elements -->
                        <div class="absolute top-0 right-0 w-40 h-40 bg-green-400 rounded-full -mr-20 -mt-20 opacity-20" />
                        
                        <!-- Success Icon -->
                        <div class="relative z-10 mb-4">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4 animate-pulse-scale">
                                <CheckCircle2 class="w-8 h-8 text-white" />
                            </div>
                        </div>
    
                        <!-- Text -->
                        <div class="relative z-10">
                            <h1 class="text-3xl font-bold mb-2">¡Formulario Completado!</h1>
                            <p class="text-green-100 text-lg">Todos tus datos han sido autocompletos</p>
                        </div>
                    </div>
    
                    <!-- Body -->
                    <div class="p-8">
                        <!-- Info Cards - DINÁMICAS -->
                        <div class="space-y-3 mb-8">
                            <!-- Experience -->
                            <div 
                                v-if="experienceCount > 0"
                                class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
                            >
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                        <Briefcase class="w-4 h-4 text-blue-500" />
                                        {{ experienceCount }} {{ experienceCount === 1 ? 'Experiencia' : 'Experiencias' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Cargadas automáticamente</p>
                                </div>
                            </div>
    
                            <!-- Education -->
                            <div 
                                v-if="educationCount > 0"
                                class="flex items-start gap-3 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800"
                            >
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-500 flex items-center justify-center mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                        <GraduationCap class="w-4 h-4 text-purple-500" />
                                        {{ educationCount }} {{ educationCount === 1 ? 'Estudio' : 'Estudios' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Cargados automáticamente</p>
                                </div>
                            </div>
    
                            <!-- Languages -->
                            <div 
                                v-if="languageCount > 0"
                                class="flex items-start gap-3 p-4 bg-pink-50 dark:bg-pink-900/20 rounded-lg border border-pink-200 dark:border-pink-800"
                            >
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-pink-500 flex items-center justify-center mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                        <Globe class="w-4 h-4 text-pink-500" />
                                        {{ languageCount }} {{ languageCount === 1 ? 'Idioma' : 'Idiomas' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Cargados automáticamente</p>
                                </div>
                            </div>
    
                            <!-- Skills -->
                            <div 
                                v-if="skillsCount > 0"
                                class="flex items-start gap-3 p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800"
                            >
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-orange-500 flex items-center justify-center mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                        <Wrench class="w-4 h-4 text-orange-500" />
                                        {{ skillsCount }} {{ skillsCount === 1 ? 'Habilidad' : 'Habilidades' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Cargadas automáticamente</p>
                                </div>
                            </div>
    
                            <!-- Projects -->
                            <div 
                                v-if="projectsCount > 0"
                                class="flex items-start gap-3 p-4 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg border border-cyan-200 dark:border-cyan-800"
                            >
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-cyan-500 flex items-center justify-center mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                        {{ projectsCount }} {{ projectsCount === 1 ? 'Proyecto' : 'Proyectos' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Cargados automáticamente</p>
                                </div>
                            </div>
                        </div>
    
                        <!-- Info Alert -->
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 mb-8">
                            <p class="text-sm text-green-900 dark:text-green-200">
                                <span class="font-semibold">✅ Listo:</span> Todos los datos han sido autocompletos. Puedes continuar editando manualmente si deseas hacer cambios.
                            </p>
                        </div>
    
                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button
                                @click="handleContinue"
                                :disabled="isProcessing || !hasData"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg font-semibold hover:shadow-lg hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2"
                            >
                                <Loader2 
                                    v-if="isProcessing"
                                    class="w-5 h-5 animate-spin"
                                />
                                <svg 
                                    v-else
                                    class="w-5 h-5" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path 
                                        stroke-linecap="round" 
                                        stroke-linejoin="round" 
                                        stroke-width="2" 
                                        d="M9 5l7 7-7 7" 
                                    />
                                </svg>
                                <span>{{ isProcessing ? 'Continuando...' : 'Continuar manualmente' }}</span>
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
        0%, 100% {
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