<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { 
    LayoutTemplate, 
    Type, 
    Image as ImageIcon, 
    Save, 
    AlertCircle, 
    Plus, 
    Trash2, 
    Cpu, 
    Zap, 
    MessageSquare,
    ChevronDown,
    ChevronUp,
    Store,
    X,
    Box,
    Users,
    Star,
    Info,
    Lock
} from 'lucide-vue-next';

// Declare route generic to avoid type errors if not strictly typed
declare const route: any;

const props = defineProps<{
    sections: any;
}>();

// Helper for image previews
const getObjectUrl = (path: string | File | null) => {
    if (!path) return null;
    if (path instanceof File) return URL.createObjectURL(path);
    if (typeof path === 'string') return path; // Already a URL
    return null;
};

// Toggle accordion state
const collapsed = ref<Record<string, boolean>>({
    auth: true,
    header: false,
    hero: true,
    tools: true,
    features: true,
    cta: true,
    footer: true,
    testimonials: true,
    about: true
});

const toggleSection = (key: string) => {
    collapsed.value[key] = !collapsed.value[key];
};

// Initialize Forms for each section
const forms = {
    auth: useForm({
        content: {
            welcome_title: props.sections?.auth?.content?.welcome_title || 'Bienvenido',
            welcome_subtitle: props.sections?.auth?.content?.welcome_subtitle || '',
            register_title: props.sections?.auth?.content?.register_title || 'Crea tu cuenta',
            register_subtitle: props.sections?.auth?.content?.register_subtitle || '',
        },
        images: { ...props.sections?.auth?.images },
    }),
    header: useForm({
        content: {
            brand_name: props.sections?.header?.content?.brand_name || 'PortafolioAI',
            logo_text: props.sections?.header?.content?.logo_text || '',
        },
        images: { ...props.sections?.header?.images },
    }),
    hero: useForm({
        content: {
            badge: props.sections?.hero?.content?.badge || '',
            cv_score_value: props.sections?.hero?.content?.cv_score_value || '',
            title_line1: props.sections?.hero?.content?.title_line1 || '',
            title_gradient: props.sections?.hero?.content?.title_gradient || '',
            description: props.sections?.hero?.content?.description || '',
            social_proof_text: props.sections?.hero?.content?.social_proof_text || '',
        },
        images: {
            main_image: props.sections?.hero?.images?.main_image || null,
        }
    }),
    tools: useForm({
        content: {
            header_subtitle: props.sections?.tools?.content?.header_subtitle || '',
            header_title: props.sections?.tools?.content?.header_title || '',
            items: Array.isArray(props.sections?.tools?.content?.items) ? props.sections?.tools?.content?.items : []
        },
        images: { ...props.sections?.tools?.images },
    }),
    features: useForm({
        content: {
            // Ensure items is an array
            items: Array.isArray(props.sections?.features?.content?.items) ? props.sections?.features?.content?.items : []
        },
        images: { ...props.sections?.features?.images },
    }),
    cta: useForm({
        content: {
            title: props.sections?.cta?.content?.title || '',
            description: props.sections?.cta?.content?.description || '',
        },
        images: { ...props.sections?.cta?.images },
    }),
    footer: useForm({
        content: {
            brand_name: props.sections?.footer?.content?.brand_name || 'PortafolioAI',
            brand_description: props.sections?.footer?.content?.brand_description || '',
            copyright_text: props.sections?.footer?.content?.copyright_text || '',
        },
        images: { ...props.sections?.footer?.images },
    }),
    testimonials: useForm({
        content: {
            title: props.sections?.testimonials?.content?.title || 'Lo que dice la comunidad',
            subtitle: props.sections?.testimonials?.content?.subtitle || 'Opiniones verificadas de profesionales destacados',
            items: Array.isArray(props.sections?.testimonials?.content?.items) ? props.sections?.testimonials?.content?.items : []
        },
        images: { ...props.sections?.testimonials?.images },
    }),
    about: useForm({
        content: {
            title: props.sections?.about?.content?.title || 'Sobre Nosotros',
            who_we_are: props.sections?.about?.content?.who_we_are || '',
            mission_title: props.sections?.about?.content?.mission_title || 'Nuestra Misión',
            mission: props.sections?.about?.content?.mission || '',
            vision_title: props.sections?.about?.content?.vision_title || 'Nuestra Visión',
            vision: props.sections?.about?.content?.vision || '',
        },
        images: { ...props.sections?.about?.images },
    }),
};

// Handle Image Upload Generic
const handleImageUpload = (event: Event, sectionKey: string, imageKey: string) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
        // @ts-ignore
        forms[sectionKey].images[imageKey] = file;
    }
};

// Submit Generic
const submitSection = (sectionKey: string) => {
    // @ts-ignore
    const form = forms[sectionKey as keyof typeof forms];
    
    // Validate tool items before submitting
    if (sectionKey === 'tools') {
         // @ts-ignore
         if(!Array.isArray(form.content.items)) {
             // @ts-ignore
             form.content.items = [];
         }
    }
    
    if (sectionKey === 'testimonials') {
         // @ts-ignore
         if(!Array.isArray(form.content.items)) {
             // @ts-ignore
             form.content.items = [];
         }
    }
    
    form.post(route('admin.landing.update', { key: sectionKey }), {
        preserveScroll: true,
        onSuccess: () => {
             // Optional toast or feedback
        },
    });
};

</script>

<template>
    <Head title="Editor de Landing Page" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            
            <!-- Header -->
            <div class="mb-10">
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Editor de Página de Inicio
                </h1>
                <p class="mt-2 text-lg text-slate-600 dark:text-slate-400">
                    Gestiona los textos, imágenes y contenidos principales de tu landing page.
                </p>
                <div class="mt-4 p-4 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-800 dark:text-indigo-300 rounded-xl text-sm border border-indigo-100 dark:border-indigo-800/50 flex items-start gap-3">
                    <AlertCircle class="w-5 h-5 shrink-0 mt-0.5" />
                    <p>Los botones y enlaces de navegación son gestionados automáticamente por el sistema.</p>
                </div>
            </div>

            <div class="space-y-8">
            
                <!-- AUTH SECTION -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" @click="toggleSection('auth')">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-xl bg-violet-500/10 text-violet-500">
                                <Lock class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Login y Sign Up</h2>
                                <p class="text-sm text-slate-500">Personaliza pantallas de acceso</p>
                            </div>
                        </div>
                        <component :is="collapsed.auth ? ChevronDown : ChevronUp" class="w-5 h-5 text-slate-400" />
                    </div>
                    
                    <div v-show="!collapsed.auth" class="p-8 bg-slate-50/50 dark:bg-slate-950/30">
                        <form @submit.prevent="submitSection('auth')" class="space-y-6">
                            <div class="space-y-4">
                                <div class="space-y-2">
                                     <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Imagen de Fondo (Derecha)</label>
                                     <div class="rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6 flex flex-col items-center justify-center gap-4 group hover:border-indigo-500/50 transition-colors cursor-pointer relative overflow-hidden h-64">
                                         <input type="file" @change="(e) => handleImageUpload(e, 'auth', 'background')" class="absolute inset-0 opacity-0 cursor-pointer z-10" accept="image/*">
                                         
                                         <div v-if="forms.auth.images.background" class="w-full h-full rounded-lg overflow-hidden relative">
                                             <img :src="getObjectUrl(forms.auth.images.background) || ''" class="w-full h-full object-cover">
                                             <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                 <p class="text-white font-medium text-sm">Cambiar Imagen</p>
                                             </div>
                                         </div>
                                         <div v-else class="text-center p-8">
                                             <div class="w-16 h-16 rounded-full bg-indigo-50 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4 text-indigo-500">
                                                 <ImageIcon class="w-8 h-8" />
                                             </div>
                                             <p class="text-sm text-slate-500 font-medium">Click para subir una imagen</p>
                                             <p class="text-xs text-slate-400 mt-1">Recomendado: 1080x1920 (Vertical)</p>
                                         </div>
                                     </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-4 p-4 rounded-xl bg-indigo-50/50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-800/30">
                                         <h3 class="text-xs font-bold uppercase text-indigo-500">Pantalla Login</h3>
                                         <div class="space-y-2">
                                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Título Bienvenida</label>
                                            <input v-model="forms.auth.content.welcome_title" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Subtítulo</label>
                                            <input v-model="forms.auth.content.welcome_subtitle" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-4 p-4 rounded-xl bg-pink-50/50 dark:bg-pink-900/10 border border-pink-100 dark:border-pink-800/30">
                                         <h3 class="text-xs font-bold uppercase text-pink-500">Pantalla Sign Up (Registro)</h3>
                                         <div class="space-y-2">
                                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Título Registro</label>
                                            <input v-model="forms.auth.content.register_title" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Subtítulo</label>
                                            <input v-model="forms.auth.content.register_subtitle" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
                                <button type="submit" :disabled="forms.auth.processing" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5 disabled:opacity-50">
                                    <Save class="w-5 h-5" />
                                    <span>Guardar Auth</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- HEADER (LOGO) SECTION -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" @click="toggleSection('header')">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-xl bg-blue-500/10 text-blue-500">
                                <Store class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Encabezado (Logo)</h2>
                                <p class="text-sm text-slate-500">Nombre de la marca visible en la barra superior</p>
                            </div>
                        </div>
                        <component :is="collapsed.header ? ChevronDown : ChevronUp" class="w-5 h-5 text-slate-400" />
                    </div>
                    
                    <div v-show="!collapsed.header" class="p-8 bg-slate-50/50 dark:bg-slate-950/30">
                        <form @submit.prevent="submitSection('header')" class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Nombre de la Marca (Logo Texto)</label>
                                <input v-model="forms.header.content.brand_name" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Imagen del Icono (Opcional)</label>
                                <div class="flex items-center gap-6">
                                    <div class="h-16 w-16 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden relative group cursor-pointer hover:border-indigo-500 transition-colors">
                                        <input type="file" @change="(e) => handleImageUpload(e, 'header', 'logo')" class="absolute inset-0 opacity-0 z-20 cursor-pointer">
                                        <img v-if="forms.header.images.logo" :src="getObjectUrl(forms.header.images.logo) || ''" class="w-full h-full object-cover">
                                        <div v-else class="text-slate-400">
                                            <ImageIcon class="w-6 h-6" />
                                        </div>
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        <p>Sube una imagen para reemplazar el icono por defecto.</p>
                                        <p>Recomendado: PNG o SVG cuadrado (128x128).</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
                                <button type="submit" :disabled="forms.header.processing" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5 disabled:opacity-50">
                                    <Save class="w-5 h-5" />
                                    <span>Guardar Encabezado</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- HERO SECTION -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" @click="toggleSection('hero')">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-xl bg-indigo-500/10 text-indigo-500">
                                <LayoutTemplate class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Hero Section</h2>
                                <p class="text-sm text-slate-500">Pantalla principal y bienvenida</p>
                            </div>
                        </div>
                        <component :is="collapsed.hero ? ChevronDown : ChevronUp" class="w-5 h-5 text-slate-400" />
                    </div>
                    
                    <div v-show="!collapsed.hero" class="p-8 bg-slate-50/50 dark:bg-slate-950/30">
                        <form @submit.prevent="submitSection('hero')" class="space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-6">
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Badge Superior</label>
                                        <input v-model="forms.hero.content.badge" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Score CV (Valor)</label>
                                        <input v-model="forms.hero.content.cv_score_value" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Título Principal</label>
                                        <input v-model="forms.hero.content.title_line1" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-bold text-lg">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Texto con Gradiente</label>
                                        <input v-model="forms.hero.content.title_gradient" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-bold text-lg">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Descripción</label>
                                        <textarea v-model="forms.hero.content.description" rows="4" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all leading-relaxed"></textarea>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Social Proof Text</label>
                                        <input v-model="forms.hero.content.social_proof_text" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Imagen Principal</label>
                                    <div class="rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6 flex flex-col items-center justify-center gap-4 group hover:border-indigo-500/50 transition-colors cursor-pointer relative overflow-hidden h-64">
                                        <input type="file" @change="(e) => handleImageUpload(e, 'hero', 'main_image')" class="absolute inset-0 opacity-0 cursor-pointer z-10" accept="image/*">
                                        
                                        <div v-if="forms.hero.images.main_image" class="w-full h-full rounded-lg overflow-hidden relative">
                                            <img :src="getObjectUrl(forms.hero.images.main_image) || ''" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <p class="text-white font-medium text-sm">Cambiar Imagen</p>
                                            </div>
                                        </div>
                                        <div v-else class="text-center p-8">
                                            <div class="w-16 h-16 rounded-full bg-indigo-50 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4 text-indigo-500">
                                                <ImageIcon class="w-8 h-8" />
                                            </div>
                                            <p class="text-sm text-slate-500 font-medium">Click para subir una imagen</p>
                                            <p class="text-xs text-slate-400 mt-1">PNG, JPG o WebP (Max 2MB)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
                                <button type="submit" :disabled="forms.hero.processing" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5 disabled:opacity-50 diabled:cursor-not-allowed">
                                    <Save class="w-5 h-5" />
                                    <span>Guardar Hero Section</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TOOLS SECTION (Portal AI Cards) -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" @click="toggleSection('tools')">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-xl bg-orange-500/10 text-orange-500">
                                <Cpu class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Herramientas (Portal AI)</h2>
                                <p class="text-sm text-slate-500">Tarjetas de herramientas flotantes (Hero Footer)</p>
                            </div>
                        </div>
                        <component :is="collapsed.tools ? ChevronDown : ChevronUp" class="w-5 h-5 text-slate-400" />
                    </div>

                    <div v-show="!collapsed.tools" class="p-8 bg-slate-50/50 dark:bg-slate-950/30">
                        <form @submit.prevent="submitSection('tools')" class="space-y-8">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Items (Tarjetas)</label>
                                    <button type="button" @click="forms.tools.content.items.push({ title: 'Nueva Herramienta', description: '', icon: 'Layout', color: 'from-blue-500 to-indigo-600', delay: '0' })" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 flex items-center gap-2 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg transition-colors">
                                        <Plus class="w-4 h-4" /> Agregar
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-6">
                                    <div v-for="(item, index) in forms.tools.content.items" :key="index" class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative group">
                                        <button type="button" @click="forms.tools.content.items.splice(index, 1)" class="absolute top-4 right-4 p-2 text-slate-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100 z-10">
                                            <Trash2 class="w-4 h-4" />
                                        </button>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label class="text-xs font-bold text-slate-500 uppercase">Título</label>
                                                <input v-model="item.title" class="w-full px-4 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-bold transition-all">
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-xs font-bold text-slate-500 uppercase">Icono (Lucide)</label>
                                                <input v-model="item.icon" class="w-full px-4 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all">
                                            </div>
                                            <div class="space-y-2 col-span-2">
                                                <label class="text-xs font-bold text-slate-500 uppercase">Descripción</label>
                                                <input v-model="item.description" class="w-full px-4 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all">
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-xs font-bold text-slate-500 uppercase">Gradiente (Tailwind)</label>
                                                <input v-model="item.color" class="w-full px-4 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all">
                                            </div>
                                             <div class="space-y-2">
                                                <label class="text-xs font-bold text-slate-500 uppercase">Delay (ms)</label>
                                                <input v-model="item.delay" class="w-full px-4 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
                                <button type="submit" :disabled="forms.tools.processing" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5 disabled:opacity-50">
                                    <Save class="w-5 h-5" />
                                    <span>Guardar Herramientas</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- FEATURES SECTION (Rich Editor) -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                     <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" @click="toggleSection('features')">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-xl bg-purple-500/10 text-purple-500">
                                <Zap class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Funcionalidades</h2>
                                <p class="text-sm text-slate-500">Lista detallada de características</p>
                            </div>
                        </div>
                        <component :is="collapsed.features ? ChevronDown : ChevronUp" class="w-5 h-5 text-slate-400" />
                    </div>

                    <div v-show="!collapsed.features" class="p-8 bg-slate-50/50 dark:bg-slate-950/30">
                        <form @submit.prevent="submitSection('features')" class="space-y-8">
                             <div class="flex justify-end">
                                <button type="button" @click="forms.features.content.items.push({ title: 'Nueva', description: '', icon: 'Star', stats: [] })" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 flex items-center gap-2 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg transition-colors">
                                    <Plus class="w-4 h-4" /> Agregar Item
                                </button>
                             </div>

                             <div class="space-y-6">
                                <div v-for="(item, index) in forms.features.content.items" :key="index" class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative group">
                                     <button type="button" @click="forms.features.content.items.splice(index, 1)" class="absolute top-4 right-4 p-2 text-slate-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100 z-10">
                                        <Trash2 class="w-4 h-4" />
                                    </button>

                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                                         <div class="lg:col-span-8 space-y-5">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-2">
                                                    <label class="text-xs font-bold text-slate-500 uppercase">Título</label>
                                                    <input v-model="item.title" class="w-full px-4 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-bold transition-all">
                                                </div>
                                                <div class="space-y-2">
                                                    <label class="text-xs font-bold text-slate-500 uppercase">Icono</label>
                                                    <input v-model="item.icon" class="w-full px-4 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all">
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-xs font-bold text-slate-500 uppercase">Descripción</label>
                                                <textarea v-model="item.description" rows="3" class="w-full px-4 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm leading-relaxed transition-all"></textarea>
                                            </div>
                                            
                                            <!-- Stats Inline -->
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between">
                                                    <label class="text-xs font-bold text-slate-500 uppercase">Estadísticas</label>
                                                    <button type="button" @click="item.stats ? item.stats.push({ label: '', value: '' }) : item.stats = [{ label: '', value: '' }]" class="text-[10px] uppercase font-bold text-indigo-500 hover:text-indigo-600">
                                                        + Stat
                                                    </button>
                                                </div>
                                                
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div v-for="(stat, sIndex) in item.stats" :key="sIndex" class="flex gap-2 items-center group/stat">
                                                        <div class="flex-1 flex gap-1">
                                                            <input v-model="stat.label" placeholder="Label" class="w-1/2 px-3 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium">
                                                            <input v-model="stat.value" placeholder="Value" class="w-1/2 px-3 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold">
                                                        </div>
                                                        <button type="button" @click="item.stats.splice(sIndex, 1)" class="text-slate-300 hover:text-red-400 opacity-0 group-hover/stat:opacity-100">
                                                            <X class="w-3 h-3" />
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                         </div>
                                         <div class="lg:col-span-4">
                                            <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Imagen</label>
                                            <div class="rounded-xl border-dashed border-2 border-slate-200 dark:border-slate-700 h-48 relative flex items-center justify-center overflow-hidden group/img cursor-pointer bg-slate-50 dark:bg-slate-800 transition-colors hover:border-indigo-500/30">
                                                 <input type="file" @change="(e) => handleImageUpload(e, 'features', 'feature_'+index)" class="absolute inset-0 opacity-0 z-20 cursor-pointer">
                                                 <img v-if="forms.features.images['feature_'+index] || item.image" :src="forms.features.images['feature_'+index] ? getObjectUrl(forms.features.images['feature_'+index]) : item.image" class="w-full h-full object-cover transition-transform duration-700 group-hover/img:scale-105">
                                                 <div v-else class="text-slate-400 flex flex-col items-center">
                                                     <ImageIcon class="w-8 h-8 mb-2 opacity-50" />
                                                     <span class="text-xs font-medium">Subir Imagen</span>
                                                 </div>
                                                 <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover/img:opacity-100 transition-opacity z-10 pointer-events-none">
                                                     <span class="text-white text-xs font-bold px-3 py-1 bg-white/20 backdrop-blur rounded-full">Cambiar</span>
                                                 </div>
                                            </div>
                                         </div>
                                    </div>
                                </div>
                             </div>

                             <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
                                <button type="submit" :disabled="forms.features.processing" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5 disabled:opacity-50">
                                    <Save class="w-5 h-5" />
                                    <span>Guardar Funcionalidades</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TESTIMONIALS SECTION -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                     <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" @click="toggleSection('testimonials')">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-xl bg-orange-500/10 text-orange-500">
                                <Users class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Comunidad (Testimonios)</h2>
                                <p class="text-sm text-slate-500">Comentarios destacados y opiniones</p>
                            </div>
                        </div>
                        <component :is="collapsed.testimonials ? ChevronDown : ChevronUp" class="w-5 h-5 text-slate-400" />
                    </div>

                    <div v-show="!collapsed.testimonials" class="p-8 bg-slate-50/50 dark:bg-slate-950/30">
                        <form @submit.prevent="submitSection('testimonials')" class="space-y-8">
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Título Sección</label>
                                    <input v-model="forms.testimonials.content.title" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Subtítulo</label>
                                    <input v-model="forms.testimonials.content.subtitle" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                                </div>
                             </div>

                             <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-800">
                                <button type="button" @click="forms.testimonials.content.items.push({ name: '', role: '', content: '', rating: 5, avatar: '' })" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 flex items-center gap-2 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg transition-colors">
                                    <Plus class="w-4 h-4" /> Agregar Testimonio
                                </button>
                             </div>

                             <div class="space-y-6">
                                <div v-for="(item, index) in forms.testimonials.content.items" :key="index" class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative group">
                                     <button type="button" @click="forms.testimonials.content.items.splice(index, 1)" class="absolute top-4 right-4 p-2 text-slate-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100 z-10">
                                        <Trash2 class="w-4 h-4" />
                                    </button>

                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                                         <div class="lg:col-span-8 space-y-5">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-2">
                                                    <label class="text-xs font-bold text-slate-500 uppercase">Nombre</label>
                                                    <input v-model="item.name" class="w-full px-4 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-bold transition-all">
                                                </div>
                                                <div class="space-y-2">
                                                    <label class="text-xs font-bold text-slate-500 uppercase">Rol / Cargo</label>
                                                    <input v-model="item.role" class="w-full px-4 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all">
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-xs font-bold text-slate-500 uppercase">Comentario</label>
                                                <textarea v-model="item.content" rows="3" class="w-full px-4 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm leading-relaxed transition-all"></textarea>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-xs font-bold text-slate-500 uppercase">Estrellas (1-5)</label>
                                                <div class="flex items-center gap-2">
                                                    <input type="number" v-model="item.rating" min="1" max="5" class="w-20 px-4 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-bold transition-all">
                                                    <div class="flex text-yellow-400">
                                                        <Star v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= item.rating ? 'fill-current' : 'text-slate-300 dark:text-slate-700'" />
                                                    </div>
                                                </div>
                                            </div>
                                         </div>
                                         <div class="lg:col-span-4">
                                            <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Avatar</label>
                                            <div class="rounded-xl border-dashed border-2 border-slate-200 dark:border-slate-700 h-48 relative flex items-center justify-center overflow-hidden group/img cursor-pointer bg-slate-50 dark:bg-slate-800 transition-colors hover:border-indigo-500/30">
                                                 <input type="file" @change="(e) => handleImageUpload(e, 'testimonials', 'testimonial_'+index)" class="absolute inset-0 opacity-0 z-20 cursor-pointer">
                                                 <img v-if="forms.testimonials.images['testimonial_'+index] || item.avatar" :src="forms.testimonials.images['testimonial_'+index] ? getObjectUrl(forms.testimonials.images['testimonial_'+index]) : item.avatar" class="w-full h-full object-cover transition-transform duration-700 group-hover/img:scale-105">
                                                 <div v-else class="text-slate-400 flex flex-col items-center">
                                                     <Users class="w-8 h-8 mb-2 opacity-50" />
                                                     <span class="text-xs font-medium">Subir Avatar</span>
                                                 </div>
                                                 <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover/img:opacity-100 transition-opacity z-10 pointer-events-none">
                                                     <span class="text-white text-xs font-bold px-3 py-1 bg-white/20 backdrop-blur rounded-full">Cambiar</span>
                                                 </div>
                                            </div>
                                         </div>
                                    </div>
                                </div>
                             </div>

                             <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
                                <button type="submit" :disabled="forms.testimonials.processing" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5 disabled:opacity-50">
                                    <Save class="w-5 h-5" />
                                    <span>Guardar Comunidad</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- CTA SECTION -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                     <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" @click="toggleSection('cta')">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-xl bg-pink-500/10 text-pink-500">
                                <MessageSquare class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Call to Action</h2>
                                <p class="text-sm text-slate-500">Bloque final de conversión</p>
                            </div>
                        </div>
                        <component :is="collapsed.cta ? ChevronDown : ChevronUp" class="w-5 h-5 text-slate-400" />
                    </div>

                    <div v-show="!collapsed.cta" class="p-8 bg-slate-50/50 dark:bg-slate-950/30">
                        <form @submit.prevent="submitSection('cta')" class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Título</label>
                                <input v-model="forms.cta.content.title" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-bold">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Descripción</label>
                                <textarea v-model="forms.cta.content.description" rows="3" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all leading-relaxed"></textarea>
                            </div>
                            
                            <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
                                <button type="submit" :disabled="forms.cta.processing" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5 disabled:opacity-50">
                                    <Save class="w-5 h-5" />
                                    <span>Guardar CTA</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ABOUT US SECTION -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                     <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" @click="toggleSection('about')">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-xl bg-teal-500/10 text-teal-500">
                                <Info class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Sobre Nosotros</h2>
                                <p class="text-sm text-slate-500">Historia, Misión y Visión</p>
                            </div>
                        </div>
                        <component :is="collapsed.about ? ChevronDown : ChevronUp" class="w-5 h-5 text-slate-400" />
                    </div>

                    <div v-show="!collapsed.about" class="p-8 bg-slate-50/50 dark:bg-slate-950/30">
                        <form @submit.prevent="submitSection('about')" class="space-y-6">
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Título de Sección</label>
                                    <input v-model="forms.about.content.title" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Quiénes Somos</label>
                                    <textarea v-model="forms.about.content.who_we_are" rows="3" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all leading-relaxed"></textarea>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                     <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Título Misión</label>
                                        <input v-model="forms.about.content.mission_title" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-bold">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Título Visión</label>
                                        <input v-model="forms.about.content.vision_title" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-bold">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Texto Misión</label>
                                        <textarea v-model="forms.about.content.mission" rows="4" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all leading-relaxed"></textarea>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Texto Visión</label>
                                        <textarea v-model="forms.about.content.vision" rows="4" class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all leading-relaxed"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
                                <button type="submit" :disabled="forms.about.processing" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5 disabled:opacity-50">
                                    <Save class="w-5 h-5" />
                                    <span>Guardar Sobre Nosotros</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- FOOTER SECTION -->
                 <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                     <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" @click="toggleSection('footer')">
                        <div class="flex items-center gap-4">
                            <div class="p-3 rounded-xl bg-slate-500/10 text-slate-500">
                                <Type class="w-6 h-6" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Footer</h2>
                                <p class="text-sm text-slate-500">Información legal y marca (Los enlaces son automáticos)</p>
                            </div>
                        </div>
                        <component :is="collapsed.footer ? ChevronDown : ChevronUp" class="w-5 h-5 text-slate-400" />
                    </div>

                    <div v-show="!collapsed.footer" class="p-8 bg-slate-50/50 dark:bg-slate-950/30">
                        <form @submit.prevent="submitSection('footer')" class="space-y-8">
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2 md:col-span-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase">Descripción de Marca</label>
                                    <textarea v-model="forms.footer.content.brand_description" rows="3" class="w-full px-4 py-3 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm leading-relaxed transition-all"></textarea>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase">Texto Copyright</label>
                                    <input v-model="forms.footer.content.copyright_text" class="w-full px-4 py-2 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all">
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end pt-6 border-t border-slate-200 dark:border-slate-800">
                                <button type="submit" :disabled="forms.footer.processing" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5 disabled:opacity-50">
                                    <Save class="w-5 h-5" />
                                    <span>Guardar Footer</span>
                                </button>
                            </div>
                        </form>
                    </div>
                 </div>

            </div>
        </div>
    </AdminLayout>
</template>
