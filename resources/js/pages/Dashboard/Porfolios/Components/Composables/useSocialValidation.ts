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

    // NUEVO: Estado de campos tocados
    const touched = reactive<{
        linkedin?: boolean;
        github?: boolean;
        website?: boolean;
    }>({});

    // Reglas de validación
    const rules = {
        linkedin: [
            (value: string) => {
                if (!value?.trim()) return null;
                // Acepta con o sin https://, pero debe contener linkedin.com/in/
                return /(linkedin\.com\/in\/[a-zA-Z0-9\-]+)/.test(value)
                    ? null
                    : 'URL de LinkedIn inválida (ej: https://linkedin.com/in/usuario)';
            },
        ],
        github: [
            (value: string) => {
                if (!value?.trim()) return null;
                // Acepta con o sin https://, pero debe contener github.com/
                return /(github\.com\/[a-zA-Z0-9\-]+)/.test(value)
                    ? null
                    : 'URL de GitHub inválida (ej: https://github.com/usuario)';
            },
        ],
        website: [
            (value: string) => {
                // Si está vacío, es válido (OPCIONAL)
                if (!value?.trim()) return null;
                const urlRegex = /^https?:\/\/.+\..+/;
                return urlRegex.test(value) ? null : 'URL inválida (ej: https://tu-sitio.com)';
            },
        ],
    };

    // MODIFICADO: Validar un campo individual con forceShow
    const validateField = (field: keyof SocialData, value: string, forceShow: boolean = false): boolean => {
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
        // Si no hay error, limpiar el error guardado
        delete errors[field];
        return true;
    };

    // NUEVO: Marcar un campo específico como tocado
    const markAsTouched = (field: keyof SocialData) => {
        touched[field] = true;
    };

    // NUEVO: Marcar todos los campos como tocados
    const markAllAsTouched = () => {
        touched.linkedin = true;
        touched.github = true;
        touched.website = true;
    };

    // MODIFICADO: Validar todos los campos con forceShow
    // IMPORTANTE: Todos los campos son opcionales, así que siempre retorna true si están vacíos
    const validateAll = (formData: SocialData, forceShow: boolean = false): boolean => {
        let isValid = true;
        Object.keys(rules).forEach((field) => {
            if (!validateField(field as keyof SocialData, formData[field as keyof SocialData], forceShow)) {
                isValid = false;
            }
        });
        return isValid;
    };

    // MODIFICADO: Obtener clase CSS para el input con forceShow
    const getErrorClass = (field: keyof SocialErrors, forceShow: boolean = false): string => {
        const hasError = errors[field];
        const wasTouched = touched[field];

        if (hasError && (forceShow || wasTouched)) {
            return 'border-red-500 focus:border-red-500 focus:ring-red-200';
        }
        return 'border-gray-300 focus:border-[#005aeb] focus:ring-[#005aeb]';
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
        Object.keys(touched).forEach((field) => {
            delete touched[field as keyof typeof touched];
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
                label: 'LinkedIn',
                placeholder: 'https://linkedin.com/in/usuario',
                prefix: null,
                helper: 'Tu perfil de LinkedIn (link completo) - Opcional',
            },
            github: {
                label: 'GitHub',
                placeholder: 'https://github.com/usuario',
                prefix: null,
                helper: 'Tu perfil de GitHub (link completo) - Opcional',
            },
            website: {
                label: 'Sitio Web Personal',
                placeholder: 'https://tu-sitio.com',
                prefix: null,
                helper: 'Tu sitio web personal o portafolio - Opcional',
            },
        };
        return info[field];
    };

    return {
        errors,
        touched,
        validateField,
        validateAll,
        markAsTouched,
        markAllAsTouched,
        getErrorClass,
        hasErrors,
        clearErrors,
        extractUsername,
        buildUrl,
        getFieldInfo,
    };
};