<template>
    <div>
        <!-- Título y Subtítulo -->
        <div class="mb-8">
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">Sobre Mí</h1>
            <p class="text-lg text-gray-600">
                Cuéntanos un poco más sobre ti, tu trayectoria y tus objetivos.
            </p>
        </div>

        <div class="space-y-6">
            <!-- Sección de descripción - Se esconde cuando está generando -->
            <transition name="fade-slide" mode="out-in">
                <div v-if="!isAutoCompleting" key="description-section">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Cuéntanos un poco más sobre ti, tu trayectoria y tus objetivos.
                    </label>
                    <textarea
                        v-model="formData.description"
                        @blur="validateField('description', formData.description, true)"
                        placeholder="Una descripción más detallada para la sección 'Sobre mí'. El resumen se autocompletará automáticamente."
                        rows="6"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        :class="{ 'border-red-500': errors.description }"
                    />
                    <p v-if="errors.description" class="text-red-500 text-sm mt-1">
                        {{ errors.description }}
                    </p>
                    <p class="text-gray-500 text-xs mt-1">
                        Mínimo 50 caracteres. Escribe para que el resumen se genere automáticamente.
                    </p>
                </div>
            </transition>

            <!-- Animación de carga de IA -->
            <transition name="fade-slide" mode="out-in">
                <div v-if="isAutoCompleting" key="loading-section" class="py-12">
                    <div class="flex flex-col items-center justify-center">
                        <!-- Animación de ondas IA -->
                        <div class="mb-6 relative w-24 h-24">
                            <div class="absolute inset-0 rounded-full border-2 border-blue-600 animate-ping opacity-75"></div>
                            <div class="absolute inset-0 rounded-full border-2 border-blue-400 animate-pulse"></div>
                            <div class="absolute inset-2 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                                <span class="text-2xl">✨</span>
                            </div>
                        </div>

                        <!-- Texto con animación -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            Generando tu resumen
                        </h3>
                        <p class="text-gray-600 text-sm mb-6 text-center max-w-xs">
                            Estamos analizando tu descripción con IA para crear un resumen profesional...
                        </p>

                        <!-- Barras de carga animadas -->
                        <div class="w-64 space-y-2">
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full animate-loading"></div>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden" style="animation-delay: 0.2s">
                                <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full animate-loading"></div>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden" style="animation-delay: 0.4s">
                                <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full animate-loading"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Sección de resumen con autocompletado -->
            <transition name="fade-slide" mode="out-in">
                <div v-if="!isAutoCompleting" key="summary-section">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Resumen (generado por IA)
                        </label>
                        <button
                            @click="handleManualAutoComplete"
                            :disabled="isAutoCompleting || !formData.description || formData.description.trim().length < 50"
                            class="px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-all transform hover:scale-105 active:scale-95"
                        >
                            {{ isAutoCompleting ? 'Generando...' : '✨ Generar resumen' }}
                        </button>
                    </div>

                    <textarea
                        v-model="formData.summary"
                        @blur="validateField('summary', formData.summary, true)"
                        placeholder="Este texto se autocompletará con IA basado en tu descripción detallada."
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        :class="{ 'border-red-500': errors.summary, 'border-green-500 bg-green-50': formData.summary && !errors.summary }"
                    />
                    <p v-if="errors.summary" class="text-red-500 text-sm mt-1">
                        {{ errors.summary }}
                    </p>

                    <!-- Mensaje de error con animación -->
                    <transition name="fade-slide">
                        <div v-if="autoCompleteError" class="flex items-center text-red-600 text-sm mt-3 bg-red-50 p-3 rounded-lg border border-red-200">
                            <svg class="h-4 w-4 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ autoCompleteError }}
                        </div>
                    </transition>

                    <!-- Indicador de completado -->
                    <transition name="fade-slide">
                        <div v-if="formData.summary && !isAutoCompleting" class="flex items-center text-green-600 text-sm mt-3 bg-green-50 p-3 rounded-lg border border-green-200">
                            <svg class="h-4 w-4 mr-2 flex-shrink-0 animate-pulse" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                            </svg>
                            ¡Resumen generado exitosamente!
                        </div>
                    </transition>
                </div>
            </transition>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAboutValidation } from './Composables/useAboutValidation'
import { useIaAutoComplete } from './Composables/useIaAutoComplete'
import type { AboutData } from './Composables/useAboutValidation'

// Props y emits
const props = defineProps<{
    modelValue: AboutData
}>()

const emit = defineEmits<{
    'update:modelValue': [value: AboutData]
}>()

// Estados locales
const formData = ref<AboutData>(props.modelValue)

// Composables
const { errors, validateField } = useAboutValidation()
const {
    isAutoCompleting,
    autoCompleteError,
    watchDescriptionForAutoComplete,
    generateAutoComplete
} = useIaAutoComplete(
    formData.value,
    (event: string, value: AboutData) => {
        formData.value = value
        emit('update:modelValue', value)
    },
    validateField
)

// Inicializar el watch automático al montar el componente
onMounted(() => {
    watchDescriptionForAutoComplete()
})

// Método para generar manualmente el resumen
const handleManualAutoComplete = async () => {
    await generateAutoComplete(formData.value.description)
}
</script>

<style scoped>
/* Transiciones suaves */
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(10px);
}

.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Animación de barras de carga */
@keyframes loading {
    0% {
        width: 0;
    }
    50% {
        width: 100%;
    }
    100% {
        width: 0;
    }
}

.animate-loading {
    animation: loading 2s ease-in-out infinite;
}

/* Animaciones auxiliares */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes ping {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

.animate-ping {
    animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}
</style>
