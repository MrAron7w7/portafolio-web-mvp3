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

interface User {
    id: number;
    first_name: string;
    last_name: string;
    profile_photo_path?: string;
}

interface Portfolio {
    id: number;
    title: string;
    slug: string;
    user_id: number;
    theme_settings?: any;
    custom_domain?: string;
}

interface Comment {
    id: number;
    user_id: number;
    content: string;
    created_at: string;
    user: User;
    replies?: Comment[];
}

interface Post {
    id: number;
    title: string;
    content: string;
    views_count: number;
    created_at: string;
    comments_count: number;
    ratings_count?: number;
    user: User;
    portfolio: Portfolio;
}

const props = defineProps<{
    post: Post;
    comments: Comment[];
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
</script>

<template>
    <DashboardLayout>
        <Head :title="post.title" />

        <div class="min-h-screen bg-slate-50 py-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                
                <!-- Back Link + Actions -->
                <div class="mb-6 flex items-center justify-between">
                    <Link :href="route('community')" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition-colors">
                        <ArrowLeft class="h-4 w-4" />
                        Volver
                    </Link>
                    <button 
                        v-if="isAuthor" 
                        type="button" 
                        class="inline-flex items-center gap-1 text-sm text-red-500 hover:text-red-600 transition-colors"
                        :disabled="deletePostForm.processing"
                        @click="deletePost"
                    >
                        <Trash2 class="h-4 w-4" />
                        Eliminar
                    </button>
                </div>

                <!-- Post Card -->
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                    <!-- Header -->
                    <div class="p-6 border-b border-slate-100">
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <div class="flex items-center gap-3">
                                <div 
                                    v-if="post.user.profile_photo_path" 
                                    class="h-10 w-10 rounded-full overflow-hidden"
                                >
                                    <img :src="post.user.profile_photo_path" :alt="post.user.first_name" class="h-full w-full object-cover">
                                </div>
                                <div 
                                    v-else 
                                    class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold text-sm"
                                >
                                    {{ post.user.first_name.charAt(0) }}
                                </div>
                                <div>
                                    <div class="font-medium text-slate-900 text-sm">
                                        {{ post.user.first_name }} {{ post.user.last_name }}
                                    </div>
                                    <div class="text-xs text-slate-500">{{ formatDate(post.created_at) }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                <span class="flex items-center gap-1">
                                    <Eye class="h-3.5 w-3.5" />
                                    {{ post.views_count }}
                                </span>
                            </div>
                        </div>
                        
                        <h1 class="text-xl font-semibold text-slate-900 mb-3">
                            {{ post.title }}
                        </h1>

                        <a 
                            v-if="post.portfolio" 
                            :href="getPortfolioUrl(post.portfolio)" 
                            target="_blank" 
                            class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                        >
                            Ver Portafolio
                            <ExternalLink class="h-3.5 w-3.5" />
                        </a>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-wrap">{{ post.content }}</p>
                    </div>

                    <!-- Rating Summary (solo info, no interactivo) -->
                    <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2 text-sm text-slate-500">
                        <div class="flex items-center gap-1">
                            <Star class="h-4 w-4 text-slate-500 fill-amber-400" />
                            <span class="font-semibold text-slate-700">{{ averageRating.toFixed(1) }}</span>
                        </div>
                        <span>·</span>
                        <span>{{ post.ratings_count ?? 0 }} votos</span>
                    </div>
                </div>

                <!-- Feedback Section (Rating + Comment juntos) -->
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h2 class="text-base font-semibold text-slate-900">Tu opinión</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Califica y comenta este portafolio</p>
                    </div>

                    <form @submit.prevent="submitFeedback" class="p-6 space-y-5">
                        <!-- Rating Stars -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <span class="text-sm font-medium text-slate-700 shrink-0">Calificación:</span>
                            <div class="flex items-center gap-1">
                                <button
                                    v-for="i in 5"
                                    :key="i"
                                    type="button"
                                    class="p-1 transition-all duration-150 hover:scale-125 focus:outline-none focus:ring-2 focus:ring-amber-400/50 rounded"
                                    @click="selectRating(i)"
                                >
                                    <Star 
                                        class="h-6 w-6 transition-colors duration-150" 
                                        :class="i <= selectedRating 
                                            ? 'text-slate-500 fill-amber-400' 
                                            : 'text-slate-500'"
                                    />
                                </button>
                                <span v-if="selectedRating > 0" class="ml-2 text-sm text-slate-500">
                                    {{ selectedRating }} de 5
                                </span>
                            </div>
                        </div>

                        <!-- Comment Textarea -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Comentario (opcional):</label>
                            <Textarea 
                                v-model="form.content" 
                                placeholder="Escribe tu opinión sobre este portafolio..." 
                                rows="3"
                                class="bg-slate-50 border-slate-200 text-sm resize-none"
                            />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between pt-2">
                            <p v-if="props.hasRated" class="text-xs text-emerald-600 flex items-center gap-1">
                                <Star class="h-3 w-3 fill-emerald-500" />
                                Ya calificaste con {{ props.userRating }} estrellas
                            </p>
                            <div v-else></div>
                            <Button 
                                type="submit" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5"
                                :disabled="isSubmitting || (selectedRating === 0 && !form.content.trim())"
                            >
                                <Send class="h-4 w-4 mr-2" />
                                {{ isSubmitting ? 'Enviando...' : 'Enviar' }}
                            </Button>
                        </div>
                    </form>
                </div>

                <!-- Comments Section -->
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                            <MessageSquare class="h-4 w-4 text-slate-400" />
                            Comentarios
                            <span class="text-slate-400 font-normal">({{ post.comments_count }})</span>
                        </h2>
                    </div>

                    <div class="p-6">
                        <!-- Comments List -->
                        <div class="space-y-4">
                            <CommentItem 
                                v-for="comment in comments" 
                                :key="comment.id" 
                                :comment="comment" 
                                :postId="post.id"
                            />
                            
                            <div v-if="comments.length === 0" class="py-8 text-center">
                                <MessageSquare class="h-8 w-8 text-slate-300 mx-auto mb-2" />
                                <p class="text-sm text-slate-500">Sin comentarios aún</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </DashboardLayout>
</template>
