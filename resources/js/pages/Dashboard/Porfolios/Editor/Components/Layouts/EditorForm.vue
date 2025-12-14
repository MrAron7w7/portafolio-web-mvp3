<!-- ============================================
   Components/Layout/EditorForm.vue
   ============================================ -->
   <script setup lang="ts">
    import { AlertCircle, ArrowLeft, ArrowRight, CheckCircle } from 'lucide-vue-next';

    import PersonalSection from '@/pages/Dashboard/Porfolios/Components/PersonalSection.vue';
    import AboutSection from '@/pages/Dashboard/Porfolios/Components/AboutSection.vue';
    import EducationSection from '@/pages/Dashboard/Porfolios/Components/EducationSection.vue';
    import LanguageSection from '@/pages/Dashboard/Porfolios/Components/LanguageSection.vue';
    import ExperienceSection from '@/pages/Dashboard/Porfolios/Components/ExperienceSection.vue';
    import SkillsSection from '@/pages/Dashboard/Porfolios/Components/SkillsSection.vue';
    import ProjectsSection from '@/pages/Dashboard/Porfolios/Components/ProjectsSection.vue';
    import SocialSection from '@/pages/Dashboard/Porfolios/Components/SocialSection.vue';
    import ConfigSection from '@/pages/Dashboard/Porfolios/Components/ConfigSection.vue';
    
    interface Props {
        formData: any;
        currentStep: number;
        steps: any[];
        showValidationError: boolean;
        isCurrentStepValid: boolean;
        personalValidation: any;
        aboutValidation: any;
        educationValidation: any;
        languageValidation: any;
        experienceValidation: any;
        skillValidation: any;
        projectValidation: any;
        socialValidation: any;
        allStepsCompleted: boolean;
    }
    
    withDefaults(defineProps<Props>(), {});
    
    defineEmits<{
        prev: [];
        next: [];
        complete: [];
        'update:showValidationError': [value: boolean];
        'load-ia-data': [data: any] 
    }>();
    </script>
    
    <template>
        <div class="lg:col-span-5">
            <div class="rounded-2xl border border-gray-200/60 bg-white p-8 shadow-xs">
                <!-- Alerta de validación -->
                <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 translate-y-2 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition-all duration-150 ease-in"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 translate-y-1 scale-95">
                    <div v-if="showValidationError"
                        class="mb-6 flex items-start space-x-3 rounded-lg bg-red-50 p-4 border border-red-200 shadow-sm">
                        <AlertCircle class="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" />
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-red-900">
                                Por favor, completa este paso
                            </p>
                            <p class="text-xs text-red-700 mt-1">
                                Corrige los campos que salgan en rojo. Los que tienen (*) son obligatorios
                            </p>
                        </div>
                        <button @click="$emit('update:showValidationError', false)"
                            class="text-red-400 hover:text-red-600 transition-colors duration-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </Transition>
    
                <!-- Componentes Dinámicos -->
                <Transition name="step-slide" mode="out-in">
                    <div :key="currentStep">
                        <PersonalSection v-if="currentStep === 1"
                            :model-value="formData.personal"
                            @update:model-value="formData.personal = $event"
                            :validation="personalValidation" />
    
                        <AboutSection v-if="currentStep === 2"
                            :model-value="formData.personal"
                            @update:model-value="formData.personal = $event"
                            :validation="aboutValidation"
                            @load-ia-data="$emit('load-ia-data', $event)"  />
    
                        <EducationSection v-if="currentStep === 3"
                            :model-value="formData.education"
                            @update:model-value="formData.education = $event"
                            :validation="educationValidation" />
    
                        <LanguageSection v-if="currentStep === 4"
                            :model-value="formData.languages"
                            @update:model-value="formData.languages = $event"
                            :languages="formData.languages"
                            :validation="languageValidation" />
    
                        <ExperienceSection v-if="currentStep === 5"
                            :model-value="formData.experience"
                            @update:model-value="formData.experience = $event"
                            :validation="experienceValidation" />
    
                        <SkillsSection v-if="currentStep === 6"
                            :model-value="formData.skills"
                            @update:model-value="formData.skills = $event"
                            :validation="skillValidation" />
    
                        <ProjectsSection v-if="currentStep === 7"
                            :model-value="formData.projects"
                            @update:model-value="formData.projects = $event"
                            :validation="projectValidation" />
    
                        <SocialSection v-if="currentStep === 8"
                            :model-value="formData.social"
                            @update:model-value="formData.social = $event"
                            :validation="socialValidation" />
    
                        <ConfigSection v-if="currentStep === 9"
                            :model-value="formData.config"
                            @update:model-value="formData.config = $event" />
                    </div>
                </Transition>
    
                <!-- Navegación entre pasos -->
                <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-8">
                    <button @click="$emit('prev')"
                        class="flex items-center space-x-2 rounded-lg border border-gray-300 px-6 py-3 text-gray-700 transition-all duration-200 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="currentStep === 1">
                        <ArrowLeft class="h-4 w-4" />
                        <span>Anterior</span>
                    </button>
    
                    <!-- Botón dinámico: Siguiente o Finalizar -->
                    <button v-if="currentStep === steps.length && allStepsCompleted"
                        @click="$emit('complete')"
                        class="flex items-center space-x-2 rounded-lg px-6 py-3 text-white transition-all duration-300 font-medium bg-green-500 hover:bg-green-600 shadow-md hover:shadow-lg cursor-pointer">
                        <CheckCircle class="h-4 w-4" />
                        <span>Finalizar formulario</span>
                    </button>
    
                    <button v-else
                        @click="$emit('next')"
                        data-next-button
                        class="flex items-center space-x-2 rounded-lg px-6 py-3 text-white transition-all duration-300 font-medium bg-[#005aeb] hover:bg-[#0048c4] shadow-md hover:shadow-lg cursor-pointer">
                        <span>{{ currentStep === steps.length ? 'Finalizar' : 'Siguiente paso' }}</span>
                        <ArrowRight class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </template>
    
    <style scoped>
    /* Transición entre steps */
    .step-slide-enter-active,
    .step-slide-leave-active {
        transition: all 0.35s ease;
    }
    
    .step-slide-enter-from {
        opacity: 0;
        transform: translateX(20px);
    }
    
    .step-slide-leave-to {
        opacity: 0;
        transform: translateX(-20px);
    }
    </style>