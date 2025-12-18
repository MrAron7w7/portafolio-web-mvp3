<script setup lang="ts">
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, usePage, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { MessageSquare, Star, Send, Plus, Search, Eye, ExternalLink } from 'lucide-vue-next';
import { route } from '@/utils/route';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

// Tipos definidos localmente para este componente
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
    theme_settings?: any;
}

interface Post {
    id: number;
    title: string;
    content: string;
    views_count: number;
    created_at: string;
    comments_count: number;
    user: User;
    portfolio: Portfolio;
}

const props = defineProps<{
    posts: {
        data: Array<Post>;
        links: Array<any>;
    };
    eligiblePortfolios: Array<{ id: number; title: string }>;
}>();

const isCreateModalOpen = ref(false);

const form = useForm({
    portfolio_id: '',
    title: '',
    content: '',
});

const submitPost = () => {
    form.post('/dashboard/comunidad', {
        onSuccess: () => {
            isCreateModalOpen.value = false;
            form.reset();
        }
    });
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        month: 'short',
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

const getPreviewStyle = (portfolio: Portfolio) => {
    const base = portfolio?.theme_settings?.primary_color || '#4f46e5';
    return {
        background: `radial-gradient(circle at 20% 20%, ${withAlpha(base, 0.18)}, transparent 35%), 
                     radial-gradient(circle at 80% 0%, ${withAlpha(base, 0.12)}, transparent 25%),
                     linear-gradient(135deg, ${withAlpha(base, 0.16)} 0%, #eef2ff 100%)`,
    };
};

const getAccentStyle = (portfolio: Portfolio) => {
    const base = portfolio?.theme_settings?.primary_color || '#4f46e5';
    return { backgroundColor: base };
};

// Toast Logic
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

        <div class="min-h-screen bg-gray-50/50 py-8 px-4 sm:px-6 lg:px-8">
            <!-- Toast -->
            <transition enter-active-class="transition ease-out duration-300" enter-from-class="transform opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showSuccess" class="fixed top-24 right-4 z-50 bg-green-600 text-white px-6 py-4 rounded-xl shadow-xl flex items-center gap-3">
                    <MessageSquare class="h-6 w-6" />
                    <span class="font-medium">{{ successMessage }}</span>
                </div>
            </transition>

            <div class="max-w-7xl mx-auto space-y-8">
                
                <!-- Hero Header -->
                <div class="relative overflow-hidden rounded-3xl bg-indigo-900 px-8 py-12 shadow-2xl">
                    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100"></div>
                    <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-indigo-500 blur-3xl opacity-30"></div>
                    <div class="absolute bottom-0 left-0 h-64 w-64 rounded-full bg-purple-500 blur-3xl opacity-20"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                        <div class="space-y-4 max-w-2xl text-center md:text-left">
                            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight leading-tight">
                                Descubre lo que otros <br>
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-300 via-purple-300 to-indigo-300">están construyendo</span>
                            </h1>
                            <p class="text-indigo-200 text-lg">
                                Un espacio curado para compartir tus portafolios públicos, recibir feedback real y conectar con creadores.
                            </p>
                        </div>
                        
                        <div>
                            <Button 
                                @click="isCreateModalOpen = true"
                                size="lg" 
                                class="bg-white text-indigo-900 hover:bg-gray-100 font-bold border-0 shadow-[0_0_20px_rgba(255,255,255,0.3)] transition-all hover:scale-105"
                            >
                                <Plus class="mr-2 h-5 w-5" /> Publicar Proyecto
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Posts Grid -->
                <div v-if="posts.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <Link 
                        v-for="post in posts.data" 
                        :key="post.id" 
                        :href="route('community.show', post.id)"
                        class="group bg-white rounded-2xl overflow-hidden border border-gray-100 hover:border-indigo-100 hover:shadow-xl transition-all duration-300 flex flex-col h-full ring-offset-2 focus:ring-2 ring-indigo-500 outline-none"
                    >
                        <!-- Thumbnail / Preview -->
                        <div class="h-48 w-full relative overflow-hidden group-hover:opacity-95 transition-opacity p-4" :style="getPreviewStyle(post.portfolio)">
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

                        <div class="p-5 flex-1 flex flex-col">
                            <h3 class="font-bold text-lg text-gray-900 leading-tight mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                                {{ post.title }}
                            </h3>
                            
                            <p class="text-gray-500 text-sm mb-4 line-clamp-3 flex-1">
                                {{ post.content }}
                            </p>

                            <!-- Footer -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-50 mt-auto">
                                <div class="flex items-center gap-2">
                                    <div class="h-6 w-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-[10px] font-bold">
                                        {{ post.user.first_name.charAt(0) }}
                                    </div>
                                    <span class="text-xs text-gray-600 font-medium truncate max-w-[80px]">
                                        {{ post.user.first_name }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <MessageSquare class="h-3 w-3" /> {{ post.comments_count }}
                                    </span>
                                    <span>{{ formatDate(post.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-20">
                    <div class="bg-indigo-50 h-20 w-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <Search class="h-10 w-10 text-indigo-400" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aún no hay publicaciones</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-8">
                        Sé el primero en compartir tu portafolio con la comunidad y recibe feedback temprano.
                    </p>
                    <Button 
                        @click="isCreateModalOpen = true"
                        variant="outline" 
                        class="border-indigo-200 text-indigo-700 hover:bg-indigo-50"
                    >
                        Publicar ahora
                    </Button>
                </div>

                <!-- Pagination (Simple) -->
                 <div v-if="posts.data.length > 0" class="flex justify-center mt-12 gap-2">
                    <template v-for="(link, key) in posts.links" :key="key">
                        <div v-if="link.url === null" class="px-4 py-2 text-sm text-gray-400 border rounded-lg" v-html="link.label" />
                        <Link v-else :href="link.url" 
                            class="px-4 py-2 text-sm border rounded-lg transition-colors"
                            :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50'"
                            v-html="link.label" 
                        />
                    </template>
                </div>

            </div>
        </div>

        <!-- Create Post Modal -->
        <Dialog v-model:open="isCreateModalOpen">
            <DialogContent class="sm:max-w-xl">
                <DialogHeader>
                    <DialogTitle class="text-2xl font-bold flex items-center gap-2">
                        <Send class="h-5 w-5 text-indigo-600" /> Publicar Portafolio
                    </DialogTitle>
                    <DialogDescription>
                        Comparte tu trabajo para que otros puedan verlo y comentar.
                        Solo puedes elegir portafolios que estén marcados como <strong>públicos</strong>.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitPost" class="space-y-6 py-4">
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Selecciona un Portafolio</Label>
                            <div v-if="eligiblePortfolios.length === 0" class="p-4 bg-yellow-50 text-yellow-800 rounded-lg text-sm border border-yellow-200">
                                No tienes portafolios públicos disponibles para publicar. 
                                <br>
                                <span class="text-xs opacity-75">Asegúrate de que tu portafolio esté en estado <strong>Público</strong> y no haya sido publicado antes.</span>
                            </div>
                            <Select v-else v-model="form.portfolio_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Elige un proyecto..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem 
                                        v-for="portfolio in eligiblePortfolios" 
                                        :key="portfolio.id" 
                                        :value="String(portfolio.id)"
                                    >
                                        {{ portfolio.title }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p class="text-xs text-red-500" v-if="form.errors.portfolio_id">{{ form.errors.portfolio_id }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label>Título del Post</Label>
                            <Input 
                                v-model="form.title" 
                                placeholder="Eje: Mi primer portafolio como diseñador UX" 
                                required
                            />
                            <p class="text-xs text-red-500" v-if="form.errors.title">{{ form.errors.title }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label>Cuéntanos tu experiencia</Label>
                            <Textarea 
                                v-model="form.content" 
                                placeholder="¿Qué aprendiste? ¿Qué tecnologías usaste? ¿Qué feedback buscas?" 
                                rows="5"
                                required
                            />
                            <p class="text-xs text-red-500" v-if="form.errors.content">{{ form.errors.content }}</p>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="ghost" @click="isCreateModalOpen = false">Cancelar</Button>
                        <Button 
                            type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white" 
                            :disabled="form.processing || eligiblePortfolios.length === 0"
                        >
                            <span v-if="form.processing">Publicando...</span>
                            <span v-else>Publicar Ahora</span>
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

    </DashboardLayout>
</template>
