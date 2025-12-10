<script setup lang="ts">
import { computed } from 'vue';
// Importa tus componentes actuales
import Moderna from '@/components/Templates/Moderna.vue';
import Creativa from '@/components/Templates/Creativa.vue';
import Ejecutiva from '@/components/Templates/Ejecutiva.vue';
import Tecnologica from '@/components/Templates/Tecnologica.vue';
import Minimalista from '@/components/Templates/Minimalista.vue';

const props = defineProps<{
    portfolio: any;
}>();

// Diccionario de componentes
const templates = {
    moderna: Moderna,
    creativa: Creativa,
    ejecutiva: Ejecutiva,
    tecnologica: Tecnologica,
    minimalista: Minimalista,
};

const currentTemplate = computed(() => {
    const key = props.portfolio.template_type as keyof typeof templates;
    return templates[key] ?? Moderna;
});
</script>

<template>
    <div class="pdf-wrapper">
        <component 
            :is="currentTemplate" 
            :data="portfolio.template_data" 
            class="print-container"
        />
    </div>
</template>

<style>
/* Estilos globales solo para la impresión */
body, html {
    margin: 0;
    padding: 0;
    background: white;
    overflow-x: hidden;
}

.pdf-wrapper {
    width: 100%;
    min-height: 100vh;
    display: inline-block;
}

/* Asegurar que ocupe el ancho completo para el PDF */
.print-container {
    width: 100%;
    display: block;
    box-sizing: border-box;
}

/* Prevenir saltos de página en elementos */
.print-container * {
    page-break-inside: avoid;
    break-inside: avoid;
}
</style>