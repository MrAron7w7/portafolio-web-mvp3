<script setup lang="ts">
    import { CheckCircle, Loader2, ArrowLeft, AlertTriangle, ArrowRight, Save } from 'lucide-vue-next';
    
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
    </script>
    
    <template>
        <Transition name="modal-fade">
            <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm" @click="$emit('close')">
                <Transition name="modal-scale">
                    <div v-show="true" class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl" @click.stop>
                        
                        <!-- HEADER -->
                        <div class="border-b px-6 py-6 text-center"
                             :class="allStepsCompleted ? 'bg-gradient-to-r from-green-50 to-emerald-50 border-green-100' : 'bg-orange-50 border-orange-100'">
                            
                            <!-- Icono -->
                            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full"
                                 :class="allStepsCompleted ? 'bg-green-100' : 'bg-orange-100'">
                                <CheckCircle v-if="allStepsCompleted" class="h-6 w-6 text-green-600" />
                                <AlertTriangle v-else class="h-6 w-6 text-orange-600" />
                            </div>
    
                            <!-- Título y Descripción -->
                            <h3 class="text-lg font-bold text-gray-900">
                                {{ allStepsCompleted ? '¿Finalizar formulario?' : 'Pasos Incompletos' }}
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">
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
                                <p class="mb-3 text-sm font-medium text-gray-700">Pasos pendientes:</p>
                                <ul class="mb-6 space-y-2 rounded-lg bg-gray-50 p-4 border border-gray-100">
                                    <li v-for="step in incompleteSteps" :key="step.id" 
                                        class="flex items-center text-sm text-gray-600">
                                        <span class="mr-2 flex h-5 w-5 items-center justify-center rounded-full bg-orange-100 text-xs font-bold text-orange-600">
                                            {{ step.id }}
                                        </span>
                                        {{ step.title }}
                                    </li>
                                </ul>
    
                                <!-- Botones de Acción Incompleto -->
                                <div class="flex flex-col gap-3">
                                    <button @click="$emit('close')"
                                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#005aeb] px-4 py-2.5 font-medium text-white transition-colors hover:bg-[#0048c4]">
                                        <ArrowRight class="h-4 w-4" />
                                        Seguir editando
                                    </button>
                                    
                                    <button @click="$emit('saveAndExit')"
                                        class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 font-medium text-gray-700 transition-colors hover:bg-gray-50">
                                        <Save class="h-4 w-4" />
                                        Guardar progreso y salir
                                    </button>
                                </div>
                            </div>
    
                            <!-- CASO B: COMPLETO (Diseño Éxito) -->
                            <div v-else>
                                <div class="mb-6 space-y-4">
                                    <div class="rounded-lg bg-blue-50 p-4 border border-blue-100 flex justify-between items-center">
                                        <span class="text-sm font-medium text-blue-900">Pasos completados</span>
                                        <span class="text-sm font-bold text-blue-700">{{ validStepsCount }}/{{ stepsLength }}</span>
                                    </div>
                                    <div class="rounded-lg bg-green-50 p-4 border border-green-100 text-sm text-green-800">
                                        ¡Todo listo! Tu portafolio está preparado para ser publicado.
                                    </div>
                                </div>
    
                                <!-- Botones de Acción Completo -->
                                <div class="grid grid-cols-2 gap-3">
                                    <button @click="$emit('close')" :disabled="isCompleting"
                                        class="flex items-center justify-center space-x-2 rounded-lg border border-gray-300 px-4 py-2.5 text-gray-700 font-medium hover:bg-gray-50">
                                        <ArrowLeft class="h-4 w-4" />
                                        <span>Volver</span>
                                    </button>
    
                                    <button @click="$emit('complete')" :disabled="isCompleting"
                                        class="flex items-center justify-center space-x-2 rounded-lg bg-green-500 px-4 py-2.5 text-white font-medium hover:bg-green-600 disabled:opacity-70">
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
    