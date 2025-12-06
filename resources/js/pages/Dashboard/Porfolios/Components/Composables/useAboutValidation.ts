import { reactive } from 'vue';

export interface AboutData {
    summary: string;
    description: string;
}

export interface AboutErrors {
    summary?: string;
    description?: string;
}

export const useAboutValidation = () => {
    const errors = reactive<AboutErrors>({});

    // NUEVO: Estado de campos tocados
    const touched = reactive<{
        summary?: boolean;
        description?: boolean;
    }>({});

    // Reglas de validación
    const rules = {
        summary: [
            (value: string) => value?.trim() ? null : 'El resumen es requerido',
            (value: string) => value?.trim().length >= 20 ? null : 'Mínimo 20 caracteres',
            (value: string) => value?.length <= 300 ? null : 'Máximo 300 caracteres',
        ],
        description: [
            (value: string) => value?.trim() ? null : 'La descripción es requerida',
            (value: string) => value?.trim().length >= 20 ? null : 'Mínimo 20 caracteres',
            (value: string) => value?.length <= 2000 ? null : 'Máximo 2000 caracteres',
        ],
    };

    // MODIFICADO: Validar un campo individual con forceShow
    const validateField = (field: keyof AboutErrors, value: string, forceShow: boolean = false): boolean => {
        const fieldRules = rules[field] || [];
        for (const rule of fieldRules) {
            const error = rule(value);
            if (error) {
                // Solo guardar error si forceShow=true O el campo fue tocado
                if (forceShow || touched[field]) {
                    errors[field] = error;
                }
                return false;
            }
        }
        delete errors[field];
        return true;
    };

    // NUEVO: Marcar un campo específico como tocado
    const markAsTouched = (field: keyof AboutErrors) => {
        touched[field] = true;
    };

    // NUEVO: Marcar todos los campos como tocados
    const markAllAsTouched = (formData: AboutData) => {
        touched.summary = true;
        touched.description = true;
    };

    // MODIFICADO: Validar todos los campos con forceShow
    const validateAll = (formData: AboutData, forceShow: boolean = false): boolean => {
        let isValid = true;
        Object.keys(rules).forEach((field) => {
            if (!validateField(field as keyof AboutErrors, formData[field as keyof AboutData] as string, forceShow)) {
                isValid = false;
            }
        });
        return isValid;
    };

    // MODIFICADO: Obtener clase CSS para el input con forceShow
    const getErrorClass = (field: keyof AboutErrors, forceShow: boolean = false): string => {
        const hasError = errors[field];
        const wasTouched = touched[field];

        if (hasError && (forceShow || wasTouched)) {
            return 'border-red-500 focus:border-red-500 focus:ring-red-200';
        }
        return 'border-gray-300 focus:border-[#005aeb] focus:ring-[#005aeb]';
    };

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
        Object.keys(touched).forEach((field) => {
            delete touched[field as keyof typeof touched];
        });
    };

    return {
        errors,
        touched,
        validateField,
        validateAll,
        markAsTouched,
        markAllAsTouched,
        getErrorClass,
        getCharCount,
        clearErrors,
    };
};