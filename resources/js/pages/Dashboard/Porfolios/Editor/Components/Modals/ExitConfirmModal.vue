<!-- ============================================
   Components/Modals/ExitConfirmModal.vue
   ============================================ -->
   <script setup lang="ts">
    import { AlertCircle, ArrowLeft } from 'lucide-vue-next';

    interface Props {
        isOpen: boolean;
        currentStep: number;
        stepsLength: number;
        hasUnsavedChanges: boolean;
    }
    
    withDefaults(defineProps<Props>(), {});
    
    defineEmits<{
        cancel: [];
        confirm: [];
    }>();
    </script>
    
    <template>
        <Transition name="modal-fade">
            <div v-if="isOpen"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
                @click="$emit('cancel')">
                <Transition name="modal-scale">
                    <div v-show="true" class="w-full max-w-md transform rounded-2xl bg-white shadow-2xl" @click.stop>
                        <!-- Header del modal -->
                        <div class="border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-5">
                            <div class="flex items-start space-x-3">
                                <div class="rounded-full bg-amber-100 p-2.5">
                                    <AlertCircle class="h-5 w-5 text-amber-600" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ currentStep === stepsLength ? '¿Terminar edición?' : '¿Salir sin terminar?' }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ hasUnsavedChanges
                                            ? 'Tienes cambios sin guardar'
                                            : 'Tu portafolio está actualizado' }}
                                    </p>
                                </div>
                            </div>
                        </div>
    
                        <!-- Contenido -->
                        <div class="px-6 py-4">
                            <div v-if="hasUnsavedChanges" class="rounded-lg bg-blue-50 p-3 border border-blue-100">
                                <p class="text-sm text-blue-900">
                                    <span class="font-semibold">⚠️ Cambios pendientes:</span> Se guardarán automáticamente antes de salir.
                                </p>
                            </div>
                            <div v-else class="rounded-lg bg-green-50 p-3 border border-green-100">
                                <p class="text-sm text-green-900">
                                    <span class="font-semibold">✓ Actualizado:</span> Tu portafolio está al día. Puedes salir con seguridad.
                                </p>
                            </div>
                        </div>
    
                        <!-- Acciones -->
                        <div class="border-t border-gray-100 bg-gray-50 px-6 py-4">
                            <div class="grid grid-cols-2 gap-3">
                                <button @click="$emit('cancel')"
                                    class="flex items-center justify-center space-x-2 rounded-lg border border-gray-300 px-4 py-2.5 text-gray-700 font-medium transition-all duration-200 hover:bg-gray-100">
                                    <ArrowLeft class="h-4 w-4" />
                                    <span>Volver</span>
                                </button>
                                <button @click="$emit('confirm')"
                                    class="flex items-center justify-center space-x-2 rounded-lg bg-red-500 px-4 py-2.5 text-white font-medium transition-all duration-200 hover:bg-red-600">
                                    <span>Salir ahora</span>
                                </button>
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
        transition: all 0.3s ease;
    }
    
    .modal-scale-enter-from,
    .modal-scale-leave-to {
        opacity: 0;
        transform: scale(0.95);
    }
    </style>