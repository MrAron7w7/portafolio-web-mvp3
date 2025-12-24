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

                <!-- Rating Gate -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 md:p-10 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Califica la publicación</h2>
                            <p class="text-gray-500 mt-1">
                                Para acceder al hilo necesitas calificar este portafolio. Tu voto ayuda a destacar lo mejor de la comunidad.
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1">
                                <button
                                    v-for="i in 5"
                                    :key="i"
                                    type="button"
                                    class="transition-colors"
                                    :class="i <= ratingForm.rating ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-300'"
                                    :disabled="ratingForm.processing"
                                    @click="setRating(i)"
                                >
                                    <svg class="h-7 w-7 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                    </svg>
                                </button>
                            </div>
                            <div class="text-sm text-gray-500">
                                <div class="font-semibold text-gray-900">{{ props.averageRating.toFixed(1) }}</div>
                                <div>{{ props.post.ratings_count ?? 0 }} votos</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discussion Section -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 md:p-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                        <MessageSquare class="h-6 w-6 text-indigo-600" />
                        Conversación <span class="text-gray-400 text-lg font-normal">({{ post.comments_count }})</span>
                    </h2>

                    <!-- New Comment Form -->
                    <div v-if="!props.hasRated" class="mb-12 rounded-2xl border border-dashed border-indigo-200 bg-indigo-50 p-6 text-indigo-700">
                        <p class="font-semibold">Primero califica esta publicación.</p>
                        <p class="text-sm mt-1">Después de votar podrás ver el hilo completo y comentar.</p>
                    </div>
                    <form v-else @submit.prevent="submitComment" class="mb-12 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Participa en la discusión</label>
                        <div class="space-y-4">
                            <Textarea 
                                v-model="form.content" 
                                placeholder="Comparte tu feedback, haz preguntas o felicita al autor..." 
                                rows="3"
                                class="bg-white"
                                required
                            />
                            <div class="flex justify-end">
                                <Button 
                                    type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white"
                                    :disabled="form.processing"
                                >
                                    <Send class="h-4 w-4 mr-2" /> Comentar
                                </Button>
                            </div>
                        </div>
                    </form>

                    <!-- Threads -->
                    <div class="space-y-8">
                        <CommentItem 
                            v-for="comment in comments" 
                            :key="comment.id" 
                            :comment="comment" 
                            :postId="post.id"
                        />
                        
                        <div v-if="props.hasRated && comments.length === 0" class="text-center py-10 text-gray-400 italic">
                            Aún no hay comentarios. Sé el primero en iniciar la conversación.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </DashboardLayout>
</template>
