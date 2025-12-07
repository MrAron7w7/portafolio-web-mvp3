<!-- ============================================
   Components/Modals/PublicToggleModal.vue
   ============================================ -->
   <script setup lang="ts">
    import { Globe, Lock, Loader2 } from 'lucide-vue-next';
    import { Transition } from 'vue';
    
    interface Props {
        isOpen: boolean;
        isToggling: boolean;
        isPublic: boolean;
        toggleMessage: string;
    }
    
    withDefaults(defineProps<Props>(), {});
    
    defineEmits<{
        close: [];
        toggle: [];
    }>();
    </script>
    
    <template>
        <Transition name="modal-fade">
            <div v-if="isOpen"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
                @click="$emit('close')">
                <Transition name="modal-scale">
                    <div class="w-full max-w-md transform rounded-2xl bg-white shadow-2xl overflow-hidden" @click.stop>
                        <!-- Header del modal -->
                        <div :class="[
                            'border-b border-gray-100 bg-gradient-to-r px-6 py-5',
                            isPublic
                                ? 'from-blue-50 to-blue-100'
                                : 'from-gray-50 to-gray-100'
                        ]">
                            <div class="flex items-start space-x-3">
                                <div :class="[
                                    'rounded-full p-2.5',
                                    isPublic
                                        ? 'bg-blue-100'
                                        : 'bg-gray-200'
                                ]">
                                    <Globe v-if="isPublic" class="h-5 w-5 text-blue-600" />
                                    <Lock v-else class="h-5 w-5 text-gray-600" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ isPublic ? 'Portafolio Público' : 'Portafolio Privado' }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ isPublic
                                            ? 'Tu portafolio es visible para todos'
                                            : 'Tu portafolio es visible solo para ti' }}
                                    </p>
                                </div>
                            </div>
                        </div>
    
                        <!-- Contenido -->
                        <div class="px-6 py-4">
                            <div v-if="isPublic" class="rounded-lg bg-blue-50 p-3 border border-blue-100">
                                <p class="text-sm text-blue-900">
                                    <span class="font-semibold">Público:</span> Cualquiera con el enlace puede ver tu portafolio.
                                </p>
                            </div>
                            <div v-else class="rounded-lg bg-gray-50 p-3 border border-gray-200">
                                <p class="text-sm text-gray-900">
                                    <span class="font-semibold">Privado:</span> Solo tú puedes ver tu portafolio en el editor.
                                </p>
                            </div>
                        </div>
    
                        <!-- Acciones -->
                        <div class="border-t border-gray-100 bg-gray-50 px-6 py-4">
                            <div class="grid grid-cols-2 gap-3">
                                <button @click="$emit('close')" :disabled="isToggling"
                                    class="flex items-center justify-center space-x-2 rounded-lg border border-gray-300 px-4 py-2.5 text-gray-700 font-medium transition-all duration-200 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span>Cancelar</span>
                                </button>
                                <button @click="$emit('toggle')" :disabled="isToggling" :class="[
                                    'flex items-center justify-center space-x-2 rounded-lg px-4 py-2.5 text-white font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed',
                                    isPublic
                                        ? 'bg-gray-500 hover:bg-gray-600'
                                        : 'bg-blue-500 hover:bg-blue-600'
                                ]">
                                    <Loader2 v-if="isToggling" class="h-4 w-4 animate-spin flex-shrink-0" />
                                    <span>{{ isToggling ? 'Cambiando...' : (isPublic ? 'Hacer Privado' : 'Hacer Público') }}</span>
                                </button>
                            </div>
                        </div>
    
                        <!-- Mensaje de estado -->
                        <Transition name="fade">
                            <div v-if="toggleMessage" class="border-t border-gray-100 bg-gray-50 px-6 py-3">
                                <p class="text-xs text-center font-medium text-green-600">
                                    {{ toggleMessage }}
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