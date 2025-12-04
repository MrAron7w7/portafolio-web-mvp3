import { computed, reactive, ref } from 'vue';

interface PersonalData {
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

interface PersonalErrors {
  firstName?: string;
  lastName?: string;
  email?: string;
  title?: string;
  phone?: string;
}

export const usePersonalValidation = () => {
  const errors = reactive<PersonalErrors>({});

  // Reglas de validación
  const rules = {
    firstName: [
      (value: string) => 
        value?.trim() ? null : 'El nombre es requerido',
      (value: string) => 
        value?.trim().length >= 2 ? null : 'Mínimo 2 caracteres',
      (value: string) => 
        value?.length <= 50 ? null : 'Máximo 50 caracteres',
      (value: string) => 
        !/\d/.test(value) ? null : 'No se permiten números',
    ],
    lastName: [
      (value: string) => 
        value?.trim() ? null : 'El apellido es requerido',
      (value: string) => 
        value?.trim().length >= 2 ? null : 'Mínimo 2 caracteres',
      (value: string) => 
        value?.length <= 50 ? null : 'Máximo 50 caracteres',
      (value: string) => 
        !/\d/.test(value) ? null : 'No se permiten números',
    ],
    email: [
      (value: string) => 
        value?.trim() ? null : 'El email es requerido',
      (value: string) => 
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) ? null : 'Email inválido',
    ],
    title: [
      (value: string) => 
        value?.trim() ? null : 'El título profesional es requerido',
      (value: string) => 
        value?.trim().length >= 3 ? null : 'Mínimo 3 caracteres',
      (value: string) => 
        value?.length <= 100 ? null : 'Máximo 100 caracteres',
    ],
    phone: [
      (value: string) => {
        if (!value?.trim()) return null; // Opcional
        return /^[\d\s\-\+\(\)]+$/.test(value) ? null : 'Formato de teléfono inválido';
      },
    ],
  };

  // Validar un campo individual
  const validateField = (field: keyof PersonalErrors, value: string): boolean => {
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
  const validateAll = (formData: PersonalData): boolean => {
    let isValid = true;
    
    Object.keys(rules).forEach((field) => {
      if (!validateField(field as keyof PersonalErrors, formData[field as keyof PersonalData] as string)) {
        isValid = false;
      }
    });
    
    return isValid;
  };

  // Computed: ¿está todo válido?
  const isValid = computed(() => {
    return Object.keys(rules).every((field) => !errors[field as keyof PersonalErrors]);
  });

  // Limpiar errores
  const clearErrors = () => {
    Object.keys(errors).forEach((field) => {
      delete errors[field as keyof PersonalErrors];
    });
  };

  return {
    errors,
    validateField,
    validateAll,
    isValid,
    clearErrors,
  };
};
