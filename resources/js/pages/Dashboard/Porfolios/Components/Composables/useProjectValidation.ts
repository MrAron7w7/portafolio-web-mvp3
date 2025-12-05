import { reactive } from 'vue';

export interface Project {
    id: number;
    name: string;
    description: string;
    image: string | null;
    link: string;
    technologies: string[];
}

export interface ProjectErrors {
    name?: string;
    description?: string;
    image?: string;
    link?: string;
    technologies?: string;
}

export const useProjectValidation = () => {
    // Estado de errores por índice
    const errors = reactive<{ [key: number]: ProjectErrors }>({});

    // Reglas de validación
    const rules = {
        name: [
            (value: string) =>
                value?.trim() ? null : 'El nombre es requerido',
            (value: string) =>
                value?.trim().length >= 3 ? null : 'Mínimo 3 caracteres',
            (value: string) =>
                value?.length <= 100 ? null : 'Máximo 100 caracteres',
        ],
        description: [
            (value: string) =>
                value?.trim() ? null : 'La descripción es requerida',
            (value: string) =>
                value?.trim().length >= 10 ? null : 'Mínimo 10 caracteres',
            (value: string) =>
                value?.length <= 500 ? null : 'Máximo 500 caracteres',
        ],
        link: [
            (value: string) => {
                if (!value?.trim()) return null; // Opcional
                const urlRegex = /^https?:\/\/.+\..+/;
                return urlRegex.test(value) ? null : 'URL inválida (ej: https://ejemplo.com)';
            },
        ],
        technologies: [
            (value: string[]) =>
                value?.length > 0 ? null : 'Agrega al menos una tecnología',
        ],
        image: [
            // La validación de imagen se hace por separado debido al manejo de archivos
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
        field: keyof Project,
        value: any
    ): boolean => {
        initErrors(index);

        switch (field) {
            case 'name':
            case 'description':
            case 'link': {
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

            case 'technologies': {
                const techRules = rules.technologies;
                for (const rule of techRules) {
                    const error = rule(value as string[]);
                    if (error) {
                        errors[index].technologies = error;
                        return false;
                    }
                }
                delete errors[index].technologies;
                return true;
            }

            case 'image': {
                // La validación de imagen se maneja en validateImage
                return true;
            }

            default:
                return true;
        }
    };

    // Validar imagen (tamaño del archivo)
    const validateImage = (index: number, file: File): boolean => {
        initErrors(index);

        const maxSizeBytes = 2 * 1024 * 1024; // 2MB

        if (file.size > maxSizeBytes) {
            errors[index].image = 'La imagen no debe superar 2MB';
            return false;
        }

        // Validar tipo de archivo
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            errors[index].image = 'Formato no válido. Usa PNG, JPG, GIF o WebP';
            return false;
        }

        delete errors[index].image;
        return true;
    };

    // Limpiar error de imagen
    const clearImageError = (index: number) => {
        initErrors(index);
        delete errors[index].image;
    };

    // Validar todo un proyecto
    const validateProject = (index: number, project: Project): boolean => {
        let isValid = true;

        if (!validateField(index, 'name', project.name)) {
            isValid = false;
        }
        if (!validateField(index, 'description', project.description)) {
            isValid = false;
        }
        if (!validateField(index, 'link', project.link)) {
            isValid = false;
        }
        if (!validateField(index, 'technologies', project.technologies)) {
            isValid = false;
        }

        return isValid;
    };

    // Validar todos los proyectos
    const validateAll = (projects: Project[]): boolean => {
        let allValid = true;
        projects.forEach((project, index) => {
            if (!validateProject(index, project)) {
                allValid = false;
            }
        });
        return allValid;
    };

    // Obtener clase CSS para el input según si tiene error
    const getErrorClass = (index: number, field: keyof ProjectErrors): string => {
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
        const newErrors: { [key: number]: ProjectErrors } = {};
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
        validateImage,
        clearImageError,
        validateProject,
        validateAll,
        getErrorClass,
        hasErrors,
        clearErrorsForIndex,
        clearAllErrors,
        reindexErrors,
        getCharCount,
    };
};
