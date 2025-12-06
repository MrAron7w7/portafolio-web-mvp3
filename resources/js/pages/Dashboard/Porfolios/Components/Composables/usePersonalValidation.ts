import { reactive } from 'vue';

export interface PersonalData {
    firstName: string;
    lastName: string;
    title: string;
    city: string;
    country: string;
    phone: string;
    email: string;
    photo: string | null;
    summary: string;
    website: string;
    linkedin: string;
    github: string;
}

export interface PersonalErrors {
    firstName?: string;
    lastName?: string;
    email?: string;
    title?: string;
    phone?: string;
}

export const usePersonalValidation = () => {
    const errors = reactive<PersonalErrors>({});

    // NUEVO: Estado de campos tocados
    const touched = reactive<{
        firstName?: boolean;
        lastName?: boolean;
        email?: boolean;
        title?: boolean;
        phone?: boolean;
    }>({});

    // Reglas de validación
    const rules = {
        firstName: [
            (value: string) => value?.trim() ? null : 'El nombre es requerido',
            (value: string) => value?.trim().length >= 2 ? null : 'Mínimo 2 caracteres',
            (value: string) => value?.length <= 50 ? null : 'Máximo 50 caracteres',
            (value: string) => !/\d/.test(value) ? null : 'No se permiten números',
        ],
        lastName: [
            (value: string) => value?.trim() ? null : 'El apellido es requerido',
            (value: string) => value?.trim().length >= 2 ? null : 'Mínimo 2 caracteres',
            (value: string) => value?.length <= 50 ? null : 'Máximo 50 caracteres',
            (value: string) => !/\d/.test(value) ? null : 'No se permiten números',
        ],
        email: [
            (value: string) => value?.trim() ? null : 'El email es requerido',
            (value: string) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) ? null : 'Email inválido',
        ],
        title: [
            (value: string) => value?.trim() ? null : 'El título profesional es requerido',
            (value: string) => value?.trim().length >= 3 ? null : 'Mínimo 3 caracteres',
            (value: string) => value?.length <= 100 ? null : 'Máximo 100 caracteres',
        ],
        phone: [
            (value: string) => {
                if (!value?.trim()) return null; // Opcional
                return /^[\d\s\-\+\(\)]+$/.test(value) ? null : 'Formato de teléfono inválido';
            },
        ],
    };

    // MODIFICADO: Validar un campo individual con forceShow
    const validateField = (field: keyof PersonalErrors, value: string, forceShow: boolean = false): boolean => {
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
    const markAsTouched = (field: keyof PersonalErrors) => {
        touched[field] = true;
    };

    // NUEVO: Marcar todos los campos como tocados
    const markAllAsTouched = (formData: PersonalData) => {
        touched.firstName = true;
        touched.lastName = true;
        touched.email = true;
        touched.title = true;
        touched.phone = true;
    };

    // MODIFICADO: Validar todos los campos con forceShow
    const validateAll = (formData: PersonalData, forceShow: boolean = false): boolean => {
        let isValid = true;
        Object.keys(rules).forEach((field) => {
            if (!validateField(field as keyof PersonalErrors, formData[field as keyof PersonalData] as string, forceShow)) {
                isValid = false;
            }
        });
        return isValid;
    };

    // MODIFICADO: Obtener clase CSS para el input con forceShow
    const getErrorClass = (field: keyof PersonalErrors, forceShow: boolean = false): string => {
        const hasError = errors[field];
        const wasTouched = touched[field];

        if (hasError && (forceShow || wasTouched)) {
            return 'border-red-500 focus:border-red-500 focus:ring-red-200';
        }
        return 'border-gray-300 focus:border-[#005aeb] focus:ring-[#005aeb]';
    };

    // Limpiar errores
    const clearErrors = () => {
        Object.keys(errors).forEach((field) => {
            delete errors[field as keyof PersonalErrors];
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
        clearErrors,
    };
};