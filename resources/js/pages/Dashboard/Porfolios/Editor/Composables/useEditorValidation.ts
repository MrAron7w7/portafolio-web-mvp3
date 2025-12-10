// ============================================
// composables/useEditorValidation.ts (CORREGIDO v2)
// ============================================
import { computed, ref, watch } from 'vue';

import { usePersonalValidation } from '@/pages/Dashboard/Porfolios/Components/Composables/usePersonalValidation';
import { useAboutValidation } from '@/pages/Dashboard/Porfolios/Components/Composables/useAboutValidation';
import { useEducationValidation } from '@/pages/Dashboard/Porfolios/Components/Composables/useEducationValidation';
import { useLanguageValidation } from '@/pages/Dashboard/Porfolios/Components/Composables/useLanguageValidation';
import { useExperienceValidation } from '@/pages/Dashboard/Porfolios/Components/Composables/useExperienceValidation';
import { useSkillValidation } from '@/pages/Dashboard/Porfolios/Components/Composables/useSkillValidation';
import { useProjectValidation } from '@/pages/Dashboard/Porfolios/Components/Composables/useProjectValidation';
import { useSocialValidation } from '@/pages/Dashboard/Porfolios/Components/Composables/useSocialValidation';

export function useEditorValidation(formData: any, currentStep: any) {
    // ============================================
    // INSTANCIAS DE VALIDADORES
    // ============================================
    const personalValidation = usePersonalValidation();
    const aboutValidation = useAboutValidation();
    const educationValidation = useEducationValidation();
    const languageValidation = useLanguageValidation();
    const experienceValidation = useExperienceValidation();
    const skillValidation = useSkillValidation();
    const projectValidation = useProjectValidation();
    const socialValidation = useSocialValidation();

    // ============================================
    // FUNCIONES DE VALIDACIÓN POR SECCIÓN
    // ============================================
    const validatePersonal = (): boolean => {
        return personalValidation.validateAll(formData.personal);
    };

    const validateAbout = (): boolean => {
        return aboutValidation.validateAll({
            summary: formData.personal.summary,
            description: formData.personal.description
        });
    };

    const validateEducation = (): boolean => {
        return educationValidation.validateAll(formData.education);
    };

    const validateLanguages = (): boolean => {
        return languageValidation.validateAll(formData.languages);
    };

    const validateExperience = (): boolean => {
        return experienceValidation.validateAll(formData.experience);
    };

    const validateSkills = (): boolean => {
        return skillValidation.validateAll(formData.skills);
    };

    const validateProjects = (): boolean => {
        return projectValidation.validateAll(formData.projects);
    };

    const validateSocial = (): boolean => {
        return socialValidation.validateAll(formData.social);
    };

    const validateConfig = (): boolean => {
        return true;
    };

    const validations = [
        validatePersonal,
        validateAbout,
        validateEducation,
        validateLanguages,
        validateExperience,
        validateSkills,
        validateProjects,
        validateSocial,
        validateConfig,
    ];

    // ============================================
    // ESTADO DE VALIDACIÓN
    // ============================================
    const showValidationError = ref(false);
    const validationErrorTimeout = ref<number.Timeout | null>(null);

    // ============================================
    // COMPUTED PROPERTIES (DEFINIR ANTES DE LOS WATCHES)
    // ============================================
    
    // ✅ CORREGIDO: Ahora valida el paso ACTUAL, no siempre el 0
    const isCurrentStepValid = computed(() => {
        const stepIndex = currentStep.value - 1;
        if (stepIndex < 0 || stepIndex >= validations.length) {
            return false;
        }
        return validations[stepIndex]?.() || false;
    });

    const validStepsCount = computed(() => {
        return validations.filter((_, index) => validations[index]?.()).length;
    });

    const allStepsCompleted = computed(() => {
        return validStepsCount.value === validations.length;
    });

    // ============================================
    // WATCHES (DESPUÉS DE DEFINIR COMPUTED)
    // ============================================
    watch(
        () => isCurrentStepValid.value,
        (isValid) => {
            if (isValid && showValidationError.value) {
                setTimeout(() => {
                    showValidationError.value = false;
                }, 500);
            }
        }
    );

    // Limpiar timeout cuando cambia el paso
    watch(
        () => currentStep.value,
        () => {
            showValidationError.value = false;
            if (validationErrorTimeout.value) {
                clearTimeout(validationErrorTimeout.value);
            }
        }
    );

    // ============================================
    // FUNCIÓN PARA MARCAR CAMPOS COMO TOCADOS
    // ============================================
    const markFieldsAsTouched = (stepId: number, validatorsMap: any) => {
        switch (stepId) {
            case 1:
                validatorsMap.personalValidation.markAllAsTouched(formData.personal);
                break;
            case 2:
                validatorsMap.aboutValidation.markAllAsTouched(formData.personal);
                break;
            case 3:
                validatorsMap.educationValidation.markAllAsTouched(formData.education);
                break;
            case 4:
                validatorsMap.languageValidation.markAllAsTouched(formData.languages);
                break;
            case 5:
                validatorsMap.experienceValidation.markAllAsTouched(formData.experience);
                break;
            case 6:
                validatorsMap.skillValidation.markAllAsTouched(formData.skills);
                break;
            case 7:
                validatorsMap.projectValidation.markAllAsTouched(formData.projects);
                break;
            case 8:
                validatorsMap.socialValidation.markAllAsTouched(formData.social);
                break;
        }
    };

    return {
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
    };
}