<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

// Estado para el menú móvil
const mobileMenuOpen = ref(false);

// Mouse tracking para efectos parallax y magnetic
const mouseX = ref(0);
const mouseY = ref(0);

// Carousel de testimonios
const currentTestimonial = ref(0);
const autoPlayInterval = ref<number | null>(null);

// Stats counters
const stats = ref([
    { value: 0, target: 15000, label: 'Usuarios Activos', suffix: '+' },
    { value: 0, target: 50000, label: 'Portafolios Creados', suffix: '+' },
    { value: 0, target: 98, label: 'Satisfacción', suffix: '%' },
]);

// Datos de beneficios con stats
const benefits = ref([
    {
        icon: '🤖',
        title: 'Diseño Automático con IA',
        description:
            'Nuestra inteligencia artificial crea diseños únicos y profesionales basados en tu perfil.',
        stat: '2min',
        statLabel: 'Creación promedio',
    },
    {
        icon: '🎨',
        title: 'Plantillas Profesionales',
        description:
            'Elige entre múltiples plantillas modernas y adaptables a cualquier industria.',
        stat: '50+',
        statLabel: 'Plantillas disponibles',
    },
    {
        icon: '🚀',
        title: 'Exportación y Hosting',
        description:
            'Publica tu portafolio al instante con hosting incluido y dominio personalizable.',
        stat: '99.9%',
        statLabel: 'Uptime garantizado',
    },
    {
        icon: '⚙️',
        title: 'Personalización Total',
        description:
            'Ajusta colores, fuentes y disposición para que refleje tu estilo personal.',
        stat: '∞',
        statLabel: 'Posibilidades',
    },
]);

// Datos de cómo funciona
const steps = ref([
    {
        step: '1',
        title: 'Cuéntanos sobre ti',
        description:
            'Completa tu información profesional, experiencia y proyectos destacados.',
        image: '📝',
    },
    {
        step: '2',
        title: 'IA genera tu portafolio',
        description:
            'Nuestra inteligencia artificial diseña automáticamente tu portafolio perfecto.',
        image: '✨',
    },
    {
        step: '3',
        title: 'Publica y comparte',
        description:
            'Comparte tu portafolio profesional con el mundo en minutos.',
        image: '🌐',
    },
]);

// Datos de testimonios
const testimonials = ref([
    {
        name: 'María González',
        role: 'Diseñadora UX/UI',
        content:
            'Increíble cómo la IA entendió exactamente lo que necesitaba. Mi portafolio nunca se había visto tan profesional.',
        avatar: '👩‍💼',
        rating: 5,
    },
    {
        name: 'Carlos Rodríguez',
        role: 'Desarrollador Full Stack',
        content:
            'En 15 minutos tenía un portafolio que normalmente me hubiera tomado días diseñar. Totalmente recomendado.',
        avatar: '👨‍💻',
        rating: 5,
    },
    {
        name: 'Ana Martínez',
        role: 'Marketing Digital',
        content:
            'El proceso fue tan sencillo y el resultado superó mis expectativas. He conseguido 3 entrevistas gracias a mi nuevo portafolio.',
        avatar: '👩‍🎓',
        rating: 5,
    },
]);

// Planes de precios
const plans = ref([
    {
        name: 'Gratis',
        price: '$0',
        period: 'siempre',
        features: [
            '1 Portafolio básico',
            'Plantillas limitadas',
            'Subdominio personal',
            'Soporte por email',
        ],
        cta: 'Comenzar Gratis',
        popular: false,
    },
    {
        name: 'Pro',
        price: '$9',
        period: 'mes',
        features: [
            'Portafolios ilimitados',
            'Todas las plantillas',
            'Dominio personalizado',
            'Análisis de visitas',
            'Soporte prioritario',
            'Exportación a PDF',
        ],
        cta: 'Probar Gratis',
        popular: true,
    },
    {
        name: 'Empresarial',
        price: '$29',
        period: 'mes',
        features: [
            'Todo lo del plan Pro',
            'Portafolios de equipo',
            'Marca blanca',
            'API de integración',
            'Soporte 24/7',
            'Entrenamiento IA personalizado',
        ],
        cta: 'Contactar Ventas',
        popular: false,
    },
]);

// Computed para parallax
const parallaxStyle = computed(() => ({
    transform: `translate(${mouseX.value * 0.02}px, ${mouseY.value * 0.02}px)`,
}));

// Funciones de carousel
const nextTestimonial = () => {
    currentTestimonial.value =
        (currentTestimonial.value + 1) % testimonials.value.length;
};

const prevTestimonial = () => {
    currentTestimonial.value =
        currentTestimonial.value === 0
            ? testimonials.value.length - 1
            : currentTestimonial.value - 1;
};

const goToTestimonial = (index: number) => {
    currentTestimonial.value = index;
};

const startAutoPlay = () => {
    autoPlayInterval.value = window.setInterval(nextTestimonial, 5000);
};

const stopAutoPlay = () => {
    if (autoPlayInterval.value) {
        clearInterval(autoPlayInterval.value);
        autoPlayInterval.value = null;
    }
};

// Animación de stats counter
const animateCounter = (stat: any) => {
    const duration = 2000;
    const steps = 60;
    const increment = stat.target / steps;
    let current = 0;

    const timer = setInterval(() => {
        current += increment;
        if (current >= stat.target) {
            stat.value = stat.target;
            clearInterval(timer);
        } else {
            stat.value = Math.floor(current);
        }
    }, duration / steps);
};

// Mouse tracking
const handleMouseMove = (e: MouseEvent) => {
    mouseX.value = (e.clientX / window.innerWidth - 0.5) * 20;
    mouseY.value = (e.clientY / window.innerHeight - 0.5) * 20;
};

// 3D Tilt effect para cards
const handleCardTilt = (e: MouseEvent, card: HTMLElement) => {
    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    const centerX = rect.width / 2;
    const centerY = rect.height / 2;

    const rotateX = ((y - centerY) / centerY) * -10;
    const rotateY = ((x - centerX) / centerX) * 10;

    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.05, 1.05, 1.05)`;
};

const resetCardTilt = (card: HTMLElement) => {
    card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
};

onMounted(() => {
    // Scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px',
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('animate-fade-in');
                    
                    // Trigger stats counter si es la sección de stats
                    if (entry.target.classList.contains('stats-section')) {
                        stats.value.forEach(stat => animateCounter(stat));
                    }
                }, index * 80);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.scroll-animate').forEach((el) => {
        observer.observe(el);
    });

    // Mouse tracking
    window.addEventListener('mousemove', handleMouseMove);

    // Auto-play carousel
    startAutoPlay();

    // 3D Tilt setup
    document.querySelectorAll('.tilt-card').forEach((card) => {
        const element = card as HTMLElement;
        element.addEventListener('mousemove', (e) =>
            handleCardTilt(e as MouseEvent, element),
        );
        element.addEventListener('mouseleave', () => resetCardTilt(element));
    });

    // Smooth scroll con offset para header fixed
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href && href !== '#ejemplos') {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    const headerOffset = 80; // altura del header
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
});

onUnmounted(() => {
    window.removeEventListener('mousemove', handleMouseMove);
    stopAutoPlay();
});
</script>

<template>
    <Head title="Inicio" />
    <div class="min-h-screen bg-white font-sans">
        <!-- Header -->
        <header
            class="fixed z-50 w-full bg-white/95 shadow-sm backdrop-blur-md transition-all duration-300"
        >
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-4">
                    <!-- Logo -->
                    <div class="flex items-center space-x-2">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-[#005aeb] to-[#0048c4] shadow-lg"
                        >
                            <span class="text-sm font-bold text-white">P</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900"
                            >PortafolioAI</span
                        >
                    </div>

                    <!-- Menú Desktop -->
                    <nav class="hidden items-center space-x-8 md:flex">
                        <a
                            href="#inicio"
                            class="font-medium text-gray-700 transition-all duration-200 hover:text-[#005aeb] hover:scale-105"
                            >Inicio</a
                        >
                        <a
                            href="#beneficios"
                            class="font-medium text-gray-700 transition-all duration-200 hover:text-[#005aeb] hover:scale-105"
                            >Funciones</a
                        >
                        <a
                            href="#planes"
                            class="font-medium text-gray-700 transition-all duration-200 hover:text-[#005aeb] hover:scale-105"
                            >Planes</a
                        >
                        <a
                            href="#testimonios"
                            class="font-medium text-gray-700 transition-all duration-200 hover:text-[#005aeb] hover:scale-105"
                            >Testimonios</a
                        >
                        <a
                            href="#contacto"
                            class="font-medium text-gray-700 transition-all duration-200 hover:text-[#005aeb] hover:scale-105"
                            >Contacto</a
                        >
                    </nav>

                    <!-- Botones Desktop -->
                    <div class="hidden items-center space-x-4 md:flex">
                         <a
                            href="/comunidad"
                            class="flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700 transition-all hover:border-[#005aeb] hover:text-[#005aeb]"
                        >
                            <span class="text-lg">💬</span>
                            Comunidad
                        </a>
                        <a
                            href="/login"
                            class="font-medium text-gray-700 transition-all duration-200 hover:text-[#005aeb] hover:scale-105"
                            >Iniciar Sesión</a
                        >
                        <a
                            href="/register"
                            class="group relative overflow-hidden rounded-lg bg-[#005aeb] px-6 py-2 font-medium text-white shadow-lg transition-all duration-300 hover:bg-[#0048c4] hover:shadow-xl hover:scale-105"
                        >
                            <span class="relative z-10">Crear Portafolio</span>
                            <div
                                class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-700 group-hover:translate-x-full"
                            ></div>
                        </a>
                    </div>

                    <!-- Menú Móvil -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="rounded-lg p-2 transition-all duration-200 hover:bg-gray-100 md:hidden"
                    >
                        <span class="text-2xl">☰</span>
                    </button>
                </div>

                <!-- Menú Móvil Desplegable -->
                <div
                    v-if="mobileMenuOpen"
                    class="animate-fade-in border-t border-gray-200 py-4 md:hidden"
                >
                    <div class="flex flex-col space-y-4">
                        <a
                            href="#inicio"
                            class="font-medium text-gray-700 transition-colors duration-200 hover:text-[#005aeb]"
                            >Inicio</a
                        >
                        <a
                            href="#beneficios"
                            class="font-medium text-gray-700 transition-colors duration-200 hover:text-[#005aeb]"
                            >Funciones</a
                        >
                        <a
                            href="#planes"
                            class="font-medium text-gray-700 transition-colors duration-200 hover:text-[#005aeb]"
                            >Planes</a
                        >
                        <a
                            href="#testimonios"
                            class="font-medium text-gray-700 transition-colors duration-200 hover:text-[#005aeb]"
                            >Testimonios</a
                        >
                        <a
                            href="#contacto"
                            class="font-medium text-gray-700 transition-colors duration-200 hover:text-[#005aeb]"
                            >Contacto</a
                        >
                        <div class="space-y-3 border-t border-gray-200 pt-4">
                            <a
                                href="/login"
                                class="block font-medium text-gray-700 transition-colors duration-200 hover:text-[#005aeb]"
                                >Iniciar Sesión</a
                            >
                            <a
                                href="/register"
                                class="block rounded-lg bg-[#005aeb] px-6 py-3 text-center font-medium text-white transition-colors duration-200 hover:bg-[#0048c4]"
                            >
                                Crear Portafolio
                            </a>
                        </div>
```
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section con Mesh Gradient -->
        <section
            id="inicio"
            class="relative min-h-screen overflow-hidden bg-gradient-to-br from-[#005aeb]/10 via-white to-[#7B2FF7]/10 pt-32 pb-20"
        >
            <!-- Mesh gradient animado de fondo -->
            <div class="absolute inset-0 opacity-40">
                <div class="mesh-gradient"></div>
            </div>

            <!-- Partículas orb con parallax -->
            <div
                :style="parallaxStyle"
                class="pointer-events-none absolute top-20 left-10 h-96 w-96 animate-float rounded-full bg-gradient-to-br from-[#005aeb]/30 to-[#7B2FF7]/20 blur-3xl"
            ></div>
            <div
                :style="parallaxStyle"
                class="pointer-events-none absolute bottom-20 right-10 h-96 w-96 animate-float-delayed rounded-full bg-gradient-to-br from-[#7B2FF7]/30 to-[#005aeb]/20 blur-3xl"
            ></div>

            <div class="container relative z-10 mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid items-center gap-12 lg:grid-cols-2 min-h-[80vh]">
                    <!-- Texto con animaciones -->
                    <div class="scroll-animate">
                        <h1
                            class="mb-6 text-5xl font-extrabold leading-tight text-gray-900 sm:text-6xl lg:text-7xl"
                        >
                            Crea tu Portafolio
                            <br />
                            Profesional en
                            <span
                                class="relative inline-block">
                                <span
                                    class="bg-gradient-to-r from-[#005aeb] via-[#7B2FF7] to-[#005aeb] bg-clip-text text-transparent animate-gradient-x"
                                    >Minutos con IA</span
                                >
                                <div
                                    class="absolute -bottom-2 left-0 right-0 h-1 bg-gradient-to-r from-[#005aeb] via-[#7B2FF7] to-[#005aeb] animate-gradient-x"
                                ></div>
                            </span>
                        </h1>
                        <p
                            class="mb-8 text-xl leading-relaxed text-gray-700 max-w-xl"
                        >
                            Genera un portafolio moderno, personalizable y listo
                            para compartir, sin conocimientos de diseño. Destaca
                            entre la multitud con ayuda de inteligencia
                            artificial.
                        </p>

                        <!-- Stats inline -->
        <div class="mb-8 flex flex-wrap gap-6">
                            <div
                                v-for="(stat, index) in stats"
                                :key="index"
                                class="stats-section scroll-animate"
                            >
                                <div
                                    class="text-3xl font-bold bg-gradient-to-r from-[#005aeb] to-[#7B2FF7] bg-clip-text text-transparent"
                                >
                                    {{ stat.value.toLocaleString()
                                    }}{{ stat.suffix }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ stat.label }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-4 sm:flex-row">
                            <a
                                href="/register"
                                class="magnetic-button group relative overflow-hidden rounded-xl bg-gradient-to-r from-[#005aeb] to-[#0048c4] px-8 py-4 text-center font-bold text-white shadow-2xl transition-all duration-300 hover:shadow-[0_20px_60px_rgba(0,90,235,0.4)] hover:scale-105"
                            >
                                <span class="relative z-10"
                                    >Crear mi Portafolio Ahora</span
                                >
                                <div
                                    class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/30 to-transparent transition-transform duration-700 group-hover:translate-x-full"
                                ></div>
                            </a>
                            <a
                                href="#ejemplos"
                                class="group rounded-xl border-2 border-gray-300 bg-white/50 backdrop-blur-sm px-8 py-4 text-center font-bold text-gray-900 transition-all duration-300 hover:border-[#005aeb] hover:bg-[#005aeb]/5 hover:scale-105"
                            >
                                Ver Ejemplos
                            </a>
                        </div>
                    </div>

                    <!-- Hero Card con Glassmorphism Premium -->
                    <div class="scroll-animate">
                        <div class="relative">
                            <div
                                class="group transform rounded-3xl bg-white/60 p-8 shadow-[0_8px_32px_rgba(0,0,0,0.12)] backdrop-blur-2xl border border-white/20 transition-all duration-700 hover:scale-105 hover:shadow-[0_20px_60px_rgba(0,90,235,0.3)]"
                            >
                                <div
                                    class="rounded-2xl bg-gradient-to-br from-[#005aeb] via-[#0048c4] to-[#7B2FF7] p-8 text-white shadow-2xl relative overflow-hidden"
                                >
                                    <!-- Pattern overlay -->
                                    <div
                                        class="absolute inset-0 opacity-10"
                                        style="
                                            background-image: radial-gradient(
                                                circle,
                                                white 1px,
                                                transparent 1px
                                            );
                                            background-size: 20px 20px;
                                        "
                                    ></div>

                                    <div
                                        class="relative z-10 mb-6 flex items-start justify-between"
                                    >
                                        <div>
                                            <h3
                                                class="text-3xl font-bold mb-2"
                                            >
                                                María González
                                            </h3>
                                            <p class="text-blue-100">
                                                Diseñadora UX/UI & Product
                                                Designer
                                            </p>
                                        </div>
                                        <div
                                            class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/30 backdrop-blur-md border border-white/20 shadow-lg"
                                        >
                                            <span class="text-3xl">👩‍💻</span>
                                        </div>
                                    </div>
                                    <div
                                        class="relative z-10 mb-6 grid grid-cols-2 gap-4"
                                    >
                                        <div
                                            class="rounded-xl bg-white/20 p-4 text-center backdrop-blur-sm border border-white/20 transition-all duration-300 hover:bg-white/30 hover:scale-105"
                                        >
                                            <p class="text-sm mb-1">
                                                Proyectos
                                            </p>
                                            <p class="text-2xl font-bold">
                                                24+
                                            </p>
                                        </div>
                                        <div
                                            class="rounded-xl bg-white/20 p-4 text-center backdrop-blur-sm border border-white/20 transition-all duration-300 hover:bg-white/30 hover:scale-105"
                                        >
                                            <p class="text-sm mb-1">
                                                Experiencia
                                            </p>
                                            <p class="text-2xl font-bold">
                                                5 años
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="relative z-10 flex flex-wrap gap-2"
                                    >
                                        <span
                                            class="rounded-full bg-white/20 px-4 py-2 text-sm backdrop-blur-sm border border-white/20 transition-all duration-300 hover:bg-white/30"
                                            >UX Design</span
                                        >
                                        <span
                                            class="rounded-full bg-white/20 px-4 py-2 text-sm backdrop-blur-sm border border-white/20 transition-all duration-300 hover:bg-white/30"
                                            >Figma</span
                                        >
                                        <span
                                            class="rounded-full bg-white/20 px-4 py-2 text-sm backdrop-blur-sm border border-white/20 transition-all duration-300 hover:bg-white/30"
                                            >UI/UX</span
                                        >
                                    </div>
                                </div>
                            </div>
                            <!-- Orbs decorativos -->
                            <div
                                class="pointer-events-none absolute -top-6 -right-6 h-32 w-32 animate-pulse rounded-full bg-[#005aeb]/30 blur-2xl"
                            ></div>
                            <div
                                class="pointer-events-none absolute -bottom-6 -left-6 h-32 w-32 animate-pulse rounded-full bg-[#7B2FF7]/30 blur-2xl"
                                style="animation-delay: 1s"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección Beneficios con 3D Tilt -->
        <section id="beneficios" class="relative bg-gray-50 py-24">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="scroll-animate mb-20 text-center">
                    <h2
                        class="mb-6 text-4xl font-extrabold text-gray-900 sm:text-5xl"
                    >
                        Por qué
                        <span
                            class="bg-gradient-to-r from-[#005aeb] to-[#7B2FF7] bg-clip-text text-transparent"
                            >elegir PortafolioAI</span
                        >
                    </h2>
                    <p class="mx-auto max-w-3xl text-xl text-gray-600">
                        Combinamos la potencia de la inteligencia artificial con
                        diseño profesional para crear tu portafolio perfecto.
                    </p>
                </div>

                <!-- Grid 2x2 uniforme -->
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-2">
                    <div
                        v-for="(benefit, index) in benefits"
                        :key="index"
                        class="tilt-card group scroll-animate relative cursor-pointer rounded-3xl bg-white/80 p-8 shadow-lg backdrop-blur-sm border border-gray-100 transition-all duration-500 hover:shadow-2xl"
                    >
                        <!-- Gradient border animado -->
                        <div
                            class="absolute inset-0 rounded-3xl bg-gradient-to-r from-[#005aeb] via-[#7B2FF7] to-[#005aeb] opacity-0 blur-xl transition-opacity duration-500 group-hover:opacity-20 -z-10 animate-gradient-x"
                        ></div>

                        <div
                            class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-[#005aeb]/20 to-[#7B2FF7]/20 text-4xl backdrop-blur-sm transition-all duration-500 group-hover:scale-110 group-hover:shadow-xl"
                        >
                            {{ benefit.icon }}
                        </div>
                        <h3
                            class="mb-4 text-2xl font-bold text-gray-900 transition-colors duration-300 group-hover:text-[#005aeb]"
                        >
                            {{ benefit.title }}
                        </h3>
                        <p class="mb-6 leading-relaxed text-gray-600">
                            {{ benefit.description }}
                        </p>

                        <!-- Mini stat -->
                        <div
                            class="mt-auto pt-4 border-t border-gray-200/50"
                        >
                            <div
                                class="text-3xl font-bold bg-gradient-to-r from-[#005aeb] to-[#7B2FF7] bg-clip-text text-transparent"
                            >
                                {{ benefit.stat }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ benefit.statLabel }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección Cómo Funciona -->
        <section class="relative bg-white py-20">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="scroll-animate mb-16 text-center">
                    <h2
                        class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl"
                    >
                        Cómo funciona
                    </h2>
                    <p class="mx-auto max-w-2xl text-xl text-gray-600">
                        Crea tu portafolio profesional en solo 3 sencillos pasos
                    </p>
                </div>

                <div class="relative grid gap-8 md:grid-cols-3">
                    <!-- Línea conectora (solo en desktop) -->
                    <div
                        class="pointer-events-none absolute top-10 left-0 hidden h-1 w-full md:block"
                    >
                        <div
                            class="h-full w-2/3 bg-gradient-to-r from-[#005aeb] via-[#7B2FF7] to-transparent opacity-20"
                            style="margin-left: 16.66%"
                        ></div>
                    </div>

                    <div
                        v-for="(step, index) in steps"
                        :key="index"
                        class="scroll-animate relative text-center"
                    >
                        <div class="relative mb-6">
                            <!-- Badge con número -->
                            <div
                                class="relative mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-[#005aeb] to-[#0048c4] text-2xl font-bold text-white shadow-lg"
                            >
                                <span class="relative z-10">{{
                                    step.step
                                }}</span>
                                <!-- Pulse effect -->
                                <div
                                    class="absolute inset-0 animate-ping rounded-full bg-[#005aeb] opacity-20"
                                ></div>
                            </div>
                            <div class="mb-4 text-6xl">{{ step.image }}</div>
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-gray-900">
                            {{ step.title }}
                        </h3>
                        <p class="leading-relaxed text-gray-600">
                            {{ step.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección Testimonios con Carousel -->
        <section id="testimonios" class="relative bg-white py-24 overflow-hidden">
            <!-- Background decoration -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#005aeb]/5 to-[#7B2FF7]/5 opacity-50"></div>
            
            <div class="container relative z-10 mx-auto px-4 sm:px-6 lg:px-8">
                <div class="scroll-animate mb-20 text-center">
                    <h2
                        class="mb-6 text-4xl font-extrabold text-gray-900 sm:text-5xl"
                    >
                        Lo que dicen
                        <span class="bg-gradient-to-r from-[#005aeb] to-[#7B2FF7] bg-clip-text text-transparent">nuestros usuarios</span>
                    </h2>
                    <p class="mx-auto max-w-3xl text-xl text-gray-600">
                        Miles de profesionales ya han transformado su presencia
                        digital con PortafolioAI
                    </p>
                </div>

                <!-- Carousel Container -->
                <div class="relative max-w-4xl mx-auto">
                    <!-- Card Container -->
                    <div class="relative min-h-[400px] flex items-center justify-center">
                        <TransitionGroup name="carousel">
                            <div
                                v-for="(testimonial, index) in testimonials"
                                v-show="index === currentTestimonial"
                                :key="index"
                                class="absolute w-full"
                            >
                                <div
                                    @mouseenter="stopAutoPlay"
                                    @mouseleave="startAutoPlay"
                                    class="group transform rounded-3xl bg-white/90 backdrop-blur-xl p-10 shadow-[0_20px_70px_rgba(0,90,235,0.15)] border-2 border-gray-100 hover:border-[#005aeb]/30 transition-all duration-500 hover:shadow-[0_30px_90px_rgba(0,90,235,0.25)] hover:scale-105"
                                >
                                    <!-- Quote decorativo grande -->
                                    <div
                                        class="absolute -top-6 -left-6 text-9xl text-[#005aeb]/10 font-serif leading-none transition-all duration-500 group-hover:text-[#005aeb]/20 group-hover:scale-110"
                                    >
                                        "
                                    </div>

                                    <div class="relative z-10">
                                        <!-- Avatar y info -->
                                        <div class="mb-6 flex items-center gap-4">
                                            <div
                                                class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-[#005aeb]/20 to-[#7B2FF7]/20 text-4xl shadow-lg ring-4 ring-white transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6"
                                            >
                                                {{ testimonial.avatar }}
                                            </div>
                                            <div>
                                                <h4
                                                    class="text-2xl font-bold text-gray-900"
                                                >
                                                    {{ testimonial.name }}
                                                </h4>
                                                <p class="text-gray-600">
                                                    {{ testimonial.role }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Rating con estrellas -->
                                        <div class="mb-6 flex gap-1">
                                            <span
                                                v-for="star in testimonial.rating"
                                                :key="star"
                                                class="text-2xl text-yellow-400 transition-transform duration-300 hover:scale-125"
                                                >★</span
                                            >
                                        </div>

                                        <!-- Contenido -->
                                        <p
                                            class="text-xl leading-relaxed text-gray-700 italic"
                                        >
                                            "{{ testimonial.content }}"
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </TransitionGroup>
                    </div>

                    <!-- Navigation Controls -->
                    <div class="mt-12 flex items-center justify-center gap-6">
                        <button
                            @click="prevTestimonial"
                            class="group flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-lg border-2 border-gray-200 transition-all duration-300 hover:shadow-xl hover:scale-110 hover:border-[#005aeb] hover:bg-[#005aeb]/5"
                        >
                            <svg
                                class="h-6 w-6 text-gray-700 transition-colors group-hover:text-[#005aeb]"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 19l-7-7 7-7"
                                ></path>
                            </svg>
                        </button>

                        <!-- Indicators -->
                        <div class="flex gap-3">
                            <button
                                v-for="(testimonial, index) in testimonials"
                                :key="index"
                                @click="goToTestimonial(index)"
                                class="h-3 rounded-full transition-all duration-300"
                                :class="
                                    index === currentTestimonial
                                        ? 'w-12 bg-gradient-to-r from-[#005aeb] to-[#7B2FF7]'
                                        : 'w-3 bg-gray-300 hover:bg-gray-400'
                                "
                            ></button>
                        </div>

                        <button
                            @click="nextTestimonial"
                            class="group flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-lg border-2 border-gray-200 transition-all duration-300 hover:shadow-xl hover:scale-110 hover:border-[#005aeb] hover:bg-[#005aeb]/5"
                        >
                            <svg
                                class="h-6 w-6 text-gray-700 transition-colors group-hover:text-[#005aeb]"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7"
                                ></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección Planes -->
        <section id="planes" class="bg-white py-20">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="scroll-animate mb-16 text-center">
                    <h2
                        class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl"
                    >
                        Elige tu plan perfecto
                    </h2>
                    <p class="mx-auto max-w-2xl text-xl text-gray-600">
                        Desde un portafolio básico hasta soluciones
                        empresariales completas
                    </p>
                </div>

                <div class="mx-auto grid max-w-5xl gap-8 md:grid-cols-3">
                    <div
                        v-for="(plan, index) in plans"
                        :key="index"
                        class="scroll-animate group relative rounded-2xl border-2 bg-white p-8 shadow-md transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl"
                        :class="
                            plan.popular
                                ? 'scale-105 border-[#005aeb] ring-4 ring-[#005aeb]/10'
                                : 'border-gray-100'
                        "
                    >
                        <!-- Glow effect para plan popular -->
                        <div
                            v-if="plan.popular"
                            class="pointer-events-none absolute inset-0 -z-10 rounded-2xl bg-gradient-to-br from-[#005aeb]/20 to-[#7B2FF7]/20 opacity-0 blur-xl transition-opacity duration-500 group-hover:opacity-100"
                        ></div>

                        <!-- Badge Popular mejorado -->
                        <div
                            v-if="plan.popular"
                            class="absolute -top-3 left-1/2 -translate-x-1/2 transform rounded-full bg-gradient-to-r from-[#005aeb] to-[#7B2FF7] px-4 py-1 text-sm font-medium text-white shadow-lg animate-pulse-glow"
                        >
                            Más Popular
                        </div>

                        <div class="mb-6 text-center">
                            <h3 class="mb-2 text-2xl font-bold text-gray-900">
                                {{ plan.name }}
                            </h3>
                            <div
                                class="mb-4 flex items-baseline justify-center"
                            >
                                <span
                                    class="bg-gradient-to-r from-[#005aeb] to-[#7B2FF7] bg-clip-text text-4xl font-bold text-transparent"
                                    >{{ plan.price }}</span
                                >
                                <span class="ml-2 text-gray-600"
                                    >/{{ plan.period }}</span
                                >
                            </div>
                        </div>

                        <ul class="mb-8 space-y-4">
                            <li
                                v-for="(feature, featureIndex) in plan.features"
                                :key="featureIndex"
                                class="flex items-center text-gray-600"
                            >
                                <!-- Checkmark SVG moderno -->
                                <svg
                                    class="mr-3 h-5 w-5 flex-shrink-0 text-green-500"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                {{ feature }}
                            </li>
                        </ul>

                        <button
                            class="group/btn relative w-full overflow-hidden rounded-lg px-6 py-3 font-semibold transition-all duration-300"
                            :class="
                                plan.popular
                                    ? 'bg-[#005aeb] text-white shadow-lg hover:bg-[#0048c4] hover:shadow-xl hover:scale-105'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:scale-105'
                            "
                        >
                            <span class="relative z-10">{{ plan.cta }}</span>
                            <div
                                v-if="plan.popular"
                                class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-700 group-hover/btn:translate-x-full"
                            ></div>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Final -->
        <section
            class="relative overflow-hidden bg-gradient-to-br from-[#005aeb] to-[#0048c4] py-20"
        >
            <!-- Pattern decorativo -->
            <div
                class="pointer-events-none absolute inset-0 opacity-10"
                style="
                    background-image: radial-gradient(
                        circle,
                        white 1px,
                        transparent 1px
                    );
                    background-size: 30px 30px;
                "
            ></div>

            <div
                class="container relative z-10 mx-auto px-4 text-center sm:px-6 lg:px-8"
            >
                <div class="scroll-animate mx-auto max-w-3xl">
                    <h2 class="mb-6 text-3xl font-bold text-white sm:text-4xl">
                        Empieza a construir tu marca personal hoy
                    </h2>
                    <p class="mb-8 text-xl text-blue-100">
                        Únete a miles de profesionales que ya están mostrando su
                        trabajo al mundo
                    </p>
                    <a
                        href="/register"
                        class="group inline-block overflow-hidden rounded-lg bg-white px-8 py-4 font-semibold text-[#005aeb] shadow-2xl transition-all duration-300 hover:bg-gray-100 hover:shadow-2xl hover:scale-110"
                    >
                        <span class="relative z-10"
                            >Crear Portafolio con IA</span
                        >
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer id="contacto" class="bg-gray-900 py-12 text-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid gap-8 md:grid-cols-4">
                    <!-- Logo y descripción -->
                    <div class="md:col-span-2">
                        <div class="mb-4 flex items-center space-x-2">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-[#005aeb] to-[#0048c4]"
                            >
                                <span class="text-sm font-bold text-white"
                                    >P</span
                                >
                            </div>
                            <span class="text-xl font-bold">PortafolioAI</span>
                        </div>
                        <p class="mb-4 max-w-md text-gray-400">
                            Creamos portafolios profesionales con inteligencia
                            artificial para que destaquen tu talento y consigas
                            las mejores oportunidades.
                        </p>
                        <div class="flex space-x-4">
                            <a
                                href="#"
                                class="text-2xl text-gray-400 transition-all duration-200 hover:scale-125 hover:text-white"
                                >📘</a
                            >
                            <a
                                href="#"
                                class="text-2xl text-gray-400 transition-all duration-200 hover:scale-125 hover:text-white"
                                >🐦</a
                            >
                            <a
                                href="#"
                                class="text-2xl text-gray-400 transition-all duration-200 hover:scale-125 hover:text-white"
                                >📷</a
                            >
                            <a
                                href="#"
                                class="text-2xl text-gray-400 transition-all duration-200 hover:scale-125 hover:text-white"
                                >💼</a
                            >
                        </div>
                    </div>

                    <!-- Enlaces rápidos -->
                    <div>
                        <h3 class="mb-4 font-semibold">Enlaces Rápidos</h3>
                        <ul class="space-y-2">
                            <li>
                                <a
                                    href="#inicio"
                                    class="text-gray-400 transition-colors duration-200 hover:text-white"
                                    >Inicio</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#beneficios"
                                    class="text-gray-400 transition-colors duration-200 hover:text-white"
                                    >Funciones</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#planes"
                                    class="text-gray-400 transition-colors duration-200 hover:text-white"
                                    >Planes</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#testimonios"
                                    class="text-gray-400 transition-colors duration-200 hover:text-white"
                                    >Testimonios</a
                                >
                            </li>
                        </ul>
                    </div>

                    <!-- Contacto -->
                    <div>
                        <h3 class="mb-4 font-semibold">Contacto</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li>📧 hola@portafolioai.com</li>
                            <li>📞 +1 (555) 123-4567</li>
                            <li>📍 Ciudad, País</li>
                        </ul>
                    </div>
                </div>

                <div
                    class="mt-8 border-t border-gray-800 pt-8 text-center text-gray-400"
                >
                    <p>
                        &copy; 2024 PortafolioAI. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* ========== ULTRA-PREMIUM ANIMATIONS ========== */

/* Scroll Fade Animations */
.scroll-animate {
    opacity: 0;
    transform: translateY(40px);
    transition:
        opacity 1s cubic-bezier(0.16, 1, 0.3, 1),
        transform 1s cubic-bezier(0.16, 1, 0.3, 1);
}

.scroll-animate.animate-fade-in {
    opacity: 1;
    transform: translateY(0);
}

/* Mesh Gradient Background */
.mesh-gradient {
    width: 100%;
    height: 100%;
    background:
        radial-gradient(at 0% 0%, rgba(0, 90, 235, 0.3) 0px, transparent 50%),
        radial-gradient(at 50% 50%, rgba(123, 47, 247, 0.2) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(0, 90, 235, 0.25) 0px, transparent 50%);
    animation: mesh-move 15s ease-in-out infinite;
}

@keyframes mesh-move {
    0%, 100% {
        transform: scale(1) translateY(0);
    }
    50% {
        transform: scale(1.05) translateY(-10px);
    }
}

/* Gradient Animation Horizontal */
@keyframes gradient-x {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

.animate-gradient-x {
    background-size: 200% 200%;
    animation: gradient-x 3s ease infinite;
}

/* Floating Particles */
@keyframes float {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
    }
    33% {
        transform: translateY(-30px) rotate(5deg);
    }
    66% {
        transform: translateY(-15px) rotate(-5deg);
    }
}

.animate-float {
    animation: float 8s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float 10s ease-in-out infinite 2s;
}

/* Pulse Glow Effect */
@keyframes pulse-glow {
    0%, 100% {
        box-shadow:
            0 0 15px rgba(0, 90, 235, 0.4),
            0 0 30px rgba(123, 47, 247, 0.2);
    }
    50% {
        box-shadow:
            0 0 25px rgba(0, 90, 235, 0.6),
            0 0 50px rgba(123, 47, 247, 0.4);
    }
}

.animate-pulse-glow {
    animation: pulse-glow 2s ease-in-out infinite;
}

/* Carousel Transitions */
.carousel-enter-active,
.carousel-leave-active {
    transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.carousel-enter-from {
    opacity: 0;
    transform: translateX(100px) scale(0.95);
}

.carousel-leave-to {
    opacity: 0;
    transform: translateX(-100px) scale(0.95);
}

/* 3D Tilt Cards */
.tilt-card {
    transform-style: preserve-3d;
    transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Magnetic Button Effect (preparar para JS) */
.magnetic-button {
    position: relative;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Levitation Effect */
@keyframes levitate {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-8px);
    }
}

.animate-levitate {
    animation: levitate 3s ease-in-out infinite;
}

/* Shimmer Effect */
@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

/* ========== UTILITY CLASSES ========== */

/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
}

/* Container */
.container {
    max-width: 1200px;
}

/* Cursor */
button,
a {
    cursor: pointer;
}

/* Backdrop Blur Support */
@supports (backdrop-filter: blur(10px)) {
    .backdrop-blur-2xl {
        backdrop-filter: blur(40px);
    }
    .backdrop-blur-xl {
        backdrop-filter: blur(24px);
    }
    .backdrop-blur-md {
        backdrop-filter: blur(12px);
    }
    .backdrop-blur-sm {
        backdrop-filter: blur(6px);
    }
}

/* Prevent Layout Shift */
.min-h-\[400px\] {
    min-height: 400px;
}

.min-h-\[80vh\] {
    min-height: 80vh;
}

/* Premium Shadow Levels */
.shadow-premium {
    box-shadow:
        0 10px 40px -10px rgba(0, 90, 235, 0.2),
        0 0 0 1px rgba(0, 90, 235, 0.05);
}

.shadow-premium-lg {
    box-shadow:
        0 20px 60px -15px rgba(0, 90, 235, 0.3),
        0 0 0 1px rgba(0, 90, 235, 0.1);
}

/* Performance Optimizations */
.tilt-card,
.animate-float,
.carousel-enter-active,
.carousel-leave-active {
    will-change: transform;
}

.scroll-animate {
    will-change: opacity, transform;
}
</style>
