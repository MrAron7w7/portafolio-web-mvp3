<!-- ============================================
   EDITO.VUE - VERSIÓN ACTUALIZADA CON IA AUTOCOMPLETE
   Con mapeo de datos IA al formulario
   ============================================ -->
<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onBeforeUnmount, watch, computed, ref, onMounted } from 'vue';

// Services
import { generateAutoCompleteSummary } from './Services/iaAutoComplete';

// Composables
import { useEditorState } from './Composables/useEditorState';
import { useEditorValidation } from './Composables/useEditorValidation';
import { useEditorActions } from './Composables/useEditorActions';
import { useEditorModals } from './Composables/useEditorModals';
import { useStartChoice } from './Composables/useStartChoice';
import { useAIAnalysis } from './Composables/useAIAnalysis';
import { useLoadIAData } from '@/pages/Dashboard/Porfolios/Components/Composables/useLoadIaData';

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
import StartChoiceModal from './Components/Modals/StartChoiceModal.vue';
import AIDescriptionModal from './Components/Modals/AIDescriptionModal.vue';
import AIResultsModal from './Components/Modals/AIResultsModal.vue';
import AIAutoCompleteConfirmModal from './Components/Modals/AIAutoCompleteConfirmModal.vue';

// Props
const props = defineProps<{
    portfolio: any;
    templateData: any;
    sections: any[];
}>();


// Estado para el modal
const showIncompleteModal = ref(false);
const incompleteStepsList = ref<any[]>([]);


// ============================================
// COMPOSABLES - INICIO (START CHOICE)
// ============================================
const {
    showStartChoice,
    isLoadingChoice,
    selectedChoice,
    isAIMode,
    isManualMode,
    handleChooseAI,
    handleChooseManual,
    closeStartChoice,
} = useStartChoice();

// ============================================
// COMPOSABLES - ANÁLISIS IA
// ============================================
const {
    showDescriptionModal,
    showResultsModal,
    showConfirmationModal,
    isAnalyzing,
    analysisResult,
    userDescription,
    openDescriptionModal,
    closeDescriptionModal,
    openResultsModal,
    closeResultsModal,
    openConfirmationModal,
    closeConfirmationModal,
    analyzeWithAI,
    autoCompleteFormData,
    reset: resetAI,
} = useAIAnalysis();

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
// COMPOSABLES - VALIDACIÓN
// ============================================
const {
    isCurrentStepValid,
    validStepsCount,
    allStepsCompleted,
    showValidationError,
    validationErrorTimeout,
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
    persistentStepValidation,
    stepValidationHistory,
    visitedSteps,
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
// COMPOSABLE - LOAD IA DATA
// ============================================
const { mapIADataToForm } = useLoadIAData();

// ============================================
// COMPUTED - DETECTAR MODALES IA ABIERTOS
// ============================================
const hasAIModalOpen = computed(() => {
    return showStartChoice.value ||
        showDescriptionModal.value ||
        showResultsModal.value ||
        showConfirmationModal.value;
});

// Flag para evitar parpadeos - controlado manualmente
const isAIModalBackdropVisible = ref(false);

// ============================================
// HANDLERS - MODAL INICIAL
// ============================================
const handleStartWithAI = async () => {
    await handleChooseAI();
    isAIModalBackdropVisible.value = true;
    console.log('🤖 Editor iniciado en modo IA');

    // Cerrar StartChoice pero mantener backdrop activo
    closeStartChoice();

    // Mostrar modal de descripción con timing sincronizado
    setTimeout(() => {
        openDescriptionModal();
    }, 320);
};

const handleStartManual = async () => {
    await handleChooseManual();
    console.log('✍️ Editor iniciado en modo Manual');

    // Cerrar backdrop cuando elige manual
    isAIModalBackdropVisible.value = false;
};

const handleCloseStartChoice = () => {
    closeStartChoice();
    if (!showDescriptionModal.value && !showResultsModal.value && !showConfirmationModal.value) {
        isAIModalBackdropVisible.value = false;
    }
};

// ============================================
// HANDLERS - ANÁLISIS IA
// ============================================
const handleAnalyzeDescription = async (description: string) => {
    isAnalyzing.value = true;
    try {
        console.log('🔄 Analizando descripción...');

        const rawResponse = await generateAutoCompleteSummary(description);

        closeDescriptionModal();

        await analyzeWithAI(rawResponse);

        console.log('✅ Listo, modal de resultados abierto CON DATOS');

    } catch (error) {
        console.error('❌ Error:', error);
    } finally {
        isAnalyzing.value = false;
    }
};

const handleAutoCompleteForm = async () => {
    console.log('⚡ Autocompletando...');

    if (!analysisResult.value) {
        console.warn('⚠️ Sin resultados');
        return;
    }

    try {
        const success = await autoCompleteFormData(formData);

        if (success) {
            console.log('✅ Autocompletado exitosamente');
        }
    } catch (error) {
        console.error('❌ Error:', error);
    }
};

const handleDescriptionClose = () => {
    closeDescriptionModal();
    if (!showStartChoice.value && !showResultsModal.value && !showConfirmationModal.value) {
        isAIModalBackdropVisible.value = false;
    }
    resetAI();
};

const handleResultsClose = () => {
    closeResultsModal();
    if (!showStartChoice.value && !showDescriptionModal.value && !showConfirmationModal.value) {
        isAIModalBackdropVisible.value = false;
    }
    resetAI();
};

const handleConfirmationClose = () => {
    console.log('📋 Modal cerrado');
    closeConfirmationModal();
    isAIModalBackdropVisible.value = false;
};

// ============================================
// HANDLER - CONFIRMACIÓN CON MAPEO DE DATOS
// ============================================
const handleConfirmationContinue = () => {
    console.log('✅ Usuario continuó');

    // ✨ MAPEAR DATOS IA AL FORMULARIO
    if (analysisResult.value) {
        console.log('📥 Mapeando datos IA al formulario...');
        console.log('   Datos a mapear:', {
            experience: analysisResult.value.experience?.length || 0,
            education: analysisResult.value.education?.length || 0,
            languages: analysisResult.value.languages?.length || 0,
            technical_skills: analysisResult.value.skills?.technical?.length || 0,
            soft_skills: analysisResult.value.skills?.soft?.length || 0,
            projects: analysisResult.value.projects?.length || 0,
        });

        const success = mapIADataToForm(analysisResult.value, formData);

        if (success) {
            console.log('✅ Datos mapeados correctamente al formData');
            console.log('   FormData actualizado:', {
                experience: formData.experience.length,
                education: formData.education.length,
                languages: formData.languages.length,
                technical_skills: formData.skills.technical.length,
                soft_skills: formData.skills.soft.length,
                projects: formData.projects.length,
            });
        } else {
            console.error('❌ Error al mapear datos');
        }
    }

    closeConfirmationModal();
    resetAI();
    isAIModalBackdropVisible.value = false;

   
};

// Manejador para cuando AboutSection emite los datos de IA
const handleLoadIAData = (iaData: any) => {
    console.log('📥 Datos de IA recibidos:', iaData);

    const success = mapIADataToForm(iaData, formData);

    if (success) {
        console.log('✅ Datos de IA cargados correctamente en el formulario');
    } else {
        console.error('❌ Error al cargar datos de IA');
    }
};

// ============================================
// NAVEGACIÓN
// ============================================

const delay = 500;
const nextStep = () => {

    /*
    if (!isCurrentStepValid.value) {
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

  
        return;
    }

    showValidationError.value = false;
*/
   


    if (currentStep.value < steps.value.length) {
        // A. Marcar el paso ACTUAL como completado
        steps.value[currentStep.value - 1].completed = true;

        // B. ✨ NUEVO: Habilitar el SIGUIENTE paso antes de ir a él
        // El siguiente paso tiene índice = currentStep.value (porque los arrays son base-0 y los IDs base-1)
        if (steps.value[currentStep.value]) {
            steps.value[currentStep.value].enabled = true;
        }

        // C. Avanzar
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

    
        setTimeout(() => {

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


        }, delay);
    }
};



const goToStep = (stepId: number) => {
    const targetStep = steps.value.find((step) => step.id === stepId);

    if (!targetStep || !targetStep.enabled) {
        console.warn(`❌ Paso ${stepId} no está habilitado`);
        return;
    }

    console.log(`🔀 Navegando a paso ${stepId}`);

    // 🔴 DETECTAR SI ES PASO INCOMPLETO
    const isIncompleteStep = visitedSteps.value.has(stepId) &&
        stepValidationHistory.value[stepId] === false;



    if (isIncompleteStep) {
        console.log(`⚠️ Paso ${stepId} incompleto, mostrando errores`);

 

        setTimeout(() => {

            markFieldsAsTouched(stepId, {
                personalValidation,
                aboutValidation,
                educationValidation,
                languageValidation,
                experienceValidation,
                skillValidation,
                projectValidation,
                socialValidation,
            });


        }, delay);  // ← 350 si es el mismo paso, 0 si es diferente
    }

    // ✨ Navegar al paso (después de detectar)
    currentStep.value = stepId;

    // Scroll suave
    setTimeout(() => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }, 100);
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


// Editor.vue -> <script setup>

const handleAttemptFinish = () => {
    // A. Validar paso actual primero
    if (!isCurrentStepValid.value) {
        showValidationError.value = true;
        return;
    }

    // B. Filtrar pasos incompletos USANDO LA VALIDACIÓN REAL
    const incomplete = steps.value.filter(step => {
        // 1. Ignorar Configuración (Paso 9)
        if (step.id === 9) return false;

        // 2. Obtener estado real de validación
        // (Si no existe en el registro, asumimos false/invalido)
        const isStepValid = persistentStepValidation.value[step.id] === true;

        // 3. El paso cuenta como incompleto si NO es válido
        return !isStepValid;
    });

    // C. Actualizar lista y abrir modal
    incompleteStepsList.value = incomplete;
    openCompleteModal();
};





const handleSaveAndExit = async () => {
    // 1. Cerrar modal
    closeCompleteModal();

    // 2. Guardar estado actual
    await saveChanges();

    // 3. Salir
    router.visit('/dashboard');
};



// ============================================
// INICIALIZACIÓN Y MONITOREO
// ============================================
const handleBeforeUnload = (e: BeforeUnloadEvent) => {
    if (hasUnsavedChanges.value) {
        e.preventDefault();
        e.returnValue = '';
    }
};

onMounted(() => {
    console.log('🔄 onMounted - showStartChoice:', showStartChoice.value);
    if (showStartChoice.value) {
        isAIModalBackdropVisible.value = true;
        document.body.classList.add('modal-open');
        console.log('✅ Backdrop inicializado en onMounted');
    }
});

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

watch(showStartChoice, (newVal) => {
    console.log('👀 watch(showStartChoice):', newVal);
    if (newVal) {
        isAIModalBackdropVisible.value = true;
        document.body.classList.add('modal-open');
        console.log('✅ Backdrop y scroll desactivados');
    } else {
        if (!showDescriptionModal.value && !showResultsModal.value && !showConfirmationModal.value) {
            isAIModalBackdropVisible.value = false;
            document.body.classList.remove('modal-open');
            console.log('✅ Backdrop y scroll activados');
        }
    }
}, { immediate: true });

watch(isAIModalBackdropVisible, (newVal) => {
    console.log('🎨 watch(isAIModalBackdropVisible):', newVal);
    document.body.classList.toggle('modal-open', newVal);
});

window.addEventListener('beforeunload', handleBeforeUnload);

onBeforeUnmount(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload);
});
</script>

<template>
    <div class="min-h-screen bg-gray-50/50">
        <!-- ✨ BACKDROP COMPARTIDO PARA MODALES IA ✨ -->
        <Transition name="fade">
            <div v-if="isAIModalBackdropVisible" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40" />
        </Transition>

        <!-- Modal de Inicio (Start Choice) -->
        <StartChoiceModal :is-open="showStartChoice" :is-loading="isLoadingChoice" @close="handleCloseStartChoice"
            @choose-ai="handleStartWithAI" @choose-manual="handleStartManual" />

        <!-- Modal de Descripción IA -->
        <AIDescriptionModal :is-open="showDescriptionModal" :is-analyzing="isAnalyzing" @close="handleDescriptionClose"
            @analyze="handleAnalyzeDescription" />

        <!-- Modal de Resultados IA -->
        <AIResultsModal :is-open="showResultsModal" :is-loading="isAnalyzing" :analysis-result="analysisResult"
            @close="handleResultsClose" @auto-complete="handleAutoCompleteForm" />

        <!-- Modal de Confirmación IA -->
        <AIAutoCompleteConfirmModal :is-open="showConfirmationModal" :is-auto-completing="isAnalyzing"
            :analysis-result="analysisResult" @close="handleConfirmationClose" @continue="handleConfirmationContinue" />

        <!-- Header -->
        <EditorHeader :portfolio="portfolio" :progress="progress" :has-unsaved-changes="hasUnsavedChanges"
            :is-saving="isSaving" :is-saved="isSaved" :is-portfolio-public="isPortfolioPublicInHeader"
            :form-data="formData" @save="saveChanges" @open-preview="openFullPreview"
            @open-public-modal="openPublicToggleModal" @finish="handleFinish" />



        <!-- Contenido principal -->
        <div class="container mx-auto px-6 py-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <!-- Sidebar -->
                <EditorSidebar :steps="steps" :current-step="currentStep" :progress="progress" :portfolio="portfolio"
                    :is-portfolio-public="isPortfolioPublicInHeader" :is-current-step-valid="isCurrentStepValid"
                    :form-data="formData" :persistent-step-validation="persistentStepValidation"
                    @go-to-step="goToStep" />

                <!-- Formulario -->
                <EditorForm :form-data="formData" :current-step="currentStep" :steps="steps"
                    :show-validation-error="showValidationError" :is-current-step-valid="isCurrentStepValid"
                    :personal-validation="personalValidation" :about-validation="aboutValidation"
                    :education-validation="educationValidation" :language-validation="languageValidation"
                    :experience-validation="experienceValidation" :skill-validation="skillValidation"
                    :project-validation="projectValidation" :social-validation="socialValidation"
                    :all-steps-completed="allStepsCompleted" @prev="prevStep" @next="nextStep"
                    @complete="openCompleteModal" @load-ia-data="handleLoadIAData" @attempt-finish="handleAttemptFinish"
                    @update:show-validation-error="(val) => showValidationError = val" />

                <!-- Preview -->
                <EditorPreview :form-data="formData" :current-template="currentTemplate" />
            </div>
        </div>
    </div>

    <!-- Modales -->
    <FullPreviewModal :is-open="showFullPreview" :form-data="formData" :current-template="currentTemplate"
        :portfolio="portfolio" @close="closeFullPreview" />

    <!-- ASÍ DEBE QUEDAR TU CompleteFormModal -->
    <CompleteFormModal :is-open="showCompleteModal" :is-completing="isCompleting"
        :incomplete-steps="incompleteStepsList" :all-steps-completed="incompleteStepsList.length === 0"
        :valid-steps-count="validStepsCount" :steps-length="steps.length" :completion-message="completionMessage"
        @close="closeCompleteModal" @complete="completeForm" @save-and-exit="handleSaveAndExit" />



    <PublicToggleModal :is-open="showPublicToggleModal" :is-toggling="isTogglingPublic"
        :is-public="isPortfolioPublicInHeader" :toggle-message="publicToggleMessage" @close="closePublicToggleModal"
        @toggle="togglePublicStatus" />

    <ExitConfirmModal :is-open="showExitConfirm" :current-step="currentStep" :steps-length="steps.length"
        :has-unsaved-changes="hasUnsavedChanges" @cancel="cancelExit" @confirm="finishAndRedirect" />
</template>

<style scoped>
/* Animación SHAKE - Validación fallida */
@keyframes shake {

    0%,
    100% {
        transform: translateX(0);
    }

    10%,
    30%,
    50%,
    70%,
    90% {
        transform: translateX(-4px);
    }

    20%,
    40%,
    60%,
    80% {
        transform: translateX(4px);
    }
}

.shake-animation {
    animation: shake 0.4s ease-in-out;
}

/* Animación Fade para Backdrop */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
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