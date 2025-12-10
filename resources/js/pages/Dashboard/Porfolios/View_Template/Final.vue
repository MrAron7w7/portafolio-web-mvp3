<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    ArrowLeft, 
    Download, 
    Share2, 
    Eye, 
    Globe, 
    Lock,
    ExternalLink,
    Copy,
    Check,
    QrCode
} from 'lucide-vue-next';
import { getTemplateComponent } from '@/components/Templates/index';

// Props del backend
const props = defineProps<{
    portfolio: {
        id: number;
        title: string;
        slug: string;
        description: string;
        template_type: string;
        template_data: any;
        is_public: boolean;
        is_completed: boolean;
        theme_settings: any;
        created_at: string;
        updated_at: string;
    };
    isOwner: boolean;
    publicUrl?: string;
}>();

// Componente de template dinámico
const TemplateComponent = computed(() => {
    return getTemplateComponent(props.portfolio.template_type);
});

// Datos formateados para el template
const templateData = computed(() => {
    const data = props.portfolio.template_data;
    if (!data) return null;

    return {
        personal: {
            name: `${data.personal?.firstName || ''} ${data.personal?.lastName || ''}`.trim() || 'Tu Nombre',
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

// Estados
const showShareModal = ref(false);
const copied = ref(false);

// Copiar URL al portapapeles
const copyUrl = async () => {
    if (props.publicUrl) {
        await navigator.clipboard.writeText(props.publicUrl);
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    }
};

// Compartir en redes sociales
const shareOnTwitter = () => {
    const url = encodeURIComponent(props.publicUrl || window.location.href);
    const text = encodeURIComponent(`Mira mi portafolio profesional: ${props.portfolio.title}`);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
};

const shareOnLinkedIn = () => {
    const url = encodeURIComponent(props.publicUrl || window.location.href);
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
};

// Imprimir / Exportar PDF
const printPortfolio = () => {
    window.print();
};
</script>

<template>
    <Head :title="portfolio.title" />

    <div class="min-h-screen bg-gray-50">
        <!-- Header de control (solo visible para el dueño) -->
        <header v-if="isOwner" class="sticky top-0 z-50 border-b border-gray-200 bg-white/95 backdrop-blur-sm no-print">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Navegación izquierda -->
                    <div class="flex items-center space-x-4">
                        <Link href="/dashboard" 
                            class="flex items-center space-x-2 rounded-lg px-3 py-2 text-gray-600 transition hover:bg-gray-100">
                            <ArrowLeft class="h-5 w-5" />
                            <span class="hidden sm:inline">Volver al Dashboard</span>
                        </Link>
                    </div>

                    <!-- Info del portafolio -->
                    <div class="flex items-center space-x-3">
                        <span :class="[
                            'flex items-center space-x-1 rounded-full px-3 py-1 text-sm font-medium',
                            portfolio.is_public 
                                ? 'bg-green-100 text-green-700' 
                                : 'bg-gray-100 text-gray-600'
                        ]">
                            <Globe v-if="portfolio.is_public" class="h-4 w-4" />
                            <Lock v-else class="h-4 w-4" />
                            <span>{{ portfolio.is_public ? 'Público' : 'Privado' }}</span>
                        </span>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center space-x-2">
                        <!-- Compartir -->
                        <button v-if="portfolio.is_public" 
                            @click="showShareModal = true"
                            class="flex items-center space-x-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-50">
                            <Share2 class="h-4 w-4" />
                            <span class="hidden sm:inline">Compartir</span>
                        </button>

                        <!-- Descargar PDF -->
                        <button @click="printPortfolio"
                            class="flex items-center space-x-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-50">
                            <Download class="h-4 w-4" />
                            <span class="hidden sm:inline">Descargar PDF</span>
                        </button>

                        <!-- Editar -->
                        <Link :href="`/dashboard/portfolio/${portfolio.id}/editor`"
                            class="flex items-center space-x-2 rounded-lg bg-[#005aeb] px-4 py-2 text-white transition hover:bg-[#0048c4]">
                            <Eye class="h-4 w-4" />
                            <span class="hidden sm:inline">Editar</span>
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido del portafolio -->
        <main class="portfolio-content">
            <component 
                v-if="templateData"
                :is="TemplateComponent" 
                :data="templateData" 
                :theme="portfolio.theme_settings"
            />

            <!-- Mensaje si no hay datos -->
            <div v-else class="flex min-h-screen items-center justify-center">
                <div class="text-center">
                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-gray-100">
                        <Eye class="h-10 w-10 text-gray-400" />
                    </div>
                    <h2 class="mb-2 text-2xl font-bold text-gray-900">Portafolio vacío</h2>
                    <p class="mb-6 text-gray-600">Este portafolio aún no tiene contenido.</p>
                    <Link v-if="isOwner" :href="`/dashboard/portfolio/${portfolio.id}/editor`"
                        class="rounded-lg bg-[#005aeb] px-6 py-3 text-white transition hover:bg-[#0048c4]">
                        Comenzar a editar
                    </Link>
                </div>
            </div>
        </main>

        <!-- Footer del portafolio -->
        <footer class="border-t border-gray-200 bg-white py-8 no-print">
            <div class="mx-auto max-w-7xl px-4 text-center">
                <p class="text-sm text-gray-500">
                    Creado con 
                    <Link href="/" class="font-medium text-[#005aeb] hover:underline">
                        PortafolioIA
                    </Link>
                </p>
            </div>
        </footer>

        <!-- Modal de compartir -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showShareModal" 
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                    @click.self="showShareModal = false">
                    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                        <h3 class="mb-4 text-xl font-bold text-gray-900">Compartir Portafolio</h3>
                        
                        <!-- URL del portafolio -->
                        <div class="mb-6">
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Enlace público
                            </label>
                            <div class="flex rounded-lg border border-gray-300 overflow-hidden">
                                <input 
                                    type="text" 
                                    :value="publicUrl" 
                                    readonly
                                    class="flex-1 bg-gray-50 px-4 py-2.5 text-sm text-gray-600"
                                />
                                <button @click="copyUrl"
                                    class="flex items-center space-x-2 bg-gray-100 px-4 transition hover:bg-gray-200">
                                    <Check v-if="copied" class="h-4 w-4 text-green-600" />
                                    <Copy v-else class="h-4 w-4 text-gray-600" />
                                </button>
                            </div>
                            <p v-if="copied" class="mt-2 text-sm text-green-600">
                                ¡Enlace copiado!
                            </p>
                        </div>

                        <!-- Redes sociales -->
                        <div class="mb-6">
                            <label class="mb-3 block text-sm font-medium text-gray-700">
                                Compartir en redes
                            </label>
                            <div class="flex space-x-3">
                                <button @click="shareOnTwitter"
                                    class="flex flex-1 items-center justify-center space-x-2 rounded-lg bg-[#1DA1F2] px-4 py-2.5 text-white transition hover:bg-[#1a8cd8]">
                                    <span>Twitter</span>
                                </button>
                                <button @click="shareOnLinkedIn"
                                    class="flex flex-1 items-center justify-center space-x-2 rounded-lg bg-[#0077B5] px-4 py-2.5 text-white transition hover:bg-[#006399]">
                                    <span>LinkedIn</span>
                                </button>
                            </div>
                        </div>

                        <!-- Cerrar -->
                        <button @click="showShareModal = false"
                            class="w-full rounded-lg border border-gray-300 py-2.5 text-gray-700 transition hover:bg-gray-50">
                            Cerrar
                        </button>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
/* Transición del modal */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .max-w-md,
.modal-leave-to .max-w-md {
    transform: scale(0.95);
}

/* Estilos de impresión */
@media print {
    .no-print {
        display: none !important;
    }

    .portfolio-content {
        margin: 0;
        padding: 0;
    }

    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}
</style>