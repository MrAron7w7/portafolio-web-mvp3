<!-- ============================================
   EDITO.VUE - REFACTORIZADO (CORREGIDO v2)
   ============================================ -->
   <script setup lang="ts">
    import { router } from '@inertiajs/vue3';
    import { onBeforeUnmount, watch } from 'vue';
    
    // Composables
    import { useEditorState } from './Composables/useEditorState';
    import { useEditorValidation } from './Composables/useEditorValidation';
    import { useEditorActions } from './Composables/useEditorActions';
    import { useEditorModals } from './Composables/useEditorModals';
    
    // Componentes de Layout
    import EditorHeader from './Components/Layouts/EditorHeader.vue';
    import EditorSidebar from './Components/Layouts/EditorSidebar.vue';
    import EditorForm from './Components/Layouts/EditorForm.vue';
    import EditorPreview from './Components/Layouts/EditorPreview.vue';
    
    // Componentes Modales
    import FullPreviewModal from './Components/Modals/FullPreviewModal.vue';
    import CompleteFormModal from './Components/Modals/CompleteFormModal.vue';
    import PublicToggleModal from './Components/Modals/PublicToggleModal.vue';
    import ExitConfirmModal from './Components/Modals/ExitConfirmModal.vue';
    
    // Props
    const props = defineProps<{
        portfolio: any;
        templateData: any;
        sections: any[];
    }>();
    
    // ============================================
    // COMPOSABLES - ESTADO
    // ============================================
    const {
        formData,
        originalFormData,
        savedPortfolioState,
        currentStep,
        steps,
        currentTemplate,
        templateComponents,
        hasUnsavedChanges,
        progress,
        isPortfolioPublicInHeader,
        isPortfolioPublicInForm,
        hasPublicStatusChanged,
    } = useEditorState(props);
    
    // ============================================
    // COMPOSABLES - VALIDACIÓN (PASAR currentStep)
    // ============================================
    const {
        isCurrentStepValid,
        validStepsCount,
        allStepsCompleted,
        showValidationError,
        validationErrors,
        validations,
        personalValidation,
        aboutValidation,
        educationValidation,
        languageValidation,
        experienceValidation,
        skillValidation,
        projectValidation,
        socialValidation,
        markFieldsAsTouched,
    } = useEditorValidation(formData, currentStep);
    
    // ============================================
    // COMPOSABLES - ACCIONES
    // ============================================
    const {
        isSaving,
        isSaved,
        isCompleting,
        isTogglingPublic,
        saveChanges,
        completeForm,
        togglePublicStatus,
    } = useEditorActions(
        router,
        props,
        formData,
        originalFormData,
        savedPortfolioState,
        currentStep,
        steps,
    );
    
    // ============================================
    // COMPOSABLES - MODALES
    // ============================================
    const {
        showFullPreview,
        openFullPreview,
        closeFullPreview,
        showCompleteModal,
        openCompleteModal,
        closeCompleteModal,
        completionMessage,
        showPublicToggleModal,
        openPublicToggleModal,
        closePublicToggleModal,
        publicToggleMessage,
        showExitConfirm,
    } = useEditorModals();
    
    // ============================================
    // NAVEGACIÓN
    // ============================================
    const nextStep = () => {
        if (!isCurrentStepValid.value) {
            showValidationError.value = true;
            markFieldsAsTouched(currentStep.value, {
                personalValidation,
                aboutValidation,
                educationValidation,
                languageValidation,
                experienceValidation,
                skillValidation,
                projectValidation,
                socialValidation,
            });
    
            const nextButton = document.querySelector('[data-next-button]');
            if (nextButton) {
                nextButton.classList.add('shake-animation');
                setTimeout(() => {
                    nextButton.classList.remove('shake-animation');
                }, 400);
            }
    
            setTimeout(() => {
                showValidationError.value = false;
            }, 4000);
            return;
        }
    
        showValidationError.value = false;
        if (currentStep.value < steps.value.length) {
            steps.value[currentStep.value - 1].completed = true;
            currentStep.value++;
            setTimeout(() => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 100);
        }
    };
    
    const prevStep = () => {
        if (currentStep.value > 1) {
            currentStep.value--;
            setTimeout(() => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 100);
        }
    };
    
    const goToStep = (stepId: number) => {
        const targetStep = steps.value.find((step) => step.id === stepId);
        if (targetStep && (targetStep.completed || stepId <= currentStep.value)) {
            currentStep.value = stepId;
            setTimeout(() => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 100);
        }
    };
    
    // ============================================
    // SALIR DEL EDITOR
    // ============================================
    const finishAndRedirect = async () => {
        if (hasUnsavedChanges.value) {
            await saveChanges();
            await new Promise(resolve => setTimeout(resolve, 1500));
        }
        router.visit('/dashboard');
    };
    
    const cancelExit = () => {
        showExitConfirm.value = false;
    };
    
    const handleFinish = async () => {
        if (hasUnsavedChanges.value) {
            showExitConfirm.value = true;
        } else {
            await finishAndRedirect();
        }
    };
    
    // ============================================
    // MONITOREO DE CAMBIOS
    // ============================================
    const handleBeforeUnload = (e: BeforeUnloadEvent) => {
        if (hasUnsavedChanges.value) {
            e.preventDefault();
            e.returnValue = '';
        }
    };
    
    watch(
        () => hasUnsavedChanges.value,
        (hasChanges) => {
            if (hasChanges) {
                document.body.classList.add('has-unsaved-changes');
            } else {
                document.body.classList.remove('has-unsaved-changes');
            }
        }
    );
    
    watch(showFullPreview, (newVal) => {
        document.body.classList.toggle('modal-open', newVal);
    });
    
    window.addEventListener('beforeunload', handleBeforeUnload);
    
    onBeforeUnmount(() => {
        window.removeEventListener('beforeunload', handleBeforeUnload);
    });
    </script>
    
    <template>
        <div class="min-h-screen bg-gray-50/50">
            <!-- Header -->
            <EditorHeader
                :portfolio="portfolio"
                :progress="progress"
                :has-unsaved-changes="hasUnsavedChanges"
                :is-saving="isSaving"
                :is-saved="isSaved"
                :is-portfolio-public="isPortfolioPublicInHeader"
                :form-data="formData"
                @save="saveChanges"
                @open-preview="openFullPreview"
                @open-public-modal="openPublicToggleModal"
                @finish="handleFinish"
            />
    
            <!-- Contenido principal -->
            <div class="container mx-auto px-6 py-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                    <!-- Sidebar -->
                    <EditorSidebar
                        :steps="steps"
                        :current-step="currentStep"
                        :progress="progress"
                        :portfolio="portfolio"
                        :is-portfolio-public="isPortfolioPublicInHeader"
                        :is-current-step-valid="isCurrentStepValid"
                        @go-to-step="goToStep"
                    />
    
                    <!-- Formulario -->
                    <EditorForm
                        :form-data="formData"
                        :current-step="currentStep"
                        :steps="steps"
                        :show-validation-error="showValidationError"
                        :is-current-step-valid="isCurrentStepValid"
                        :personal-validation="personalValidation"
                        :about-validation="aboutValidation"
                        :education-validation="educationValidation"
                        :language-validation="languageValidation"
                        :experience-validation="experienceValidation"
                        :skill-validation="skillValidation"
                        :project-validation="projectValidation"
                        :social-validation="socialValidation"
                        :all-steps-completed="allStepsCompleted"
                        @prev="prevStep"
                        @next="nextStep"
                        @complete="openCompleteModal"
                        @update:show-validation-error="(val) => showValidationError = val"
                    />
    
                    <!-- Preview -->
                    <EditorPreview
                        :form-data="formData"
                        :current-template="currentTemplate"
                    />
                </div>
            </div>
        </div>
    
        <!-- Modales -->
        <FullPreviewModal
            :is-open="showFullPreview"
            :form-data="formData"
            :current-template="currentTemplate"
            :portfolio="portfolio"
            @close="closeFullPreview"
        />
    
        <CompleteFormModal
            :is-open="showCompleteModal"
            :is-completing="isCompleting"
            :all-steps-completed="allStepsCompleted"
            :valid-steps-count="validStepsCount"
            :steps-length="steps.length"
            :completion-message="completionMessage"
            @close="closeCompleteModal"
            @complete="completeForm"
        />
    
        <PublicToggleModal
            :is-open="showPublicToggleModal"
            :is-toggling="isTogglingPublic"
            :is-public="isPortfolioPublicInHeader"
            :toggle-message="publicToggleMessage"
            @close="closePublicToggleModal"
            @toggle="togglePublicStatus"
        />
    
        <ExitConfirmModal
            :is-open="showExitConfirm"
            :current-step="currentStep"
            :steps-length="steps.length"
            :has-unsaved-changes="hasUnsavedChanges"
            @cancel="cancelExit"
            @confirm="finishAndRedirect"
        />
    </template>
    
    <style scoped>
    /* Animación SHAKE - Validación fallida */
    @keyframes shake {
        0%, 100% {
            transform: translateX(0);
        }
        10%, 30%, 50%, 70%, 90% {
            transform: translateX(-4px);
        }
        20%, 40%, 60%, 80% {
            transform: translateX(4px);
        }
    }
    
    .shake-animation {
        animation: shake 0.4s ease-in-out;
    }
    
    /* Control de modales */
    :global(body.modal-open) {
        overflow: hidden !important;
        position: fixed;
        width: 100%;
    }
    
    :global(body.has-unsaved-changes) {
        --unsaved-indicator: #f59e0b;
    }
    </style>