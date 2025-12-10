// ============================================
// composables/useEditorModals.ts
// ============================================
import { ref } from 'vue';

export function useEditorModals() {
    // ============================================
    // MODAL: VISTA PREVIA COMPLETA
    // ============================================
    const showFullPreview = ref(false);

    const openFullPreview = () => {
        showFullPreview.value = true;
    };

    const closeFullPreview = () => {
        showFullPreview.value = false;
    };

    // ============================================
    // MODAL: COMPLETAR FORMULARIO
    // ============================================
    const showCompleteModal = ref(false);
    const completionMessage = ref('');

    const openCompleteModal = () => {
        showCompleteModal.value = true;
    };

    const closeCompleteModal = () => {
        showCompleteModal.value = false;
    };

    // ============================================
    // MODAL: CAMBIAR PÚBLICO/PRIVADO
    // ============================================
    const showPublicToggleModal = ref(false);
    const publicToggleMessage = ref('');

    const openPublicToggleModal = () => {
        showPublicToggleModal.value = true;
    };

    const closePublicToggleModal = () => {
        showPublicToggleModal.value = false;
    };

    // ============================================
    // MODAL: CONFIRMACIÓN AL SALIR
    // ============================================
    const showExitConfirm = ref(false);

    return {
        // Full Preview
        showFullPreview,
        openFullPreview,
        closeFullPreview,

        // Complete Form
        showCompleteModal,
        completionMessage,
        openCompleteModal,
        closeCompleteModal,

        // Public Toggle
        showPublicToggleModal,
        publicToggleMessage,
        openPublicToggleModal,
        closePublicToggleModal,

        // Exit Confirm
        showExitConfirm,
    };
}