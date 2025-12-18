// ============================================
// useStartChoice - Composable para el modal inicial
// ============================================
import { ref, computed } from 'vue';

export function useStartChoice() {
    const showStartChoice = ref(true);
    const isLoadingChoice = ref(false);
    const selectedChoice = ref<'ai' | 'manual' | null>(null);

    /**
     * Abre el modal de inicio
     */
    const openStartChoice = () => {
        showStartChoice.value = true;
        selectedChoice.value = null;
    };

    /**
     * Cierra el modal de inicio
     */
    const closeStartChoice = () => {
        showStartChoice.value = false;
        selectedChoice.value = null;
    };

    /**
     * Maneja la selección con IA
     */
    const handleChooseAI = async () => {
        isLoadingChoice.value = true;
        selectedChoice.value = 'ai';

        try {
            console.log('📊 Modo IA seleccionado');

            // Esperar un poco para que el usuario vea la animación
            await new Promise(resolve => setTimeout(resolve, 800));

            // Cerrar el modal
            closeStartChoice();
        } catch (error) {
            console.error('❌ Error al seleccionar modo IA:', error);
        } finally {
            isLoadingChoice.value = false;
        }
    };

    /**
     * Maneja la selección manual
     */
    const handleChooseManual = async () => {
        isLoadingChoice.value = true;
        selectedChoice.value = 'manual';

        try {
            console.log('✍️ Modo Manual seleccionado');

            // Esperar un poco para que el usuario vea la animación
            await new Promise(resolve => setTimeout(resolve, 800));

            // Cerrar el modal
            closeStartChoice();
        } catch (error) {
            console.error('❌ Error al seleccionar modo manual:', error);
        } finally {
            isLoadingChoice.value = false;
        }
    };

    /**
     * Computed para saber si el usuario ya hizo una selección
     */
    const hasChosen = computed(() => selectedChoice.value !== null);

    /**
     * Computed para saber si está en modo IA
     */
    const isAIMode = computed(() => selectedChoice.value === 'ai');

    /**
     * Computed para saber si está en modo manual
     */
    const isManualMode = computed(() => selectedChoice.value === 'manual');

    return {
        // Estado
        showStartChoice,
        isLoadingChoice,
        selectedChoice,
        hasChosen,
        isAIMode,
        isManualMode,

        // Métodos
        openStartChoice,
        closeStartChoice,
        handleChooseAI,
        handleChooseManual,
    };
}