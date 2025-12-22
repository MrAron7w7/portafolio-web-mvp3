<!-- ============================================
   Components/Layout/EditorHeader.vue
   ============================================ -->
   <script setup lang="ts">
    import {
        MoreVertical,
        Eye,
        Save,
        Check,
        Loader2,
        Globe,
        Lock,
        X,
        Edit3,
    } from 'lucide-vue-next';
    import { router } from '@inertiajs/vue3';
    
    interface Props {
        portfolio: any;
        progress: number;
        hasUnsavedChanges: boolean;
        isSaving: boolean;
        isSaved: boolean;
        isPortfolioPublic: boolean;
        formData: any;
    }
    

    const props = withDefaults(defineProps<Props>(), {}); 
    
    const emits = defineEmits<{
        save: [];
        openPreview: [];
        openPublicModal: [];
        finish: [];
    }>();
    
    const handleViewPublic = () => {
        router.visit(`/dashboard/portfolio/${props.portfolio.id}`);
    };
    </script>
    
    <template>
        <header class="border-b border-gray-200/60 bg-white sticky top-0 z-30">
            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3 shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#005aeb]">
                        <span class="text-sm font-bold text-white">P</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">PortafolioAI</span>
                </div>
    
                    <!-- Centro: Progreso -->
                    <div class="hidden items-center space-x-4 md:flex">
                        <div class="flex items-center space-x-2">
                            <div :class="[
                                'h-2.5 w-2.5 rounded-full transition-colors duration-300',
                                hasUnsavedChanges ? 'bg-amber-500 animate-pulse' : 'bg-green-500'
                            ]"></div>
                            <span class="text-xs font-medium text-gray-600">
                                {{ hasUnsavedChanges ? 'Sin guardar' : 'Guardado' }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-[#005aeb]">{{ progress }}%</span>
                            completado
                        </div>
                        <div class="h-2 w-32 rounded-full bg-gray-200">
                            <div class="h-2 rounded-full bg-[#005aeb] transition-all duration-500"
                                :style="{ width: progress + '%' }"></div>
                        </div>
                    </div>

                    <!-- Título del Portafolio (Centro-Derecha) -->
                    <div class="hidden flex-1 max-w-[280px] px-6 lg:block">
                        <div class="relative group">
                            <input
                                type="text"
                                v-model="formData.config.title"
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-lg px-4 py-2 text-sm font-semibold !text-gray-700 transition-all duration-200 focus:bg-white focus:border-[#005aeb] focus:ring-4 focus:ring-blue-50 focus:outline-none placeholder:text-gray-400 group-hover:border-gray-300"
                                placeholder="Nombre de tu proyecto..."
                            />
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <Edit3 class="w-3.5 h-3.5 text-gray-400" />
                            </div>
                        </div>
                    </div>

                    <!-- Derecha: Acciones -->
                    <div class="flex items-center space-x-3">
                        <!-- Botón Público/Privado -->
                        <button
                            @click="emits('openPublicModal')"
                            :disabled="!formData.config.is_completed"
                            :class="[
                                'hidden items-center space-x-2 rounded-lg px-4 py-2 font-medium transition-all duration-200 sm:flex',
                                !formData.config.is_completed
                                    ? 'bg-gray-50 text-gray-400 border border-gray-200 cursor-not-allowed opacity-50'
                                    : isPortfolioPublic
                                        ? 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100'
                                        : 'bg-gray-100 text-gray-700 border border-gray-300 hover:bg-gray-200'
                            ]">
                            <Globe v-if="isPortfolioPublic && formData.config.is_completed" class="h-4 w-4" />
                            <Lock v-else class="h-4 w-4" />
                            <span class="text-sm">
                                {{ !formData.config.is_completed ? 'Desactivado' : (isPortfolioPublic ? 'Público' : 'Privado') }}
                            </span>
                        </button>
    
                        <!-- Botón Vista Previa -->
                        <button @click="emits('openPreview')"
                            class="hidden items-center space-x-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-50 sm:flex">
                            <Eye class="h-4 w-4" />
                            <span>Vista previa</span>
                        </button>
    
                        <!-- Botón Guardar -->
                        <button
                            @click="emits('save')"
                            :disabled="isSaving || isSaved || !hasUnsavedChanges"
                            :class="[
                                'flex items-center space-x-2 rounded-lg font-medium px-4 py-2 transition-all duration-300',
                                !isSaving && !isSaved && hasUnsavedChanges && 'bg-[#005aeb] text-white hover:bg-[#0048c4] shadow-md hover:shadow-lg',
                                isSaving && 'bg-[#005aeb] text-white opacity-75 cursor-not-allowed',
                                isSaved && 'bg-green-500 text-white shadow-lg shadow-green-500/30',
                                !hasUnsavedChanges && !isSaving && !isSaved && 'bg-gray-100 text-gray-500 cursor-not-allowed'
                            ]">
                            <Loader2 v-if="isSaving" class="w-5 h-5 animate-spin" />
                            <Check v-else-if="isSaved" class="w-5 h-5 animate-bounce" />
                            <Save v-else class="h-4 w-4" />
                            <span class="text-sm">
                                {{ isSaving ? 'Guardando...' : isSaved ? '¡Guardado!' : 'Guardar' }}
                            </span>
                        </button>
    
                        <!-- Dropdown -->
                        <div class="relative group">
                            <button
                                class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200 text-gray-600 hover:text-gray-900">
                                <MoreVertical class="h-5 w-5" />
                            </button>
                            <div
                                class="absolute right-0 mt-2 w-48 rounded-lg border border-gray-200 bg-white shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-20">
                                <button
                                    @click="handleViewPublic"
                                    :disabled="!isPortfolioPublic"
                                    class="flex items-center gap-3 w-full px-4 py-2.5 text-left text-sm transition-colors first:rounded-t-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-white">
                                    <Eye class="w-5 h-5" />
                                    Ver portafolio público
                                </button>
                                <button
                                    @click="emits('finish')"
                                    class="flex items-center gap-3 w-full px-4 py-2.5 text-left text-sm text-red-600 hover:bg-red-50 transition-colors last:rounded-b-lg border-t border-gray-100">
                                    <X class="w-4 h-4 flex-shrink-0" />
                                    Salir del editor
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    </template>
    
    <style scoped>
    /* Animaciones heredadas del archivo principal */
    </style>