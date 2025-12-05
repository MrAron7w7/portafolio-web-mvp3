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

    // Validar un campo individual
    const validateField = (
        index: number,
        field: keyof Experience,
        value: any,
        experience: Experience
    ): boolean => {
        initErrors(index);

        switch (field) {
            case 'company':
            case 'position':
            case 'description': {
                const fieldRules = rules[field] || [];
                for (const rule of fieldRules) {
                    const error = rule(value);
                    if (error) {
                        errors[index][field] = error;
                        return false;
                    }
                }
                delete errors[index][field];
                return true;
            }

            case 'startDate': {
                if (!value) {
                    errors[index].startDate = 'La fecha de inicio es requerida';
                    return false;
                }
                // Validar que no sea posterior a endDate
                if (experience.endDate && !experience.current && value > experience.endDate) {
                    errors[index].startDate = 'La fecha de inicio no puede ser posterior a la de fin';
                    if (!errors[index].endDate) {
                        errors[index].endDate = 'La fecha de fin no puede ser anterior a la de inicio';
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
                        errors[index].endDate = 'La fecha de fin es requerida o marca "Trabajo actual"';
                        return false;
                    }
                    if (experience.startDate && value < experience.startDate) {
                        errors[index].endDate = 'La fecha de fin no puede ser anterior a la de inicio';
                        if (!errors[index].startDate) {
                            errors[index].startDate = 'La fecha de inicio no puede ser posterior a la de fin';
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
                        errors[index].endDate = 'La fecha de fin es requerida o marca "Trabajo actual"';
                        return false;
                    }
                }
                return true;
            }

            default:
                return true;
        }
    };

    // Validar toda una experiencia
    const validateExperience = (index: number, experience: Experience): boolean => {
        let isValid = true;

        // Validar campos requeridos
        if (!validateField(index, 'company', experience.company, experience)) {
            isValid = false;
        }
        if (!validateField(index, 'position', experience.position, experience)) {
            isValid = false;
        }
        if (!validateField(index, 'startDate', experience.startDate, experience)) {
            isValid = false;
        }
        if (!experience.current) {
            if (!validateField(index, 'endDate', experience.endDate, experience)) {
                isValid = false;
            }
        }
        if (!validateField(index, 'description', experience.description, experience)) {
            isValid = false;
        }

        return isValid;
    };

    // Validar todas las experiencias
    const validateAll = (experiences: Experience[]): boolean => {
        let allValid = true;
        experiences.forEach((exp, index) => {
            if (!validateExperience(index, exp)) {
                allValid = false;
            }
        });
        return allValid;
    };

    // Obtener clase CSS para el input según si tiene error
    const getErrorClass = (index: number, field: keyof ExperienceErrors): string => {
        return errors[index]?.[field]
            ? 'border-red-500 focus:border-red-500 focus:ring-red-200'
            : 'border-gray-300 focus:border-[#005aeb] focus:ring-[#005aeb]';
    };

    // Verificar si un índice tiene errores
    const hasErrors = (index: number): boolean => {
        return Object.keys(errors[index] || {}).length > 0;
    };

    // Limpiar errores de un índice específico
    const clearErrorsForIndex = (index: number) => {
        delete errors[index];
    };

    // Limpiar todos los errores
    const clearAllErrors = () => {
        Object.keys(errors).forEach((key) => {
            delete errors[Number(key)];
        });
    };

    // Reindexar errores después de eliminar un elemento
    const reindexErrors = (removedIndex: number) => {
        const newErrors: { [key: number]: ExperienceErrors } = {};
        Object.keys(errors).forEach((key) => {
            const numKey = Number(key);
            if (numKey < removedIndex) {
                newErrors[numKey] = errors[numKey];
            } else if (numKey > removedIndex) {
                newErrors[numKey - 1] = errors[numKey];
            }
        });
        // Limpiar y reasignar
        clearAllErrors();
        Object.assign(errors, newErrors);
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
        validateField,
        validateExperience,
        validateAll,
        getErrorClass,
        hasErrors,
        clearErrorsForIndex,
        clearAllErrors,
        reindexErrors,
        getCharCount,
    };
};