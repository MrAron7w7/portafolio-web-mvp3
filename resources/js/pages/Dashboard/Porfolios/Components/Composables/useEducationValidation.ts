import { reactive } from 'vue';

export interface Education {
    id: number;
    degree: string;
    institution: string;
    startDate: string;
    endDate: string;
    description: string;
    current: boolean;
}

export interface EducationErrors {
    degree?: string;
    institution?: string;
    startDate?: string;
    endDate?: string;
    description?: string;
}

export const useEducationValidation = () => {
    // Estado de errores por índice de educación
    const errors = reactive<{ [key: number]: EducationErrors }>({});

    // Reglas de validación
    const rules = {
        degree: [
            (value: string) =>
                value?.trim() ? null : 'El título/carrera es requerido',
            (value: string) =>
                value?.trim().length >= 3 ? null : 'Mínimo 3 caracteres',
            (value: string) =>
                value?.length <= 100 ? null : 'Máximo 100 caracteres',
        ],
        institution: [
            (value: string) =>
                value?.trim() ? null : 'La institución es requerida',
            (value: string) =>
                value?.trim().length >= 3 ? null : 'Mínimo 3 caracteres',
            (value: string) =>
                value?.length <= 150 ? null : 'Máximo 150 caracteres',
        ],
        startDate: [
            (value: string) =>
                value ? null : 'La fecha de inicio es requerida',
        ],
        description: [
            (value: string) => {
                if (!value?.trim()) return null; // Opcional
                return value.trim().length >= 10 ? null : 'Mínimo 10 caracteres';
            },
            (value: string) => {
                if (!value?.trim()) return null; // Opcional
                return value.length <= 500 ? null : 'Máximo 500 caracteres';
            },
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
        field: keyof Education,
        value: any,
        education: Education
    ): boolean => {
        initErrors(index);

        switch (field) {
            case 'degree':
            case 'institution':
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
                if (education.endDate && !education.current && value > education.endDate) {
                    errors[index].startDate = 'La fecha de inicio no puede ser posterior a la de graduación';
                    if (!errors[index].endDate) {
                        errors[index].endDate = 'La fecha de graduación no puede ser anterior a la de inicio';
                    }
                    return false;
                }
                delete errors[index].startDate;
                // Limpiar error relacionado de endDate si existía
                if (errors[index].endDate === 'La fecha de graduación no puede ser anterior a la de inicio') {
                    delete errors[index].endDate;
                }
                return true;
            }

            case 'endDate': {
                if (!education.current) {
                    if (!value) {
                        errors[index].endDate = 'La fecha de graduación es requerida o marca "Actualmente estudiando"';
                        return false;
                    }
                    if (education.startDate && value < education.startDate) {
                        errors[index].endDate = 'La fecha de graduación no puede ser anterior a la de inicio';
                        if (!errors[index].startDate) {
                            errors[index].startDate = 'La fecha de inicio no puede ser posterior a la de graduación';
                        }
                        return false;
                    }
                    delete errors[index].endDate;
                    // Limpiar error relacionado de startDate si existía
                    if (errors[index].startDate === 'La fecha de inicio no puede ser posterior a la de graduación') {
                        delete errors[index].startDate;
                    }
                    return true;
                }
                delete errors[index].endDate;
                return true;
            }

            case 'current': {
                if (value) {
                    // Si marca "actualmente estudiando", limpiar error de endDate
                    delete errors[index].endDate;
                } else {
                    // Si desmarca, validar que tenga endDate
                    if (!education.endDate) {
                        errors[index].endDate = 'La fecha de graduación es requerida o marca "Actualmente estudiando"';
                        return false;
                    }
                }
                return true;
            }

            default:
                return true;
        }
    };

    // Validar toda una educación
    const validateEducation = (index: number, education: Education): boolean => {
        let isValid = true;

        // Validar campos requeridos
        if (!validateField(index, 'degree', education.degree, education)) {
            isValid = false;
        }
        if (!validateField(index, 'institution', education.institution, education)) {
            isValid = false;
        }
        if (!validateField(index, 'startDate', education.startDate, education)) {
            isValid = false;
        }
        if (!education.current) {
            if (!validateField(index, 'endDate', education.endDate, education)) {
                isValid = false;
            }
        }
        if (education.description) {
            if (!validateField(index, 'description', education.description, education)) {
                isValid = false;
            }
        }

        return isValid;
    };

    // Validar todas las educaciones
    const validateAll = (educations: Education[]): boolean => {
        let allValid = true;
        educations.forEach((edu, index) => {
            if (!validateEducation(index, edu)) {
                allValid = false;
            }
        });
        return allValid;
    };

    // Obtener clase CSS para el input según si tiene error
    const getErrorClass = (index: number, field: keyof EducationErrors): string => {
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
        const newErrors: { [key: number]: EducationErrors } = {};
        Object.keys(errors).forEach((key) => {
            const numKey = Number(key);
            if (numKey < removedIndex) {
                newErrors[numKey] = errors[numKey];
            } else if (numKey > removedIndex) {
                newErrors[numKey - 1] = errors[numKey];
            }
            // Si es el índice eliminado, no lo copiamos
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
        validateEducation,
        validateAll,
        getErrorClass,
        hasErrors,
        clearErrorsForIndex,
        clearAllErrors,
        reindexErrors,
        getCharCount,
    };
};