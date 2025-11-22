<script setup lang="ts">
import { computed } from 'vue';
import { getTemplateComponent } from './index';

// Props
const props = defineProps<{
    templateType: string;
    data: any;
    theme?: any;
}>();

// Componente dinámico basado en el tipo de template
const templateComponent = computed(() => {
    return getTemplateComponent(props.templateType);
});
</script>

<template>
    <component :is="templateComponent" :data="data" :theme="theme" />
</template>

<style>
/* Estilos globales para todos los templates */
* {
    box-sizing: border-box;
}

/* Print styles - útil para exportar a PDF */
@media print {
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    /* Ocultar elementos innecesarios al imprimir */
    .no-print {
        display: none !important;
    }

    /* Asegurar que el contenido no se corte entre páginas */
    .avoid-break {
        page-break-inside: avoid;
    }
}
</style>
