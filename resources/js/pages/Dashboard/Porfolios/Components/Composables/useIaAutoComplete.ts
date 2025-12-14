// Composables/useIaAutoComplete.ts
import { ref, watch } from 'vue';
import { generateAutoCompleteSummary } from '../../Editor/Services/iaAutoComplete';
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
      
      // El JSON está en result.summary (como string JSON)
      let parsedData: any = null;
      let summaryText = '';

      // Intentar parsear el JSON de summary
      if (result.summary) {
        try {
          parsedData = JSON.parse(result.summary);
          summaryText = parsedData?.personal?.summary || '';
        } catch (e) {
          // Si no es JSON, usar como texto normal
          summaryText = result.summary;
          parsedData = null;
        }
      }

      if (summaryText) {
        const updated = { ...modelValue, summary: summaryText };
        emit('update:modelValue', updated);
        validateField('summary', summaryText, false);
      }

      // Retornar el objeto parseado con el texto del summary
      return {
        summary: summaryText,
        data: parsedData // Aquí está el JSON con idiomas, skills, etc.
      };
    } catch (error: any) {
      autoCompleteError.value =
        error.message ?? 'Error al autocompletar el resumen';
      return null;
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


  // Función para generar manualmente (sin debounce) - Retorna la respuesta completa
  const generateAutoComplete = async (description: string) => {
    if (!description || description.trim().length < 50) {
      autoCompleteError.value = 'La descripción debe tener al menos 50 caracteres';
      return null;
    }


    const result = await performAutoComplete(description);
    return result; // ← Retorna { summary: texto, data: jsonObject }
  };


  return {
    isAutoCompleting,
    autoCompleteError,
    watchDescriptionForAutoComplete,
    generateAutoComplete,
  };
}