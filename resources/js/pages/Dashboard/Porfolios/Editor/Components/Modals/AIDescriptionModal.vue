<!-- ============================================
   AIDescriptionModal.vue - CON SCROLL INTERNO
   ============================================ -->

   <script setup lang="ts">
    import { ref, computed, watch } from 'vue';
    
    
    // Props
    const props = defineProps<{
        isOpen: boolean;
        isAnalyzing?: boolean;
    }>();
    
    
    // Emits
    const emit = defineEmits<{
        (e: 'close'): void;
        (e: 'analyze', description: string): void;
    }>();
    
    
    // Estado local
    const description = ref('');
    const charCount = computed(() => description.value.length);
    const maxChars = 2500;
    const minChars = 50;
    const isValid = computed(() => charCount.value >= minChars);
    const errorMessage = ref('');
    
    
    // ============================================
    // WATCH - Limpiar al cerrar
    // ============================================
    watch(() => props.isOpen, (newVal) => {
        if (!newVal) {
            // Limpiar cuando el modal se cierra
            description.value = '';
            errorMessage.value = '';
            console.log('✅ AIDescriptionModal limpio');
        }
    });
    
    
    // ============================================
    // MÉTODOS
    // ============================================
    
    /**
     * ✅ FIX: Emite description directamente al padre
     * El padre (Edito.vue) es quien llama al endpoint
     */
    const handleAnalyze = async () => {
        if (!isValid.value) {
            errorMessage.value = `La descripción debe tener mínimo ${minChars} caracteres`;
            return;
        }
        
        console.log('📝 Emitiendo descripción:', description.value.substring(0, 50) + '...');
        emit('analyze', description.value);
    };
    
    
    /**
     * ✅ FIX: Cierre sincronizado con el padre
     * El padre valida si hay otros modales abiertos
     */
    const handleClose = () => {
        // ✅ Verificar que NO estamos en análisis
        if (props.isAnalyzing) {
            console.warn('⚠️ No se puede cerrar mientras se analiza');
            return;
        }
        
        console.log('❌ Cerrando AIDescriptionModal');
        emit('close');
    };
    
    
    const handleClear = () => {
        description.value = '';
        errorMessage.value = '';
    };
    

</script>

<template>
    <!-- Modal Container -->
    <Transition name="scale">
        <div 
            v-if="isOpen" 
            class="fixed inset-0 flex items-center justify-center z-50 px-4 py-4 pointer-events-none"
        >
            <!-- Modal Card - CON SCROLL ✨ -->
            <div 
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden pointer-events-auto flex flex-col"
                @click.stop
            >
                <!-- Header - STICKY ✨ -->
                <div class="sticky top-0 z-10 overflow-hidden bg-linear-to-r from-purple-500 to-purple-600 px-8 py-12 text-white shrink-0">
                    <!-- Decorative elements -->
                    <div class="absolute top-0 right-0 w-40 h-40 bg-purple-400 rounded-full -mr-20 -mt-20 opacity-20" />
                    
                    <!-- Close button -->
                    <button
                        @click="handleClose"
                        :disabled="isAnalyzing"
                        class="absolute top-4 right-4 p-2 hover:bg-purple-600 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed z-20"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Content -->
                    <div class="relative z-10">
                        <h1 class="text-3xl font-bold mb-2">📝 Describe Tu Perfil</h1>
                        <p class="text-purple-100 text-lg">Cuéntame sobre tu experiencia profesional</p>
                    </div>
                </div>

                <!-- Body - CON SCROLL ✨ -->
                <div class="overflow-y-auto flex-1 px-8 pt-8 pb-8">
                    <!-- Error Message -->
                    <Transition name="fade">
                        <div 
                            v-if="errorMessage"
                            class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800"
                        >
                            <p class="text-sm text-red-900 dark:text-red-200">
                                <span class="font-semibold">⚠️ Error:</span> {{ errorMessage }}
                            </p>
                        </div>
                    </Transition>

                    <!-- Info -->
                    <div class="mb-6 p-5 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 shadow-sm">
                        <p class="text-sm font-bold text-blue-900 dark:text-blue-100 mb-3 flex items-center gap-2">
                            <span class="text-lg">💡</span> Consejo para un portafolio profesional:
                        </p>
                        <p class="text-sm text-blue-800 dark:text-blue-300 mb-4 leading-relaxed">
                            Cuanto más detallada sea tu descripción, más preciso será el análisis de la IA. Te sugerimos estructurar tu texto incluyendo:
                        </p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            <div class="space-y-2">
                                <div class="flex items-start gap-2 text-xs text-blue-800 dark:text-blue-300">
                                    <div class="mt-1 h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></div>
                                    <p><b>Sobre ti:</b> Tu nombre, título, ciudad, país, email y teléfono.</p>
                                </div>
                                <div class="flex items-start gap-2 text-xs text-blue-800 dark:text-blue-300">
                                    <div class="mt-1 h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></div>
                                    <p><b>Experiencia:</b> Cargos, empresas, fechas y tus logros más relevantes.</p>
                                </div>
                                <div class="flex items-start gap-2 text-xs text-blue-800 dark:text-blue-300">
                                    <div class="mt-1 h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></div>
                                    <p><b>Proyectos:</b> Nombres de proyectos, qué hiciste y qué tecnologías usaste.</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-start gap-2 text-xs text-blue-800 dark:text-blue-300">
                                    <div class="mt-1 h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></div>
                                    <p><b>Habilidades:</b> Herramientas técnicas (React, SQL) y blandas (Liderazgo).</p>
                                </div>
                                <div class="flex items-start gap-2 text-xs text-blue-800 dark:text-blue-300">
                                    <div class="mt-1 h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></div>
                                    <p><b>Educación:</b> Títulos obtenidos, instituciones y años de estudio.</p>
                                </div>
                                <div class="flex items-start gap-2 text-xs text-blue-800 dark:text-blue-300">
                                    <div class="mt-1 h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></div>
                                    <p><b>Redes e Idiomas:</b> Enlaces (LinkedIn/GitHub) e idiomas con su nivel.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Textarea -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                            Tu Descripción
                        </label>
                        <textarea
                            v-model="description"
                            :maxlength="maxChars"
                            :disabled="isAnalyzing"
                            placeholder="Ejemplo: 'Hola, soy Roberto Pérez, Desarrollador Web con 5 años de experiencia en Laravel y Vue. He trabajado en TechCorp donde lideré el rediseño de su plataforma e-commerce. Domino SQL, AWS y metodologías ágiles. Me gradué de la Universidad X y hablo inglés avanzado...'"
                            class="w-full h-48 p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:border-purple-500 focus:outline-none dark:bg-slate-700 dark:text-white resize-none transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        />
                        
                        <!-- Character count -->
                        <div class="flex justify-between items-center mt-2 text-sm">
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ charCount }} / {{ maxChars }} caracteres
                            </span>
                            <span 
                                :class="[
                                    'font-semibold',
                                    charCount < minChars ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-emerald-400'
                                ]"
                            >
                                {{ charCount < minChars ? `Mínimo ${minChars}` : '✓ Listo' }}
                            </span>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <button
                            @click="handleClear"
                            :disabled="isAnalyzing || charCount === 0"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium"
                        >
                            Limpiar
                        </button>
                        
                 
                    </div>

                    <!-- Info text -->
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-6">
                        Usaremos IA para extraer información de tu descripción
                    </p>
                </div>

                <!-- Action Button - STICKY ✨ -->
                <div class="sticky bottom-0 z-10 p-8 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-slate-800 shrink-0">
                    <button
                        @click="handleAnalyze"
                        :disabled="!isValid || isAnalyzing"
                        :class="[
                            'w-full py-3 px-6 rounded-lg font-semibold text-lg transition-all duration-300',
                            'flex items-center justify-center gap-2',
                            isValid && !isAnalyzing
                                ? 'bg-linear-to-r from-purple-500 to-purple-600 text-white hover:shadow-lg hover:-translate-y-0.5'
                                : 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed'
                        ]"
                    >
                        <svg 
                            v-if="!isAnalyzing"
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
                        
                        <div 
                            v-else
                            class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"
                        />
                        
                        <span>
                            {{ isAnalyzing ? 'Analizando...' : 'Analizar con IA' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Custom scrollbar */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: rgba(107, 114, 128, 0.5) transparent;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: rgba(107, 114, 128, 0.5);
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: rgba(107, 114, 128, 0.7);
}

/* Transiciones */
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

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
