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
    QrCode,
    X,
    AlertTriangle,
    Edit3
} from 'lucide-vue-next';
import { getTemplateComponent } from '@/components/Templates/index';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);
const windowOrigin = typeof window !== 'undefined' ? window.location.origin : '';

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
        access_mode: 'owner_only' | 'link';
        link_permission?: 'view' | 'view_edit'; // Permiso del enlace
        is_completed: boolean;
        theme_settings: any;
        created_at: string;
        updated_at: string;
        share_token?: string;
    };
    isOwner: boolean;
    publicUrl?: string;
    canEdit?: boolean; // Viene del backend
}>();

// Estado de compartición calculado
const shareStatus = computed(() => {
    if (props.portfolio.access_mode === 'link') {
        return {
            label: 'Público',
            icon: Globe,
            classes: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            isPublic: true
        };
    }
    return {
        label: 'Privado',
        icon: Lock,
        classes: 'bg-gray-100 text-gray-600 dark:bg-slate-800 dark:text-slate-400',
        isPublic: false
    };
});

// Puede editar: usa el valor del backend, con fallback a cálculo local
const canEdit = computed(() => {
    if (props.canEdit !== undefined) return props.canEdit;
    if (props.isOwner) return true;
    return props.portfolio.link_permission === 'view_edit';
});

// URL del editor: dashboard para dueños, público para invitados
const editUrl = computed(() => {
    if (props.isOwner) {
        return `/dashboard/portfolio/${props.portfolio.id}/editor`;
    }
    // Para invitados con permiso, usar editor público
    return `/p/${props.portfolio.slug}/edit`;
});

// Estado para mostrar advertencia de no poder editar
const showNoEditWarning = ref(false);

// Manejar clic en editar
const handleEditClick = () => {
    if (canEdit.value) {
        // Navegar al editor (se manejará con Link)
        return true;
    } else {
        // Mostrar advertencia
        showNoEditWarning.value = true;
        setTimeout(() => showNoEditWarning.value = false, 4000);
        return false;
    }
};

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

    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-300">
        <!-- Header de control (visible para todos) -->
        <header class="sticky top-0 z-50 border-b border-gray-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm no-print transition-colors duration-300">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Navegación izquierda -->
                    <div class="flex items-center space-x-4">
                        <Link v-if="isOwner" href="/dashboard" 
                            class="flex items-center space-x-2 rounded-lg px-3 py-2 text-gray-600 dark:text-slate-400 transition hover:bg-gray-100 dark:hover:bg-slate-800">
                            <ArrowLeft class="h-5 w-5" />
                            <span class="hidden sm:inline">Volver al Dashboard</span>
                        </Link>
                        <div v-else class="flex items-center space-x-2 text-gray-600 dark:text-slate-400">
                            <span class="text-sm font-medium truncate max-w-[200px]">{{ portfolio.title }}</span>
                        </div>
                    </div>

                    <!-- Info del portafolio (solo dueño ve el estado) -->
                    <div v-if="isOwner" class="flex items-center space-x-3">
                        <span :class="['flex items-center space-x-1 rounded-full px-3 py-1 text-sm font-medium', shareStatus.classes]">
                            <component :is="shareStatus.icon" class="h-4 w-4" />
                            <span>{{ shareStatus.label }}</span>
                        </span>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center space-x-2">
                        <!-- Compartir (visible si es público) -->
                        <button v-if="shareStatus.isPublic" 
                            @click="showShareModal = true"
                            class="flex items-center space-x-2 rounded-lg border border-gray-300 dark:border-slate-700 px-4 py-2 text-gray-700 dark:text-slate-300 transition hover:bg-gray-50 dark:hover:bg-slate-800">
                            <Share2 class="h-4 w-4" />
                            <span class="hidden sm:inline">Compartir</span>
                        </button>

                        <!-- Descargar PDF -->
                        <button @click="printPortfolio"
                            class="flex items-center space-x-2 rounded-lg border border-gray-300 dark:border-slate-700 px-4 py-2 text-gray-700 dark:text-slate-300 transition hover:bg-gray-50 dark:hover:bg-slate-800">
                            <Download class="h-4 w-4" />
                            <span class="hidden sm:inline">Descargar PDF</span>
                        </button>

                        <!-- Logica del Botón Editar -->
                        <div v-if="canEdit">
                            <!-- Caso 1: Usuario autenticado -> Ir al editor -->
                            <Link v-if="isAuthenticated" :href="editUrl"
                                class="flex items-center space-x-2 rounded-lg bg-[#005aeb] px-4 py-2 text-white transition hover:bg-[#0048c4]">
                                <Edit3 class="h-4 w-4" />
                                <span class="hidden sm:inline">Editar</span>
                            </Link>

                            <!-- Caso 2: Usuario NO autenticado -> Ir a Login -->
                            <a v-else href="/login"
                                class="flex items-center space-x-2 rounded-lg bg-[#005aeb] px-4 py-2 text-white transition hover:bg-[#0048c4]">
                                <UserPlus class="h-4 w-4" />
                                <span class="hidden sm:inline">Inicia sesión</span>
                            </a>
                        </div>
                        
                        <!-- Caso 3: Sin permiso de edición -->
                        <button v-else
                            @click="handleEditClick"
                            class="flex items-center space-x-2 rounded-lg bg-gray-200 dark:bg-slate-800 px-4 py-2 text-gray-500 dark:text-slate-500 cursor-not-allowed">
                            <Lock class="h-4 w-4" />
                            <span class="hidden sm:inline">Solo lectura</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Toast de advertencia -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="translate-y-2 opacity-0"
                enter-to-class="translate-y-0 opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="translate-y-0 opacity-100"
                leave-to-class="-translate-y-2 opacity-0">
                <div v-if="showNoEditWarning" 
                    class="absolute left-1/2 -translate-x-1/2 top-20 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-900/50 rounded-xl px-4 py-3 shadow-lg flex items-center gap-3 z-50">
                    <div class="p-1.5 bg-amber-100 dark:bg-amber-900/40 rounded-lg">
                        <AlertTriangle class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-amber-900 dark:text-amber-200">Solo puedes ver este portafolio</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400/80">El propietario no ha habilitado la edición.</p>
                    </div>
                    <button @click="showNoEditWarning = false" class="ml-2 text-amber-400 hover:text-amber-600">
                        <X class="h-4 w-4" />
                    </button>
                </div>
            </Transition>
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
                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-gray-100 dark:bg-slate-800">
                        <Eye class="h-10 w-10 text-gray-400 dark:text-slate-500" />
                    </div>
                    <h2 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">Portafolio vacío</h2>
                    <p class="mb-6 text-gray-600 dark:text-slate-400">Este portafolio aún no tiene contenido.</p>
                    <Link v-if="isOwner" :href="`/dashboard/portfolio/${portfolio.id}/editor`"
                        class="rounded-lg bg-[#005aeb] px-6 py-3 text-white transition hover:bg-[#0048c4]">
                        Comenzar a editar
                    </Link>
                </div>
            </div>
        </main>

        <!-- Footer del portafolio -->
        <footer class="border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-8 no-print transition-colors duration-300">
            <div class="mx-auto max-w-7xl px-4 text-center">
                <p class="text-sm text-gray-500 dark:text-slate-400">
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
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                    @click.self="showShareModal = false">
                    <div class="relative w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl border border-gray-100 dark:border-slate-800 transition-colors duration-300">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Compartir Portafolio</h3>
                            <button @click="showShareModal = false" class="rounded-full p-1 bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                <X class="h-5 w-5" />
                            </button>
                        </div>
                        
                        <!-- URL del portafolio -->
                        <div class="mb-6">
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-slate-300">
                                Enlace público
                            </label>
                            <div class="flex rounded-lg border border-gray-300 dark:border-slate-700 overflow-hidden">
                                <input 
                                    type="text" 
                                    :value="publicUrl || (portfolio.share_token ? `${$page.props.app_url || windowOrigin}/p/${portfolio.share_token}` : 'Enlace no disponible')" 
                                    readonly
                                    class="flex-1 bg-gray-50 dark:bg-slate-800 px-4 py-2.5 text-sm text-gray-600 dark:text-slate-300 focus:outline-none"
                                    :class="{ 'text-gray-400 italic': !publicUrl && !portfolio.share_token }"
                                />
                                <button 
                                    @click="copyUrl"
                                    :disabled="!publicUrl && !portfolio.share_token"
                                    class="flex items-center space-x-2 bg-gray-100 dark:bg-slate-700 px-4 transition hover:bg-gray-200 dark:hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <Check v-if="copied" class="h-4 w-4 text-green-600 dark:text-green-400" />
                                    <Copy v-else class="h-4 w-4 text-gray-600 dark:text-slate-400" />
                                </button>
                            </div>
                            <p v-if="copied" class="mt-2 text-sm text-green-600 font-medium">
                                ¡Enlace copiado al portapapeles!
                            </p>
                            <p v-if="!publicUrl && !portfolio.share_token" class="mt-2 text-xs text-red-500">
                                No se ha podido generar el enlace. Por favor, vuelve a activarlo desde el editor.
                            </p>
                        </div>

                        <!-- Redes sociales -->
                        <div>
                            <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-slate-300">
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