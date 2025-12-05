import { reactive } from 'vue';

export interface SocialData {
    linkedin: string;
    github: string;
    website: string;
}

export interface SocialErrors {
    linkedin?: string;
    github?: string;
    website?: string;
}

export const useSocialValidation = () => {
    // Estado de errores
    const errors = reactive<SocialErrors>({});

    // Reglas de validación
    const rules = {
        linkedin: [
            (value: string) => {
                if (!value?.trim()) return null; // Opcional
                return /^linkedin\.com\/in\/[a-zA-Z0-9\-]+$/.test(value)
                    ? null
                    : 'URL de LinkedIn inválida (ej: linkedin.com/in/usuario)';
            },
        ],
        github: [
            (value: string) => {
                if (!value?.trim()) return null; // Opcional
                return /^github\.com\/[a-zA-Z0-9\-]+$/.test(value)
                    ? null
                    : 'URL de GitHub inválida (ej: github.com/usuario)';
            },
        ],
        website: [
            (value: string) => {
                if (!value?.trim()) return null; // Opcional
                const urlRegex = /^https?:\/\/.+\..+/;
                return urlRegex.test(value) ? null : 'URL inválida (ej: https://tu-sitio.com)';
            },
        ],
    };

    // Validar un campo individual
    const validateField = (field: keyof SocialData, value: string): boolean => {
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
    const validateAll = (formData: SocialData): boolean => {
        let isValid = true;

        Object.keys(rules).forEach((field) => {
            if (!validateField(field as keyof SocialData, formData[field as keyof SocialData])) {
                isValid = false;
            }
        });

        return isValid;
    };

    // Obtener clase CSS para el input según si tiene error
    const getErrorClass = (field: keyof SocialErrors): string => {
        return errors[field]
            ? 'border-red-500 focus:border-red-500 focus:ring-red-200'
            : 'border-gray-300 focus:border-[#005aeb] focus:ring-[#005aeb]';
    };

    // Verificar si hay errores
    const hasErrors = (): boolean => {
        return Object.keys(errors).length > 0;
    };

    // Limpiar errores
    const clearErrors = () => {
        Object.keys(errors).forEach((field) => {
            delete errors[field as keyof SocialErrors];
        });
    };

    // Validar y extraer nombre de usuario desde URL
    const extractUsername = (field: 'linkedin' | 'github', url: string): string => {
        if (field === 'linkedin') {
            return url.replace('linkedin.com/in/', '');
        } else if (field === 'github') {
            return url.replace('github.com/', '');
        }
        return url;
    };

    // Construir URL desde nombre de usuario
    const buildUrl = (field: 'linkedin' | 'github', username: string): string => {
        if (!username?.trim()) return '';
        if (field === 'linkedin') {
            return `linkedin.com/in/${username}`;
        } else if (field === 'github') {
            return `github.com/${username}`;
        }
        return username;
    };

    // Obtener información sobre el campo (para UI hints)
    const getFieldInfo = (field: keyof SocialData) => {
        const info = {
            linkedin: {
                label: '[translate:LinkedIn]',
                placeholder: '[translate:usuario]',
                prefix: '[translate:linkedin.com/in/]',
                helper: '[translate:Tu perfil de LinkedIn (solo nombre de usuario)]',
            },
            github: {
                label: '[translate:GitHub]',
                placeholder: '[translate:usuario]',
                prefix: '[translate:github.com/]',
                helper: '[translate:Tu perfil de GitHub (solo nombre de usuario)]',
            },
            website: {
                label: '[translate:Sitio Web Personal]',
                placeholder: '[translate:https://tu-sitio.com]',
                prefix: null,
                helper: '[translate:Tu sitio web personal o portafolio]',
            },
        };

        return info[field];
    };

    return {
        errors,
        validateField,
        validateAll,
        getErrorClass,
        hasErrors,
        clearErrors,
        extractUsername,
        buildUrl,
        getFieldInfo,
    };
};