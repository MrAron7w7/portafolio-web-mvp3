<script setup lang="ts">
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { MessageSquare, ArrowLeft, ExternalLink, Calendar, Eye, Send, Trash2 } from 'lucide-vue-next';
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

const form = useForm({
    content: '',
    parent_id: null
});

const deletePostForm = useForm({});
const ratingForm = useForm({
    rating: props.userRating ?? 0,
});

const submitComment = () => {
    form.post(route('community.comment.store', props.post.id), {
        onSuccess: () => form.reset(),
        preserveScroll: true
    });
};

const setRating = (value: number) => {
    ratingForm.rating = value;
    ratingForm.post(route('community.rate', props.post.id), {
        preserveScroll: true,
    });
};

const deletePost = () => {
    if (!confirm('¿Seguro que quieres eliminar esta publicación? Esto también eliminará los comentarios asociados.')) {
        return;
    }

    deletePostForm.delete(route('community.destroy', props.post.id), {
        preserveScroll: true,
    });
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// URL logic for external link
const getPortfolioUrl = (portfolio: Portfolio | null) => {
    if (!portfolio) return '#';
    
    if (portfolio.custom_domain) {
        return `https://${portfolio.custom_domain}`;
    }
    // Adjust this base URL logic as needed for the app
    return `/p/${portfolio.slug}`;
};
</script>

<template>
    <DashboardLayout>
        <Head :title="post.title" />

        <div class="min-h-screen bg-gray-50/50 py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                
                <!-- Back Link + Actions -->
                <div class="mb-6 flex items-center justify-between gap-3">
                    <Link :href="route('community')" class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600 transition-colors">
                        <ArrowLeft class="h-4 w-4 mr-1" /> Volver a la Comunidad
                    </Link>
                    <Button 
                        v-if="isAuthor" 
                        type="button" 
                        variant="ghost" 
                        class="text-red-600 hover:text-red-700 hover:bg-red-50"
                        :disabled="deletePostForm.processing"
                        @click="deletePost"
                    >
                        <Trash2 class="h-4 w-4 mr-1" /> Eliminar publicación
                    </Button>
                </div>

                <!-- Post Content -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <!-- Header -->
                    <div class="bg-indigo-900 p-8 text-white relative overflow-hidden">
                        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10 brightness-100"></div>
                        <div class="absolute top-0 right-0 h-48 w-48 bg-purple-500 rounded-full blur-3xl opacity-20 transform translate-x-10 -translate-y-10"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-4 text-indigo-200 text-sm font-medium">
                                <span class="bg-indigo-800/50 backdrop-blur px-3 py-1 rounded-full border border-indigo-700/50 flex items-center gap-2">
                                    <Calendar class="h-3 w-3" /> {{ formatDate(post.created_at) }}
                                </span>
                                <span class="bg-indigo-800/50 backdrop-blur px-3 py-1 rounded-full border border-indigo-700/50 flex items-center gap-2">
                                    <Eye class="h-3 w-3" /> {{ post.views_count }} vistas
                                </span>
                            </div>
                            
                            <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-6 leading-tight">
                                {{ post.title }}
                            </h1>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div v-if="post.user.profile_photo_path" class="h-10 w-10 rounded-full border-2 border-indigo-400 overflow-hidden">
                                        <img :src="post.user.profile_photo_path" :alt="post.user.first_name" class="h-full w-full object-cover">
                                    </div>
                                    <div v-else class="h-10 w-10 rounded-full bg-indigo-700 flex items-center justify-center text-white font-bold border-2 border-indigo-400">
                                        {{ post.user.first_name.charAt(0) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-white">{{ post.user.first_name }} {{ post.user.last_name }}</div>
                                        <div class="text-xs text-indigo-300">Autor</div>
                                    </div>
                                </div>
                                
                                <a v-if="post.portfolio" :href="getPortfolioUrl(post.portfolio)" target="_blank" class="hidden sm:inline-flex items-center gap-2 bg-white text-indigo-900 hover:bg-indigo-50 px-5 py-2.5 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-sm">
                                    Ver Portafolio <ExternalLink class="h-4 w-4" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-8 md:p-10">
                        <!-- Mobile CTA -->
                        <div class="sm:hidden mb-8">
                            <a v-if="post.portfolio" :href="getPortfolioUrl(post.portfolio)" target="_blank" class="w-full flex justify-center items-center gap-2 bg-indigo-600 text-white hover:bg-indigo-700 px-5 py-3 rounded-xl font-bold transition-all shadow-md text-sm">
                                Ver Portafolio <ExternalLink class="h-4 w-4" />
                            </a>
                        </div>

                        <div class="prose prose-lg text-gray-700 max-w-none leading-relaxed">
                             <p class="whitespace-pre-wrap">{{ post.content }}</p>
                        </div>
                    </div>
                </div>

                <!-- Interaction & Discussion Section -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 md:p-10">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                                <MessageSquare class="h-6 w-6 text-indigo-600" />
                                Conversación <span class="text-gray-400 text-lg font-normal">({{ post.comments_count }})</span>
                            </h2>
                            <div class="flex items-center gap-2 text-sm text-gray-500 bg-gray-50 px-4 py-2 rounded-full border border-gray-100">
                                <span class="font-bold text-gray-900 flex items-center gap-1">
                                    {{ props.averageRating.toFixed(1) }}
                                    <svg class="h-4 w-4 text-yellow-400 fill-current" viewBox="0 0 24 24"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                </span>
                                <span class="w-px h-3 bg-gray-300 mx-1"></span>
                                <span>{{ props.post.ratings_count ?? 0 }} votos</span>
                            </div>
                        </div>

                        <!-- Unified Interaction Area -->
                        <div class="mb-10 bg-gray-50/50 rounded-2xl border border-gray-100 p-6 md:p-8 transition-all hover:border-indigo-100 hover:shadow-sm">
                            <!-- Rating Section -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">Tu calificación</h3>
                                    <p class="text-sm text-gray-500 mt-0.5">Ayuda a la comunidad con tu voto</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-1">
                                        <button
                                            v-for="i in 5"
                                            :key="i"
                                            type="button"
                                            class="transition-all transform hover:scale-110 focus:outline-none"
                                            :class="i <= ratingForm.rating ? 'text-yellow-400' : 'text-gray-200 hover:text-yellow-300'"
                                            :disabled="ratingForm.processing"
                                            @click="setRating(i)"
                                        >
                                            <svg class="h-8 w-8 fill-current drop-shadow-sm" viewBox="0 0 24 24">
                                                <path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <span class="text-2xl font-bold text-gray-700 w-12 text-center" v-if="ratingForm.rating > 0">
                                        {{ ratingForm.rating.toFixed(1) }}
                                    </span>
                                    <span class="text-xl font-bold text-gray-300 w-12 text-center" v-else>
                                        0.0
                                    </span>
                                </div>
                            </div>

                            <!-- Comment Form (Blocked if not rated) -->
                            <div v-if="!props.hasRated" class="relative">
                                <div class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-xl border border-dashed border-gray-200">
                                    <div class="text-center p-4">
                                        <p class="text-sm font-medium text-gray-500">Califica la publicación para unirte al debate</p>
                                    </div>
                                </div>
                                <div class="opacity-40 pointer-events-none">
                                    <Textarea 
                                        placeholder="Comparte tu feedback..." 
                                        rows="3"
                                        class="bg-white border-gray-200"
                                        disabled
                                    />
                                    <div class="flex justify-end mt-3">
                                        <Button disabled variant="secondary">Comentar</Button>
                                    </div>
                                </div>
                            </div>

                            <form v-else @submit.prevent="submitComment">
                                <div class="space-y-4">
                                    <Textarea 
                                        v-model="form.content" 
                                        placeholder="Escribe un comentario constructivo..." 
                                        rows="3"
                                        class="bg-white border-gray-200 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 transition-all resize-y min-h-[100px]"
                                        required
                                    />
                                    <div class="flex items-center justify-end gap-3">
                                        <span class="text-xs text-gray-400 hidden sm:inline-block">Presiona Enter para nueva línea</span>
                                        <Button 
                                            type="submit" 
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 rounded-xl shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transition-all hover:-translate-y-0.5"
                                            :disabled="form.processing"
                                        >
                                            <Send class="h-4 w-4 mr-2" /> 
                                            Publicar comentario
                                        </Button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Threads List -->
                        <div class="space-y-8 pl-0 md:pl-2">
                            <CommentItem 
                                v-for="comment in comments" 
                                :key="comment.id" 
                                :comment="comment" 
                                :postId="post.id"
                            />
                            
                            <div v-if="props.hasRated && comments.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="bg-gray-50 p-4 rounded-full mb-3">
                                    <MessageSquare class="h-6 w-6 text-gray-300" />
                                </div>
                                <p class="text-gray-500 font-medium">Aún no hay comentarios</p>
                                <p class="text-sm text-gray-400">Sé el primero en compartir tu opinión</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </DashboardLayout>
</template>
