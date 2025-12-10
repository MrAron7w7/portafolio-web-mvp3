<!-- ============================================
   Components/Layout/EditorSidebar.vue
   ============================================ -->
   <script setup lang="ts">
    import { Check, Globe, Lock } from 'lucide-vue-next';
    
    interface Props {
        steps: any[];
        currentStep: number;
        progress: number;
        portfolio: any;
        isPortfolioPublic: boolean;
        isCurrentStepValid: boolean;
    }
    
    withDefaults(defineProps<Props>(), {});
    
    defineEmits<{
        goToStep: [stepId: number];
    }>();
    </script>
    
    <template>
        <div class="lg:col-span-3">
            <div class="sticky top-8 rounded-2xl border border-gray-200/60 bg-white p-6 shadow-xs">
                <!-- Lista de pasos -->
                <nav class="space-y-2">
                    <div v-for="(step, index) in steps" :key="step.id"
                        class="group flex items-start space-x-4 transition-opacity duration-200"
                        :class="{ 'opacity-60': !step.completed && step.id > currentStep }">
                        <!-- Línea vertical -->
                        <div class="flex flex-col items-center">
                            <div :class="[
                                'flex h-8 w-8 items-center justify-center rounded-full border-2 text-sm font-semibold transition-all duration-300',
                                step.completed
                                    ? 'border-green-500 bg-green-50 text-green-600'
                                    : currentStep === step.id
                                        ? 'border-[#005aeb] bg-[#005aeb] text-white scale-110 shadow-lg'
                                        : 'border-gray-300 bg-white text-gray-400',
                            ]">
                                <Check v-if="step.completed" class="h-4 w-4" />
                                <span v-else>{{ step.id }}</span>
                            </div>
                            <!-- Línea conectora -->
                            <div v-if="index < steps.length - 1" :class="[
                                'mt-2 h-8 w-0.5 transition-all duration-300',
                                step.completed
                                    ? 'bg-green-500'
                                    : currentStep > step.id
                                        ? 'bg-[#005aeb]'
                                        : 'bg-gray-200',
                            ]"></div>
                        </div>
    
                        <!-- Texto del paso -->
                        <div class="min-w-0 flex-1 pb-6">
                            <button @click="$emit('goToStep', step.id)" :disabled="!step.completed && step.id > currentStep"
                                class="text-left disabled:cursor-not-allowed">
                                <p :class="[
                                    'font-medium transition-colors duration-200',
                                    currentStep === step.id
                                        ? 'text-[#005aeb]'
                                        : step.completed
                                            ? 'text-gray-900'
                                            : 'text-gray-500',
                                ]">
                                    {{ step.title }}
                                </p>
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