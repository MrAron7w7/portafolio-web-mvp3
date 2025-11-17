<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

// Estado para el menú móvil
const mobileMenuOpen = ref(false);

// Datos de beneficios
const benefits = ref([
    {
        icon: '🤖',
        title: 'Diseño Automático con IA',
        description:
            'Nuestra inteligencia artificial crea diseños únicos y profesionales basados en tu perfil.',
    },
    {
        icon: '🎨',
        title: 'Plantillas Profesionales',
        description:
            'Elige entre múltiples plantillas modernas y adaptables a cualquier industria.',
    },
    {
        icon: '🚀',
        title: 'Exportación y Hosting',
        description:
            'Publica tu portafolio al instante con hosting incluido y dominio personalizable.',
    },
    {
        icon: '⚙️',
        title: 'Personalización Total',
        description:
            'Ajusta colores, fuentes y disposición para que refleje tu estilo personal.',
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
    },
    {
        name: 'Carlos Rodríguez',
        role: 'Desarrollador Full Stack',
        content:
            'En 15 minutos tenía un portafolio que normalmente me hubiera tomado días diseñar. Totalmente recomendado.',
        avatar: '👨‍💻',
    },
    {
        name: 'Ana Martínez',
        role: 'Marketing Digital',
        content:
            'El proceso fue tan sencillo y el resultado superó mis expectativas. He conseguido 3 entrevistas gracias a mi nuevo portafolio.',
        avatar: '👩‍🎓',
    },
]);

// Datos de planes
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

// Efectos de scroll suave
onMounted(() => {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px',
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.scroll-animate').forEach((el) => {
        observer.observe(el);
    });
});
</script>

<template>
    <Head title="Inicio" />
    <div class="min-h-screen bg-white font-sans">
        <!-- Header -->
        <header
            class="fixed z-50 w-full bg-white/95 shadow-sm backdrop-blur-sm transition-all duration-300"
        >
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-4">
                    <!-- Logo -->
                    <div class="flex items-center space-x-2">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#005aeb]"
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
                    </nav>

                    <!-- Botones Desktop -->
                    <div class="hidden items-center space-x-4 md:flex">
                        <a
                            href="/login"
                            class="font-medium text-gray-700 transition-colors duration-200 hover:text-[#005aeb]"
                            >Iniciar Sesión</a
                        >
                        <a
                            href="/register"
                            class="rounded-lg bg-[#005aeb] px-6 py-2 font-medium text-white shadow-sm transition-colors duration-200 hover:bg-[#0048c4]"
                        >
                            Crear Portafolio
                        </a>
                    </div>

                    <!-- Menú Móvil -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="rounded-lg p-2 transition-colors duration-200 hover:bg-gray-100 md:hidden"
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
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section
            id="inicio"
            class="bg-gradient-to-br from-[#005aeb]/5 to-white pt-32 pb-20"
        >
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <!-- Texto -->
                    <div class="scroll-animate">
                        <h1
                            class="mb-6 text-4xl leading-tight font-bold text-gray-900 sm:text-5xl lg:text-6xl"
                        >
                            Crea tu Portafolio Profesional en Minutos con
                            <span class="text-[#005aeb]">IA</span>
                        </h1>
                        <p class="mb-8 text-xl leading-relaxed text-gray-600">
                            Genera un portafolio moderno, personalizable y listo
                            para compartir, sin conocimientos de diseño. Destaca
                            entre la multitud con ayuda de inteligencia
                            artificial.
                        </p>
                        <div class="flex flex-col gap-4 sm:flex-row">
                            <a
                                href="/register"
                                class="rounded-lg bg-[#005aeb] px-8 py-4 text-center font-semibold text-white shadow-lg transition-all duration-200 hover:bg-[#0048c4] hover:shadow-xl"
                            >
                                Crear mi Portafolio Ahora
                            </a>
                            <a
                                href="#ejemplos"
                                class="rounded-lg border-2 border-gray-300 px-8 py-4 text-center font-semibold text-gray-700 transition-all duration-200 hover:border-[#005aeb] hover:text-[#005aeb]"
                            >
                                Ver Ejemplos
                            </a>
                        </div>
                    </div>

                    <!-- Imagen Hero -->
                    <div class="scroll-animate">
                        <div class="relative">
                            <div
                                class="rotate-2 transform rounded-2xl bg-white p-6 shadow-2xl transition-transform duration-500 hover:rotate-0"
                            >
                                <div
                                    class="rounded-xl bg-gradient-to-br from-[#005aeb] to-[#7B2FF7] p-8 text-white"
                                >
                                    <div
                                        class="mb-6 flex items-start justify-between"
                                    >
                                        <div>
                                            <h3 class="text-2xl font-bold">
                                                María González
                                            </h3>
                                            <p class="text-blue-100">
                                                Diseñadora UX/UI & Product
                                                Designer
                                            </p>
                                        </div>
                                        <div
                                            class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20"
                                        >
                                            <span class="text-2xl">👩‍💻</span>
                                        </div>
                                    </div>
                                    <div class="mb-6 grid grid-cols-2 gap-4">
                                        <div
                                            class="rounded-lg bg-white/20 p-3 text-center"
                                        >
                                            <p class="text-sm">Proyectos</p>
                                            <p class="text-xl font-bold">24+</p>
                                        </div>
                                        <div
                                            class="rounded-lg bg-white/20 p-3 text-center"
                                        >
                                            <p class="text-sm">Experiencia</p>
                                            <p class="text-xl font-bold">
                                                5 años
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <span
                                            class="rounded-full bg-white/20 px-3 py-1 text-sm"
                                            >UX Design</span
                                        >
                                        <span
                                            class="rounded-full bg-white/20 px-3 py-1 text-sm"
                                            >Figma</span
                                        >
                                        <span
                                            class="rounded-full bg-white/20 px-3 py-1 text-sm"
                                            >UI/UX</span
                                        >
                                    </div>
                                </div>
                            </div>
                            <!-- Elementos decorativos -->
                            <div
                                class="absolute -top-4 -right-4 h-24 w-24 rounded-full bg-[#005aeb]/10 blur-xl"
                            ></div>
                            <div
                                class="absolute -bottom-4 -left-4 h-32 w-32 rounded-full bg-[#7B2FF7]/10 blur-xl"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección Beneficios -->
        <section id="beneficios" class="bg-gray-50 py-20">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="scroll-animate mb-16 text-center">
                    <h2
                        class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl"
                    >
                        Por qué elegir PortafolioAI
                    </h2>
                    <p class="mx-auto max-w-2xl text-xl text-gray-600">
                        Combinamos la potencia de la inteligencia artificial con
                        diseño profesional para crear tu portafolio perfecto.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="(benefit, index) in benefits"
                        :key="index"
                        class="scroll-animate rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition-all duration-300 hover:shadow-lg"
                    >
                        <div class="mb-4 text-4xl">{{ benefit.icon }}</div>
                        <h3 class="mb-3 text-xl font-semibold text-gray-900">
                            {{ benefit.title }}
                        </h3>
                        <p class="leading-relaxed text-gray-600">
                            {{ benefit.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección Cómo Funciona -->
        <section class="bg-white py-20">
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

                <div class="grid gap-8 md:grid-cols-3">
                    <div
                        v-for="(step, index) in steps"
                        :key="index"
                        class="scroll-animate text-center"
                    >
                        <div class="relative mb-6">
                            <div
                                class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-[#005aeb] text-2xl font-bold text-white"
                            >
                                {{ step.step }}
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

        <!-- Sección Testimonios -->
        <section id="testimonios" class="bg-gray-50 py-20">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="scroll-animate mb-16 text-center">
                    <h2
                        class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl"
                    >
                        Lo que dicen nuestros usuarios
                    </h2>
                    <p class="mx-auto max-w-2xl text-xl text-gray-600">
                        Miles de profesionales ya han transformado su presencia
                        digital con PortafolioAI
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-3">
                    <div
                        v-for="(testimonial, index) in testimonials"
                        :key="index"
                        class="scroll-animate rounded-2xl bg-white p-6 shadow-sm transition-all duration-300 hover:shadow-lg"
                    >
                        <div class="mb-4 flex items-center">
                            <div class="mr-4 text-3xl">
                                {{ testimonial.avatar }}
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">
                                    {{ testimonial.name }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    {{ testimonial.role }}
                                </p>
                            </div>
                        </div>
                        <p class="text-gray-700 italic">
                            "{{ testimonial.content }}"
                        </p>
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
                        class="scroll-animate relative rounded-2xl border-2 bg-white p-8 shadow-sm transition-all duration-300 hover:shadow-lg"
                        :class="
                            plan.popular
                                ? 'scale-105 border-[#005aeb]'
                                : 'border-gray-100'
                        "
                    >
                        <!-- Badge Popular -->
                        <div
                            v-if="plan.popular"
                            class="absolute -top-3 left-1/2 -translate-x-1/2 transform rounded-full bg-[#005aeb] px-4 py-1 text-sm font-medium text-white"
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
                                    class="text-4xl font-bold text-gray-900"
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
                                <span
                                    class="mr-3 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-green-100"
                                >
                                    <span
                                        class="h-2 w-2 rounded-full bg-green-500"
                                    ></span>
                                </span>
                                {{ feature }}
                            </li>
                        </ul>

                        <button
                            class="w-full rounded-lg px-6 py-3 font-semibold transition-all duration-200"
                            :class="
                                plan.popular
                                    ? 'bg-[#005aeb] text-white shadow-lg hover:bg-[#0048c4]'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            "
                        >
                            {{ plan.cta }}
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Final -->
        <section class="bg-[#005aeb] py-20">
            <div class="container mx-auto px-4 text-center sm:px-6 lg:px-8">
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
                        class="inline-block rounded-lg bg-white px-8 py-4 font-semibold text-[#005aeb] shadow-lg transition-all duration-200 hover:bg-gray-100 hover:shadow-xl"
                    >
                        Crear Portafolio con IA
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
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#005aeb]"
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
                                class="text-gray-400 transition-colors duration-200 hover:text-white"
                                >📘</a
                            >
                            <a
                                href="#"
                                class="text-gray-400 transition-colors duration-200 hover:text-white"
                                >🐦</a
                            >
                            <a
                                href="#"
                                class="text-gray-400 transition-colors duration-200 hover:text-white"
                                >📷</a
                            >
                            <a
                                href="#"
                                class="text-gray-400 transition-colors duration-200 hover:text-white"
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
/* Animaciones personalizadas */
.scroll-animate {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease-out;
}

.scroll-animate.animate-fade-in {
    opacity: 1;
    transform: translateY(0);
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Estilos personalizados para mejor visualización */
.container {
    max-width: 1200px;
}
</style>
