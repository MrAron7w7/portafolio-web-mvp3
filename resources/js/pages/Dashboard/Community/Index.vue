<script setup lang="ts">
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, usePage, useForm, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { MessageSquare, Star, Send, ThumbsUp, Medal, Calendar, CheckCircle } from 'lucide-vue-next';

const props = defineProps<{
    comments: {
        data: Array<{
            id: number;
            content: string;
            rating: number;
            is_featured: boolean;
            type: string;
            created_at: string;
            user: {
                id: number;
                first_name: string;
                last_name: string;
                profile_photo_path?: string;
            }
        }>;
        links: Array<any>;
    }
}>();

const form = useForm({
    content: '',
    rating: 5
});

const submitComment = () => {
    form.post('/dashboard/comunidad', {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
const page = usePage();
const showSuccess = ref(false);
const successMessage = ref('');

watch(() => page.props.flash.success, (msg) => {
    if (msg) {
        successMessage.value = msg as string;
        showSuccess.value = true;
        setTimeout(() => showSuccess.value = false, 3000);
    }
});
</script>

<template>
    <DashboardLayout>
        <Head title="Comunidad" />
        
        <div class="min-h-screen bg-gray-50/50 py-8 px-4 sm:px-6 lg:px-8 relative">
            <!-- Toast Notification -->
            <div v-show="showSuccess" class="fixed top-24 right-4 z-50 bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 animate-fade-in-up transition-all duration-300 transform">
                <CheckCircle class="h-6 w-6 text-white" />
                <div>
                    <h4 class="font-bold">¡Éxito!</h4>
                    <p class="text-sm font-medium">{{ successMessage }}</p>
                </div>
            </div>
            <div class="max-w-5xl mx-auto space-y-8">
                
                <!-- Header Section with Glassmorphism -->
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-600 p-8 text-white shadow-2xl">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 h-64 w-64 rounded-full bg-pink-500/20 blur-3xl"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="space-y-2 text-center md:text-left">
                            <h1 class="text-4xl font-black tracking-tight flex items-center gap-3 justify-center md:justify-start">
                                <MessageSquare class="h-10 w-10 text-yellow-300" />
                                Comunidad
                            </h1>
                            <p class="text-indigo-100 text-lg max-w-xl">
                                Comparte tu experiencia, sugiere mejoras y conecta con otros creadores. 
                                ¡Tu opinión construye esta plataforma!
                            </p>
                        </div>
                        
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 transform rotate-2 hover:rotate-0 transition-transform duration-300 mobile-hidden">
                            <div class="flex items-center gap-3">
                                <div class="bg-yellow-400 p-2 rounded-full">
                                    <Medal class="h-6 w-6 text-yellow-900" />
                                </div>
                                <div>
                                    <p class="font-bold text-sm">Destacado</p>
                                    <p class="text-xs text-indigo-100">Los mejores comentarios<br>aparecen en la home</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Feed Section -->
                    <div class="lg:col-span-2 space-y-6">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                            <span class="bg-indigo-100 text-indigo-600 p-2 rounded-lg">
                                <Calendar class="h-5 w-5" />
                            </span>
                            Últimas Interacciones
                        </h2>

                        <div class="space-y-4">
                            <div v-for="comment in comments.data" :key="comment.id" 
                                class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-md hover:border-indigo-100 group">
                                
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div v-if="comment.user.profile_photo_path" class="h-12 w-12 rounded-full overflow-hidden border-2 border-indigo-50">
                                            <img :src="comment.user.profile_photo_path" :alt="comment.user.first_name" class="h-full w-full object-cover">
                                        </div>
                                        <div v-else class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-lg shadow-inner">
                                            {{ comment.user.first_name.charAt(0) }}
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                                    {{ comment.user.first_name }} {{ comment.user.last_name }}
                                                    <span v-if="comment.is_featured" class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full border border-yellow-200 flex items-center gap-1">
                                                        <Star class="h-3 w-3 fill-current" /> Destacado
                                                    </span>
                                                </h3>
                                                <p class="text-xs text-gray-500">{{ formatDate(comment.created_at) }}</p>
                                            </div>
                                            <div class="flex gap-0.5" title="Calificación">
                                                <Star v-for="i in 5" :key="i" 
                                                    class="h-4 w-4" 
                                                    :class="i <= comment.rating ? 'text-yellow-400 fill-current' : 'text-gray-200'" 
                                                />
                                            </div>
                                        </div>
                                        
                                        <div class="prose prose-sm text-gray-600 max-w-none">
                                            <p>{{ comment.content }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Empty State -->
                            <div v-if="comments.data.length === 0" class="text-center py-12 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                                <MessageSquare class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                                <p class="text-gray-500 font-medium">Aún no hay comentarios. ¡Sé el primero!</p>
                            </div>
                        </div>

                        <!-- Pagination (Simple implementation) -->
                        <div v-if="comments.data.length > 0" class="flex justify-center mt-6 gap-2">
                            <template v-for="(link, key) in comments.links" :key="key">
                                <div v-if="link.url === null" class="px-4 py-2 text-sm text-gray-400 border rounded-lg" v-html="link.label" />
                                <Link v-else :href="link.url" 
                                    class="px-4 py-2 text-sm border rounded-lg transition-colors"
                                    :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                    v-html="link.label" 
                                />
                            </template>
                        </div>
                    </div>

                    <!-- Sidebar / Form Section -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-lg border border-indigo-50 p-6 sticky top-8">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="bg-yellow-100 p-1.5 rounded-lg text-yellow-600">
                                    <Send class="h-5 w-5" />
                                </span>
                                Deja tu opinión
                            </h3>
                            
                            <form @submit.prevent="submitComment" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Calificación</label>
                                    <div class="flex gap-2">
                                        <button type="button" v-for="i in 5" :key="i" 
                                            @click="form.rating = i"
                                            class="p-1 rounded-full transition-transform hover:scale-110 focus:outline-none"
                                        >
                                            <Star class="h-8 w-8 transition-colors" 
                                                :class="i <= form.rating ? 'text-yellow-400 fill-current' : 'text-gray-200'" 
                                            />
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1 pl-1">
                                        {{ form.rating === 5 ? '¡Excelente!' : form.rating === 1 ? 'Mejorable' : 'Tu valoración nos ayuda' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tu Comentario</label>
                                    <textarea 
                                        v-model="form.content"
                                        rows="4"
                                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none transition-all"
                                        placeholder="¿Qué te parece la plataforma? ¿Qué mejorarías?"
                                        required
                                    ></textarea>
                                    <p class="text-xs text-red-500 mt-1" v-if="form.errors.content">{{ form.errors.content }}</p>
                                </div>

                                <button type="submit" 
                                    :disabled="form.processing"
                                    class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="form.processing">Enviando...</span>
                                    <span v-else class="flex items-center gap-2">
                                        Publicar Comentario <Send class="h-4 w-4" />
                                    </span>
                                </button>
                            </form>

                            <div class="mt-8 pt-6 border-t border-gray-100">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">¿Por qué comentar?</h4>
                                <ul class="space-y-3 text-sm text-gray-600">
                                    <li class="flex items-start gap-2">
                                        <ThumbsUp class="h-4 w-4 text-indigo-500 mt-0.5" />
                                        Ayudas a otros usuarios a decidir.
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <Star class="h-4 w-4 text-indigo-500 mt-0.5" />
                                        Tus ideas pueden convertirse en funciones.
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <Medal class="h-4 w-4 text-indigo-500 mt-0.5" />
                                        Posibilidad de aparecer destacado en la portada.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
.mobile-hidden {
    @media (max-width: 768px) {
        display: none;
    }
}
</style>
