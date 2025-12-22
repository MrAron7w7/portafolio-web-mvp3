// ============================================
// composables/useEditorState.ts
// ============================================
import { computed, reactive, ref } from 'vue';
import Academica from '@/components/Templates/Academica.vue';
import Creativa from '@/components/Templates/Creativa.vue';
import Ejecutiva from '@/components/Templates/Ejecutiva.vue';
import Minimalista from '@/components/Templates/Minimalista.vue';
import Moderna from '@/components/Templates/Moderna.vue';
import Tecnologica from '@/components/Templates/Tecnologica.vue';

export function useEditorState(props: any) {
    // ============================================
    // ESTADO PRINCIPAL
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

    // ============================================
    // PASOS DEL WIZARD
    // ============================================
    const currentStep = ref(1);


const steps = ref([
    { id: 1, title: 'Información personal', completed: false, enabled: true, hasError: false, icon: 'User' },
    // El resto debe iniciar en FALSE (o calcularse dinámicamente según datos previos)
    { id: 2, title: 'Sobre mí', completed: false, enabled: false, hasError: false, icon: 'FileText' },
    { id: 3, title: 'Formación Académica', completed: false, enabled: false, hasError: false, icon: 'Palette' },
    { id: 4, title: 'Idiomas', completed: false, enabled: false, hasError: false, icon: 'Globe' },
    { id: 5, title: 'Experiencia', completed: false, enabled: false, hasError: false, icon: 'Briefcase' },
    { id: 6, title: 'Habilidades', completed: false, enabled: false, hasError: false, icon: 'Star' },
    { id: 7, title: 'Proyectos', completed: false, enabled: false, hasError: false, icon: 'Zap' },
    { id: 8, title: 'Redes sociales', completed: false, enabled: false, hasError: false, icon: 'Link' },
    { id: 9, title: 'Configuración', completed: false, enabled: false, hasError: false, icon: 'Settings' },
]);


    

    // ============================================
    // TEMPLATES
    // ============================================
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

    // ============================================
    // DATOS DEL FORMULARIO
    // ============================================
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
                    return { id: index + 1, name: skill, level: 50, category: 'Technical' };
                } else {
                    return { id: index + 1, name: skill.name || '', level: skill.level || 50, category: 'Technical' };
                }
            }),
            soft: soft.map((skill: any, index: number) => {
                if (typeof skill === 'string') {
                    return { id: index + 1000, name: skill, level: 50, category: 'Soft' };
                } else {
                    return { id: index + 1000, name: skill.name || '', level: skill.level || 50, category: 'Soft' };
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
            title: props.portfolio.title || 'Mi Portafolio',
            theme: 'moderna',
            is_public: props.portfolio.is_public || false,
            is_completed: props.portfolio.is_completed || false,
            accessType: props.portfolio.is_public ? 'link' : 'private', // 'private' | 'link'
            linkPermission: 'view', // 'view' | 'edit'
        },
        languages: props.templateData.languages || [],
        certifications: props.templateData.certifications || [],
    });

    originalFormData.value = JSON.parse(JSON.stringify(formData));

    // ============================================
    // ESTADO PÚBLICO/PRIVADO
    // ============================================
    const isPortfolioPublicInHeader = computed(() => {
        return savedPortfolioState.value.is_public;
    });

    const isPortfolioPublicInForm = computed(() => {
        return formData.config.is_public;
    });

    const hasPublicStatusChanged = computed(() => {
        return formData.config.is_public !== savedPortfolioState.value.is_public;
    });

    // ============================================
    // PROGRESO
    // ============================================
    const progress = computed(() => {
        const completedSteps = steps.value.filter((step) => step.completed).length;
        return Math.round((completedSteps / steps.value.length) * 100);
    });

    return {
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
    };
}