import { computed, reactive } from 'vue';

interface AboutData {
    summary: string;
    description: string;
}

interface AboutErrors {
    summary?: string;
    description?: string;
}

export const useAboutValidation = () => {
    const errors = reactive<AboutErrors>({});

    // Reglas de validación
    const rules = {
        summary: [
            (value: string) =>
                value?.trim() ? null : 'El resumen es requerido',
            (value: string) =>
                value?.trim().length >= 20 ? null : 'Mínimo 20 caracteres',
            (value: string) =>
                value?.length <= 300 ? null : 'Máximo 300 caracteres',
        ],
        description: [
           
        ],
    };

    // Validar un campo individual
    const validateField = (field: keyof AboutErrors, value: string): boolean => {
        const fieldRules = rules[field] || [];
        for (const rule of fieldRules) {
            const error = rule(value);
            if (error) {
                errors[field] = error;
                return false;
            }
        }
        delete errors[field];
        return true;
    };

    // Validar todos los campos
    const validateAll = (formData: AboutData): boolean => {
        let isValid = true;
        Object.keys(rules).forEach((field) => {
            if (!validateField(field as keyof AboutErrors, formData[field as keyof AboutData] as string)) {
                isValid = false;
            }
        });
        return isValid;
    };

    // Computed: ¿está todo válido?
    const isValid = computed(() => {
        return Object.keys(rules).every((field) => !errors[field as keyof AboutErrors]);
    });

    // Contador de caracteres
    const getCharCount = (value: string, field: keyof AboutErrors) => {
        const maxChars = field === 'summary' ? 300 : 2000;
        const currentLength = value?.length || 0;
        return {
            current: currentLength,
            max: maxChars,
            remaining: maxChars - currentLength,
            isNearLimit: currentLength > maxChars * 0.8,
            isOverLimit: currentLength > maxChars,
        };
    };

    // Limpiar errores
    const clearErrors = () => {
        Object.keys(errors).forEach((field) => {
            delete errors[field as keyof AboutErrors];
        });
    };

    return {
        errors,
        validateField,
        validateAll,
        isValid,
        getCharCount,
        clearErrors,
    };
};