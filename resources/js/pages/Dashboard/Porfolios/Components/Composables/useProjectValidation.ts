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

    // NUEVO: Estado de campos tocados por índice
    const touched = reactive<{ 
        [key: number]: { 
            name?: boolean; 
            description?: boolean; 
            link?: boolean; 
            technologies?: boolean;
            image?: boolean;
        } 
    }>({});

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

    // NUEVO: Inicializar touched para un índice
    const initTouched = (index: number) => {
        if (!touched[index]) {
            touched[index] = {};
        }
    };

    // MODIFICADO: Validar un campo individual con forceShow
    const validateField = (
        index: number,
        field: keyof Project,
        value: any,
        forceShow: boolean = false
    ): boolean => {
        initErrors(index);
        initTouched(index);

        switch (field) {
            case 'name':
            case 'description':
            case 'link': {
                const fieldRules = rules[field] || [];
                for (const rule of fieldRules) {
                    const error = rule(value);
                    if (error) {
                        // Solo guardar error si forceShow=true O el campo fue tocado
                        if (forceShow || touched[index][field as keyof typeof touched[number]]) {
                            errors[index][field as keyof ProjectErrors] = error;
                        }
                        return false;
                    }
                }
                delete errors[index][field as keyof ProjectErrors];
                return true;
            }

            case 'technologies': {
                const techRules = rules.technologies;
                for (const rule of techRules) {
                    const error = rule(value as string[]);
                    if (error) {
                        // Solo guardar error si forceShow=true O el campo fue tocado
                        if (forceShow || touched[index].technologies) {
                            errors[index].technologies = error;
                        }
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

    // NUEVO: Marcar un campo específico como tocado
    const markAsTouched = (index: number, field: keyof Project) => {
        initTouched(index);
        touched[index][field as keyof typeof touched[number]] = true;
    };

    // NUEVO: Marcar todos los campos como tocados
    const markAllAsTouched = (projects: Project[] | undefined | null) => {
        if (!projects || !Array.isArray(projects)) return;

        projects.forEach((_, index) => {
            initTouched(index);
            touched[index].name = true;
            touched[index].description = true;
            touched[index].link = true;
            touched[index].technologies = true;
            touched[index].image = true;
        });
    };

    // Validar imagen (tamaño del archivo)
    const validateImage = (index: number, file: File): boolean => {
        initErrors(index);
        initTouched(index);
        touched[index].image = true;

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
    const validateProject = (index: number, project: Project, forceShow: boolean = false): boolean => {
        let isValid = true;
        if (!validateField(index, 'name', project.name, forceShow)) {
            isValid = false;
        }
        if (!validateField(index, 'description', project.description, forceShow)) {
            isValid = false;
        }
        if (!validateField(index, 'link', project.link, forceShow)) {
            isValid = false;
        }
        if (!validateField(index, 'technologies', project.technologies, forceShow)) {
            isValid = false;
        }
        return isValid;
    };

    // MODIFICADO: Validar todos los proyectos con forceShow
    const validateAll = (projects: Project[] | undefined | null, forceShow: boolean = false): boolean => {
        if (!projects || !Array.isArray(projects)) {
            return true;
        }
        if (projects.length === 0) {
            return true;
        }

        let allValid = true;
        projects.forEach((project, index) => {
            if (!validateProject(index, project, forceShow)) {
                allValid = false;
            }
        });
        return allValid;
    };

    // MODIFICADO: Obtener clase CSS para el input con forceShow
    const getErrorClass = (index: number, field: keyof ProjectErrors, forceShow: boolean = false): string => {
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
        const newErrors: { [key: number]: ProjectErrors } = {};
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

        // Limpiar y reasignar
        clearAllErrors();
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
        validateImage,
        clearImageError,
        validateProject,
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