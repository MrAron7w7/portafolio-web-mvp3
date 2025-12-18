<!-- ============================================
   START CHOICE MODAL - MODAL INICIAL
   Permite elegir entre crear con IA o manualmente
   ============================================ -->
   <script setup lang="ts">
    import { ref, computed } from 'vue';
    import { PencilRuler,Bot } from 'lucide-vue-next';
    
    // Props
    const props = defineProps<{
        isOpen: boolean;
        isLoading?: boolean;
    }>();
    
    // Emits
    const emit = defineEmits<{
        (e: 'close'): void;
        (e: 'choose-ai'): void;
        (e: 'choose-manual'): void;
    }>();
    
    // Estado local
    const selectedOption = ref<'ai' | 'manual' | null>(null);
    const isProcessing = ref(false);
    
    // Métodos
    const handleSelectAI = async () => {
        selectedOption.value = 'ai';
        isProcessing.value = true;
        
        // Esperar un poco para animación
        await new Promise(resolve => setTimeout(resolve, 300));
        
        emit('choose-ai');
        
        // Reset después de un tiempo
        setTimeout(() => {
            isProcessing.value = false;
            selectedOption.value = null;
        }, 1000);
    };
    
    const handleSelectManual = async () => {
        selectedOption.value = 'manual';
        isProcessing.value = true;
        
        // Esperar un poco para animación
        await new Promise(resolve => setTimeout(resolve, 300));
        
        emit('choose-manual');
        
        // Reset después de un tiempo
        setTimeout(() => {
            isProcessing.value = false;
            selectedOption.value = null;
        }, 1000);
    };
    
    const handleClose = () => {
        if (!isProcessing.value) {
            emit('close');
        }
    };
    
    // Computed
    const showBackdrop = computed(() => props.isOpen || props.isLoading);
    </script>
    
    <template>

    
        <!-- Modal Container -->
        <Transition name="scale">
            <div 
                v-if="isOpen" 
                class="fixed inset-0 flex items-center justify-center z-50 px-4"
            >
                <div 
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden"
                    @click.stop
                >
                    <!-- Header -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-teal-500 to-teal-600 px-8 py-12 text-white">
                        <!-- Decorative elements -->
                        <div class="absolute top-0 right-0 w-40 h-40 bg-teal-400 rounded-full -mr-20 -mt-20 opacity-20" />
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-teal-400 rounded-full -ml-16 -mb-16 opacity-20" />
                        
                        <!-- Close button -->
                        <button
                            @click="handleClose"
                            :disabled="isProcessing"
                            class="absolute top-4 right-4 p-2 hover:bg-teal-600 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
    
                        <!-- Content -->
                        <div class="relative z-10">
                            <h1 class="text-3xl font-bold mb-2">¿Cómo quieres editar tu portafolio?</h1>
                            <p class="text-teal-100 text-lg">Elige la opción que mejor se adapte a ti</p>
                        </div>
                    </div>
    
                    <!-- Body -->
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Opción 1: Con IA -->
                            <button
                                @click="handleSelectAI"
                                :disabled="isProcessing"
                                :class="[
                                    'group relative h-64 rounded-xl border-2 transition-all duration-300 overflow-hidden',
                                    'disabled:opacity-50 disabled:cursor-not-allowed',
                                    selectedOption === 'ai'
                                        ? 'border-teal-500 bg-teal-50/50 dark:bg-teal-900/20 scale-105'
                                        : 'border-gray-200 dark:border-gray-700 hover:border-teal-400 hover:shadow-lg dark:hover:bg-gray-700/50'
                                ]"
                            >
                                <!-- Background gradient -->
                                <div class="absolute inset-0 bg-gradient-to-br from-teal-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                                
                                <!-- Content -->
                                <div class="relative z-10 h-full flex flex-col items-center justify-center text-center px-6">
                                    <!-- Icon -->
                                    <div class="mb-4 p-4 bg-teal-100 dark:bg-teal-900/40 rounded-xl group-hover:scale-110 transition-transform">
                                        <Bot class="w-12 h-12 text-teal-600 dark:text-teal-400" />
                                    </div>
    
                                    <!-- Text -->
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Con asistente IA</h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                        Rellena tu portafolio con contenido inteligente y ahorra tiempo
                                    </p>
    
                                    <!-- Badge -->
                                    <div class="mt-4 inline-block bg-teal-500/20 px-3 py-1 rounded-full">
                                        <span class="text-xs font-semibold text-teal-700 dark:text-teal-300">⚡ Recomendado</span>
                                    </div>
    
                                    <!-- Loading indicator -->
                                    <Transition name="fade">
                                        <div v-if="selectedOption === 'ai' && isProcessing" class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-slate-800/80">
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="w-6 h-6 border-3 border-teal-500 border-t-transparent rounded-full animate-spin" />
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Iniciando...</span>
                                            </div>
                                        </div>
                                    </Transition>
                                </div>
                            </button>
    
                            <!-- Opción 2: Manual -->
                            <button
                                @click="handleSelectManual"
                                :disabled="isProcessing"
                                :class="[
                                    'group relative h-64 rounded-xl border-2 transition-all duration-300 overflow-hidden',
                                    'disabled:opacity-50 disabled:cursor-not-allowed',
                                    selectedOption === 'manual'
                                        ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/20 scale-105'
                                        : 'border-gray-200 dark:border-gray-700 hover:border-blue-400 hover:shadow-lg dark:hover:bg-gray-700/50'
                                ]"
                            >
                                <!-- Background gradient -->
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                                
                                <!-- Content -->
                                <div class="relative z-10 h-full flex flex-col items-center justify-center text-center px-6">
                                    <!-- Icon -->
                                    <div class="mb-4 p-4 bg-blue-100 dark:bg-blue-900/40 rounded-xl group-hover:scale-110 transition-transform">
                                        <PencilRuler class="w-12 h-12 text-blue-600 dark:text-blue-400" />
                                    </div>
    
                                    <!-- Text -->
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2"> Continuar manualmente</h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                        Ser tu propio guía  sobre cada detalle de tu portafolio
                                    </p>
    
                                    <!-- Badge -->
                                    <div class="mt-4 inline-block bg-blue-500/20 px-3 py-1 rounded-full">
                                        <span class="text-xs font-semibold text-blue-700 dark:text-blue-300">🎨 Personalizado</span>
                                    </div>
    
                                    <!-- Loading indicator -->
                                    <Transition name="fade">
                                        <div v-if="selectedOption === 'manual' && isProcessing" class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-slate-800/80">
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="w-6 h-6 border-3 border-blue-500 border-t-transparent rounded-full animate-spin" />
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Iniciando...</span>
                                            </div>
                                        </div>
                                    </Transition>
                                </div>
                            </button>
                        </div>
    
                        <!-- Info text -->
                        <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <p class="text-sm text-blue-900 dark:text-blue-200">
                                <span class="font-semibold">💡 Consejo:</span> Puedes cambiar tu enfoque en cualquier momento. Ambas opciones tienen las mismas características.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </template>
    
    <style scoped>
    /* Animaciones de Transición */
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
    
    /* Animación de carga */
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    </style>