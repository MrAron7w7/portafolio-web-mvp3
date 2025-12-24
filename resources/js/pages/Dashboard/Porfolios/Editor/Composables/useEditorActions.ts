// ============================================
// composables/useEditorActions.ts
// ============================================
import { ref } from 'vue';

export function useEditorActions(
    router: any,
    props: any,
    formData: any,
    originalFormData: any,
    savedPortfolioState: any,
    currentStep: any,
    steps: any,
) {
    const isSaving = ref(false);
    const isSaved = ref(false);
    const isCompleting = ref(false);
    const isTogglingPublic = ref(false);
    const SUCCESS_DURATION = 2500;

    // ============================================
    // FUNCIÓN AUXILIAR: SERIALIZAR DATOS
    // ============================================
    const serializeFormData = () => {
        return {
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
    };

    // ============================================
    // GUARDAR CAMBIOS
    // ============================================
    const saveChanges = async () => {
        try {
            isSaving.value = true;
            isSaved.value = false;

            const templateData = serializeFormData();

            // Si es edición pública, usar fetch en lugar de Inertia router
            if (props.isPublicEdit) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch(`/p/${props.portfolio.slug}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ template_data: templateData })
                });

                if (response.ok) {
                    isSaving.value = false;
                    isSaved.value = true;
                    originalFormData.value = JSON.parse(JSON.stringify(formData));

                    setTimeout(() => {
                        isSaved.value = false;
                    }, SUCCESS_DURATION);
                    return true;
                } else {
                    const error = await response.json();
                    console.error('Error al guardar (público):', error);
                    isSaving.value = false;
                    return false;
                }
            }

            // Modo normal (autenticado) - usar Inertia router
            return new Promise((resolve) => {
                router.put(
                    `/dashboard/portfolio/${props.portfolio.id}`,
                    {
                        template_data: templateData,
                        config: {
                            title: formData.config.title,
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
                                resolve(true);
                            }, SUCCESS_DURATION);
                        },
                        onError: () => {
                            isSaving.value = false;
                            resolve(false);
                        },
                    },
                );
            });
        } catch (error) {
            isSaving.value = false;
            console.error('Error al guardar:', error);
            return false;
        }
    };

    // ============================================
    // COMPLETAR FORMULARIO
    // ============================================
    const completionMessage = ref('');

    const completeForm = async () => {
        try {
            isCompleting.value = true;
            completionMessage.value = 'Finalizando formulario...';

            const templateData = serializeFormData();

            return new Promise((resolve) => {
                router.put(
                    `/dashboard/portfolio/${props.portfolio.id}`,
                    {
                        template_data: templateData,
                        config: {
                            title: formData.config.title,
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
                                router.visit('/dashboard');
                                resolve(true);
                            }, 2000);
                        },
                        onError: (errors: any) => {
                            isCompleting.value = false;
                            completionMessage.value = 'Error al completar el formulario. Intenta nuevamente.';
                            console.error('Error al completar:', errors);
                            resolve(false);
                        },
                    },
                );
            });
        } catch (error) {
            isCompleting.value = false;
            completionMessage.value = 'Error inesperado. Intenta nuevamente.';
            console.error('Error:', error);
            return false;
        }
    };

    // ============================================
    // CAMBIAR ESTADO PÚBLICO/PRIVADO
    // ============================================
    const publicToggleMessage = ref('');

    const togglePublicStatus = async () => {
        try {
            isTogglingPublic.value = true;
            publicToggleMessage.value = '';

            const newPublicStatus = !formData.config.is_public;
            const templateData = serializeFormData();

            return new Promise((resolve) => {
                router.put(
                    `/dashboard/portfolio/${props.portfolio.id}`,
                    {
                        template_data: templateData,
                        config: {
                            title: formData.config.title,
                            theme: formData.config.theme,
                            is_public: newPublicStatus,
                        },
                    },
                    {
                        preserveScroll: true,
                        onSuccess: () => {
                            isTogglingPublic.value = false;

                            formData.config.is_public = newPublicStatus;
                            savedPortfolioState.value.is_public = newPublicStatus;

                            if (newPublicStatus) {
                                publicToggleMessage.value = '✓ Tu portafolio ahora es público';
                            } else {
                                publicToggleMessage.value = '✓ Tu portafolio ahora es privado';
                            }

                            setTimeout(() => {
                                publicToggleMessage.value = '';
                                resolve(true);
                            }, 2000);
                        },
                        onError: () => {
                            isTogglingPublic.value = false;
                            publicToggleMessage.value = 'Error al cambiar estado. Intenta nuevamente.';
                            resolve(false);
                        },
                    },
                );
            });
        } catch (error) {
            isTogglingPublic.value = false;
            publicToggleMessage.value = 'Error inesperado. Intenta nuevamente.';
            console.error('Error:', error);
            return false;
        }
    };

    return {
        isSaving,
        isSaved,
        isCompleting,
        isTogglingPublic,
        completionMessage,
        publicToggleMessage,
        saveChanges,
        completeForm,
        togglePublicStatus,
    };
}