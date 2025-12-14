<!-- ============================================
   AIDescriptionModal.vue - FIXES CIERRE + PROPS
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
    const maxChars = 2000;
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
    
    
    const exampleDescription = () => {
        description.value = `Soy un desarrollador full-stack con 5 años de experiencia en Laravel, Vue.js y React. 
He trabajado en proyectos de e-commerce, dashboards administrativos y aplicaciones web responsivas. 
Tengo certificaciones en AWS y experiencia con bases de datos SQL y NoSQL. 
Hablo español, inglés y francés con fluidez. Mis habilidades incluyen desarrollo frontend, backend, 
diseño de APIs REST y gestión de servidores. He liderado equipos de 3-5 personas y tengo experiencia 
en metodologías ágiles.`;
    };
</script>


<template>
    <!-- Modal Container - SIN BACKDROP (compartido desde Edito.vue) -->
    <Transition name="scale">
        <div 
            v-if="isOpen" 
            class="fixed inset-0 flex items-center justify-center z-50 px-4 pointer-events-none"
        >
            <!-- Modal Card - CON pointer-events-auto -->
            <div 
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden pointer-events-auto"
                @click.stop
            >
                <!-- Header -->
                <div class="relative overflow-hidden bg-gradient-to-r from-purple-500 to-purple-600 px-8 py-12 text-white">
                    <!-- Decorative elements -->
                    <div class="absolute top-0 right-0 w-40 h-40 bg-purple-400 rounded-full -mr-20 -mt-20 opacity-20" />
                    
                    <!-- Close button -->
                    <button
                        @click="handleClose"
                        :disabled="isAnalyzing"
                        class="absolute top-4 right-4 p-2 hover:bg-purple-600 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
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

                <!-- Body -->
                <div class="p-8">
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
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <p class="text-sm text-blue-900 dark:text-blue-200">
                            <span class="font-semibold">💡 Consejo:</span> Cuanto más detallado sea tu descripción, mejor será el análisis de IA. Incluye experiencia, habilidades, idiomas, educación, etc.
                        </p>
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
                            placeholder="Comienza escribiendo sobre ti... Por ejemplo: 'Soy desarrollador full-stack con 5 años de experiencia...'"
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
                                    charCount < minChars ? 'text-orange-600' : 'text-green-600'
                                ]"
                            >
                                {{ charCount < minChars ? `Mínimo ${minChars}` : '✓ Listo' }}
                            </span>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 mb-4">
                        <button
                            @click="handleClear"
                            :disabled="isAnalyzing || charCount === 0"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium"
                        >
                            Limpiar
                        </button>
                        
                        <button
                            @click="exampleDescription"
                            :disabled="isAnalyzing"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium"
                        >
                            Ver Ejemplo
                        </button>
                    </div>

                    <!-- Action Button -->
                    <button
                        @click="handleAnalyze"
                        :disabled="!isValid || isAnalyzing"
                        :class="[
                            'w-full py-3 px-6 rounded-lg font-semibold text-lg transition-all duration-300',
                            'flex items-center justify-center gap-2',
                            isValid && !isAnalyzing
                                ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white hover:shadow-lg hover:-translate-y-0.5'
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

                    <!-- Info text -->
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-4">
                        Usaremos IA para extraer información de tu descripción
                    </p>
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

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>