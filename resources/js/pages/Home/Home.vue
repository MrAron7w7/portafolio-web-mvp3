<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { 
    Sparkles, 
    FileText, 
    MessageSquare, 
    Mic, 
    Layout, 
    Rocket, 
    Users,
    CheckCircle2,
    ArrowRight,
    Github,
    Twitter,
    Linkedin,
    Mail,
    Shield,
    Zap,
    Globe,
    Star,
    Info
} from 'lucide-vue-next';

const props = defineProps<{
    sections?: any;
}>();

const iconsMap: Record<string, any> = {
    Sparkles, FileText, MessageSquare, Mic, Layout, Rocket, Users, 
    CheckCircle2, ArrowRight, Github, Twitter, Linkedin, Mail, Shield, Zap, Globe, Star, Info
};


// Datos del usuario autenticado
const page = usePage();
const isAuthenticated = computed(() => page.props.auth?.user != null);

// Estado para el menú móvil
const mobileMenuOpen = ref(false);

// Mouse tracking para efectos parallax
const mouseX = ref(0);
const mouseY = ref(0);

// Herramientas Reales (Cards Flotantes)
// Herramientas Reales (Cards Flotantes)
const tools = computed(() => {
    const items = props.sections?.tools?.content?.items || [
        {
            icon: 'Layout',
            title: 'Plantillas Premium',
            description: 'Diseños optimizados para conversión, adaptables a tu marca personal.',
            color: 'from-blue-500 to-indigo-600',
            delay: '0'
        },
        {
            icon: 'FileText',
            title: 'ATS Scanner',
            description: 'Optimización de CV con IA para superar filtros de reclutamiento.',
            color: 'from-purple-500 to-pink-600',
            delay: '100'
        },
        {
            icon: 'Mic',
            title: 'Entrevistas IA',
            description: 'Simulaciones realistas con feedback inmediato sobre tus respuestas.',
            color: 'from-orange-400 to-red-500',
            delay: '200'
        },
        {
            icon: 'MessageSquare',
            title: 'Comunidad',
            description: 'Networking estratégico con profesionales de tu industria.',
            color: 'from-emerald-400 to-teal-600',
            delay: '300'
        }
    ];

    return items.map((item: any) => ({
        ...item,
        iconComponent: iconsMap[item.icon] || Layout // Default icon fallback
    }));
});

// Feature Spotlights
// Feature Spotlights
const features = computed(() => {
    const items = props.sections?.features?.content?.items || [
        {
            title: 'Diseño Inteligente que Impacta',
            description: 'Nuestra tecnología analiza tu perfil y genera un portafolio que destaca tus fortalezas clave. Sin arrastrar y soltar interminable, solo resultados profesionales al instante.',
            icon: 'Zap',
            image: '/imagenes/AnalizaPerfil.PNG',
            imageGradient: 'from-indigo-500/10 to-purple-500/10',
            stats: [
                { label: 'Más rápido', value: '10x' },
                { label: 'Personalizable', value: '100%' }
            ]
        },
        {
            title: 'Domina las Entrevistas Técnicas',
            description: 'Practica con un entrenador de IA que se adapta a tu rol. Recibe consejos sobre claridad, tono y contenido técnico para llegar confiado a la entrevista real.',
            icon: 'Mic',
            // No image provided yet for this one, keeping abstract or could use same logic
            imageGradient: 'from-orange-500/10 to-red-500/10',
            stats: [
                { label: 'Preguntas Reales', value: '500+' },
                { label: 'Feedback', value: 'Instantáneo' }
            ]
        }
    ];

    return items.map((item: any) => ({
        ...item,
        iconComponent: iconsMap[item.icon] || Zap
    }));
});

// Testimonials Data
const testimonials = computed(() => {
    return props.sections?.testimonials?.content?.items || [];
});

// Footer Links
const footerLinks = {
    product: [
        { name: 'Funcionalidades', href: '#herramientas' },
        { name: 'Plantillas', href: '#' },
        { name: 'Precios', href: '#' }, // Placeholder
        { name: 'Showcase', href: '#' }
    ],
    company: [
        { name: 'Sobre Nosotros', href: '#nosotros' },
        { name: 'Blog', href: '#' },
        { name: 'Carreras', href: '#' },
        { name: 'Contacto', href: '#' }
    ],
    legal: [
        { name: 'Privacidad', href: '#' },
        { name: 'Términos', href: '#' },
        { name: 'Cookies', href: '#' }
    ]
};

// Mouse tracking logic
const handleMouseMove = (e: MouseEvent) => {
    mouseX.value = (e.clientX / window.innerWidth - 0.5) * 20;
    mouseY.value = (e.clientY / window.innerHeight - 0.5) * 20;
};

// 3D Tilt effect
const handleCardTilt = (e: MouseEvent, card: HTMLElement) => {
    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    const centerX = rect.width / 2;
    const centerY = rect.height / 2;
    const rotateX = ((y - centerY) / centerY) * -5;
    const rotateY = ((x - centerX) / centerX) * 5;

    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
};

const resetCardTilt = (card: HTMLElement) => {
    card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
};

onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.scroll-animate').forEach((el) => observer.observe(el));
    window.addEventListener('mousemove', handleMouseMove);

    document.querySelectorAll('.tilt-card').forEach((card) => {
        const element = card as HTMLElement;
        element.addEventListener('mousemove', (e) => handleCardTilt(e as MouseEvent, element));
        element.addEventListener('mouseleave', () => resetCardTilt(element));
    });

     // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (e) => {
            const href = (e.currentTarget as HTMLAnchorElement).getAttribute('href');
            if (href) {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    const headerOffset = 80;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
                }
            }
        });
    });
});

onUnmounted(() => {
    window.removeEventListener('mousemove', handleMouseMove);
});
</script>

<template>
    <Head title="Bienvenido" />
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans selection:bg-indigo-500/30 transition-colors duration-300">
        <!-- Header -->
        <header class="fixed z-50 w-full bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-300 dark:border-slate-700 transition-all duration-300">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-4">
                    <!-- Logo -->
                    <div class="flex lg:flex-1">
                        <a href="#" class="-m-1.5 p-1.5 flex items-center gap-3 group">
                            <img 
                                v-if="sections?.header?.images?.logo" 
                                :src="sections?.header?.images?.logo" 
                                alt="Logo" 
                                class="h-10 w-10 rounded-xl object-contain"
                            />
                            <div v-else class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform duration-300">
                                 <Sparkles class="h-5 w-5 text-white" />
                            </div>
                            <span class="text-xl font-bold bg-clip-text text-transparent bg-linear-to-r from-slate-900 via-indigo-900 to-slate-700 dark:from-white dark:via-indigo-200 dark:to-slate-300">
                                {{ sections?.header?.content?.brand_name || 'PortafolioAI' }}
                            </span>
                        </a>
                    </div>

                    <!-- Menú Desktop -->
                    <nav class="hidden items-center gap-6 md:flex">
                        <a href="#inicio" class="text-sm font-medium text-slate-600 dark:text-slate-300 transition-colors hover:text-indigo-600 dark:hover:text-white">Inicio</a>
                        <a href="#funcionalidades" class="text-sm font-medium text-slate-600 dark:text-slate-300 transition-colors hover:text-indigo-600 dark:hover:text-white">Funcionalidades</a>
                        <a href="#comunidad" class="text-sm font-medium text-slate-600 dark:text-slate-300 transition-colors hover:text-indigo-600 dark:hover:text-white">Comunidad</a>
                        <a href="#nosotros" class="text-sm font-medium text-slate-600 dark:text-slate-300 transition-colors hover:text-indigo-600 dark:hover:text-white">Sobre Nosotros</a>
                    </nav>

                    <!-- Auth Buttons -->
                    <div class="hidden items-center gap-4 md:flex ml-8">
                        <template v-if="!isAuthenticated">
                            <a href="/login" class="text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-white transition-colors">Iniciar Sesión</a>
                            <a href="/register" class="group relative inline-flex items-center justify-center rounded-xl bg-slate-900 dark:bg-white px-5 py-2.5 text-sm font-semibold text-white dark:text-slate-900 shadow-lg shadow-slate-900/10 transition-all duration-200 hover:bg-slate-800 dark:hover:bg-slate-200 hover:shadow-xl hover:-translate-y-0.5">
                                <span>Crear Cuenta</span>
                                <ArrowRight class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                            </a>
                        </template>
                        <template v-else>
                            <a href="/dashboard" class="group relative inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition-all duration-200 hover:bg-indigo-700 hover:shadow-indigo-500/40 hover:-translate-y-0.5">
                                <span>Ir al Dashboard</span>
                                <Layout class="ml-2 h-4 w-4" />
                            </a>
                        </template>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="rounded-xl p-2 text-slate-600 transition-colors hover:bg-slate-100 md:hidden">
                        <span class="text-2xl">☰</span>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div v-show="mobileMenuOpen" class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 md:hidden animate-fade-in-up">
                <div class="space-y-1 p-4">
                    <a href="#inicio" class="block rounded-lg px-4 py-3 text-base font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-white">Inicio</a>
                    <a href="#funcionalidades" class="block rounded-lg px-4 py-3 text-base font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-white">Funcionalidades</a>
                    <a href="#comunidad" class="block rounded-lg px-4 py-3 text-base font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-white">Comunidad</a>
                    <a href="#nosotros" class="block rounded-lg px-4 py-3 text-base font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-white">Sobre Nosotros</a>
                    <div class="mt-4 space-y-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                         <template v-if="!isAuthenticated">
                            <a href="/login" class="block w-full rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-3 text-center font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">Iniciar Sesión</a>
                            <a href="/register" class="block w-full rounded-lg bg-indigo-600 px-4 py-3 text-center font-semibold text-white">Comenzar Gratis</a>
                         </template>
                         <template v-else>
                            <a href="/dashboard" class="block w-full rounded-lg bg-indigo-600 px-4 py-3 text-center font-semibold text-white">Ir al Dashboard</a>
                         </template>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section id="inicio" class="relative overflow-hidden pt-36 pb-24 lg:pt-48 lg:pb-32">
            <!-- Background Decorations -->
            <div class="absolute inset-0 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-size-[24px_24px] dark:opacity-20"></div>
                <div class="absolute left-0 right-0 top-0 -z-10 m-auto h-[310px] w-[310px] rounded-full bg-indigo-500 opacity-20 blur-[100px]"></div>
                <div class="absolute right-0 top-0 -z-10 h-[500px] w-[500px] rounded-full bg-purple-500 opacity-10 blur-[120px]"></div>
            </div>

            <div class="container relative z-10 mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="text-center lg:text-left scroll-animate">
                        <div class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-white/80 px-4 py-1.5 shadow-sm backdrop-blur-sm mb-8">
                            <span class="flex h-2 w-2 rounded-full bg-indigo-500 animate-pulse"></span>
                            <span class="text-sm font-semibold text-indigo-700">{{ sections?.hero?.content?.badge || 'v2.0 Ahora con IA Avanzada' }}</span>
                        </div>

                        <h1 class="mb-6 text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-6xl lg:text-7xl leading-[1.1]">
                            {{ sections?.hero?.content?.title_line1 || 'Potencia tu' }} <br/>
                            <span class="bg-linear-to-r from-indigo-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent animate-gradient-x">{{ sections?.hero?.content?.title_gradient || 'Marca Personal' }}</span>
                        </h1>
                        
                        <p class="mb-10 text-xl text-slate-600 dark:text-slate-400 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                            {{ sections?.hero?.content?.description || 'La plataforma todo en uno para construir tu portafolio, optimizar tu CV y preparar entrevistas. Diseñada para desarrolladores y creativos ambiciosos.' }}
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <a :href="sections?.hero?.content?.primary_button_link || '/register'" class="group inline-flex items-center justify-center rounded-xl bg-slate-900 dark:bg-white px-8 py-4 text-base font-bold text-white dark:text-slate-900 shadow-xl shadow-slate-900/20 dark:shadow-white/5 transition-all hover:bg-slate-800 dark:hover:bg-slate-100 hover:-translate-y-1">
                                {{ sections?.hero?.content?.primary_button_text || 'Comenzar Gratis' }}
                                <Rocket class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1" />
                            </a>
                            <a :href="sections?.hero?.content?.secondary_button_link || '#funcionalidades'" class="inline-flex items-center justify-center rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-8 py-4 text-base font-bold text-slate-700 dark:text-slate-300 shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-700">
                                {{ sections?.hero?.content?.secondary_button_text || 'Explorar Demo' }}
                            </a>
                        </div>
                        
                        <!-- Mini Social Proof -->
                        <div class="mt-10 flex items-center justify-center lg:justify-start gap-4 text-sm font-medium text-slate-500 dark:text-slate-400">
                            <div class="flex -space-x-3">
                                <div v-for="i in 4" :key="i" class="h-10 w-10 rounded-full border-2 border-white dark:border-slate-800 bg-slate-200 dark:bg-slate-700 flex items-center justify-center overflow-hidden">
                                     <!-- Placeholder avatars -->
                                    <div class="w-full h-full bg-linear-to-br from-indigo-100 to-purple-100 dark:from-indigo-900 dark:to-purple-900"></div>
                                </div>
                            </div>
                            <div class="flex flex-col text-left">
                                <div class="flex text-yellow-500">★★★★★</div>
                                <span>{{ sections?.hero?.content?.social_proof_text || 'Uniendo a la comunidad tech' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Visual -->
                    <div class="relative lg:h-[600px] w-full hidden lg:block scroll-animate">
                         <!-- Abstract Dashboard Mockup -->
                         <div class="absolute inset-0 bg-linear-to-tr from-indigo-500/10 to-purple-500/10 rounded-3xl transform rotate-3 scale-95 blur-2xl"></div>
                         <div class="relative h-full w-full bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-300 dark:border-slate-600 overflow-hidden transform transition-transform hover:scale-[1.01] duration-500">
                            <!-- Image Container -->
                            <div class="absolute inset-0 bg-slate-50 dark:bg-slate-900 flex items-center justify-center overflow-hidden">
                                <img 
                                    :src="sections?.hero?.images?.main_image || '/imagenes/Template.PNG'" 
                                    alt="Portafolio Template Preview" 
                                    class="w-full h-full object-cover object-top opacity-95 hover:opacity-100 transition-opacity duration-700"
                                />
                                <!-- Overlay gradient for better text contrast if needed, or just polish -->
                                <div class="absolute inset-0 bg-linear-to-t from-white/20 to-transparent pointer-events-none"></div>
                            </div>
                            
                            <!-- Floating Card Overlay -->
                            <div class="absolute bottom-12 left-6 bg-white dark:bg-slate-800 p-4 rounded-xl shadow-xl border border-slate-100 dark:border-slate-700 animate-float-delayed">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-green-100 dark:bg-green-500/10 text-green-600 dark:text-green-400 rounded-lg">
                                        <CheckCircle2 class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase">{{ sections?.hero?.content?.cv_score_label || 'CV Score' }}</div>
                                        <div class="text-lg font-bold text-slate-900 dark:text-white">{{ sections?.hero?.content?.cv_score_value || '98/100' }}</div>
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tools / Portal AI Section -->
        <section id="herramientas" class="py-24 bg-white dark:bg-slate-900 relative z-20 -mt-20 lg:-mt-32">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="(tool, index) in tools" :key="index" 
                        class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.25)] shadow-slate-400/40 dark:shadow-none border border-slate-300 dark:border-slate-600 hover:-translate-y-2 hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.4)] hover:shadow-slate-500/50 transition-all duration-300 group scroll-animate"
                        :style="{ transitionDelay: `${tool.delay}ms` }"
                    >
                        <div class="h-12 w-12 rounded-xl bg-linear-to-br mb-6 flex items-center justify-center text-white shadow-lg transform group-hover:scale-110 transition-transform duration-300" :class="tool.color">
                            <component :is="tool.iconComponent" class="h-6 w-6" />
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ tool.title }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                            {{ tool.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>


        
        <!-- Feature Spotlights (Futuristic 3D) -->
        <section id="funcionalidades" class="py-32 bg-slate-50 dark:bg-slate-950 relative overflow-hidden transition-colors duration-300">
             <!-- Futuristic Background Elements -->
             <!-- Grid: Dark gray in dark mode, Light gray in light mode -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#4f4f4f2e_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f2e_1px,transparent_1px)] bg-size-[4rem_4rem] mask-[radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
            <div class="absolute top-0 left-0 right-0 h-px bg-linear-to-r from-transparent via-indigo-500 to-transparent opacity-50"></div>
            
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 space-y-40 relative z-10">
                <div v-for="(feature, index) in features" :key="index" class="relative group scroll-animate">
                    <div class="grid lg:grid-cols-2 gap-20 items-center" :class="{ 'lg:flex-row-reverse': (index as number) % 2 === 1 }">
                        <!-- Content -->
                        <div :class="{ 'order-2': (index as number) % 2 === 1 }">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-500/10 border border-indigo-500/20 mb-8 shadow-[0_0_15px_rgba(99,102,241,0.3)]">
                                <component :is="feature.iconComponent" class="h-7 w-7 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <h3 class="text-4xl font-bold text-slate-900 dark:text-white mb-6 leading-tight tracking-tight">
                                {{ feature.title }}
                            </h3>
                            <p class="text-lg text-slate-600 dark:text-slate-400 mb-10 leading-relaxed">
                                {{ feature.description }}
                            </p>
                            
                            <dl class="grid grid-cols-2 gap-x-8 gap-y-8 pt-8 border-t border-slate-200 dark:border-slate-800/60">
                                <div v-for="(stat, sIndex) in feature.stats" :key="sIndex">
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-2">{{ stat.label }}</dt>
                                    <dd class="text-3xl font-bold text-slate-900 dark:text-white">{{ stat.value }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <!-- Visual (3D Perspective) -->
                        <div :class="{ 'order-1': (index as number) % 2 === 1 }" class="relative perspective-container group-hover:scale-[1.02] transition-transform duration-700 ease-out">
                             <!-- Glowing Backdrop -->
                            <div class="absolute inset-0 bg-linear-to-tr opacity-20 blur-[80px] -z-10 transition-opacity duration-500 group-hover:opacity-30" :class="feature.imageGradient"></div>
                            
                            <!-- 3D Card -->
                            <div class="relative transform-3d transition-transform duration-500 rounded-3xl border border-slate-300 dark:border-slate-600 bg-white/80 dark:bg-slate-800/50 backdrop-blur-xl shadow-2xl overflow-hidden aspect-16/10">
                                 <!-- Glass Reflection -->
                                 <div class="absolute inset-0 bg-linear-to-br from-white/40 dark:from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-20 pointer-events-none"></div>
                                 
                                 <template v-if="feature.image">
                                    <div class="w-full h-full p-8 flex items-center justify-center bg-slate-50 dark:bg-slate-900/40 relative">
                                        <!-- Grid overlay inside image area -->
                                        <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] dark:bg-[linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] bg-size-[20px_20px]"></div>
                                        
                                        <div class="relative z-10 w-full h-full shadow-2xl rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700/50 group-hover:shadow-[0_0_40px_rgba(79,70,229,0.15)] transition-shadow duration-500">
                                            <img 
                                                :src="feature.image" 
                                                :alt="feature.title" 
                                                class="w-full h-full object-cover object-top"
                                            />
                                        </div>
                                    </div>
                                 </template>
                                 <template v-else>
                                     <!-- Abstract visualization placeholder (Futuristic) -->
                                     <div class="w-full h-full flex flex-col items-center justify-center relative bg-slate-50 dark:bg-slate-900">
                                         <div class="absolute inset-0 bg-indigo-500/5"></div>
                                         <div class="h-24 w-24 rounded-full bg-linear-to-tr from-indigo-500 to-purple-500 blur-2xl opacity-40 animate-pulse"></div>
                                         <div class="text-indigo-600 dark:text-indigo-400 font-mono text-sm tracking-widest uppercase z-10 border border-indigo-500/30 px-4 py-2 rounded bg-white/50 dark:bg-indigo-950/30 backdrop-blur-sm shadow-sm">
                                             AI Analysis Active
                                         </div>
                                     </div>
                                 </template>
                            </div>
                            
                            <!-- Decorative floating elements -->
                            <div class="absolute -top-6 -right-6 h-20 w-20 bg-indigo-500/20 rounded-full blur-2xl animate-float-delayed"></div>
                            <div class="absolute -bottom-8 -left-8 h-32 w-32 bg-purple-500/20 rounded-full blur-3xl animate-float"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="comunidad" class="py-32 bg-white dark:bg-slate-900 relative transition-colors duration-300">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mx-auto text-center mb-16 scroll-animate">
                    <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl mb-6">
                        {{ sections?.testimonials?.content?.title || 'Lo que dice la comunidad' }}
                    </h2>
                    <p class="text-xl text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ sections?.testimonials?.content?.subtitle || 'Opiniones verificadas de profesionales destacados que ya consiguieron empleo.' }}
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div v-for="(testimonial, index) in testimonials" :key="index" class="bg-slate-50 dark:bg-slate-800/40 p-8 rounded-3xl border border-slate-300 dark:border-slate-600 hover:shadow-xl hover:shadow-indigo-500/5 hover:-translate-y-1 transition-all duration-300 scroll-animate group">
                        <!-- Stars -->
                        <div class="flex gap-1 text-yellow-400 mb-6">
                            <Star v-for="i in 5" :key="i" class="w-5 h-5 fill-current" :class="i <= testimonial.rating ? 'text-yellow-400' : 'text-slate-300 dark:text-slate-600'" />
                        </div>
                        
                        <p class="text-slate-700 dark:text-slate-300 mb-8 leading-relaxed text-lg italic">
                            "{{ testimonial.content }}"
                        </p>
                        
                        <div class="flex items-center gap-4 mt-auto border-t border-slate-200 dark:border-slate-700/50 pt-6">
                            <div class="h-12 w-12 rounded-full overflow-hidden bg-slate-200 dark:bg-slate-700 ring-2 ring-white dark:ring-slate-800">
                                <img 
                                    v-if="testimonial.avatar" 
                                    :src="testimonial.avatar" 
                                    :alt="testimonial.name" 
                                    class="h-full w-full object-cover"
                                />
                                <div v-else class="h-full w-full flex items-center justify-center text-slate-400">
                                    <Users class="h-6 w-6" />
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ testimonial.name }}</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ testimonial.role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Us Section -->
        <section id="nosotros" class="py-32 bg-slate-50 dark:bg-slate-950 relative overflow-hidden transition-colors duration-300">
             <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#4f4f4f2e_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f2e_1px,transparent_1px)] bg-size-[4rem_4rem] mask-[radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
            
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="max-w-4xl mx-auto text-center mb-20 scroll-animate">
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-white/80 px-4 py-1.5 shadow-sm backdrop-blur-sm mb-8">
                         <Info class="h-4 w-4 text-indigo-600" />
                         <span class="text-sm font-semibold text-indigo-700">{{ sections?.about?.content?.title || 'Sobre Nosotros' }}</span>
                    </div>
                    
                    <h2 class="text-3xl lg:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-8">
                        Construyendo el futuro del <br>
                        <span class="bg-clip-text text-transparent bg-linear-to-r from-indigo-500 to-purple-600">talento digital</span>
                    </h2>
                    
                    <p class="text-xl text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ sections?.about?.content?.who_we_are || 'Somos un equipo de desarrolladores y diseñadores apasionados por la tecnología y el crecimiento profesional.' }}
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
                     <!-- Mission Card -->
                    <div class="bg-white dark:bg-slate-900 p-10 rounded-3xl shadow-sm border border-slate-300 dark:border-slate-600 scroll-animate hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 group">
                        <div class="h-14 w-14 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                             <Rocket class="h-7 w-7" />
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">{{ sections?.about?.content?.mission_title || 'Nuestra Misión' }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-lg">
                            {{ sections?.about?.content?.mission || 'Democratizar el acceso al éxito profesional.' }}
                        </p>
                    </div>

                    <!-- Vision Card -->
                    <div class="bg-white dark:bg-slate-900 p-10 rounded-3xl shadow-sm border border-slate-300 dark:border-slate-600 scroll-animate hover:shadow-xl hover:shadow-purple-500/10 transition-all duration-300 group">
                        <div class="h-14 w-14 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                             <Globe class="h-7 w-7" />
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">{{ sections?.about?.content?.vision_title || 'Nuestra Visión' }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-lg">
                            {{ sections?.about?.content?.vision || 'Convertirnos en el ecosistema integral de carrera líder a nivel global.' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 bg-slate-900 relative isolate overflow-hidden">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(45rem_50rem_at_top,var(--color-indigo-100),white)] opacity-20"></div>
            <div class="absolute inset-y-0 right-1/2 -z-10 mr-16 w-[200%] origin-bottom-left skew-x-[-30deg] bg-slate-900 shadow-xl shadow-indigo-600/10 ring-1 ring-indigo-50 sm:mr-28 lg:mr-0 xl:mr-16 xl:origin-center"></div>
            <div class="mx-auto max-w-2xl text-center px-4">
                <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ sections?.cta?.content?.title || 'Comienza a construir tu futuro hoy.' }}</h2>
                <p class="mx-auto mt-6 text-lg leading-8 text-slate-300">
                    {{ sections?.cta?.content?.description || 'Únete a miles de profesionales que ya están acelerando su carrera con nuestras herramientas de IA.' }}
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
<a :href="sections?.cta?.content?.primary_button_link || '/register'" class="rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:-translate-y-1">
                        {{ sections?.cta?.content?.primary_button_text || 'Crear Cuenta Gratis' }}
                    </a>
                    <a :href="sections?.cta?.content?.secondary_button_link || '#'" class="text-sm font-semibold leading-6 text-white group">
                        {{ sections?.cta?.content?.secondary_button_text || 'Saber más' }} <span aria-hidden="true" class="inline-block transition-transform group-hover:translate-x-1">→</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <!-- Footer -->
        <footer class="bg-slate-950 pt-20 pb-10 border-t border-slate-800 transition-colors duration-300">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-12 mb-16">
                    <div class="col-span-2 lg:col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <img 
                                v-if="sections?.header?.images?.logo" 
                                :src="sections?.header?.images?.logo" 
                                alt="Logo" 
                                class="h-10 w-10 rounded-xl object-contain"
                            />
                            <div v-else class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white">
                                <Sparkles class="h-5 w-5" />
                            </div>
                            <span class="text-xl font-bold bg-clip-text text-transparent bg-linear-to-r from-white via-indigo-200 to-slate-200 tracking-tight">{{ sections?.header?.content?.brand_name || 'PortafolioAI' }}</span>
                        </div>
                        <p class="text-slate-400 text-sm leading-relaxed mb-6 max-w-sm">
                            {{ sections?.footer?.content?.brand_description || 'Empoderando a la próxima generación de profesionales con herramientas impulsadas por inteligencia artificial.' }}
                        </p>
                        <div class="flex gap-4 text-slate-500">
                             <a href="#" class="hover:text-indigo-400 transition-colors"><Twitter class="h-5 w-5" /></a>
                             <a href="#" class="hover:text-indigo-400 transition-colors"><Github class="h-5 w-5" /></a>
                             <a href="#" class="hover:text-indigo-400 transition-colors"><Linkedin class="h-5 w-5" /></a>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-bold text-white mb-6 text-sm uppercase tracking-wider">Producto</h4>
                        <ul class="space-y-4 text-sm text-slate-400">
                            <li v-for="link in footerLinks.product" :key="link.name">
                                <a :href="link.href" class="hover:text-indigo-400 transition-colors">{{ link.name }}</a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-white mb-6 text-sm uppercase tracking-wider">Compañía</h4>
                        <ul class="space-y-4 text-sm text-slate-400">
                            <li v-for="link in footerLinks.company" :key="link.name">
                                <a :href="link.href" class="hover:text-indigo-400 transition-colors">{{ link.name }}</a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-white mb-6 text-sm uppercase tracking-wider">Legal</h4>
                        <ul class="space-y-4 text-sm text-slate-400">
                            <li v-for="link in footerLinks.legal" :key="link.name">
                                <a :href="link.href" class="hover:text-indigo-400 transition-colors">{{ link.name }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-slate-400">
                    <p>&copy; {{ new Date().getFullYear() }} {{ sections?.footer?.content?.copyright_text || 'PortafolioAI. Todos los derechos reservados.' }}</p>
                    <div class="flex gap-6 items-center">
                        <div class="flex items-center gap-2">
                             <div class="h-2 w-2 rounded-full bg-green-500"></div>
                             <span>Sistemas Operativos</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Globe class="h-4 w-4" />
                            <span>Español</span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.perspective-container {
    perspective: 1000px;
}

.transform-3d {
    transform-style: preserve-3d;
    transform: rotateX(5deg) rotateY(-5deg) rotateZ(1deg);
}

.group:hover .transform-3d {
    transform: rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale(1.02);
}

.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
    opacity: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-float-delayed {
    animation: float 6s ease-in-out infinite;
    animation-delay: 2s;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
</style>
