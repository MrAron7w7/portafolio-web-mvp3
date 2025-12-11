// Composables/useIaAutoComplete.ts
import { ref, watch } from 'vue';
import { generateAutoCompleteSummary } from '../Services/iaAutoComplete';
import type { AboutData } from './useAboutValidation';

export function useIaAutoComplete(
  modelValue: Readonly<AboutData>,
  emit: (event: 'update:modelValue', value: AboutData) => void,
  validateField: (field: string, value: string, forceShow: boolean) => void
) {
  const isAutoCompleting = ref(false);
  const autoCompleteError = ref<string | null>(null);

  let autoCompleteTimeout: number | undefined;

  // Función reutilizable para generar el resumen
  const performAutoComplete = async (description: string) => {
    try {
      isAutoCompleting.value = true;
      autoCompleteError.value = null;

      const result = await generateAutoCompleteSummary(description);
      const autoCompletedSummary = result.summary ?? result.output ?? '';

      if (autoCompletedSummary) {
        const updated = { ...modelValue, summary: autoCompletedSummary };
        emit('update:modelValue', updated);
        validateField('summary', autoCompletedSummary, false);
      }
    } catch (error: any) {
      autoCompleteError.value =
        error.message ?? 'Error al autocompletar el resumen';
    } finally {
      isAutoCompleting.value = false;
    }
  };

  // Watch automático con debounce
  const watchDescriptionForAutoComplete = () => {
    watch(
      () => modelValue.description,
      (newDescription) => {
        if (!newDescription || newDescription.trim().length < 50) {
          return;
        }

        if (autoCompleteTimeout) window.clearTimeout(autoCompleteTimeout);

        autoCompleteTimeout = window.setTimeout(async () => {
          await performAutoComplete(newDescription);
        }, 800);
      }
    );
  };

  // Función para generar manualmente (sin debounce)
  const generateAutoComplete = async (description: string) => {
    if (!description || description.trim().length < 50) {
      autoCompleteError.value = 'La descripción debe tener al menos 50 caracteres';
      return;
    }

    await performAutoComplete(description);
  };

  return {
    isAutoCompleting,
    autoCompleteError,
    watchDescriptionForAutoComplete,
    generateAutoComplete, // ← Nueva función exportada
  };
}
