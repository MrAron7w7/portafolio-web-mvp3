<!-- ============================================
   Components/Layout/EditorSidebar.vue
   SOLO HISTORIAL - ICONOS TAMBIÉN RESPETAN enabled
   ============================================ -->
<script setup lang="ts">
import { Check, Globe, Lock, X } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    steps: any[];
    currentStep: number;
    progress: number;
    portfolio: any;
    isPortfolioPublic: boolean;
    isCurrentStepValid: boolean;
    formData: any;
    persistentStepValidation: Record<number, boolean>;
}

const props = withDefaults(defineProps<Props>(), {});

defineEmits<{
    goToStep: [stepId: number];
}>();

// ============================================
// COMPUTED - Conteos por sección
// ============================================
const sectionCounts = computed(() => {
    return {
        1: 0,
        2: 0,
        3: (props.formData?.education?.length || 0),
        4: (props.formData?.languages?.length || 0),
        5: (props.formData?.experience?.length || 0),
        6: ((props.formData?.skills?.technical?.length || 0) + (props.formData?.skills?.soft?.length || 0)),
        7: (props.formData?.projects?.length || 0),
        8: 0,
        9: 0,
    };
});

// ============================================
// COMPUTED - Obtener estado de validación del paso
// ============================================
const getStepStatus = (stepId: number, step: any) => {
    // Si es el paso actual, usar el valor en vivo
    if (stepId === props.currentStep) {
        return {
            isValid: props.isCurrentStepValid,
            message: props.isCurrentStepValid ? 'Completado' : 'Incompleto',
            isCompleted: props.isCurrentStepValid
        };
    }

    // ✨ CRÍTICO: Para otros pasos, SIEMPRE lee del historial, IGNORA step.completed
    const persistentStatus = props.persistentStepValidation?.[stepId];

    return {
        isValid: persistentStatus ?? false,
        message: persistentStatus ? 'Completado' : 'Incompleto',
        isCompleted: persistentStatus ?? false
    };
};

const shouldShowIndicator = (stepId: number, step: any) => {
    // No mostrar en el paso actual
    if (stepId === props.currentStep) {
        return false;
    }

    // ✨ CRÍTICO: Solo mostrar indicador si:
    // 1. El paso está habilitado
    // 2. Y tiene estado registrado en el historial
    return step.enabled && props.persistentStepValidation?.[stepId] !== undefined;
};

// ============================================
// COMPUTED - Color del indicador
// ============================================
const getIndicatorColor = (stepId: number, step: any) => {
    const status = getStepStatus(stepId, step);
    return status.isValid ? 'bg-green-500' : 'bg-orange-500';
};

const getTextColor = (stepId: number, step: any) => {
    const status = getStepStatus(stepId, step);
    return status.isValid ? 'text-green-600' : 'text-orange-600';
};
</script>

<template>
    <div class="lg:col-span-3">
        <div class="sticky top-8 rounded-2xl border border-gray-200/60 bg-white p-6 shadow-xs">
            <!-- Lista de pasos -->
            <nav class="space-y-2">
                <div v-for="(step, index) in steps" :key="step.id"
                    class="group flex items-start space-x-4 transition-opacity duration-200"
                    :class="{ 'opacity-60': !step.enabled }">
                    <!-- Línea vertical -->
                    <div class="flex flex-col items-center">
                        <div :class="[
                            'flex h-8 w-8 items-center justify-center rounded-full border-2 text-sm font-semibold transition-all duration-300 relative',
                            !step.enabled
                                ? 'border-gray-300 bg-gray-100 text-gray-400'
                                : currentStep === step.id
                                    ? 'border-[#005aeb] bg-[#005aeb] text-white scale-110 shadow-lg'
                                    : getStepStatus(step.id, step).isCompleted
                                        ? 'border-green-500 bg-green-50 text-green-600'
                                        : shouldShowIndicator(step.id, step) && !getStepStatus(step.id, step).isCompleted
                                            ? 'border-orange-500 bg-orange-50 text-orange-600'
                                            : 'border-gray-300 bg-white text-gray-400',
                        ]">
                            <!-- ✨ Iconos: SOLO si está habilitado y es paso actual O tiene indicador -->
                            <!-- CÍRCULO CON ICONOS/NÚMEROS -->

                            <!-- 1. CASO CHECK: Solo si está habilitado, completado y no es el actual -->
                            <Check
                                v-if="step.enabled && getStepStatus(step.id, step).isCompleted && step.id !== currentStep"
                                class="h-4 w-4" />

                            <!-- 2. CASO ERROR (X): Solo si está habilitado, incompleto y debe mostrar error -->
                            <X v-else-if="step.enabled && shouldShowIndicator(step.id, step) && !getStepStatus(step.id, step).isCompleted"
                                class="h-4 w-4 text-orange-500" />

                            <!-- 3. RESTO DE CASOS (Incluye Deshabilitados): Muestra el número -->
                            <!-- Aquí el 'text-gray-400' del padre debería actuar, pero si quieres asegurarlo, usa la clase condicional aquí -->
                            <span v-else :class="{ 'text-gray-400': !step.enabled }">
                                {{ step.id }}
                            </span>


                            <!-- Badge de conteo -->
                            <div v-if="[3, 4, 5, 6, 7].includes(step.id) && sectionCounts[step.id as keyof typeof sectionCounts] > 0"
                                :class="[
                                    'absolute -top-2 -right-2 flex items-center justify-center rounded-full text-xs font-bold',
                                    'w-5 h-5 text-white',
                                    !step.enabled
                                        ? 'bg-gray-400'
                                        : currentStep === step.id
                                            ? 'bg-[#005aeb]'
                                            : getStepStatus(step.id, step).isCompleted
                                                ? 'bg-green-500'
                                                : 'bg-orange-400',
                                ]">
                                {{ sectionCounts[step.id as keyof typeof sectionCounts] }}
                            </div>
                        </div>
                        <!-- Línea conectora -->
                        <div v-if="index < steps.length - 1" :class="[
                            'mt-2 h-8 w-0.5 transition-all duration-300',
                            !step.enabled
                                ? 'bg-gray-300'
                                : getStepStatus(step.id, step).isCompleted
                                    ? 'bg-green-500'
                                    : currentStep > step.id
                                        ? 'bg-[#005aeb]'
                                        : 'bg-gray-200',
                        ]"></div>
                    </div>

                    <!-- Texto del paso -->
                    <div class="min-w-0 flex-1 pb-6">
                        <button @click="$emit('goToStep', step.id)"
                        :disabled="!step.enabled"

                            class="text-left disabled:cursor-not-allowed w-full">
                            <div class="flex items-center justify-between">
                                <p :class="[
                                    'font-medium transition-colors duration-200',
                                    !step.enabled
                                        ? 'text-gray-400'
                                        : currentStep === step.id
                                            ? 'text-[#005aeb]'
                                            : getStepStatus(step.id, step).isCompleted
                                                ? 'text-gray-900'
                                                : 'text-gray-900',
                                ]">
                                    {{ step.title }}
                                </p>
                                <!-- Texto de conteo en la etiqueta -->
                                <span
                                    v-if="[3, 4, 5, 6, 7].includes(step.id) && sectionCounts[step.id as keyof typeof sectionCounts] > 0"
                                    :class="[
                                        'text-xs font-semibold ml-2 flex-shrink-0',
                                        !step.enabled
                                            ? 'text-gray-400'
                                            : currentStep === step.id
                                                ? 'text-[#005aeb]'
                                                : getStepStatus(step.id, step).isCompleted
                                                    ? 'text-green-600'
                                                    : 'text-gray-600',
                                    ]">
                                    ({{ sectionCounts[step.id as keyof typeof sectionCounts] }})
                                </span>
                            </div>

                            <!-- Indicador de validación (PASOS VISITADOS - NO ACTUALES) -->
                            <!-- ✨ Lee SOLO del historial - SOLO SI ESTÁ HABILITADO -->
                            <div v-if="step.enabled && shouldShowIndicator(step.id, step)"
                                class="mt-1 flex items-center space-x-1">
                                <div :class="[
                                    'h-2 w-2 rounded-full transition-colors duration-200',
                                    getIndicatorColor(step.id, step)
                                ]"></div>
                                <p :class="[
                                    'text-xs transition-colors duration-200',
                                    getTextColor(step.id, step)
                                ]">
                                    {{ getStepStatus(step.id, step).message }}
                                </p>
                            </div>

                            <!-- Indicador de validación (PASO ACTUAL) -->
                            <!-- ✨ Muestra estado en tiempo real mientras editas -->
                            <div v-if="currentStep === step.id" class="mt-1 flex items-center space-x-1">
                                <div :class="[
                                    'h-2 w-2 rounded-full transition-colors duration-200',
                                    isCurrentStepValid ? 'bg-green-500' : 'bg-orange-500'
                                ]"></div>
                                <p :class="[
                                    'text-xs transition-colors duration-200',
                                    isCurrentStepValid ? 'text-green-600' : 'text-orange-600'
                                ]">
                                    {{ isCurrentStepValid ? 'Completado' : 'Incompleto' }}
                                </p>
                            </div>
                        </button>
                    </div>
                </div>
            </nav>

            <!-- Progreso y enlaces -->
            <div class="mt-4 space-y-4 border-t border-gray-200 pt-6">
                <!-- Barra de progreso -->
                <div>
                    <div class="mb-2 flex justify-between text-sm text-gray-600">
                        <span>Progreso total</span>
                        <span class="font-medium text-[#005aeb]">{{ progress }}%</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-200">
                        <div class="h-2 rounded-full bg-[#005aeb] transition-all duration-500"
                            :style="{ width: progress + '%' }"></div>
                    </div>
                </div>

                <!-- Información del portfolio -->
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-900">
                        Plantilla: <span class="text-[#005aeb]">{{ portfolio.template_type }}</span>
                    </p>
                    <p class="mt-2 text-sm font-medium text-gray-900 flex items-center">
                        Estado:
                        <span :class="[
                            'flex items-center ml-2 gap-1',
                            isPortfolioPublic ? 'text-blue-600' : 'text-gray-600'
                        ]">
                            <Globe v-if="isPortfolioPublic" class="h-4 w-4 flex-shrink-0" />
                            <Lock v-else class="h-4 w-4 flex-shrink-0" />
                            <span>{{ isPortfolioPublic ? 'Público' : 'Privado' }}</span>
                        </span>
                    </p>
                    <p class="mt-1 text-xs text-gray-600">
                        ID: {{ portfolio.id }}
                    </p>
                </div>

                <!-- Enlaces legales -->
                <div class="space-y-2 text-xs text-gray-500">
                    <div class="flex space-x-4">
                        <a href="#" class="transition-colors duration-200 hover:text-gray-700">Condiciones</a>
                        <a href="#" class="transition-colors duration-200 hover:text-gray-700">Privacidad</a>
                        <a href="#" class="transition-colors duration-200 hover:text-gray-700">Contacto</a>
                    </div>
                    <p class="text-gray-400">
                        &copy; 2024 PortafolioAI. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Estilos heredados */
</style>