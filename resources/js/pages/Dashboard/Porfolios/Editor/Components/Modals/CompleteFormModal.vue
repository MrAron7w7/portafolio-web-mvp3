<script setup lang="ts">
    import { CheckCircle, Loader2, ArrowLeft, AlertTriangle, ArrowRight, Save, Check, Zap } from 'lucide-vue-next';
    import { ref, watch } from 'vue';
    
    interface Props {
        isOpen: boolean;
        isCompleting: boolean;
        allStepsCompleted: boolean;
        validStepsCount: number;
        stepsLength: number;
        completionMessage: string;
        incompleteSteps?: any[];
    }
    
    const props = withDefaults(defineProps<Props>(), {
        incompleteSteps: () => []
    });
    
    defineEmits<{
        close: [];
        complete: [];
        saveAndExit: [];
    }>();

    // ============================================
    // ESTADOS - CONFIRMACIÓN Y REDIRECCIÓN
    // ============================================
    const showCompletionConfirm = ref(false);
    const redirectCountdown = ref(3);
    const isRedirecting = ref(false);
    let countdownInterval: ReturnType<typeof setInterval> | null = null;

    // ============================================
    // WATCHER - DETECTAR COMPLETACIÓN EXITOSA
    // ============================================
    watch(
        () => props.isCompleting,
        (newVal) => {
            // Cuando termina la completación (isCompleting pasa de true a false)
            if (!newVal && showCompletionConfirm.value && !isRedirecting.value) {
                console.log('✅ Completación exitosa detectada');
                // ⏳ ESPERAR A QUE TRANSICIÓN TERMINE (300ms = modal-scale duration)
                setTimeout(() => {
                    isRedirecting.value = true;
                    startRedirectCountdown();
                }, 320);
            }
        }
    );

    // ============================================
    // WATCHER - LIMPIAR INTERVAL AL DESMONTAR
    // ============================================
    watch(
        () => props.isOpen,
        (newVal) => {
            if (!newVal && countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
                resetState();
            }
        }
    );

    // ============================================
    // FUNCIONES - REDIRECCIÓN
    // ============================================
    const startRedirectCountdown = () => {
        console.log('⏳ Iniciando countdown de redirección...');
        redirectCountdown.value = 3;
        
        countdownInterval = setInterval(() => {
            redirectCountdown.value--;
            console.log(`⏱️ Segundos restantes: ${redirectCountdown.value}`);
            
            if (redirectCountdown.value <= 0) {
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                    countdownInterval = null;
                }
                console.log('🚀 Redirigiendo a dashboard...');
                window.location.href = '/dashboard';
            }
        }, 1000);
    };

    const resetState = () => {
        showCompletionConfirm.value = false;
        isRedirecting.value = false;
        redirectCountdown.value = 3;
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
    };

    // ============================================
    // HANDLERS - COMPLETAR FORMULARIO
    // ============================================
    const handleCompleteClick = () => {
        console.log('🎯 Usuario hace click en Finalizar');
        showCompletionConfirm.value = true;
        // Emit 'complete' dispara completeForm() en Editor.vue
        // El watcher detectará cuando isCompleting cambie a false
    };

    const handleManualRedirect = () => {
        console.log('⏭️ Usuario hace click en "Volver al Dashboard Ahora"');
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
        window.location.href = '/dashboard';
    };

    const handleBackToEditor = () => {
        console.log('↩️ Usuario vuelve al editor');
        resetState();
    };
</script>

<template>
    <Transition name="modal-fade">
        <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm" @click="$emit('close')">
            
            <!-- ✨ MODAL - CONFIRMACIÓN DE ÉXITO (TRANSICIÓN SEPARADA) ✨ -->
            <Transition name="modal-scale" mode="out-in">
                <div v-if="isRedirecting" key="success" class="w-full max-w-md overflow-hidden rounded-2xl bg-white dark:bg-slate-900 shadow-2xl" @click.stop>
                    
                    <!-- HEADER - ÉXITO -->
                    <div class="border-b border-green-100 dark:border-emerald-900/30 bg-linear-to-r from-green-50 to-emerald-50 dark:from-emerald-900/10 dark:to-green-900/10 px-6 py-8 text-center">
                        
                        <!-- Icono Animado -->
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-emerald-900/20">
                            <CheckCircle class="h-8 w-8 text-green-600 dark:text-emerald-400 animate-bounce" />
                        </div>

                        <!-- Título -->
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                            ¡Portafolio Finalizado!
                        </h3>
                        
                        <!-- Descripción -->
                        <p class="mt-2 text-sm text-gray-600 dark:text-slate-400">
                            Tu portafolio ha sido completado exitosamente y está listo para ser compartido.
                        </p>
                    </div>

                    <!-- CONTENIDO - CONFIRMACIÓN -->
                    <div class="p-6">
                        
       
                        <!-- Redirección Automática -->
                        <div class="mb-6 rounded-lg bg-amber-50 dark:bg-amber-900/10 p-4 border border-amber-100 dark:border-amber-900/30">
                            <p class="text-center text-sm text-amber-900 dark:text-amber-400">
                                <span class="font-semibold">Redirigiendo en {{ redirectCountdown }}s...</span>
                            </p>
                            <div class="mt-3 h-1 w-full overflow-hidden rounded-full bg-amber-200 dark:bg-amber-900/40">
                                <div 
                                    class="h-full bg-amber-500 dark:bg-amber-400 transition-all duration-1000"
                                    :style="{ width: `${(redirectCountdown / 3) * 100}%` }"
                                />
                            </div>
                        </div>

                        <!-- Botón Manual -->
                        <button 
                            @click="handleManualRedirect"
                            class="w-full rounded-lg bg-[#005aeb] dark:bg-blue-600 px-4 py-2.5 font-medium text-white transition-colors hover:bg-[#0048c4] dark:hover:bg-blue-500 flex items-center justify-center gap-2"
                        >
                            <ArrowRight class="h-4 w-4" />
                            Volver al Dashboard Ahora
                        </button>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- MODAL - CONTENIDO ORIGINAL (PRE-COMPLETACIÓN) -->
                <!-- ============================================ -->
                <div v-else key="form" class="w-full max-w-md overflow-hidden rounded-2xl bg-white dark:bg-slate-900 shadow-xl" @click.stop>
                    
                    <!-- HEADER -->
                    <div class="border-b px-6 py-6 text-center"
                         :class="allStepsCompleted ? 'bg-linear-to-r from-green-50 to-emerald-50 dark:from-emerald-900/10 dark:to-green-900/10 border-green-100 dark:border-emerald-900/20' : 'bg-orange-50 dark:bg-orange-950/20 border-orange-100 dark:border-orange-900/20'">
                        
                        <!-- Icono -->
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full"
                             :class="allStepsCompleted ? 'bg-green-100 dark:bg-emerald-900/20' : 'bg-orange-100 dark:bg-orange-900/20'">
                            <CheckCircle v-if="allStepsCompleted" class="h-6 w-6 text-green-600 dark:text-emerald-400" />
                            <AlertTriangle v-else class="h-6 w-6 text-orange-600 dark:text-orange-400" />
                        </div>

                        <!-- Título y Descripción -->
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ allStepsCompleted ? '¿Finalizar formulario?' : 'Pasos Incompletos' }}
                        </h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-slate-400">
                            {{ allStepsCompleted 
                                ? 'Has completado toda la información requerida.' 
                                : 'No puedes finalizar el portafolio porque hay pasos requeridos sin completar.' 
                            }}
                        </p>
                    </div>

                    <!-- CONTENIDO -->
                    <div class="p-6">
                        
                        <!-- CASO A: INCOMPLETO (Diseño tipo Alerta) -->
                        <div v-if="!allStepsCompleted">
                            <p class="mb-3 text-sm font-medium text-gray-700 dark:text-slate-300">Pasos pendientes:</p>
                            <ul class="mb-6 space-y-2 rounded-lg bg-gray-50 dark:bg-slate-800/50 p-4 border border-gray-100 dark:border-slate-800">
                                <li v-for="step in incompleteSteps" :key="step.id" 
                                    class="flex items-center text-sm text-gray-600 dark:text-slate-400">
                                    <span class="mr-2 flex h-5 w-5 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/20 text-xs font-bold text-orange-600 dark:text-orange-400">
                                        {{ step.id }}
                                    </span>
                                    {{ step.title }}
                                </li>
                            </ul>

                            <!-- Botones de Acción Incompleto -->
                            <div class="flex flex-col gap-3">
                                <button @click="$emit('close')"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#005aeb] dark:bg-blue-600 px-4 py-2.5 font-medium text-white transition-colors hover:bg-[#0048c4] dark:hover:bg-blue-500">
                                    <ArrowRight class="h-4 w-4" />
                                    Seguir editando
                                </button>
                                
                                <button @click="$emit('saveAndExit')"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 font-medium text-gray-700 dark:text-slate-300 transition-colors hover:bg-gray-50 dark:hover:bg-slate-750">
                                    <Save class="h-4 w-4" />
                                    Guardar progreso y salir
                                </button>
                            </div>
                        </div>

                        <!-- CASO B: COMPLETO (Diseño Éxito) -->
                        <div v-else>
                            <div class="mb-6 space-y-4">
                                <div class="rounded-lg bg-blue-50 dark:bg-blue-900/10 p-4 border border-blue-100 dark:border-blue-900/20 flex justify-between items-center">
                                    <span class="text-sm font-medium text-blue-900 dark:text-blue-400">Pasos completados</span>
                                    <span class="text-sm font-bold text-blue-700 dark:text-blue-300">{{ validStepsCount }}/{{ stepsLength }}</span>
                                </div>
                                <div class="rounded-lg bg-green-50 dark:bg-emerald-900/10 p-4 border border-green-100 dark:border-emerald-900/20 text-sm text-green-800 dark:text-emerald-400">
                                    ¡Todo listo! Tu portafolio está preparado para ser publicado.
                                </div>
                            </div>

                            <!-- Botones de Acción Completo -->
                            <div class="grid grid-cols-2 gap-3">
                                <button @click="handleBackToEditor()" @click.stop="$emit('close')" :disabled="isCompleting"
                                    class="flex items-center justify-center space-x-2 rounded-lg border border-gray-300 dark:border-slate-700 px-4 py-2.5 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-50 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                    <ArrowLeft class="h-4 w-4" />
                                    <span>Volver</span>
                                </button>

                                <button @click="handleCompleteClick()" @click.stop="$emit('complete')" :disabled="isCompleting"
                                    class="flex items-center justify-center space-x-2 rounded-lg bg-green-500 dark:bg-emerald-600 px-4 py-2.5 text-white font-medium hover:bg-green-600 dark:hover:bg-emerald-500 disabled:opacity-70 disabled:cursor-not-allowed transition-all">
                                    <Loader2 v-if="isCompleting" class="h-4 w-4 animate-spin" />
                                    <CheckCircle v-else class="h-4 w-4" />
                                    <span>{{ isCompleting ? 'Finalizando...' : 'Finalizar' }}</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}
.modal-scale-enter-active,
.modal-scale-leave-active {
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-scale-enter-from,
.modal-scale-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>
