import { reactive } from 'vue';

export interface Skill {
    id: number;
    name: string;
    level: number;
    category: string;
}

export interface SkillErrors {
    name?: string;
    level?: string;
}

export interface SkillsData {
    technical: Skill[];
    soft: Skill[];
}

export const useSkillValidation = () => {
    // Estado de errores por tipo y índice
    const errors = reactive<{ [key: string]: { [key: number]: SkillErrors } }>({
        technical: {},
        soft: {},
    });

    // NUEVO: Estado de campos tocados por tipo e índice
    const touched = reactive<{ 
        [key: string]: { 
            [key: number]: { 
                name?: boolean; 
                level?: boolean;
            } 
        } 
    }>({
        technical: {},
        soft: {},
    });

    // Reglas de validación
    const rules = {
        name: [
            (value: string) =>
                value?.trim() ? null : 'El nombre de la habilidad es requerido',
            (value: string) =>
                value?.trim().length >= 2 ? null : 'Mínimo 2 caracteres',
            (value: string) =>
                value?.length <= 50 ? null : 'Máximo 50 caracteres',
        ],
        level: [
            (value: number) =>
                value >= 0 && value <= 100 ? null : 'El nivel debe estar entre 0 y 100',
        ],
    };

    // Inicializar errores para un tipo
    const initErrorsForType = (type: 'technical' | 'soft') => {
        if (!errors[type]) {
            errors[type] = {};
        }
    };

    // Inicializar errores para un índice
    const initErrors = (type: 'technical' | 'soft', index: number) => {
        initErrorsForType(type);
        if (!errors[type][index]) {
            errors[type][index] = {};
        }
    };

    // NUEVO: Inicializar touched para un tipo
    const initTouchedForType = (type: 'technical' | 'soft') => {
        if (!touched[type]) {
            touched[type] = {};
        }
    };

    // NUEVO: Inicializar touched para un índice
    const initTouchedForIndex = (type: 'technical' | 'soft', index: number) => {
        initTouchedForType(type);
        if (!touched[type][index]) {
            touched[type][index] = {};
        }
    };

    // MODIFICADO: Validar un campo individual con forceShow
    const validateField = (
        type: 'technical' | 'soft',
        index: number,
        field: keyof Skill,
        value: any,
        forceShow: boolean = false
    ): boolean => {
        initErrors(type, index);
        initTouchedForIndex(type, index);

        switch (field) {
            case 'name': {
                const nameRules = rules.name;
                for (const rule of nameRules) {
                    const error = rule(value);
                    if (error) {
                        // Solo guardar error si forceShow=true O el campo fue tocado
                        if (forceShow || touched[type][index].name) {
                            errors[type][index].name = error;
                        }
                        return false;
                    }
                }
                delete errors[type][index].name;
                return true;
            }

            case 'level': {
                const levelRules = rules.level;
                for (const rule of levelRules) {
                    const error = rule(value);
                    if (error) {
                        // Solo guardar error si forceShow=true O el campo fue tocado
                        if (forceShow || touched[type][index].level) {
                            errors[type][index].level = error;
                        }
                        return false;
                    }
                }
                delete errors[type][index].level;
                return true;
            }

            default:
                return true;
        }
    };

    // NUEVO: Marcar un campo específico como tocado
    const markAsTouched = (type: 'technical' | 'soft', index: number, field: keyof Skill) => {
        initTouchedForIndex(type, index);
        touched[type][index][field as keyof typeof touched[string][number]] = true;
    };

    // NUEVO: Marcar todos los campos como tocados
    const markAllAsTouched = (skillsData: SkillsData) => {
        if (skillsData?.technical && Array.isArray(skillsData.technical)) {
            skillsData.technical.forEach((_, index) => {
                initTouchedForIndex('technical', index);
                touched['technical'][index].name = true;
                touched['technical'][index].level = true;
            });
        }

        if (skillsData?.soft && Array.isArray(skillsData.soft)) {
            skillsData.soft.forEach((_, index) => {
                initTouchedForIndex('soft', index);
                touched['soft'][index].name = true;
                touched['soft'][index].level = true;
            });
        }
    };

    // Validar una habilidad completa
    const validateSkill = (type: 'technical' | 'soft', index: number, skill: Skill, forceShow: boolean = false): boolean => {
        let isValid = true;
        if (!validateField(type, index, 'name', skill.name, forceShow)) {
            isValid = false;
        }
        if (!validateField(type, index, 'level', skill.level, forceShow)) {
            isValid = false;
        }
        return isValid;
    };

    // MODIFICADO: Validar todas las habilidades de un tipo con forceShow
    const validateByType = (type: 'technical' | 'soft', skills: Skill[], forceShow: boolean = false): boolean => {
        let allValid = true;
        skills.forEach((skill, index) => {
            if (!validateSkill(type, index, skill, forceShow)) {
                allValid = false;
            }
        });
        return allValid;
    };

    // MODIFICADO: Validar todas las habilidades con forceShow
    const validateAll = (skillsData: SkillsData, forceShow: boolean = false): boolean => {
        let allValid = true;
        if (!validateByType('technical', skillsData.technical, forceShow)) {
            allValid = false;
        }
        if (!validateByType('soft', skillsData.soft, forceShow)) {
            allValid = false;
        }
        return allValid;
    };

    // MODIFICADO: Obtener clase CSS para el input con forceShow
    const getErrorClass = (type: 'technical' | 'soft', index: number, field: keyof SkillErrors, forceShow: boolean = false): string => {
        const hasError = errors[type]?.[index]?.[field];
        const wasTouched = touched[type]?.[index]?.[field as keyof typeof touched[string][number]];

        if (hasError && (forceShow || wasTouched)) {
            return 'border-red-500 focus:border-red-500 focus:ring-red-200';
        }
        return 'border-gray-300 focus:border-[#005aeb] focus:ring-[#005aeb]';
    };

    // Verificar si una habilidad tiene errores
    const hasErrors = (type: 'technical' | 'soft', index: number): boolean => {
        return Object.keys(errors[type]?.[index] || {}).length > 0;
    };

    // Limpiar errores de una habilidad específica
    const clearErrorsForSkill = (type: 'technical' | 'soft', index: number) => {
        if (errors[type]) {
            delete errors[type][index];
        }
        if (touched[type]) {
            delete touched[type][index];
        }
    };

    // Limpiar errores de un tipo
    const clearErrorsForType = (type: 'technical' | 'soft') => {
        errors[type] = {};
        touched[type] = {};
    };

    // Limpiar todos los errores
    const clearAllErrors = () => {
        errors.technical = {};
        errors.soft = {};
        touched.technical = {};
        touched.soft = {};
    };

    // Reindexar errores después de eliminar una habilidad
    const reindexErrors = (type: 'technical' | 'soft', removedIndex: number) => {
        if (!errors[type]) return;

        const newErrors: { [key: number]: SkillErrors } = {};
        const newTouched: { [key: number]: { name?: boolean; level?: boolean } } = {};

        Object.keys(errors[type]).forEach((key) => {
            const numKey = Number(key);
            if (numKey < removedIndex) {
                newErrors[numKey] = errors[type][numKey];
                newTouched[numKey] = touched[type][numKey];
            } else if (numKey > removedIndex) {
                newErrors[numKey - 1] = errors[type][numKey];
                newTouched[numKey - 1] = touched[type][numKey];
            }
        });

        errors[type] = newErrors;
        touched[type] = newTouched;
    };

    // Obtener el porcentaje formateado
    const getLevelLabel = (level: number): string => {
        if (level <= 25) return 'Principiante';
        if (level <= 50) return 'Intermedio';
        if (level <= 75) return 'Avanzado';
        return 'Experto';
    };

    return {
        errors,
        touched,
        validateField,
        validateSkill,
        validateByType,
        validateAll,
        markAsTouched,
        markAllAsTouched,
        getErrorClass,
        hasErrors,
        clearErrorsForSkill,
        clearErrorsForType,
        clearAllErrors,
        reindexErrors,
        getLevelLabel,
    };
};
