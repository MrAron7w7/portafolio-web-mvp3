<script setup lang="ts">
import { MoreVertical } from 'lucide-vue-next';

import Academica from '@/components/Templates/Academica.vue';
import Creativa from '@/components/Templates/Creativa.vue';
import Ejecutiva from '@/components/Templates/Ejecutiva.vue';
import Minimalista from '@/components/Templates/Minimalista.vue';
import Moderna from '@/components/Templates/Moderna.vue';
import Tecnologica from '@/components/Templates/Tecnologica.vue';
import { router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    Briefcase,
    Check,
    Eye,
    FileText,
    Link,
    Palette,
    Save,
    Settings,
    Star,
    User,
    Zap,
    AlertCircle,
    CheckCircle,
    Loader2 ,
    Globe,
    Lock,
    X,
} from 'lucide-vue-next';
import { computed, reactive, ref, watch, onBeforeUnmount } from 'vue';


import AboutSection from './Components/AboutSection.vue';
import ConfigSection from './Components/ConfigSection.vue';
import EducationSection from './Components/EducationSection.vue';
import LanguageSection from './Components/LanguageSection.vue';
import ExperienceSection from './Components/ExperienceSection.vue';
import PersonalSection from './Components/PersonalSection.vue';
import PreviewContainer from './Components/PreviewContainer.vue';
import ProjectsSection from './Components/ProjectsSection.vue';
import SkillsSection from './Components/SkillsSection.vue';
import SocialSection from './Components/SocialSection.vue';








// Props del portfolio
const props = defineProps<{
    portfolio: any;
    templateData: any;
    sections: any[];
}>();

// ============================================
// ESTADO DE CAMBIOS SIN GUARDAR
// ============================================

const originalFormData = ref<any>(null);
const savedPortfolioState = ref({
    is_public: props.portfolio.is_public || false,
});

const deepEqual = (obj1: any, obj2: any): boolean => {
    if (obj1 === obj2) return true;
    if (obj1 == null || obj2 == null) return obj1 === obj2;

    const keys1 = Object.keys(obj1);
    const keys2 = Object.keys(obj2);

    if (keys1.length !== keys2.length) return false;

    for (const key of keys1) {
        if (!keys2.includes(key)) return false;
        if (typeof obj1[key] === 'object' && typeof obj2[key] === 'object') {
            if (!deepEqual(obj1[key], obj2[key])) return false;
        } else if (obj1[key] !== obj2[key]) {
            return false;
        }
    }
    return true;
};

const hasUnsavedChanges = computed(() => {
    if (!originalFormData.value) return false;
    return !deepEqual(formData, originalFormData.value);
});

// Estado del wizard
const currentStep = ref(1);
const steps = ref([
    { id: 1, title: 'Información personal', completed: false, hasError: false, icon: User },
    { id: 2, title: 'Sobre mí', completed: false, hasError: false, icon: FileText },
    { id: 3, title: 'Formación Académica', completed: false, hasError: false, icon: Palette },
    { id: 4, title: 'Idiomas', completed: false, hasError: false, icon: Globe },
    { id: 5, title: 'Experiencia', completed: false, hasError: false, icon: Briefcase },
    { id: 6, title: 'Habilidades', completed: false, hasError: false, icon: Star },
    { id: 7, title: 'Proyectos', completed: false, hasError: false, icon: Zap },
    { id: 8, title: 'Redes sociales', completed: false, hasError: false, icon: Link },
    { id: 9, title: 'Configuración', completed: false, hasError: false, icon: Settings },

]);

const templateComponents = {
    academica: Academica,
    creativa: Creativa,
    ejecutiva: Ejecutiva,
    minimalista: Minimalista,
    moderna: Moderna,
    tecnologica: Tecnologica,
};

const currentTemplate = computed(() => {
    return templateComponents[props.portfolio.template_type as keyof typeof templateComponents] || Moderna;
});

const technical = props.templateData.skills?.technical || [];
const soft = props.templateData.skills?.soft || [];

const formData = reactive({
    personal: {
        firstName: props.templateData.personal.firstName?.trim() || '',
        lastName: props.templateData.personal.lastName?.trim() || '',
        title: props.templateData.personal.title || '',
        city: props.templateData.personal.location?.split(',')?.[0]?.trim() ?? '',
        country: props.templateData.personal.location?.split(',')?.[1]?.trim() ?? '',

        phone: props.templateData.personal.phone || '',
        email: props.templateData.personal.email || '',
        photo: props.templateData.personal.photo || '',
        website: props.templateData.personal.website || '',
        linkedin: props.templateData.personal.linkedin || '',
        github: props.templateData.personal.github || '',
        summary: props.templateData.personal.summary || '',
        description: props.templateData.personal.description || '',
    },
    experience:
        props.templateData.experience?.map((exp: any, index: number) => ({
            id: index + 1,
            company: exp.company || '',
            position: exp.position || '',
            period: `${exp.startDate} - ${exp.current ? 'Presente' : exp.endDate}`,
            description: exp.description || '',
            startDate: exp.startDate || '',
            endDate: exp.endDate || '',
            current: exp.current || false,
        })) || [],
    skills: {
        technical: technical.map((skill: any, index: number) => {
            if (typeof skill === 'string') {
                return {
                    id: index + 1,
                    name: skill,
                    level: 50,
                    category: 'Technical',
                };
            } else {
                return {
                    id: index + 1,
                    name: skill.name || '',
                    level: skill.level || 50,
                    category: 'Technical',
                };
            }
        }),
        soft: soft.map((skill: any, index: number) => {
            if (typeof skill === 'string') {
                return {
                    id: index + 1000,
                    name: skill,
                    level: 50,
                    category: 'Soft',
                };
            } else {
                return {
                    id: index + 1000,
                    name: skill.name || '',
                    level: skill.level || 50,
                    category: 'Soft',
                };
            }
        }),
    },
    social: {
        linkedin: props.templateData.personal.linkedin || '',
        github: props.templateData.personal.github || '',
        website: props.templateData.personal.website || '',
    },
    projects: props.templateData.projects || [],
    education: props.templateData.education || [],
    config: {
        theme: 'moderna',
        is_public: props.portfolio.is_public || false,
        is_completed: props.portfolio.is_completed || false,
    },
    languages: props.templateData.languages || [],
    certifications: props.templateData.certifications || [],
});

originalFormData.value = JSON.parse(JSON.stringify(formData));

// ============================================
// ESTADO PÚBLICO/PRIVADO (SIN AFECTAR HEADER)
// ============================================

const showPublicToggleModal = ref(false);
const isTogglingPublic = ref(false);
const publicToggleMessage = ref('');

// El estado del HEADER siempre refleja lo guardado
const isPortfolioPublicInHeader = computed(() => {
    return savedPortfolioState.value.is_public;
});

// El estado del INPUT es temporal en formData
const isPortfolioPublicInForm = computed(() => {
    return formData.config.is_public;
});

// Detectar si el toggle ha cambiado sin guardar
const hasPublicStatusChanged = computed(() => {
    return formData.config.is_public !== savedPortfolioState.value.is_public;
});

const togglePublicStatus = async () => {
    try {
        isTogglingPublic.value = true;
        publicToggleMessage.value = '';

        const newPublicStatus = !formData.config.is_public;

        const templateData = {
            personal: {
                firstName: formData.personal.firstName.trim(),
                lastName: formData.personal.lastName.trim(),
                title: formData.personal.title,
                email: formData.personal.email,
                phone: formData.personal.phone,
                location: `${formData.personal.city}, ${formData.personal.country}`
                    .trim()
                    .replace(/^,\s*|\s*,$/g, ''),
                website: formData.social.website ?? formData.personal.website,
                photo: formData.personal.photo || null,
                linkedin: formData.social.linkedin ?? formData.personal.linkedin,
                github: formData.social.github ?? formData.personal.github,
                summary: formData.personal.summary || formData.personal.summary,
                description: formData.personal.description,
            },
            experience: formData.experience.map((exp: any) => ({
                company: exp.company,
                position: exp.position,
                startDate: exp.startDate,
                endDate: exp.endDate,
                current: exp.current,
                description: exp.description,
            })),
            skills: {
                technical: formData.skills.technical.map((skill: any) => ({
                    name: skill.name,
                    level: skill.level
                })),
                soft: formData.skills.soft.map((skill: any) => ({
                    name: skill.name,
                    level: skill.level
                })),
            },
            projects: formData.projects,
            education: formData.education,
            languages: formData.languages,
            certifications: [],

        };

        router.put(
            `/dashboard/portfolio/${props.portfolio.id}`,
            {
                template_data: templateData,
                config: {
                    theme: formData.config.theme,
                    is_public: newPublicStatus,
                },
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    isTogglingPublic.value = false;

                    // Solo actualizar el estado guardado, no el form
                    formData.config.is_public = newPublicStatus;
                    savedPortfolioState.value.is_public = newPublicStatus;

                    if (newPublicStatus) {
                        publicToggleMessage.value = '✓ Tu portafolio ahora es público';
                    } else {
                        publicToggleMessage.value = '✓ Tu portafolio ahora es privado';
                    }

                    setTimeout(() => {
                        showPublicToggleModal.value = false;
                        publicToggleMessage.value = '';
                    }, 2000);
                },
                onError: () => {
                    isTogglingPublic.value = false;
                    publicToggleMessage.value = 'Error al cambiar estado. Intenta nuevamente.';
                },
            },
        );
    } catch (error) {
        isTogglingPublic.value = false;
        publicToggleMessage.value = 'Error inesperado. Intenta nuevamente.';
        console.error('Error:', error);
    }
};

const openPublicToggleModal = () => {
    showPublicToggleModal.value = true;
};

const closePublicToggleModal = () => {
    if (!isTogglingPublic.value) {
        showPublicToggleModal.value = false;
    }
};

// ============================================
// VALIDACIONES POR SECCIÓN
// ============================================

import { usePersonalValidation } from './Components/Composables/usePersonalValidation';
import { useAboutValidation } from './Components/Composables/useAboutValidation';
import { useEducationValidation } from './Components/Composables/useEducationValidation';
import { useLanguageValidation } from './Components/Composables/useLanguageValidation';
import { useExperienceValidation } from './Components/Composables/useExperienceValidation';
import { useSkillValidation } from './Components/Composables/useSkillValidation';
import { useProjectValidation } from './Components/Composables/useProjectValidation';
import { useSocialValidation } from './Components/Composables/useSocialValidation';

const personalValidation = usePersonalValidation();
const { validateAll: validatePersonalFields } = personalValidation;

const aboutValidation = useAboutValidation();
const { validateAll: validateAboutField } = aboutValidation;

const educationValidation = useEducationValidation();
const { validateAll: validateEducationFields } = educationValidation;


// AHORA: Guardamos toda la instancia en una constante
const languageValidation = useLanguageValidation();
// Extraemos la función para usarla en el script del editor
const { validateAll: validateLanguageFields } = languageValidation;

const experienceValidation = useExperienceValidation();
const { validateAll: validateExperienceFields } = experienceValidation;

const skillValidation = useSkillValidation();
const { validateAll: validateSkillFields } = skillValidation;

const projectValidation = useProjectValidation();
const { validateAll: validateProjectFields } = projectValidation;

const socialValidation = useSocialValidation();
const { validateAll: validateSocialFields } = socialValidation;




const validatePersonal = (): boolean => {
    return validatePersonalFields(formData.personal);
};

const validateAbout = (): boolean => {
    return validateAboutField({
        summary: formData.personal.summary,
        description: formData.personal.description
    });
};

const validateEducation = (): boolean => {
    return validateEducationFields(formData.education);
};

const validateLanguages = (): boolean => {
    return validateLanguageFields(formData.languages);
};

const validateExperience = (): boolean => {
    return validateExperienceFields(formData.experience);
};

const validateSkills = (): boolean => {
    return validateSkillFields(formData.skills);
};

const validateProjects = (): boolean => {
    return validateProjectFields(formData.projects);
};

const validateSocial = (): boolean => {
    return validateSocialFields(formData.social);
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

const isCurrentStepValid = computed(() => {
    return validations[currentStep.value - 1]?.() || false;
});

const isStepValid = (stepId: number): boolean => {
    return validations[stepId - 1]?.() || false;
};

const validStepsCount = computed(() => {
    return steps.value.filter((_, index) => validations[index]?.()).length;
});

const allStepsCompleted = computed(() => {
    return validStepsCount.value === steps.value.length;
});

watch(
    () => formData.personal,
    () => {
        steps.value[0].completed = validatePersonal();
    },
    { deep: true }
);

watch(
    () => formData.personal.description,
    () => {
        steps.value[1].completed = validateAbout();
    },
    { deep: true }
);

watch(
    () => formData.education,
    () => {
        steps.value[2].completed = validateEducation();
    },
    { deep: true }
);

watch(
    () => formData.languages,
    () => {
        steps.value[3].completed = validateLanguages();
    },
    { deep: true }
);


watch(
    () => formData.experience,
    () => {
        steps.value[4].completed = validateExperience();
    },
    { deep: true }
);

watch(
    () => formData.skills,
    () => {
        steps.value[5].completed = validateSkills();
    },
    { deep: true }
);

watch(
    () => formData.projects,
    () => {
        steps.value[6].completed = validateProjects();
    },
    { deep: true }
);



// ============================================
// NUEVA LÓGICA DE VALIDACIÓN CON FEEDBACK
// ============================================

const showValidationError = ref(false);
const validationErrorTimeout = ref<NodeJS.Timeout | null>(null);

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
// PROGRESO CALCULADO
// ============================================

const progress = computed(() => {
    const completedSteps = steps.value.filter((step) => step.completed).length;
    return Math.round((completedSteps / steps.value.length) * 100);
});

const stepCompletion = computed(() => {
    return steps.value.map((step, index) => ({
        stepId: step.id,
        completed: validations[index]?.() || false,
    }));
});

// ============================================
// FUNCIONES DE NAVEGACIÓN
// ============================================

const nextStep = () => {
    if (!isCurrentStepValid.value) {
        showValidationError.value = true;



        if (currentStep.value === 1) {
            // Marcar todos los campos como tocados para mostrar errores
            personalValidation.markAllAsTouched(formData.personal);
            // Re-validar para que se muestren los errores
            validatePersonal();
        }

        if (currentStep.value === 2) {
            // Marcar todos los campos como tocados para mostrar errores
            aboutValidation.markAllAsTouched(formData.personal);
            // Re-validar para que se muestren los errores
            validateAbout();
        }

        if (currentStep.value === 3) {
            // Marcar todos los campos como tocados para mostrar errores
            educationValidation.markAllAsTouched(formData.education);
            // Re-validar para que se muestren los errores
            validateEducation();
        }
        // Si el paso actual es Idiomas (ID 4)
        if (currentStep.value === 4) {
            // Marcar todos los campos como tocados para mostrar errores
            languageValidation.markAllAsTouched(formData.languages);
            // Re-validar para que se muestren los errores
            validateLanguages();
        }

        // Si el paso actual es Experiencia (ID 5)
        if (currentStep.value === 5) {
            // Marcar todos los campos como tocados para mostrar errores
            experienceValidation.markAllAsTouched(formData.experience);
            // Re-validar para que se muestren los errores
            validateExperience();
        }

        if (currentStep.value === 6) {
            // Marcar todos los campos como tocados para mostrar errores
            skillValidation.markAllAsTouched(formData.skills);
            // Re-validar para que se muestren los errores
            validateSkills();
        }

        if (currentStep.value === 7) {
            // Marcar todos los campos como tocados para mostrar errores
            projectValidation.markAllAsTouched(formData.projects);
            // Re-validar para que se muestren los errores
            validateProjects();
        }

        if (currentStep.value === 8) {
            // Marcar todos los campos como tocados para mostrar errores
            socialValidation.markAllAsTouched(formData.social);
            // Re-validar para que se muestren los errores
            validateSocial();
        }

        const nextButton = document.querySelector('[data-next-button]');
        if (nextButton) {
            nextButton.classList.add('shake-animation');
            setTimeout(() => {
                nextButton.classList.remove('shake-animation');
            }, 400);
        }

        if (validationErrorTimeout.value) {
            clearTimeout(validationErrorTimeout.value);
        }
        validationErrorTimeout.value = setTimeout(() => {
            showValidationError.value = false;
        }, 4000);

        return;
    }

    showValidationError.value = false;
    if (validationErrorTimeout.value) {
        clearTimeout(validationErrorTimeout.value);
    }

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
// GUARDAR CAMBIOS
// ============================================

const isSaving = ref(false);
const isSaved = ref(false);
const SUCCESS_DURATION = 2500;

const saveChanges = async () => {
    try {
        isSaving.value = true;
        isSaved.value = false;

        const templateData = {
            personal: {
                firstName: formData.personal.firstName.trim(),
                lastName: formData.personal.lastName.trim(),
                title: formData.personal.title,
                email: formData.personal.email,
                phone: formData.personal.phone,
                location: `${formData.personal.city}, ${formData.personal.country}`
                    .trim()
                    .replace(/^,\s*|\s*,$/g, ''),
                website: formData.social.website ?? formData.personal.website,
                photo: formData.personal.photo || null,
                linkedin: formData.social.linkedin ?? formData.personal.linkedin,
                github: formData.social.github ?? formData.personal.github,
                summary: formData.personal.summary || formData.personal.summary,
                description: formData.personal.description,
            },
            experience: formData.experience.map((exp: any) => ({
                company: exp.company,
                position: exp.position,
                startDate: exp.startDate,
                endDate: exp.endDate,
                current: exp.current,
                description: exp.description,
            })),
            skills: {
                technical: formData.skills.technical.map((skill: any) => ({
                    name: skill.name,
                    level: skill.level
                })),
                soft: formData.skills.soft.map((skill: any) => ({
                    name: skill.name,
                    level: skill.level
                })),
            },
            projects: formData.projects,
            education: formData.education,
            languages: formData.languages,
            certifications: [],
        };

        router.put(
            `/dashboard/portfolio/${props.portfolio.id}`,
            {
                template_data: templateData,
                config: {
                    theme: formData.config.theme,
                    is_public: formData.config.is_public,
               
                },
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    isSaving.value = false;
                    isSaved.value = true;

                    originalFormData.value = JSON.parse(JSON.stringify(formData));
                    savedPortfolioState.value.is_public = formData.config.is_public;

                    setTimeout(() => {
                        isSaved.value = false;
                    }, SUCCESS_DURATION);
                },
                onError: () => {
                    isSaving.value = false;
                },
            },
        );
    } catch (error) {
        isSaving.value = false;
        console.error('Error al guardar:', error);
    }
};

// ============================================
// FINALIZACIÓN DE FORMULARIO
// ============================================

const showCompleteModal = ref(false);
const isCompleting = ref(false);
const completionMessage = ref('');

const completeForm = async () => {
    try {
        if (!allStepsCompleted.value) {
            console.warn('No todos los pasos están completos');
            return;
        }

        isCompleting.value = true;
        completionMessage.value = 'Finalizando formulario...';

        const templateData = {
            personal: {
                firstName: formData.personal.firstName.trim(),
                lastName: formData.personal.lastName.trim(),
                title: formData.personal.title,
                email: formData.personal.email,
                phone: formData.personal.phone,
                location: `${formData.personal.city}, ${formData.personal.country}`
                    .trim()
                    .replace(/^,\s*|\s*,$/g, ''),
                website: formData.social.website ?? formData.personal.website,
                photo: formData.personal.photo || null,
                linkedin: formData.social.linkedin ?? formData.personal.linkedin,
                github: formData.social.github ?? formData.personal.github,
                summary: formData.personal.summary || formData.personal.summary,
                description: formData.personal.description,
            },
            experience: formData.experience.map((exp: any) => ({
                company: exp.company,
                position: exp.position,
                startDate: exp.startDate,
                endDate: exp.endDate,
                current: exp.current,
                description: exp.description,
            })),
            skills: {
                technical: formData.skills.technical.map((skill: any) => ({
                    name: skill.name,
                    level: skill.level
                })),
                soft: formData.skills.soft.map((skill: any) => ({
                    name: skill.name,
                    level: skill.level
                })),
            },
            projects: formData.projects,
            education: formData.education,
            languages: formData.languages,
            certifications: [],
        };

        router.put(
            `/dashboard/portfolio/${props.portfolio.id}`,
            {
                template_data: templateData,
                config: {
                    theme: formData.config.theme,
                    is_public: formData.config.is_public,
                    is_completed: true
                },
                status: 'completed',
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    isCompleting.value = false;
                    completionMessage.value = '¡Formulario completado exitosamente!';

                    setTimeout(() => {
                        showCompleteModal.value = false;
                        setTimeout(() => {
                            router.visit('/dashboard/portfolio');
                        }, 1500);
                    }, 2000);
                },
                onError: (errors) => {
                    isCompleting.value = false;
                    completionMessage.value = 'Error al completar el formulario. Intenta nuevamente.';
                    console.error('Error al completar:', errors);
                },
            },
        );
    } catch (error) {
        isCompleting.value = false;
        completionMessage.value = 'Error inesperado. Intenta nuevamente.';
        console.error('Error:', error);
    }
};

const openCompleteModal = () => {
    if (!allStepsCompleted.value) {
        return;
    }
    showCompleteModal.value = true;
};

const closeCompleteModal = () => {
    if (!isCompleting.value) {
        showCompleteModal.value = false;
    }
};

// ============================================
// FUNCIONES DE CERRAR/TERMINAR
// ============================================

const showExitConfirm = ref(false);
const isExiting = ref(false);

const handleFinish = async () => {
    if (hasUnsavedChanges.value) {
        showExitConfirm.value = true;
    } else {
        await finishAndRedirect();
    }
};

const finishAndRedirect = async () => {
    isExiting.value = true;

    if (hasUnsavedChanges.value) {
        await saveChanges();
        await new Promise(resolve => setTimeout(resolve, 1500));
    }

    router.visit('/dashboard');
};

const cancelExit = () => {
    showExitConfirm.value = false;
};

// ============================================
// MONITOR DE CAMBIOS EN TIEMPO REAL
// ============================================

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

const handleBeforeUnload = (e: BeforeUnloadEvent) => {
    if (hasUnsavedChanges.value) {
        e.preventDefault();
        e.returnValue = '';
    }
};

window.addEventListener('beforeunload', handleBeforeUnload);

// ============================================
// VISTA PREVIA COMPLETA
// ============================================

const showFullPreview = ref(false);

const openFullPreview = () => {
    showFullPreview.value = true;
};

const closeFullPreview = () => {
    showFullPreview.value = false;
};

onBeforeUnmount(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload);
    if (validationErrorTimeout.value) {
        clearTimeout(validationErrorTimeout.value);
    }
});

watch(showFullPreview, (newVal) => {
    document.body.classList.toggle('modal-open', newVal);
});
</script>



<template>
    <div class="min-h-screen bg-gray-50/50">
        <!-- Header -->
        <header class="border-b border-gray-200/60 bg-white sticky top-0 z-30">
            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
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

                    <!-- Derecha: Acciones -->
                    <div class="flex items-center space-x-3">
                        <!-- Botón Público/Privado (SIEMPRE MUESTRA ESTADO GUARDADO) -->
                        <button 
  @click="openPublicToggleModal" 
  :disabled="!formData.config.is_completed"
  :class="[
    'hidden items-center space-x-2 rounded-lg px-4 py-2 font-medium transition-all duration-200 sm:flex',
    !formData.config.is_completed
      ? 'bg-gray-50 text-gray-400 border border-gray-200 cursor-not-allowed opacity-50'
      : formData.config.is_public
        ? 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100'
        : 'bg-gray-100 text-gray-700 border border-gray-300 hover:bg-gray-200'
  ]">
  <Globe v-if="formData.config.is_public && formData.config.is_completed" class="h-4 w-4" />
  <Lock v-else class="h-4 w-4" />
  <span class="text-sm">
    {{ !formData.config.is_completed ? 'Desactivado' : (formData.config.is_public ? 'Público' : 'Privado') }}
  </span>
</button>

                        <button @click="openFullPreview"
                            class="hidden items-center space-x-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-50 sm:flex">
                            <Eye class="h-4 w-4" />
                            <span>Vista previa</span>
                        </button>

                        <!-- Botón Guardar mejorado -->
                        <button @click="saveChanges" :disabled="isSaving || isSaved || !hasUnsavedChanges" :class="[
                            'flex items-center space-x-2 rounded-lg font-medium px-4 py-2 transition-all duration-300',
                            !isSaving && !isSaved && hasUnsavedChanges && 'bg-[#005aeb] text-white hover:bg-[#0048c4] shadow-md hover:shadow-lg',
                            isSaving && 'bg-[#005aeb] text-white opacity-75 cursor-not-allowed',
                            isSaved && 'bg-green-500 text-white shadow-lg shadow-green-500/30',
                            !hasUnsavedChanges && !isSaving && !isSaved && 'bg-gray-100 text-gray-500 cursor-not-allowed'
                        ]">
                            <Loader2 v-if="isSaving" class="w-5 h-5 animate-spin" />
                            <Check v-else-if="isSaved" class="w-5 h-5 animate-bounce" />
                            <Save v-else  class="h-4 w-4" />
                            <span class="text-sm">
                                {{ isSaving ? 'Guardando...' : isSaved ? '¡Guardado!' : 'Guardar' }}
                            </span>
                        </button>

                        <!-- Dropdown con más opciones -->
                        <div class="relative group">
                            <button
                                class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200 text-gray-600 hover:text-gray-900">
                                <MoreVertical class="h-5 w-5" />
                            </button>

                            <!-- Menu desplegable -->
                            <div
                                class="absolute right-0 mt-2 w-48 rounded-lg border border-gray-200 bg-white shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-20">
                                <button @click="router.visit(`/dashboard/portfolio/${portfolio.id}`)"
                                    :disabled="!savedPortfolioState.is_public" class="flex items-center gap-3 w-full px-4 py-2.5 text-left text-sm transition-colors first:rounded-t-lg
                                        text-gray-700 hover:bg-gray-50
                                        disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-white">

                                    <Eye class="w-5 h-5" />

                                    Ver portafolio público
                                </button>

                                <button @click="handleFinish"
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

        <!-- Contenido principal -->
        <div class="container mx-auto px-6 py-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <!-- Columna Izquierda - Stepper -->
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
                                        <component :is="step.icon" v-else-if="currentStep === step.id"
                                            class="h-4 w-4" />
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
                                    <span 
                                        :class="[
                                        'flex items-center ml-2 gap-1',
                                        isPortfolioPublicInHeader ? 'text-blue-600' : 'text-gray-600'
                                        ]"
                                    >
                                        <!-- Public State -->
                                        <template v-if="isPortfolioPublicInHeader">
                                        <Globe class="h-4 w-4 flex-shrink-0" />
                                        <span>Público</span>
                                        </template>

                                        <!-- Private State -->
                                        <template v-else>
                                        <Lock class="h-4 w-4 flex-shrink-0" />
                                        <span>Privado</span>
                                        </template>
                                    </span>
                                </p>


                                <p class="mt-1 text-xs text-gray-600">
                                    ID: {{ portfolio.id }}
                                </p>
                            </div>

                            <!-- Enlaces legales -->
                            <div class="space-y-2 text-xs text-gray-500">
                                <div class="flex space-x-4">
                                    <a href="#"
                                        class="transition-colors duration-200 hover:text-gray-700">Condiciones</a>
                                    <a href="#"
                                        class="transition-colors duration-200 hover:text-gray-700">Privacidad</a>
                                    <a href="#" class="transition-colors duration-200 hover:text-gray-700">Contacto</a>
                                </div>
                                <p class="text-gray-400">
                                    &copy; 2024 PortafolioAI. Todos los derechos reservados.
                                </p>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <!-- Columna Central - Formulario -->
                <div class="lg:col-span-5">
                    <div class="rounded-2xl border border-gray-200/60 bg-white p-8 shadow-xs">
                        <!-- Alerta de validación -->
                        <Transition enter-active-class="transition-all duration-300 ease-out"
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
                                        Corrige los campos que salgan en rojo. Los que tienen (*) son obligatorios<br>
                                    </p>
                                </div>
                                <button @click="showValidationError = false"
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
                                <!-- Componentes Dinámicos -->
                                <PersonalSection v-if="currentStep === 1" v-model="formData.personal"
                                    :validation="personalValidation" />
                                <AboutSection v-if="currentStep === 2" v-model="formData.personal"
                                    :validation="aboutValidation" />
                                <EducationSection v-if="currentStep === 3" v-model="formData.education"
                                    :validation="educationValidation" />
                                <LanguageSection v-if="currentStep === 4" v-model="formData.languages"
                                    :languages="formData.languages" :validation="languageValidation" />
                                <ExperienceSection v-if="currentStep === 5" v-model="formData.experience"
                                    :validation="experienceValidation" />
                                <SkillsSection v-if="currentStep === 6" v-model="formData.skills"
                                    :validation="skillValidation" />
                                <ProjectsSection v-if="currentStep === 7" v-model="formData.projects"
                                    :validation="projectValidation" />
                                <SocialSection v-if="currentStep === 8" v-model="formData.social"
                                    :validation="socialValidation" />
                                <ConfigSection v-if="currentStep === 9" v-model="formData.config" />
                            </div>
                        </Transition>

                        <!-- Navegación entre pasos -->
                        <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-8">
                            <button @click="prevStep"
                                class="flex items-center space-x-2 rounded-lg border border-gray-300 px-6 py-3 text-gray-700 transition-all duration-200 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="currentStep === 1">
                                <ArrowLeft class="h-4 w-4" />
                                <span>Anterior</span>
                            </button>

                            <!-- Botón dinámico: Siguiente o Finalizar -->
                            <button v-if="currentStep === steps.length && allStepsCompleted" @click="openCompleteModal"
                                class="flex items-center space-x-2 rounded-lg px-6 py-3 text-white transition-all duration-300 font-medium bg-green-500 hover:bg-green-600 shadow-md hover:shadow-lg cursor-pointer">
                                <CheckCircle class="h-4 w-4" />
                                <span>Finalizar formulario</span>
                            </button>

                            <button v-else @click="nextStep" data-next-button
                                class="flex items-center space-x-2 rounded-lg px-6 py-3 text-white transition-all duration-300 font-medium bg-[#005aeb] hover:bg-[#0048c4] shadow-md hover:shadow-lg cursor-pointer">
                                <span>{{ currentStep === steps.length ? 'Finalizar' : 'Siguiente paso' }}</span>
                                <ArrowRight class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Vista Previa -->
                <div class="lg:col-span-4">
                    <div
                        class="sticky top-8 h-[calc(100vh-8rem)] overflow-hidden rounded-2xl border border-gray-200/60 bg-white shadow-xs">
                        <PreviewContainer>
                            <component :is="currentTemplate" :data="{
                                personal: {
                                    name: `${formData.personal.firstName} ${formData.personal.lastName}`.trim(),
                                    title: formData.personal.title,
                                    email: formData.personal.email,
                                    phone: formData.personal.phone,
                                    photo: formData.personal.photo,
                                    location:
                                        `${formData.personal.city}, ${formData.personal.country}`
                                            .trim()
                                            .replace(
                                                /^,\s*|\s*,$/g,
                                                '',
                                            ),
                                    website: formData.social.website || formData.personal.website,
                                    linkedin:
                                        formData.social.linkedin || formData.personal.linkedin,
                                    github: formData.social.github || formData.personal.github,
                                    summary: formData.personal.summary || formData.personal.summary,
                                    description: formData.personal.description,
                                },
                                experience: formData.experience.map(
                                    (exp: any) => ({
                                        company: exp.company,
                                        position: exp.position,
                                        startDate: exp.startDate,
                                        endDate: exp.endDate,
                                        current: exp.current,
                                        description: exp.description,
                                    }),
                                ),
                                skills: {
                                    technical: formData.skills.technical.map((skill: any) => ({
                                        name: skill.name,
                                        level: skill.level,
                                    })),
                                    soft: formData.skills.soft.map((skill: any) => ({
                                        name: skill.name,
                                        level: skill.level,
                                    })),
                                },

                                projects: formData.projects,
                                education: formData.education,
                                languages: formData.languages,
                                certifications: formData.certifications,
                            }" class="w-full" />
                        </PreviewContainer>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de vista previa completa -->
    <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0"
        enter-to-class="opacity-100" leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100" leave-to-class="opacity-0">

        <!-- Backdrop oscuro -->
        <div v-if="showFullPreview"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
            @click="closeFullPreview">

            <!-- Modal -->
            <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 scale-75"
                enter-to-class="opacity-100 scale-100" leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">

                <div class="max-h-[90vh] w-full max-w-9xl transform rounded-2xl bg-white shadow-2xl overflow-hidden" @click.stop>

                    <!-- Header del modal -->
                    <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 p-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">
                                Vista Previa: {{ portfolio.template_type }}
                            </h3>
                            <p class="mt-1 text-gray-600">
                                Vista previa completa de tu portafolio
                            </p>
                        </div>
                        <button @click="closeFullPreview"
                            class="rounded-lg p-2 transition-colors duration-200 hover:bg-gray-200">
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <!-- Contenido del modal -->
                    <div class="max-h-[80vh] overflow-auto">
                        <component :is="currentTemplate" :data="{
                            personal: {
                                name: `${formData.personal.firstName} ${formData.personal.lastName}`.trim(),
                                title: formData.personal.title,
                                email: formData.personal.email,
                                phone: formData.personal.phone,
                                photo: formData.personal.photo,
                                location:
                                    `${formData.personal.city}, ${formData.personal.country}`
                                        .trim()
                                        .replace(/^,\s*|\s*,$/g, ''),
                                website: formData.social.website || formData.personal.website,
                                linkedin: formData.social.linkedin || formData.personal.linkedin,
                                github: formData.social.github || formData.personal.github,
                                summary: formData.personal.summary || formData.personal.summary,
                                description: formData.personal.description,
                            },
                            experience: formData.experience.map((exp: any) => ({
                                company: exp.company,
                                position: exp.position,
                                startDate: exp.startDate,
                                endDate: exp.endDate,
                                current: exp.current,
                                description: exp.description,
                            })),
                            skills: {
                                technical: formData.skills.technical.map((skill: any) => ({
                                    name: skill.name,
                                    level: skill.level,
                                })),
                                soft: formData.skills.soft.map((skill: any) => ({
                                    name: skill.name,
                                    level: skill.level,
                                })),
                            },
                            projects: formData.projects,
                            education: formData.education,
                            languages: formData.languages,
                            certifications: formData.certifications,
                        }" />
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>

    <!-- Modal de Finalización -->
    <Transition name="modal-fade">
        <div v-if="showCompleteModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
            @click="closeCompleteModal">
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
                                <span class="font-semibold">✓ Pasos completados:</span> {{ validStepsCount }}/{{
                                    steps.length }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3 border border-green-100">
                            <p class="text-sm text-green-900">
                                <span class="font-semibold">📋 Estado:</span> Completaste todos los pasos del
                                formulario. Tu portafolio ya está preparado.

                            </p>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="border-t border-gray-100 bg-gray-50 px-6 py-4">
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="closeCompleteModal" :disabled="isCompleting"
                                class="flex items-center justify-center space-x-2 rounded-lg border border-gray-300 px-4 py-2.5 text-gray-700 font-medium transition-all duration-200 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                <ArrowLeft class="h-4 w-4" />
                                <span>Volver</span>
                            </button>
                            <button @click="completeForm" :disabled="isCompleting || !allStepsCompleted"
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

    <!-- Modal de Toggle Público/Privado (SOLO CAMBIA LO GUARDADO) -->
    <Transition name="modal-fade">
        <div v-if="showPublicToggleModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
            @click="closePublicToggleModal">
            <Transition name="modal-scale">
                <div class="w-full max-w-md transform rounded-2xl bg-white shadow-2xl overflow-hidden" @click.stop>

                    <!-- Header del modal -->
                    <div :class="[
                        'border-b border-gray-100 bg-gradient-to-r px-6 py-5',
                        isPortfolioPublicInHeader
                            ? 'from-blue-50 to-blue-100'
                            : 'from-gray-50 to-gray-100'
                    ]">
                        <div class="flex items-start space-x-3">
                            <div :class="[
                                'rounded-full p-2.5',
                                isPortfolioPublicInHeader
                                    ? 'bg-blue-100'
                                    : 'bg-gray-200'
                            ]">
                                <Globe v-if="isPortfolioPublicInHeader" class="h-5 w-5 text-blue-600" />
                                <Lock v-else class="h-5 w-5 text-gray-600" />
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ isPortfolioPublicInHeader ? 'Portafolio Público' : 'Portafolio Privado' }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ isPortfolioPublicInHeader
                                        ? 'Tu portafolio es visible para todos'
                                        : 'Tu portafolio es visible solo para ti' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido -->
                    <div class="px-6 py-4">
                        <div v-if="isPortfolioPublicInHeader" class="rounded-lg bg-blue-50 p-3 border border-blue-100">
                            <p class="text-sm text-blue-900">
                                <span class="font-semibold">Público:</span> Cualquiera con el enlace puede ver tu
                                portafolio.
                            </p>
                        </div>
                        <div v-else class="rounded-lg bg-gray-50 p-3 border border-gray-200">
                            <p class="text-sm text-gray-900">
                                <span class="font-semibold">Privado:</span> Solo tú puedes ver tu portafolio en el
                                editor.
                            </p>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="border-t border-gray-100 bg-gray-50 px-6 py-4">
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="closePublicToggleModal" :disabled="isTogglingPublic"
                                class="flex items-center justify-center space-x-2 rounded-lg border border-gray-300 px-4 py-2.5 text-gray-700 font-medium transition-all duration-200 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span>Cancelar</span>
                            </button>
                            <button @click="togglePublicStatus" :disabled="isTogglingPublic" :class="[
                                'flex items-center justify-center space-x-2 rounded-lg px-4 py-2.5 text-white font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed',
                                isPortfolioPublicInHeader
                                    ? 'bg-gray-500 hover:bg-gray-600'
                                    : 'bg-blue-500 hover:bg-blue-600'
                            ]">
                                <Loader2 v-if="isTogglingPublic" class="h-4 w-4 animate-spin flex-shrink-0" />
                                <span>{{ isTogglingPublic ? 'Cambiando...' : (isPortfolioPublicInHeader ? 'Hacer Privado' : 'Hacer Público') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Mensaje de estado -->
                    <Transition name="fade">
                        <div v-if="publicToggleMessage" class="border-t border-gray-100 bg-gray-50 px-6 py-3">
                            <p class="text-xs text-center font-medium text-green-600">
                                {{ publicToggleMessage }}
                            </p>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </div>
    </Transition>

    <!-- Modal de Confirmación al Salir -->
    <Transition name="modal-fade">
        <div v-if="showExitConfirm"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
            @click="cancelExit">
            <Transition name="modal-scale">
                <div class="w-full max-w-md transform rounded-2xl bg-white shadow-2xl" @click.stop>

                    <!-- Header del modal -->
                    <div class="border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-5">
                        <div class="flex items-start space-x-3">
                            <div class="rounded-full bg-amber-100 p-2.5">
                                <AlertCircle class="h-5 w-5 text-amber-600" />
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ currentStep === steps.length ? '¿Terminar edición?' : '¿Salir sin terminar?' }}
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
                                <span class="font-semibold">⚠️ Cambios pendientes:</span> Se guardarán automáticamente
                                al terminar.
                            </p>
                        </div>
                        <div v-else class="rounded-lg bg-green-50 p-3 border border-green-100">
                            <p class="text-sm text-green-900">
                                <span class="font-semibold">✓ Todo guardado:</span> Tu portafolio está actualizado.
                            </p>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="border-t border-gray-100 bg-gray-50 px-6 py-4">
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="cancelExit"
                                class="flex items-center justify-center space-x-2 rounded-lg border border-gray-300 px-4 py-2.5 text-gray-700 font-medium transition-all duration-200 hover:bg-gray-100">
                                <ArrowLeft class="h-4 w-4" />
                                <span>Volver a editar</span>
                            </button>
                            <button @click="finishAndRedirect" :disabled="isExiting"
                                class="flex items-center justify-center space-x-2 rounded-lg bg-[#005aeb] px-4 py-2.5 text-white font-medium transition-all duration-200 hover:bg-[#0048c4] disabled:opacity-50 disabled:cursor-not-allowed">
                                <Check class="h-4 w-4" />
                                <span>{{ isExiting ? 'Cerrando...' : 'Terminar' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>



<style scoped>
/* ============================================
   CSS MODAL VISTA PREVIA COMPLETA
   ============================================ */

:global(body.modal-open) {
    overflow: hidden !important;
    position: fixed;
    width: 100%;
}

/* ============================================
   POSICIONAMIENTO
   ============================================ */

.sticky {
    position: sticky;
}

/* ============================================
   FOCUS STATES
   ============================================ */

input:focus,
select:focus {
    outline: none;
}

/* ============================================
   TRANSICIONES SUAVES
   ============================================ */

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Shadow muy sutiles */
.shadow-xs {
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
}

/* ============================================
   ANIMACIÓN SUAVE PARA CHECKMARK - GUARDAR
   ============================================ */

@keyframes bounce-smooth {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-3px);
    }
}

.animate-bounce {
    animation: bounce-smooth 0.6s ease-in-out;
}

/* ============================================
   TRANSICIONES SUAVES DE COLORES EN BOTONES
   ============================================ */

button {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* ============================================
   ANIMACIÓN DE ENTRADA SUAVE PARA PASOS
   ============================================ */

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.group {
    animation: slideIn 0.3s ease-out;
}

/* ============================================
   ANIMACIÓN SHAKE - BOTÓN VALIDACIÓN FALLIDA
   ============================================ */

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

/* ============================================
   TRANSICIÓN ENTRE STEPS DEL FORMULARIO
   ============================================ */

.step-slide-enter-active,
.step-slide-leave-active {
    transition: all 0.35s ease;
}

/* Estado inicial cuando entra el nuevo step */
.step-slide-enter-from {
    opacity: 0;
    transform: translateX(20px);
}

/* Estado final cuando sale el step actual */
.step-slide-leave-to {
    opacity: 0;
    transform: translateX(-20px);
}

/* ============================================
   INDICADOR VISUAL DE CAMBIOS SIN GUARDAR
   ============================================ */

:global(body.has-unsaved-changes) {
    --unsaved-indicator: #f59e0b;
}

/* ============================================
   TRANSICIONES MODALES
   ============================================ */

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

/* ============================================
   TRANSICIÓN FADE (PARA MENSAJES)
   ============================================ */

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* ============================================
   INDICADOR ANIMADO PULSANTE
   ============================================ */

@keyframes pulse-gentle {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.7;
    }
}

.animate-pulse {
    animation: pulse-gentle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* ============================================
   ANIMACIÓN SPIN (PARA LOADING)
   ============================================ */

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* ============================================
   ESTILOS PARA MEJOR ACCESIBILIDAD
   ============================================ */

/* Asegurar que los botones sean fáciles de clickear */
button {
    min-height: 2.5rem;
    min-width: 2.5rem;
}

/* Mejorar visibilidad en focus */
button:focus-visible {
    outline: 2px solid #005aeb;
    outline-offset: 2px;
}

/* ============================================
   MEJORAS VISUALES EN MODALES
   ============================================ */

/* Gradiente suave en headers */
.bg-gradient-to-r {
    background-image: linear-gradient(to right, var(--tw-gradient-stops));
}

/* Sombra consistente en modales */
.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
}

/* ============================================
   RESPONSIVE IMPROVEMENTS
   ============================================ */

@media (max-width: 768px) {
    .sticky {
        position: relative;
    }

    button {
        min-height: 2rem;
        min-width: 2rem;
        padding: 0.5rem 1rem;
    }
}

/* ============================================
   UTILIDADES DE OVERFLOW (PARA MODALES)
   ============================================ */

.overflow-auto {
    overflow: auto;
    -webkit-overflow-scrolling: touch;
}

/* ============================================
   ANIMACIÓN SUAVE PARA COMPLETACIÓN
   ============================================ */

@keyframes completion-pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
    }

    70% {
        box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
    }

    100% {
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
    }
}

.animate-completion {
    animation: completion-pulse 2s infinite;
}

/* ============================================
   Z-INDEX MANAGEMENT
   ============================================ */

/* Asegurar que los dropdowns aparezcan sobre otros elementos */
.z-20 {
    z-index: 20;
}

.z-50 {
    z-index: 50;
}
</style>