// Template Component Registry
import { defineAsyncComponent } from 'vue';

// Importar templates de forma lazy para mejor performance
const templates: Record<string, any> = {
    moderna: defineAsyncComponent(() => import('./Moderna.vue')),
    minimalista: defineAsyncComponent(() => import('./Minimalista.vue')),
    ejecutiva: defineAsyncComponent(() => import('./Ejecutiva.vue')),
    creativa: defineAsyncComponent(() => import('./Creativa.vue')),
    tecnologica: defineAsyncComponent(() => import('./Tecnologica.vue')),
    academica: defineAsyncComponent(() => import('./Academica.vue')),
};

// Obtener componente de template por nombre
export const getTemplateComponent = (templateType: string) => {
    const normalizedType = templateType?.toLowerCase() || 'moderna';
    return templates[normalizedType] || templates.moderna;
};

// Lista de templates disponibles
export const availableTemplates = Object.keys(templates);

export default templates;
