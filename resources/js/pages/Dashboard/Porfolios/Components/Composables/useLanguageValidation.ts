import { reactive } from 'vue';

export interface Language {
  id: number;
  name: string;
  level: string;
}

export interface LanguageErrors {
  name?: string;
  level?: string;
}

export const languageLevels = [
  'Nativo',
  'Avanzado',
  'Intermedio',
  'Básico',
];

export const useLanguageValidation = () => {
  const errors = reactive<{ [key: number]: LanguageErrors }>({});
  
  // NUEVO: Objeto para rastrear campos tocados
  const touched = reactive<{ [key: number]: { name?: boolean; level?: boolean } }>({});

  const rules = {
    name: [
      (value: string) =>
        value?.trim() ? null : 'El idioma es requerido',
      (value: string) =>
        value?.trim().length >= 2 ? null : 'Mínimo 2 caracteres',
      (value: string) =>
        value?.length <= 50 ? null : 'Máximo 50 caracteres',
    ],
    level: [
      (value: string) =>
        value ? null : 'El nivel es requerido',
      (value: string) =>
        languageLevels.includes(value) ? null : 'Nivel no válido',
    ],
  };

  const initErrors = (index: number) => {
    if (!errors[index]) {
      errors[index] = {};
    }
  };

  // NUEVO: Inicializar estado touched
  const initTouched = (index: number) => {
    if (!touched[index]) {
      touched[index] = {};
    }
  };

  // MODIFICADO: Agregar parámetro forceShow
  const validateField = (
    index: number,
    field: keyof Language,
    value: any,
    forceShow: boolean = false
  ): boolean => {
    initErrors(index);
    initTouched(index);

    switch (field) {
      case 'name': {
        const nameRules = rules.name;
        for (const rule of nameRules) {
          const error = rule(value);
          if (error) {
            // Solo guardar error si forceShow=true O el campo fue tocado
            if (forceShow || touched[index].name) {
              errors[index].name = error;
            }
            return false;
          }
        }
        delete errors[index].name;
        return true;
      }

      case 'level': {
        const levelRules = rules.level;
        for (const rule of levelRules) {
          const error = rule(value);
          if (error) {
            // Solo guardar error si forceShow=true O el campo fue tocado
            if (forceShow || touched[index].level) {
              errors[index].level = error;
            }
            return false;
          }
        }
        delete errors[index].level;
        return true;
      }

      default:
        return true;
    }
  };

  // NUEVO: Marcar un campo específico como tocado
  const markAsTouched = (index: number, field: keyof Language) => {
    initTouched(index);
    touched[index][field] = true;
  };

  // NUEVO: Marcar todos los campos de todos los idiomas como tocados
  const markAllAsTouched = (languages: Language[] | undefined | null) => {
    if (!languages || !Array.isArray(languages)) return;
    
    languages.forEach((_, index) => {
      initTouched(index);
      touched[index].name = true;
      touched[index].level = true;
    });
  };

  const validateLanguage = (index: number, language: Language, forceShow: boolean = false): boolean => {
    let isValid = true;
    if (!validateField(index, 'name', language.name, forceShow)) {
      isValid = false;
    }
    if (!validateField(index, 'level', language.level, forceShow)) {
      isValid = false;
    }
    return isValid;
  };

  // MODIFICADO: Agregar parámetro forceShow
  const validateAll = (languages: Language[] | undefined | null, forceShow: boolean = false): boolean => {
    if (!languages || !Array.isArray(languages)) {
      return true;
    }
    if (languages.length === 0) {
      return true;
    }

    let allValid = true;
    languages.forEach((language, index) => {
      if (!validateLanguage(index, language, forceShow)) {
        allValid = false;
      }
    });
    return allValid;
  };

  // MODIFICADO: Solo aplicar clases de error si el campo fue tocado o forceShow
  const getErrorClass = (index: number, field: keyof LanguageErrors, forceShow: boolean = false): string => {
    const hasError = errors[index]?.[field];
    const wasTouched = touched[index]?.[field];
    
    if (hasError && (forceShow || wasTouched)) {
      return 'border-red-500 focus:border-red-500 focus:ring-red-200';
    }
    return 'border-gray-300 focus:border-[#005aeb] focus:ring-[#005aeb]';
  };

  const hasErrors = (index: number): boolean => {
    return Object.keys(errors[index] || {}).length > 0;
  };

  const clearErrorsForLanguage = (index: number) => {
    if (errors[index]) {
      delete errors[index];
    }
    if (touched[index]) {
      delete touched[index];
    }
  };

  const clearAllErrors = () => {
    Object.keys(errors).forEach((key) => {
      delete errors[Number(key)];
    });
    Object.keys(touched).forEach((key) => {
      delete touched[Number(key)];
    });
  };

  const reindexErrors = (removedIndex: number) => {
    const newErrors: { [key: number]: LanguageErrors } = {};
    const newTouched: { [key: number]: { name?: boolean; level?: boolean } } = {};
    
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

    // Reemplazar objetos
    Object.keys(errors).forEach((key) => {
      delete errors[Number(key)];
    });
    Object.keys(touched).forEach((key) => {
      delete touched[Number(key)];
    });
    
    Object.assign(errors, newErrors);
    Object.assign(touched, newTouched);
  };

  const getLevelLabel = (level: string): string => {
    return level;
  };

  return {
    errors,
    touched,
    validateField,
    validateLanguage,
    validateAll,
    markAsTouched,
    markAllAsTouched,
    getErrorClass,
    hasErrors,
    clearErrorsForLanguage,
    clearAllErrors,
    reindexErrors,
    getLevelLabel,
  };
};
