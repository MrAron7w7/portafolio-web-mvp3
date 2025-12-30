<script setup lang="ts">
/**
 * Vista de Post - Diseño Minimalista y Profesional
 */
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { MessageSquare, ArrowLeft, ExternalLink, Eye, Send, Trash2, Star } from 'lucide-vue-next';
import { route } from '@/utils/route';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea/index';
import CommentItem from './Partials/CommentItem.vue';

// Template Previews
import AcademicaPreview from '@/components/Templates/Academica.vue';
import CreativaPreview from '@/components/Templates/Creativa.vue';
import EjecutivaPreview from '@/components/Templates/Ejecutiva.vue';
import MinimalistaPreview from '@/components/Templates/Minimalista.vue';
import ModernaPreview from '@/components/Templates/Moderna.vue';
import TecnologicaPreview from '@/components/Templates/Tecnologica.vue';

import type { User, Portfolio, Comment, Post, PaginatedComments } from '@/types/community';





const props = defineProps<{
    post: Post;
    comments: PaginatedComments;
    hasRated: boolean;
    userRating: number | null;
    averageRating: number;
}>();

const page = usePage();
const isAuthor = computed(() => page.props?.auth?.user?.id === props.post.user.id);

// Estado local para las estrellas (no envía al servidor inmediatamente)
const selectedRating = ref(props.userRating ?? 0);
const isSubmitting = ref(false);

const form = useForm({
    content: '',
    parent_id: null
});

const deletePostForm = useForm({});

// Solo actualiza el estado local, NO envía al servidor
const selectRating = (value: number) => {
    selectedRating.value = value;
};

// Envía calificación y comentario juntos
const submitFeedback = async () => {
    isSubmitting.value = true;
    
    try {
        // Si hay calificación seleccionada, enviarla primero
        if (selectedRating.value > 0) {
            await router.post(route('community.rate', props.post.id), {
                rating: selectedRating.value
            }, {
                preserveScroll: true,
                preserveState: true,
                only: ['hasRated', 'userRating', 'averageRating', 'post', 'comments'],
            });
        }
        
        // Si hay comentario, enviarlo
        if (form.content.trim()) {
            form.post(route('community.comment.store', props.post.id), {
                preserveScroll: true,
                onSuccess: () => form.reset(),
            });
        }
    } finally {
        isSubmitting.value = false;
    }
};

const deletePost = () => {
    if (!confirm('¿Eliminar esta publicación?')) return;
    deletePostForm.delete(route('community.destroy', props.post.id), {
        preserveScroll: true,
    });
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getPortfolioUrl = (portfolio: Portfolio | null) => {
    if (!portfolio) return '#';
    if (portfolio.custom_domain) return `https://${portfolio.custom_domain}`;
    return `/p/${portfolio.slug}`;
};

// Component map
const previewComponents: Record<string, any> = {
    Academica: AcademicaPreview,
    Moderna: ModernaPreview,
    Minimalista: MinimalistaPreview,
    Ejecutiva: EjecutivaPreview,
    Creativa: CreativaPreview,
    Tecnologica: TecnologicaPreview,
};

// Preview data (Mock data)
const previewData = {
    personal: {
        name: 'Tu Nombre',
        title: 'Tu Título Profesional',
        email: 'tu.email@ejemplo.com',
        phone: '+00 000 000 000',
        location: 'Tu Ciudad, País',
        summary: 'Aquí aparecerá tu resumen profesional una vez que completes tu perfil.',
    },
    experience: [
        {
            company: 'Empresa Ejemplo',
            position: 'Rol Principal',
            startDate: '2022-01',
            current: true,
            description: 'Liderando proyectos increíbles y creando valor.',
        },
    ],
    education: [],
    skills: { technical: [{ name: 'Habilidad 1', level: 90 }, { name: 'Habilidad 2', level: 80 }], soft: [] },
    projects: [],
};

const getTemplateData = (portfolio: any = null) => {
    // 1. Si el portafolio tiene datos reales, usarlos
    if (portfolio && portfolio.template_data) {
        const data = portfolio.template_data;
        // Fix: Asegurar que personal.name existe
        if (data.personal && !data.personal.name && data.personal.firstName) {
            data.personal.name = `${data.personal.firstName} ${data.personal.lastName || ''}`.trim();
        }
        return data;
    }

    // 2. Fallback: Construir datos usando la info del usuario del post
    // Usamos el mock como base ESTRUCTURAL, pero sobrescribimos contenido visible
    const fallback = JSON.parse(JSON.stringify(previewData));
    
    // Sobrescribir header/personal info con datos del autor del post
    if (props.post && props.post.user) {
        fallback.personal.name = `${props.post.user.first_name} ${props.post.user.last_name}`;
        fallback.personal.title = 'Profesional'; // Título genérico si no hay data
        
        // Limpiar datos dummy que podrían confundir (ej. experiencia falsa)
        // Opcional: Podríamos dejar algunos placeholders genéricos o borrarlos
        fallback.personal.email = '';
        fallback.personal.phone = '';
        fallback.personal.location = '';
        fallback.personal.summary = 'Este portafolio aún no tiene contenido detallado.';
    }

    return fallback;
};

const getPreviewComponent = (type: string) => {
    if (!type) return null;
    if (previewComponents[type]) return previewComponents[type];
    const capitalized = type.charAt(0).toUpperCase() + type.slice(1).toLowerCase();
    if (previewComponents[capitalized]) return previewComponents[capitalized];
    return null;
};
</script>

<template>
    <DashboardLayout>
        <Head :title="post.title" />

        <div class="min-h-screen bg-slate-50/50 py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-5xl mx-auto space-y-8">
                
                <!-- Navigation -->
                <div class="flex items-center justify-between">
                    <Link :href="route('community')" class="group inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors">
                        <div class="p-1 rounded-lg bg-white border border-slate-200 group-hover:border-slate-300 transition-colors shadow-sm">
                            <ArrowLeft class="h-4 w-4" />
                        </div>
                        Volver a la Comunidad
                    </Link>
                    
                    <button 
                        v-if="isAuthor" 
                        type="button" 
                        class="inline-flex items-center gap-2 text-sm font-medium text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors"
                        :disabled="deletePostForm.processing"
                        @click="deletePost"
                    >
                        <Trash2 class="h-4 w-4" />
                        Eliminar Publicación
                    </button>
                </div>

                <!-- Hero Card -->
                <div class="bg-white rounded-[24px] border border-slate-200/60 shadow-xl shadow-slate-200/40 overflow-hidden">
                    <div class="flex flex-col md:flex-row">
                        
                        <!-- Left: Content & Info -->
                        <div class="flex-1 p-8 md:p-10 flex flex-col justify-center relative">
                            <!-- Background Decoration -->
                            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-linear-to-br from-indigo-50 to-violet-50 blur-3xl opacity-50 pointer-events-none"></div>

                            <!-- Author Meta -->
                            <div class="relative z-10 flex items-center gap-4 mb-6">
                                <div class="relative group cursor-pointer">
                                    <div class="absolute -inset-0.5 bg-linear-to-br from-indigo-500 to-violet-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity blur-sm"></div>
                                    <div 
                                        v-if="post.user.avatar_url" 
                                        class="relative h-12 w-12 rounded-full ring-2 ring-white overflow-hidden shadow-sm"
                                    >
                                        <img :src="post.user.avatar_url" :alt="post.user.first_name" class="h-full w-full object-cover">
                                    </div>
                                    <div 
                                        v-else 
                                        class="relative h-12 w-12 rounded-full ring-2 ring-white bg-linear-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white font-bold text-lg shadow-sm"
                                    >
                                        {{ post.user.first_name.charAt(0) }}
                                    </div>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 leading-tight">
                                        {{ post.user.first_name }} {{ post.user.last_name }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mt-0.5">
                                        <span>{{ formatDate(post.created_at) }}</span>
                                        <span>•</span>
                                        <span class="flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded-full text-slate-600">
                                            <Eye class="h-3 w-3" />
                                            {{ post.views_count }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Title & Pitch -->
                            <div class="relative z-10 mb-8">
                                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight mb-4 leading-tight">
                                    {{ post.title }}
                                </h1>
                                <p class="text-lg text-slate-600 leading-relaxed font-light text-pretty">
                                    {{ post.content }}
                                </p>
                            </div>

                            <!-- Action -->
                            <div class="relative z-10 flex items-center gap-4">
                                <a 
                                    v-if="post.portfolio" 
                                    :href="getPortfolioUrl(post.portfolio)" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                                >
                                    Ver Portafolio Completo
                                    <ExternalLink class="h-4 w-4" />
                                </a>
                            </div>
                        </div>

                        <!-- Right: Visual Preview -->
                        <div class="w-full md:w-[45%] bg-slate-50 border-t md:border-t-0 md:border-l border-slate-100 p-8 flex items-center justify-center relative overflow-hidden group">
                            <!-- Background Pattern -->
                             <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#6366f1 1px, transparent 1px); background-size: 20px 20px;"></div>
                            
                            <!-- Perspective Container -->
                             <div class="relative w-full max-w-sm perspective-1000">
                                <div class="relative aspect-3/4 w-full bg-white rounded-2xl shadow-2xl transition-transform duration-500 group-hover:rotate-y-2 group-hover:rotate-x-2 transform-style-3d border-[6px] border-slate-900/5 overflow-hidden">
                                     <!-- Scaling Wrapper for Template -->
                                    <div 
                                        v-if="post.portfolio && getPreviewComponent(post.portfolio.template_type)" 
                                        class="absolute inset-0 h-[250%] w-[250%] origin-top-left scale-[0.4] pointer-events-none select-none bg-white"
                                    >
                                        <component
                                            :is="getPreviewComponent(post.portfolio.template_type)"
                                            :data="getTemplateData(post.portfolio)"
                                        />
                                    </div>
                                    <div v-else class="flex h-full w-full items-center justify-center bg-slate-100 text-slate-400">
                                        <span class="text-sm font-medium">Vista previa no disponible</span>
                                    </div>

                                    <!-- Overlay CTA -->
                                    <a 
                                        v-if="post.portfolio"
                                        :href="getPortfolioUrl(post.portfolio)"
                                        target="_blank"
                                        class="absolute inset-0 flex items-center justify-center bg-slate-900/40 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-all duration-300 z-20 cursor-pointer"
                                    >
                                        <div class="bg-white text-slate-900 px-6 py-3 rounded-full font-bold shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 flex items-center gap-2">
                                            Explorar
                                            <ExternalLink class="h-4 w-4" />
                                        </div>
                                    </a>
                                </div>
                             </div>
                        </div>
                    </div>
                    
                    <!-- Footer Info -->
                    <div class="px-8 py-4 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between text-sm">
                        <div class="text-slate-500">
                            Publicado el {{ formatDate(post.created_at) }}
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-slate-700">Calificación Global:</span>
                            <div class="flex items-center gap-1 bg-white px-2 py-1 rounded-md border border-slate-200 shadow-sm">
                                <Star class="h-4 w-4 text-amber-400 fill-amber-400" />
                                <span class="font-bold text-slate-900">{{ averageRating.toFixed(1) }}</span>
                                <span class="text-slate-400 text-xs">({{ post.ratings_count ?? 0 }})</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stacked Layout: Feedback & Comments -->
                <div class="space-y-8">
                    
                    <!-- Feedback Section -->
                    <!-- Feedback Section -->
                    <div class="bg-white rounded-[24px] border border-slate-200 p-6 shadow-sm">
                        <form @submit.prevent="submitFeedback" class="space-y-6">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">Tu Opinión</h3>
                                    <p class="text-sm text-slate-500">¿Qué te pareció este portafolio?</p>
                                </div>

                                <!-- Interactive Stars -->
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1 bg-slate-50 p-2 rounded-xl border border-slate-100">
                                        <button
                                            v-for="i in 5"
                                            :key="i"
                                            type="button"
                                            class="group p-0.5 focus:outline-none transition-transform active:scale-95"
                                            @click="selectRating(i)"
                                        >
                                            <Star 
                                                class="h-6 w-6 transition-all duration-200" 
                                                :class="i <= selectedRating 
                                                    ? 'text-amber-400 fill-amber-400 drop-shadow-sm' 
                                                    : 'text-slate-900 group-hover:text-slate-700'"
                                            />
                                        </button>
                                    </div>
                                    
                                    <div v-if="selectedRating > 0" class="flex items-center justify-center h-11 w-14 bg-amber-50/50 rounded-xl border border-amber-100 transition-all animate-in fade-in slide-in-from-left-2">
                                        <span class="text-lg font-black text-amber-500">{{ selectedRating }}.0</span>
                                    </div>
                                </div>
                            </div>

                            <Textarea 
                                v-model="form.content" 
                                placeholder="Comparte tus comentarios constructivos..." 
                                rows="3"
                                class="w-full bg-white border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl resize-none text-sm shadow-sm"
                            />

                            <div v-if="props.hasRated" class="text-xs text-emerald-600 font-medium bg-emerald-50 py-2 px-4 rounded-lg border border-emerald-100 inline-block">
                                ✨ Ya enviaste una calificación
                            </div>

                            <div class="flex justify-end">
                                <Button 
                                    type="submit" 
                                    class="bg-slate-900 hover:bg-slate-800 text-white rounded-xl py-2 px-6 shadow-lg shadow-slate-900/10 transition-all active:scale-[0.98]"
                                    :disabled="isSubmitting || (selectedRating === 0 && !form.content.trim())"
                                >
                                    <span v-if="isSubmitting" class="flex items-center gap-2">
                                        <div class="h-4 w-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                        Enviando...
                                    </span>
                                    <span v-else class="flex items-center gap-2">
                                        <Send class="h-4 w-4" />
                                        Enviar Feedback
                                    </span>
                                </Button>
                            </div>
                        </form>
                    </div>

                    <!-- Comments Section -->
                    <div class="bg-white rounded-[24px] border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                <MessageSquare class="h-5 w-5 text-indigo-500" />
                                Comentarios
                            </h3>
                            <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-full">
                                {{ post.comments_count }}
                            </span>
                        </div>

                        <div class="p-6">
                            <div class="space-y-6">
                                <CommentItem 
                                    v-for="comment in comments.data" 
                                    :key="comment.id" 
                                    :comment="comment" 
                                    :postId="post.id"
                                />
                                
                                <div v-if="comments.data.length === 0" class="py-12 flex flex-col items-center justify-center text-center">
                                    <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                        <MessageSquare class="h-8 w-8 text-slate-300" />
                                    </div>
                                    <p class="text-slate-900 font-medium">Sé el primero en comentar</p>
                                    <p class="text-slate-500 text-sm mt-1">Comparte tu opinión con la comunidad</p>
                                </div>

                                <!-- Pagination Controls -->
                                <div v-if="comments.links.length > 3" class="mt-8 flex justify-center">
                                    <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 shadow-sm">
                                        <template v-for="(link, k) in comments.links" :key="k">
                                            <div 
                                                v-if="link.url === null"  
                                                class="px-3 py-1.5 text-xs text-slate-400 font-medium"
                                                v-html="link.label"
                                            />
                                            <Link 
                                                v-else 
                                                :href="link.url" 
                                                class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors border"
                                                :class="link.active 
                                                    ? 'bg-indigo-50 border-indigo-200 text-indigo-700' 
                                                    : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                                                v-html="link.label"
                                                preserve-scroll
                                            />
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
.perspective-1000 {
    perspective: 1000px;
}
.transform-style-3d {
    transform-style: preserve-3d;
}
.rotate-y-2 {
    transform: rotateY(2deg);
}
.rotate-x-2 {
    transform: rotateX(2deg);
}
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
