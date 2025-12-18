<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, computed, onUnmounted } from 'vue';
import { Star, MessageCircle, ArrowLeft, Eye, MessageSquare } from 'lucide-vue-next';

// Props desde el controlador
const props = defineProps<{
    posts: Array<{
        id: number;
        title: string;
        content: string;
        views_count: number;
        created_at: string;
        comments_count: number;
        user: {
            first_name: string;
            last_name: string;
            profile_photo_path?: string;
        };
        portfolio: {
            title: string;
            slug: string;
            theme_settings?: any;
        };
        comments?: Array<{
            id: number;
            content: string;
            created_at: string;
            user: {
                first_name: string;
                last_name: string;
                profile_photo_path?: string;
            };
        }>;
    }>;
    isAuthenticated: boolean;
}>();

// Mouse tracking para efecto parallax
const mouseX = ref(0);
const mouseY = ref(0);

const handleMouseMove = (e: MouseEvent) => {
    mouseX.value = (e.clientX / window.innerWidth - 0.5) * 20;
    mouseY.value = (e.clientY / window.innerHeight - 0.5) * 20;
};

// Computed para parallax
const parallaxStyle = computed(() => ({
    transform: `translate(${mouseX.value * 0.05}px, ${mouseY.value * 0.05}px)`,
}));

// Formateo de fecha
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long', 
        day: 'numeric'
    });
};

const withAlpha = (hex: string, alpha: number) => {
    if (!hex || !hex.startsWith('#') || (hex.length !== 7 && hex.length !== 4)) return hex;
    const normalized = hex.length === 4
        ? `#${hex[1]}${hex[1]}${hex[2]}${hex[2]}${hex[3]}${hex[3]}`
        : hex;
    const opacity = Math.round(alpha * 255).toString(16).padStart(2, '0');
    return `${normalized}${opacity}`;
};

const getPreviewStyle = (portfolio: { theme_settings?: any }) => {
    const base = portfolio?.theme_settings?.primary_color || '#4f46e5';
    return {
        background: `radial-gradient(circle at 20% 20%, ${withAlpha(base, 0.18)}, transparent 35%), 
                     radial-gradient(circle at 80% 0%, ${withAlpha(base, 0.12)}, transparent 25%),
                     linear-gradient(135deg, ${withAlpha(base, 0.16)} 0%, #eef2ff 100%)`,
    };
};

const getAccentStyle = (portfolio: { theme_settings?: any }) => {
    const base = portfolio?.theme_settings?.primary_color || '#4f46e5';
    return { backgroundColor: base };
};

// Modal para invitados
const showGuestModal = ref(false);
const pendingPostTitle = ref('');

const attemptComment = (postId: number, title: string) => {
    if (!props.isAuthenticated) {
        pendingPostTitle.value = title;
        showGuestModal.value = true;
        return;
    }

    router.visit(`/dashboard/comunidad/${postId}`);
};

onMounted(() => {
    window.addEventListener('mousemove', handleMouseMove);
});

onUnmounted(() => {
    window.removeEventListener('mousemove', handleMouseMove);
});
</script>

<template>
    <Head title="Comunidad" />
    
    <div class="min-h-screen bg-white font-sans selection:bg-[#005aeb] selection:text-white overflow-hidden relative">
        
        <!-- Botón Flotante Regresar (Standalone Navigation) -->
        <div class="fixed top-6 left-6 z-50">
            <a href="/" class="group flex items-center gap-2 rounded-full bg-white/80 backdrop-blur-md px-5 py-3 shadow-lg ring-1 ring-gray-900/5 transition-all duration-300 hover:bg-white hover:scale-105 hover:shadow-xl hover:ring-[#005aeb]/30">
                <ArrowLeft class="w-5 h-5 text-gray-600 group-hover:text-[#005aeb] transition-colors" />
                <span class="font-medium text-gray-700 group-hover:text-[#005aeb] transition-colors">Volver al Inicio</span>
            </a>
        </div>

        <!-- Hero Section (Standalone Context) -->
        <section class="relative min-h-[50vh] flex items-center justify-center overflow-hidden bg-gradient-to-br from-[#005aeb]/5 via-white to-[#7B2FF7]/5 pt-20 pb-10">
             <!-- Partículas orb con parallax -->
             <div :style="parallaxStyle" class="pointer-events-none absolute top-20 left-10 h-96 w-96 animate-float rounded-full bg-gradient-to-br from-[#005aeb]/30 to-[#7B2FF7]/20 blur-3xl opacity-50"></div>
             <div :style="parallaxStyle" class="pointer-events-none absolute bottom-20 right-10 h-96 w-96 animate-float-delayed rounded-full bg-gradient-to-br from-[#7B2FF7]/30 to-[#005aeb]/20 blur-3xl opacity-50"></div>

            <div class="container relative z-10 mx-auto px-4 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 border border-blue-100 shadow-sm mb-8 animate-fade-in-up">
                    <span class="flex h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-sm font-medium text-gray-600">Comunidad Oficial</span>
                </div>

                <h1 class="mb-6 text-5xl md:text-7xl font-extrabold leading-tight text-gray-900">
                    Voces de la <br />
                    <span class="bg-gradient-to-r from-[#005aeb] via-[#7B2FF7] to-[#005aeb] bg-clip-text text-transparent animate-gradient-x">Comunidad</span>
                </h1>
                
                <p class="mx-auto mb-10 max-w-2xl text-xl leading-relaxed text-gray-600">
                    Un espacio dedicado a quienes están construyendo el futuro con PortafolioAI.
                </p>

                <!-- Mini Stats (Minimalist) -->
                <div class="flex flex-wrap justify-center gap-8 text-sm font-medium text-gray-500">
                    <div class="flex items-center gap-2">
                        <Star class="w-4 h-4 text-yellow-400 fill-current" />
                        <span>4.9 Valoración</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <MessageCircle class="w-4 h-4 text-[#005aeb]" />
                        <span>{{ props.posts.length }}+ Historias</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Posts Grid + Hilos -->
        <section class="py-20 bg-white relative">
             <div class="absolute inset-0 bg-gradient-to-b from-transparent via-blue-50/20 to-transparent pointer-events-none"></div>

            <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <p class="text-sm uppercase font-semibold tracking-wide text-blue-600">Vitrina de portafolios</p>
                        <h2 class="text-3xl font-bold text-gray-900">Explora proyectos reales y conversa con sus autores</h2>
                        <p class="text-gray-500 mt-2">Solo mostramos portafolios publicados y conversaciones reales de usuarios.</p>
                    </div>
                    <div class="hidden sm:flex items-center gap-3 text-sm text-gray-500">
                        <div class="flex items-center gap-1 px-3 py-1 rounded-full bg-white shadow-sm ring-1 ring-gray-100">
                            <Eye class="w-4 h-4 text-indigo-500" /> <span>Visitas activas</span>
                        </div>
                        <div class="flex items-center gap-1 px-3 py-1 rounded-full bg-white shadow-sm ring-1 ring-gray-100">
                            <MessageSquare class="w-4 h-4 text-indigo-500" /> <span>Hilos públicos</span>
                        </div>
                    </div>
                </div>

                <div v-if="props.posts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div 
                        v-for="post in props.posts" 
                        :key="post.id"
                        class="group relative flex flex-col rounded-2xl bg-white border border-gray-100 shadow-sm ring-1 ring-transparent hover:ring-indigo-100 hover:shadow-xl transition-all duration-300 overflow-hidden"
                    >
                        <div class="h-44 w-full relative overflow-hidden p-4" :style="getPreviewStyle(post.portfolio)">
                            <div class="absolute inset-0 bg-white/30 blur-3xl"></div>
                            <div class="relative z-10 h-full flex flex-col justify-between space-y-3">
                                <div class="flex justify-between text-[11px] font-semibold text-indigo-900/70">
                                    <span class="bg-white/70 backdrop-blur px-3 py-1 rounded-full border border-white/60 shadow-sm">Portafolio público</span>
                                    <span class="bg-white/80 backdrop-blur px-2 py-1 rounded-full flex items-center gap-1 text-indigo-700 shadow-sm">
                                        <Eye class="h-3 w-3" /> {{ post.views_count }}
                                    </span>
                                </div>
                                <div class="flex-1 flex items-end gap-2">
                                    <div class="flex-1 space-y-2">
                                        <div class="h-5 w-28 rounded-full bg-white/80 border border-white/60 shadow-sm" :style="getAccentStyle(post.portfolio)"></div>
                                        <div class="grid grid-cols-3 gap-2">
                                            <div class="col-span-2 h-10 rounded-xl bg-white/80 border border-white/70 shadow-sm"></div>
                                            <div class="h-10 rounded-xl bg-white/60 border border-white/60"></div>
                                            <div class="h-8 rounded-lg bg-white/75 border border-white/60"></div>
                                            <div class="col-span-2 h-8 rounded-lg bg-white/70 border border-white/60"></div>
                                        </div>
                                    </div>
                                    <div class="h-14 w-14 rounded-xl bg-white/80 border border-white/70 shadow-inner flex items-center justify-center text-indigo-900 font-bold">
                                        {{ post.portfolio.title.substring(0,2).toUpperCase() }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 flex-1 flex flex-col space-y-3">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <div class="h-6 w-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-[10px] font-bold">
                                    {{ post.user.first_name.charAt(0) }}
                                </div>
                                <span class="font-semibold text-gray-700">{{ post.user.first_name }}</span>
                                <span class="text-gray-300">•</span>
                                <span>{{ formatDate(post.created_at) }}</span>
                            </div>

                            <h3 class="font-bold text-lg text-gray-900 leading-tight line-clamp-2 group-hover:text-indigo-600 transition-colors">
                                {{ post.title }}
                            </h3>
                            <p class="text-gray-500 text-sm line-clamp-3">
                                {{ post.content }}
                            </p>

                            <div class="space-y-3">
                                <div v-if="post.comments && post.comments.length" class="space-y-3">
                                    <div v-for="comment in post.comments" :key="comment.id" class="rounded-xl bg-gray-50 px-3 py-2 border border-gray-100">
                                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                                            <div class="h-5 w-5 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px] font-bold">
                                                {{ comment.user.first_name.charAt(0) }}
                                            </div>
                                            <span class="font-semibold text-gray-700">{{ comment.user.first_name }}</span>
                                            <span class="text-gray-300">•</span>
                                            <span>{{ formatDate(comment.created_at) }}</span>
                                        </div>
                                        <p class="text-gray-600 text-sm line-clamp-2">{{ comment.content }}</p>
                                    </div>
                                </div>
                                <div v-else class="rounded-xl bg-gray-50 px-3 py-3 border border-dashed border-gray-200 text-sm text-gray-500">
                                    Sé el primero en iniciar la conversación.
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-3 mt-auto border-t border-gray-100">
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <MessageSquare class="h-4 w-4 text-indigo-500" />
                                    <span>{{ post.comments_count }} comentarios</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a :href="`/p/${post.portfolio.slug}`" class="text-xs font-semibold text-indigo-700 hover:text-indigo-900 transition-colors">Ver portafolio</a>
                                    <button 
                                        type="button" 
                                        class="text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-2 rounded-lg shadow-sm transition-colors"
                                        @click.stop="attemptComment(post.id, post.title)"
                                    >
                                        Comentar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="bg-gray-50 p-6 rounded-full mb-4">
                        <MessageCircle class="w-10 h-10 text-gray-400" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aún no hay publicaciones</h3>
                    <p class="text-gray-500 mb-6">Comparte tu primer portafolio público y abre el hilo de conversación.</p>
                    <a href="/register" class="text-[#005aeb] font-medium hover:underline">Unirse a la comunidad &rarr;</a>
                </div>
            </div>
        </section>

        <!-- Simple Footer -->
        <footer class="py-10 bg-white border-t border-gray-100 text-center">
             <p class="text-gray-400 text-sm">
                &copy; {{ new Date().getFullYear() }} PortafolioAI. Todos los derechos reservados.
            </p>
        </footer>

        <!-- Modal Invitados -->
        <div v-if="showGuestModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative">
                <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600" @click="showGuestModal = false">✕</button>
                <div class="space-y-3 text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-lg font-bold">💬</div>
                    <h3 class="text-xl font-bold text-gray-900">La comunidad se disfruta más registrada</h3>
                    <p class="text-gray-600 text-sm">
                        Únete para comentar en “{{ pendingPostTitle }}”, guardar tus portafolios y seguir las conversaciones.
                    </p>
                    <div class="flex flex-col gap-2 pt-2">
                        <a href="/register" class="w-full rounded-lg bg-indigo-600 text-white font-semibold py-2.5 hover:bg-indigo-700 transition-colors">Crear cuenta gratis</a>
                        <a href="/login" class="w-full rounded-lg border border-gray-200 text-gray-700 font-semibold py-2.5 hover:border-indigo-200 hover:text-indigo-700 transition-colors">Ya tengo cuenta</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}
.animate-float { animation: float 6s ease-in-out infinite; }

@keyframes float-delayed {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}
.animate-float-delayed { animation: float-delayed 8s ease-in-out infinite 2s; }

@keyframes gradient-x {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
.animate-gradient-x {
    background-size: 200% 200%;
    animation: gradient-x 15s ease infinite;
}

.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
