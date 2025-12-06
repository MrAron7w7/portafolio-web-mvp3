import { reactive } from 'vue';

export interface Experience {
    id: number;
    company: string;
    position: string;
    period: string;
    description: string;
    startDate: string;
    endDate: string;
    current: boolean;
}

export interface ExperienceErrors {
    company?: string;
    position?: string;
    startDate?: string;
    endDate?: string;
    description?: string;
}

export const useExperienceValidation = () => {
    // Estado de errores por índice
    const errors = reactive<{ [key: number]: ExperienceErrors }>({});

    // NUEVO: Objeto para rastrear campos tocados
    const touched = reactive<{ 
        [key: number]: { 
            company?: boolean; 
            position?: boolean; 
            startDate?: boolean; 
            endDate?: boolean; 
            description?: boolean;
            current?: boolean;
        } 
    }>({});

    // Reglas de validación
    const rules = {
        company: [
            (value: string) =>
                value?.trim() ? null : 'La empresa es requerida',
            (value: string) =>
                value?.trim().length >= 3 ? null : 'Mínimo 3 caracteres',
            (value: string) =>
                value?.length <= 100 ? null : 'Máximo 100 caracteres',
        ],
        position: [
            (value: string) =>
                value?.trim() ? null : 'El cargo es requerido',
            (value: string) =>
                value?.trim().length >= 3 ? null : 'Mínimo 3 caracteres',
            (value: string) =>
                value?.length <= 100 ? null : 'Máximo 100 caracteres',
        ],
        startDate: [
            (value: string) =>
                value ? null : 'La fecha de inicio es requerida',
        ],
        description: [
            (value: string) =>
                value?.trim() ? null : 'La descripción es requerida',
            (value: string) =>
                value?.trim().length >= 10 ? null : 'Mínimo 10 caracteres',
            (value: string) =>
                value?.length <= 500 ? null : 'Máximo 500 caracteres',
        ],
    };

    // Inicializar errores para un índice
    const initErrors = (index: number) => {
        if (!errors[index]) {
            errors[index] = {};
        }
    };

    // NUEVO: Inicializar estado touched
    const initTouched = (index: number) => {
        if (!touched[index]) {
            touched[index] = {};
        }
    };

    // MODIFICADO: Agregar parámetro forceShow
    const validateField = (
        index: number,
        field: keyof Experience,
        value: any,
        experience: Experience,
        forceShow: boolean = false
    ): boolean => {
        initErrors(index);
        initTouched(index);

        switch (field) {
            case 'company':
            case 'position':
            case 'description': {
                const fieldRules = rules[field] || [];
                for (const rule of fieldRules) {
                    const error = rule(value);
                    if (error) {
                        // Solo guardar error si forceShow=true O el campo fue tocado
                        if (forceShow || touched[index][field]) {
                            errors[index][field as keyof ExperienceErrors] = error;
                        }
                        return false;
                    }
                }
                delete errors[index][field as keyof ExperienceErrors];
                return true;
            }

            case 'startDate': {
                if (!value) {
                    // Solo mostrar error si forceShow o fue tocado
                    if (forceShow || touched[index].startDate) {
                        errors[index].startDate = 'La fecha de inicio es requerida';
                    }
                    return false;
                }
                // Validar que no sea posterior a endDate
                if (experience.endDate && !experience.current && value > experience.endDate) {
                    if (forceShow || touched[index].startDate) {
                        errors[index].startDate = 'La fecha de inicio no puede ser posterior a la de fin';
                    }
                    if (forceShow || touched[index].endDate) {
                        if (!errors[index].endDate) {
                            errors[index].endDate = 'La fecha de fin no puede ser anterior a la de inicio';
                        }
                    }
                    return false;
                }
                delete errors[index].startDate;
                // Limpiar error relacionado de endDate si existía
                if (errors[index].endDate === 'La fecha de fin no puede ser anterior a la de inicio') {
                    delete errors[index].endDate;
                }
                return true;
            }

            case 'endDate': {
                if (!experience.current) {
                    if (!value) {
                        if (forceShow || touched[index].endDate) {
                            errors[index].endDate = 'La fecha de fin es requerida o marca "Trabajo actual"';
                        }
                        return false;
                    }
                    if (experience.startDate && value < experience.startDate) {
                        if (forceShow || touched[index].endDate) {
                            errors[index].endDate = 'La fecha de fin no puede ser anterior a la de inicio';
                        }
                        if (forceShow || touched[index].startDate) {
                            if (!errors[index].startDate) {
                                errors[index].startDate = 'La fecha de inicio no puede ser posterior a la de fin';
                            }
                        }
                        return false;
                    }
                    delete errors[index].endDate;
                    // Limpiar error relacionado de startDate si existía
                    if (errors[index].startDate === 'La fecha de inicio no puede ser posterior a la de fin') {
                        delete errors[index].startDate;
                    }
                    return true;
                }
                delete errors[index].endDate;
                return true;
            }

            case 'current': {
                if (value) {
                    // Si marca "trabajo actual", limpiar error de endDate
                    delete errors[index].endDate;
                } else {
                    // Si desmarca, validar que tenga endDate
                    if (!experience.endDate) {
                        if (forceShow || touched[index].endDate) {
                            errors[index].endDate = 'La fecha de fin es requerida o marca "Trabajo actual"';
                        }
                        return false;
                    }
                }
                return true;
            }

            default:
                return true;
        }
    };

    // NUEVO: Marcar un campo específico como tocado
    const markAsTouched = (index: number, field: keyof Experience) => {
        initTouched(index);
        touched[index][field as keyof typeof touched[number]] = true;
    };

    // NUEVO: Marcar todos los campos como tocados
    const markAllAsTouched = (experiences: Experience[] | undefined | null) => {
        if (!experiences || !Array.isArray(experiences)) return;

        experiences.forEach((_, index) => {
            initTouched(index);
            touched[index].company = true;
            touched[index].position = true;
            touched[index].startDate = true;
            touched[index].endDate = true;
            touched[index].description = true;
            touched[index].current = true;
        });
    };

    // Validar toda una experiencia
    const validateExperience = (index: number, experience: Experience, forceShow: boolean = false): boolean => {
        let isValid = true;

        // Validar campos requeridos
        if (!validateField(index, 'company', experience.company, experience, forceShow)) {
            isValid = false;
        }
        if (!validateField(index, 'position', experience.position, experience, forceShow)) {
            isValid = false;
        }
        if (!validateField(index, 'startDate', experience.startDate, experience, forceShow)) {
            isValid = false;
        }
        if (!experience.current) {
            if (!validateField(index, 'endDate', experience.endDate, experience, forceShow)) {
                isValid = false;
            }
        }
        if (!validateField(index, 'description', experience.description, experience, forceShow)) {
            isValid = false;
        }

        return isValid;
    };

    // MODIFICADO: Agregar parámetro forceShow
    const validateAll = (experiences: Experience[] | undefined | null, forceShow: boolean = false): boolean => {
        if (!experiences || !Array.isArray(experiences)) {
            return true;
        }
        if (experiences.length === 0) {
            return true;
        }

        let allValid = true;
        experiences.forEach((exp, index) => {
            if (!validateExperience(index, exp, forceShow)) {
                allValid = false;
            }
        });
        return allValid;
    };

    // MODIFICADO: Solo aplicar clases de error si el campo fue tocado o forceShow
    const getErrorClass = (index: number, field: keyof ExperienceErrors, forceShow: boolean = false): string => {
        const hasError = errors[index]?.[field];
        const wasTouched = touched[index]?.[field as keyof typeof touched[number]];

        if (hasError && (forceShow || wasTouched)) {
            return 'border-red-500 focus:border-red-500 focus:ring-red-200';
        }
        return 'border-gray-300 focus:border-[#005aeb] focus:ring-[#005aeb]';
    };

    // Verificar si un índice tiene errores
    const hasErrors = (index: number): boolean => {
        return Object.keys(errors[index] || {}).length > 0;
    };

    // Limpiar errores de un índice específico
    const clearErrorsForIndex = (index: number) => {
        delete errors[index];
        delete touched[index];
    };

    // Limpiar todos los errores
    const clearAllErrors = () => {
        Object.keys(errors).forEach((key) => {
            delete errors[Number(key)];
        });
        Object.keys(touched).forEach((key) => {
            delete touched[Number(key)];
        });
    };

    // Reindexar errores después de eliminar un elemento
    const reindexErrors = (removedIndex: number) => {
        const newErrors: { [key: number]: ExperienceErrors } = {};
        const newTouched: { [key: number]: typeof touched[number] } = {};

        Object.keys(errors).forEach((key) => {
            const numKey = Number(key);
            if (numKey < removedIndex) {
                newErrors[numKey] = errors[numKey];
                newTouched[numKey] = touched[numKey];
            } else if (numKey > removedIndex) {
                newErrors[numKey - 1] = errors[numKey];
                newTouched[numKey - 1] = touched[numKey];
            }
        });

        // Reemplazar objetos
        Object.keys(errors).forEach((key) => {
            delete errors[Number(key)];
        });
        Object.keys(touched).forEach((key) => {
            delete touched[Number(key)];
        });

        Object.assign(errors, newErrors);
        Object.assign(touched, newTouched);
    };

    // Contador de caracteres para description
    const getCharCount = (value: string) => {
        const maxChars = 500;
        const currentLength = value?.length || 0;
        return {
            current: currentLength,
            max: maxChars,
            remaining: maxChars - currentLength,
            isNearLimit: currentLength > maxChars * 0.8,
            isOverLimit: currentLength > maxChars,
        };
    };

    return {
        errors,
        touched,
        validateField,
        validateExperience,
        validateAll,
        markAsTouched,
        markAllAsTouched,
        getErrorClass,
        hasErrors,
        clearErrorsForIndex,
        clearAllErrors,
        reindexErrors,
        getCharCount,
    };
};