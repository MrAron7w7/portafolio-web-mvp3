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

    // Validar un campo individual
    const validateField = (
        type: 'technical' | 'soft',
        index: number,
        field: keyof Skill,
        value: any
    ): boolean => {
        initErrors(type, index);

        switch (field) {
            case 'name': {
                const nameRules = rules.name;
                for (const rule of nameRules) {
                    const error = rule(value);
                    if (error) {
                        errors[type][index].name = error;
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
                        errors[type][index].level = error;
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

    // Validar una habilidad completa
    const validateSkill = (type: 'technical' | 'soft', index: number, skill: Skill): boolean => {
        let isValid = true;

        if (!validateField(type, index, 'name', skill.name)) {
            isValid = false;
        }
        if (!validateField(type, index, 'level', skill.level)) {
            isValid = false;
        }

        return isValid;
    };

    // Validar todas las habilidades de un tipo
    const validateByType = (type: 'technical' | 'soft', skills: Skill[]): boolean => {
        let allValid = true;
        skills.forEach((skill, index) => {
            if (!validateSkill(type, index, skill)) {
                allValid = false;
            }
        });
        return allValid;
    };

    // Validar todas las habilidades
    const validateAll = (skillsData: SkillsData): boolean => {
        let allValid = true;

        if (!validateByType('technical', skillsData.technical)) {
            allValid = false;
        }
        if (!validateByType('soft', skillsData.soft)) {
            allValid = false;
        }

        return allValid;
    };

    // Obtener clase CSS para el input según si tiene error
    const getErrorClass = (type: 'technical' | 'soft', index: number, field: keyof SkillErrors): string => {
        return errors[type]?.[index]?.[field]
            ? 'border-red-500 focus:border-red-500 focus:ring-red-200'
            : 'border-gray-300 focus:border-[#005aeb] focus:ring-[#005aeb]';
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
    };

    // Limpiar errores de un tipo
    const clearErrorsForType = (type: 'technical' | 'soft') => {
        errors[type] = {};
    };

    // Limpiar todos los errores
    const clearAllErrors = () => {
        errors.technical = {};
        errors.soft = {};
    };

    // Reindexar errores después de eliminar una habilidad
    const reindexErrors = (type: 'technical' | 'soft', removedIndex: number) => {
        if (!errors[type]) return;

        const newErrors: { [key: number]: SkillErrors } = {};
        Object.keys(errors[type]).forEach((key) => {
            const numKey = Number(key);
            if (numKey < removedIndex) {
                newErrors[numKey] = errors[type][numKey];
            } else if (numKey > removedIndex) {
                newErrors[numKey - 1] = errors[type][numKey];
            }
        });

        errors[type] = newErrors;
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
        validateField,
        validateSkill,
        validateByType,
        validateAll,
        getErrorClass,
        hasErrors,
        clearErrorsForSkill,
        clearErrorsForType,
        clearAllErrors,
        reindexErrors,
        getLevelLabel,
    };
};