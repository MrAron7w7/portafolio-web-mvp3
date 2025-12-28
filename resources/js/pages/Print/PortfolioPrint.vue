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

// Datos formateados para el template (Misma lógica que en Final.vue)
const templateData = computed(() => {
    const data = props.portfolio.template_data;
    if (!data) return null;
 
    return {
        personal: {
            // Construir el nombre completo si existe firstName/lastName, o usar name si ya existe
            name: (data.personal?.name && data.personal.name !== 'Tu Nombre') 
                ? data.personal.name 
                : `${data.personal?.firstName || ''} ${data.personal?.lastName || ''}`.trim() || 'Tu Nombre',
            title: data.personal?.title || 'Tu Título Profesional',
            email: data.personal?.email || '',
            phone: data.personal?.phone || '',
            photo: data.personal?.photo || null,
            location: data.personal?.location || '',
            website: data.personal?.website || '',
            linkedin: data.personal?.linkedin || '',
            github: data.personal?.github || '',
            summary: data.personal?.summary || '',
            description: data.personal?.description || '',
        },
        experience: data.experience || [],
        education: data.education || [],
        skills: {
            technical: data.skills?.technical || [],
            soft: data.skills?.soft || [],
        },
        projects: data.projects || [],
        languages: data.languages || [],
        certifications: data.certifications || [],
    };
});
</script>

<template>
    <div class="pdf-wrapper">
        <component 
            v-if="templateData"
            :is="currentTemplate" 
            :data="templateData" 
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