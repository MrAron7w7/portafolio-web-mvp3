// ============================================
// composables/useEditorValidation.ts (VERSIÓN v4 - CON REBOTE DE ERRORES MEJORADO)
// TRACKING PERSISTENTE + REBOTE AUTOMÁTICO AL ENTRAR A PASO INCOMPLETO
// ============================================
import { computed, ref, watch, nextTick } from 'vue';

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
    // TRACKING PERSISTENTE DE VALIDACIÓN POR PASO
    // ============================================
    const stepValidationHistory = ref<Record<number, boolean>>({
        1: false,
        2: false,
        3: false,
        4: false,
        5: false,
        6: false,
        7: false,
        8: false,
        9: false,
    });

    // ✨ Registro de pasos visitados (para detectar primer ingreso)
    const visitedSteps = ref(new Set<number>([1])); // Paso 1 visitado por defecto

    // ============================================
    // FUNCIONES DE VALIDACIÓN POR SECCIÓN
    // ============================================
    const validatePersonal = (): boolean => {
        return personalValidation.validateAll(formData.personal);
    };

    const validateAbout = (): boolean => {
        return aboutValidation.validateAll({
            summary: formData.personal.summary
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
    const validationErrorTimeout = ref<ReturnType<typeof setTimeout> | null>(null);

    // ============================================
    // COMPUTED PROPERTIES
    // ============================================
    
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

    const persistentStepValidation = computed(() => {
        return stepValidationHistory.value;
    });

    // ============================================
    // FUNCIÓN PARA MARCAR CAMPOS COMO TOCADOS
    // ============================================
    const markFieldsAsTouched = (stepId: number, validatorsMap: any) => {
        console.log(`🔴 markFieldsAsTouched ejecutado para paso ${stepId}`);
        
        switch (stepId) {
            case 1:
                validatorsMap.personalValidation.markAllAsTouched(formData.personal);
                console.log('   ✅ Personal marcado como touched');
                break;
            case 2:
                validatorsMap.aboutValidation.markAllAsTouched(formData.personal);
                console.log('   ✅ About marcado como touched');
                break;
            case 3:
                validatorsMap.educationValidation.markAllAsTouched(formData.education);
                console.log('   ✅ Education marcado como touched');
                break;
            case 4:
                validatorsMap.languageValidation.markAllAsTouched(formData.languages);
                console.log('   ✅ Languages marcado como touched');
                break;
            case 5:
                validatorsMap.experienceValidation.markAllAsTouched(formData.experience);
                console.log('   ✅ Experience marcado como touched');
                break;
            case 6:
                validatorsMap.skillValidation.markAllAsTouched(formData.skills);
                console.log('   ✅ Skills marcado como touched');
                break;
            case 7:
                validatorsMap.projectValidation.markAllAsTouched(formData.projects);
                console.log('   ✅ Projects marcado como touched');
                break;
            case 8:
                validatorsMap.socialValidation.markAllAsTouched(formData.social);
                console.log('   ✅ Social marcado como touched');
                break;
        }
    };

    // ============================================
    // FUNCIÓN TRIGGER DE ERRORES VISUALES (REUTILIZABLE)
    // ============================================
    const triggerVisualValidation = (stepId: number = currentStep.value) => {
        console.log(`⚠️ triggerVisualValidation para paso ${stepId}`);
        
   
        // Esperar a que Vue renderice, luego marcar campos
        nextTick(() => {
            setTimeout(() => {
                markFieldsAsTouched(stepId, {
                    personalValidation,
                    aboutValidation,
                    educationValidation,
                    languageValidation,
                    experienceValidation,
                    skillValidation,
                    projectValidation,
                    socialValidation
                });
            }, 0); // nextTick ya espera el render
        });

       
       
    };

    // ============================================
    // WATCH - GESTIÓN DE CAMBIO DE PASO
    // ============================================
    watch(
        () => currentStep.value,
        (newStep, oldStep) => {
            console.log(`\n📍 Cambio de paso: ${oldStep} → ${newStep}`);

            // 1. GUARDAR ESTADO DEL PASO ANTERIOR
            if (oldStep !== undefined && oldStep > 0) {
                const stepIndex = oldStep - 1;
                const isOldStepValid = validations[stepIndex]?.() || false;
                stepValidationHistory.value[oldStep] = isOldStepValid;
                
                console.log(`💾 Paso ${oldStep} → Guardado: ${isOldStepValid ? '✅' : '❌'}`);
            }

            // 2. LIMPIAR ERRORES GLOBALES
            showValidationError.value = false;
            if (validationErrorTimeout.value) {
                clearTimeout(validationErrorTimeout.value);
            }

            // 3. ✨ REBOTE DE ERRORES AL ENTRAR
            // Solo si ya visitamos este paso antes Y está inválido
            if (visitedSteps.value.has(newStep) && stepValidationHistory.value[newStep] === false) {
                console.log(`⚠️  REBOTANDO ERRORES EN PASO: ${newStep}`);
                
                // Usar nextTick para esperar a que Vue monte el componente
                nextTick(() => {
                    setTimeout(() => {
                        triggerVisualValidation(newStep);
                    }, 300); // Tiempo exacto de la transición step-slide
                });
            }

            // 4. MARCAR PASO COMO VISITADO
            visitedSteps.value.add(newStep);

            console.log(`📊 Historial:`, stepValidationHistory.value);
            console.log(`👣 Visitados:`, Array.from(visitedSteps.value));
        }
    );

    // ============================================
    // WATCH - FEEDBACK VISUAL EN TIEMPO REAL
    // ============================================
    watch(
        () => isCurrentStepValid.value,
        (isValid) => {
            if (isValid && showValidationError.value) {
                // Si se vuelve válido, cerrar la alerta
                setTimeout(() => {
                    showValidationError.value = false;
                }, 500);
            }
        }
    );

    // ============================================
    // RETURN
    // ============================================
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
        triggerVisualValidation, // ✨ Exportamos para usar desde Editor.vue
        persistentStepValidation,
        stepValidationHistory,
        visitedSteps,
    };
}