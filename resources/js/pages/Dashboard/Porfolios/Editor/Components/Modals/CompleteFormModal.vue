<!-- ============================================
   Components/Modals/CompleteFormModal.vue
   ============================================ -->
   <script setup lang="ts">
    import { CheckCircle, Loader2, ArrowLeft } from 'lucide-vue-next';
    import { Transition } from 'vue';
    
    interface Props {
        isOpen: boolean;
        isCompleting: boolean;
        allStepsCompleted: boolean;
        validStepsCount: number;
        stepsLength: number;
        completionMessage: string;
    }
    
    withDefaults(defineProps<Props>(), {});
    
    defineEmits<{
        close: [];
        complete: [];
    }>();
    </script>
    
    <template>
        <Transition name="modal-fade">
            <div v-if="isOpen"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
                @click="$emit('close')">
                <Transition name="modal-scale">
                    <div class="w-full max-w-md transform rounded-2xl bg-white shadow-2xl" @click.stop>
                        <!-- Header del modal -->
                        <div class="border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-5">
                            <div class="flex items-start space-x-3">
                                <div class="rounded-full bg-green-100 p-2.5">
                                    <CheckCircle class="h-5 w-5 text-green-600" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        ¿Finalizar formulario?
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ allStepsCompleted ? '¡Todos los pasos completados!' : 'Completa todos los pasos para finalizar' }}
                                    </p>
                                </div>
                            </div>
                        </div>
    
                        <!-- Contenido -->
                        <div class="px-6 py-4">
                            <div class="rounded-lg bg-blue-50 p-3 border border-blue-100 mb-4">
                                <p class="text-sm text-blue-900">
                                    <span class="font-semibold">✓ Pasos completados:</span> {{ validStepsCount }}/{{ stepsLength }}
                                </p>
                            </div>
                            <div class="rounded-lg bg-green-50 p-3 border border-green-100">
                                <p class="text-sm text-green-900">
                                    <span class="font-semibold">📋 Estado:</span> Completaste todos los pasos del formulario. Tu portafolio ya está preparado.
                                </p>
                            </div>
                        </div>
    
                        <!-- Acciones -->
                        <div class="border-t border-gray-100 bg-gray-50 px-6 py-4">
                            <div class="grid grid-cols-2 gap-3">
                                <button @click="$emit('close')" :disabled="isCompleting"
                                    class="flex items-center justify-center space-x-2 rounded-lg border border-gray-300 px-4 py-2.5 text-gray-700 font-medium transition-all duration-200 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <ArrowLeft class="h-4 w-4" />
                                    <span>Volver</span>
                                </button>
                                <button @click="$emit('complete')" :disabled="isCompleting || !allStepsCompleted"
                                    class="flex items-center justify-center space-x-2 rounded-lg bg-green-500 px-4 py-2.5 text-white font-medium transition-all duration-200 hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <Loader2 v-if="isCompleting" class="h-4 w-4 animate-spin flex-shrink-0" />
                                    <CheckCircle v-else class="h-4 w-4" />
                                    <span>{{ isCompleting ? 'Finalizando...' : 'Finalizar' }}</span>
                                </button>
                            </div>
                        </div>
    
                        <!-- Mensaje de estado -->
                        <Transition name="fade">
                            <div v-if="completionMessage" class="border-t border-gray-100 bg-gray-50 px-6 py-3">
                                <p class="text-xs text-center font-medium" :class="[
                                    completionMessage.includes('éxito') ? 'text-green-600' :
                                        completionMessage.includes('Error') ? 'text-red-600' :
                                            'text-gray-600'
                                ]">
                                    {{ completionMessage }}
                                </p>
                            </div>
                        </Transition>
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
        transition: all 0.3s ease;
    }
    
    .modal-scale-enter-from,
    .modal-scale-leave-to {
        opacity: 0;
        transform: scale(0.95);
    }
    
    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.2s ease;
    }
    
    .fade-enter-from,
    .fade-leave-to {
        opacity: 0;
    }
    </style>
    
    