<script setup lang="ts">
/**
 * Comunidad - Diseño Minimalista y Profesional
 * Filosofía: Claridad, simplicidad, enfoque en el contenido
 */
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, usePage, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { MessageSquare, Star, Plus, Eye } from 'lucide-vue-next';
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
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

// Types
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
    ratings_count?: number;
    ratings_avg_rating?: number | null;
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

const getThemeColor = (portfolio: Portfolio) => {
    return portfolio?.theme_settings?.primary_color || '#6366f1';
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

        <div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6 lg:px-8">
            <!-- Toast -->
            <transition 
                enter-active-class="transition ease-out duration-300" 
                enter-from-class="opacity-0 translate-y-2" 
                enter-to-class="opacity-100 translate-y-0" 
                leave-active-class="transition ease-in duration-200" 
                leave-from-class="opacity-100" 
                leave-to-class="opacity-0"
            >
                <div v-if="showSuccess" class="fixed top-20 right-4 z-50 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-2 text-sm font-medium">
                    <MessageSquare class="h-4 w-4" />
                    {{ successMessage }}
                </div>
            </transition>

            <div class="max-w-6xl mx-auto space-y-8">
                
                <!-- Header - Clean & Minimal -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Comunidad</h1>
                        <p class="text-slate-500 text-sm mt-1">Descubre portafolios y conecta con creadores</p>
                    </div>
                    
                    <button 
                        @click="isCreateModalOpen = true"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition-all hover:bg-indigo-700"
                    >
                        <Plus class="h-4 w-4" />
                        Publicar
                    </button>
                </div>

                <!-- Posts Grid - Minimalist Cards -->
                <div v-if="posts.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <Link 
                        v-for="post in posts.data" 
                        :key="post.id" 
                        :href="route('community.show', post.id)"
                        class="group bg-white rounded-xl border border-slate-200 overflow-hidden transition-all duration-200 hover:shadow-md hover:border-slate-300 hover:-translate-y-0.5"
                    >
                        <!-- Preview -->
                        <div 
                            class="h-32 relative overflow-hidden"
                            :style="{ backgroundColor: getThemeColor(post.portfolio) + '15' }"
                        >
                            <!-- Simple gradient overlay -->
                            <div 
                                class="absolute inset-0 opacity-30"
                                :style="{ 
                                    background: `linear-gradient(135deg, ${getThemeColor(post.portfolio)} 0%, transparent 100%)`
                                }"
                            ></div>
                            
                            <!-- Portfolio initial -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span 
                                    class="text-3xl font-bold opacity-20"
                                    :style="{ color: getThemeColor(post.portfolio) }"
                                >
                                    {{ post.portfolio?.title?.substring(0,2).toUpperCase() ?? 'NA' }}
                                </span>
                            </div>

                            <!-- Views badge -->
                            <div class="absolute top-3 right-3 flex items-center gap-1 bg-white/90 backdrop-blur-sm rounded-full px-2 py-1 text-xs text-slate-600">
                                <Eye class="h-3 w-3" />
                                {{ post.views_count }}
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <h3 class="font-semibold text-slate-900 text-sm leading-tight mb-1 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                                {{ post.title }}
                            </h3>
                            
                            <p class="text-slate-500 text-xs mb-3 line-clamp-2">
                                {{ post.content }}
                            </p>

                            <!-- Footer -->
                            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                <div class="flex items-center gap-2">
                                    <div 
                                        class="h-5 w-5 rounded-full flex items-center justify-center text-white text-[9px] font-semibold"
                                        :style="{ backgroundColor: getThemeColor(post.portfolio) }"
                                    >
                                        {{ post.user.first_name.charAt(0) }}
                                    </div>
                                    <span class="text-xs text-slate-500 truncate max-w-[60px]">
                                        {{ post.user.first_name }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-400">
                                    <span class="flex items-center gap-0.5">
                                        <Star class="h-3 w-3 text-amber-400 fill-amber-400" />
                                        {{ Number(post.ratings_avg_rating ?? 0).toFixed(1) }}
                                    </span>
                                    <span class="flex items-center gap-0.5">
                                        <MessageSquare class="h-3 w-3" />
                                        {{ post.comments_count }}
                                    </span>
                                    <span>{{ formatDate(post.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-16">
                    <div class="h-16 w-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                        <MessageSquare class="h-8 w-8 text-slate-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-1">Sin publicaciones</h3>
                    <p class="text-slate-500 text-sm max-w-sm mx-auto mb-6">
                        Sé el primero en compartir tu portafolio con la comunidad.
                    </p>
                    <button 
                        @click="isCreateModalOpen = true"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-medium text-slate-700 transition-all hover:bg-slate-200"
                    >
                        <Plus class="h-4 w-4" />
                        Publicar ahora
                    </button>
                </div>

                <!-- Pagination -->
                <div v-if="posts.data.length > 0" class="flex justify-center gap-1.5">
                    <template v-for="(link, key) in posts.links" :key="key">
                        <span 
                            v-if="link.url === null" 
                            class="px-3 py-1.5 text-xs text-slate-400 rounded-lg" 
                            v-html="link.label" 
                        />
                        <Link 
                            v-else 
                            :href="link.url" 
                            class="px-3 py-1.5 text-xs rounded-lg transition-colors"
                            :class="link.active 
                                ? 'bg-indigo-600 text-white' 
                                : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'"
                            v-html="link.label" 
                        />
                    </template>
                </div>

            </div>
        </div>

        <!-- Create Post Modal -->
        <Dialog v-model:open="isCreateModalOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="text-lg font-semibold">Publicar Portafolio</DialogTitle>
                    <DialogDescription class="text-sm text-slate-500">
                        Comparte tu trabajo con la comunidad.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitPost" class="space-y-4 py-2">
                    <div class="space-y-3">
                        <div class="space-y-1.5">
                            <Label class="text-sm">Portafolio</Label>
                            <div v-if="eligiblePortfolios.length === 0" class="p-3 bg-amber-50 text-amber-700 rounded-lg text-xs">
                                No tienes portafolios disponibles para publicar.
                            </div>
                            <Select v-else v-model="form.portfolio_id">
                                <SelectTrigger class="h-9">
                                    <SelectValue placeholder="Seleccionar..." />
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

                        <div class="space-y-1.5">
                            <Label class="text-sm">Descripción</Label>
                            <Textarea 
                                v-model="form.content" 
                                placeholder="Cuéntanos sobre tu proyecto..." 
                                rows="3"
                                required
                            />
                            <p class="text-xs text-red-500" v-if="form.errors.content">{{ form.errors.content }}</p>
                        </div>
                    </div>

                    <DialogFooter class="gap-2">
                        <Button type="button" variant="ghost" size="sm" @click="isCreateModalOpen = false">
                            Cancelar
                        </Button>
                        <Button 
                            type="submit" 
                            size="sm"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white" 
                            :disabled="form.processing || eligiblePortfolios.length === 0"
                        >
                            {{ form.processing ? 'Publicando...' : 'Publicar' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

    </DashboardLayout>
</template>
