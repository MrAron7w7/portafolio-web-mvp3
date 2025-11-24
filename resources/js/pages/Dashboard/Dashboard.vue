<script setup lang="ts">
// // // // import dashboard from '@/routes/dashboard';
import { Link } from '@inertiajs/vue3';
import {
    Bell,
    Calendar,
    ChevronDown,
    Download,
    Edit,
    Eye,
    Folder,
    Menu,
    Plus,
    Search,
    Trash2,
} from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

// Estado del sidebar en móvil
const sidebarOpen = ref(false);

// Datos del usuario
const user = ref({
    name: 'Arón',
    email: 'aron@ejemplo.com',
    avatar: 'A',
});

// Métricas del dashboard
const metrics = ref([
    {
        title: 'Portafolios creados',
        value: '3',
        icon: Folder,
        color: 'bg-blue-500',
    },
]);

// Portafolios del usuario
const portfolios = ref([
    {
        id: 1,
        title: 'Portafolio Personal',
        description: 'Mi portafolio profesional como diseñador UX/UI',
        image: 'Design',
        createdAt: '15 Ene 2024',
        status: 'publicado',
        statusColor: 'bg-green-100 text-green-800',
        views: 124,
        progress: 100,
    },
    {
        id: 2,
        title: 'Proyecto Freelance',
        description: 'Trabajos de diseño para clientes independientes',
        image: 'Work',
        createdAt: '10 Ene 2024',
        status: 'finalizado',
        statusColor: 'bg-blue-100 text-blue-800',
        views: 67,
        progress: 100,
    },
    {
        id: 3,
        title: 'Portafolio Académico',
        description: 'Proyectos universitarios y de investigación',
        image: 'Book',
        createdAt: '05 Ene 2024',
        status: 'borrador',
        statusColor: 'bg-gray-100 text-gray-800',
        views: 0,
        progress: 65,
    },
]);

// Estado vacío para probar
const hasPortfolios = ref(true);

// Función para eliminar portafolio
const deletePortfolio = (portfolioId: number) => {
    portfolios.value = portfolios.value.filter((p) => p.id !== portfolioId);
};

// Cerrar sidebar en móvil al hacer click fuera
onMounted(() => {
    document.addEventListener('click', (e) => {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('.sidebar-toggle');

        if (
            sidebarOpen.value &&
            !sidebar?.contains(e.target as Node) &&
            !toggleBtn?.contains(e.target as Node)
        ) {
            sidebarOpen.value = false;
        }
    });
});
</script>

<template>
    <div class="flex min-h-screen bg-gray-50/50">
        <!-- Sidebar Overlay para móvil -->
        <div
            v-if="sidebarOpen"
            class="bg-opacity-50 fixed inset-0 z-40 bg-black lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <!-- Contenido principal -->
        <div class="flex min-w-0 flex-1 flex-col lg:ml-0">
            <!-- Header -->
            <header
                class="sticky top-0 z-30 border-b border-gray-200/60 bg-white/95 backdrop-blur-sm"
            >
                <div class="flex items-center justify-between p-4">
                    <!-- Botón menú móvil y buscador -->
                    <div class="flex flex-1 items-center space-x-4">
                        <button
                            @click="sidebarOpen = true"
                            class="sidebar-toggle rounded-lg p-2 transition-colors duration-200 hover:bg-gray-100 lg:hidden"
                        >
                            <Menu class="h-5 w-5" />
                        </button>

                        <!-- Buscador -->
                        <div class="relative max-w-md flex-1">
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                            >
                                <Search class="h-4 w-4 text-gray-400" />
                            </div>
                            <input
                                type="text"
                                placeholder="Buscar portafolios..."
                                class="w-full rounded-lg border border-gray-300 bg-white/50 py-2.5 pr-4 pl-10 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                            />
                        </div>
                    </div>

                    <!-- Iconos derecha -->
                    <div class="flex items-center space-x-3">
                        <!-- Notificaciones -->
                        <button
                            class="relative rounded-lg p-2.5 transition-colors duration-200 hover:bg-gray-100"
                        >
                            <Bell class="h-5 w-5 text-gray-600" />
                            <span
                                class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full border-2 border-white bg-red-500 text-xs text-white"
                                >3</span
                            >
                        </button>

                        <!-- Perfil -->
                        <div class="relative">
                            <button
                                class="flex items-center space-x-3 rounded-lg p-2 transition-colors duration-200 hover:bg-gray-100"
                            >
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-[#005aeb] to-[#7B2FF7] text-sm font-semibold text-white"
                                >
                                    {{ user.avatar }}
                                </div>
                                <span
                                    class="hidden text-sm font-medium text-gray-700 sm:block"
                                    >{{ user.name }}</span
                                >
                                <ChevronDown class="h-4 w-4 text-gray-400" />
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Contenido del dashboard -->
            <main class="flex-1 p-6">
                <!-- Header del contenido -->
                <div
                    class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between"
                >
                    <div class="flex-1">
                        <h1
                            class="mb-2 text-2xl font-bold text-gray-900 lg:text-3xl"
                        >
                            Hola {{ user.name }} 👋, bienvenido a tu panel de
                            portafolios
                        </h1>
                        <p class="text-lg text-gray-600">
                            Gestiona, crea o edita tus portafolios
                            profesionales.
                        </p>
                    </div>

                    <!-- Botón crear portafolios -->
                    <!-- <Link :href="dashboard.template.url()">
                        <button
                            class="mt-4 flex items-center space-x-2 rounded-lg bg-[#005aeb] px-6 py-3 font-semibold whitespace-nowrap text-white shadow-sm transition-all duration-200 hover:bg-[#0048c4] hover:shadow-md lg:mt-0"
                        >
                            <Plus class="h-5 w-5" />
                            <span>Crear portafolio con IA</span>
                        </button>
                    </Link> -->
                </div>

                <!-- Métricas rápidas -->
                <div
                    class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <div
                        v-for="metric in metrics"
                        :key="metric.title"
                        class="group rounded-xl border border-gray-200/60 bg-white p-5 shadow-xs transition-all duration-200 hover:shadow-md"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ metric.value }}
                                </p>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ metric.title }}
                                </p>
                            </div>
                            <div
                                :class="[
                                    metric.color,
                                    'flex h-12 w-12 items-center justify-center rounded-xl text-white transition-transform duration-200 group-hover:scale-110',
                                ]"
                            >
                                <component :is="metric.icon" class="h-6 w-6" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de portafolios -->
                <section>
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Mis Portafolios
                        </h2>
                        <p class="text-sm text-gray-600">
                            {{ portfolios.length }} portafolio(s)
                        </p>
                    </div>

                    <!-- Estado vacío -->
                    <div
                        v-if="!hasPortfolios"
                        class="rounded-2xl border-2 border-dashed border-gray-300/60 bg-white p-12 text-center"
                    >
                        <div class="mx-auto max-w-md">
                            <div
                                class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-[#005aeb] to-[#7B2FF7]"
                            >
                                <Folder class="h-10 w-10 text-white" />
                            </div>
                            <h3 class="mb-4 text-2xl font-bold text-gray-900">
                                Todavía no has creado ningún portafolio.
                            </h3>
                            <p class="mb-8 text-gray-600">
                                Comienza creando tu primer portafolio
                                profesional con nuestra inteligencia artificial.
                            </p>
                        </div>
                    </div>

                    <!-- Grid de portafolios -->
                    <div
                        v-else
                        class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3"
                    >
                        <div
                            v-for="portfolio in portfolios"
                            :key="portfolio.id"
                            class="group overflow-hidden rounded-2xl border border-gray-200/60 bg-white shadow-xs transition-all duration-300 hover:border-gray-300 hover:shadow-lg"
                        >
                            <!-- Preview del portafolio -->
                            <div
                                class="relative h-48 overflow-hidden bg-gradient-to-br from-[#005aeb] to-[#7B2FF7]"
                            >
                                <div
                                    class="absolute inset-0 flex items-center justify-center"
                                >
                                    <div
                                        class="text-4xl font-semibold text-white opacity-90"
                                    >
                                        {{ portfolio.image.charAt(0) }}
                                    </div>
                                </div>
                                <!-- Badge de estado -->
                                <div class="absolute top-4 right-4">
                                    <span
                                        :class="[
                                            portfolio.statusColor,
                                            'rounded-full border border-white/20 px-3 py-1.5 text-xs font-medium capitalize',
                                        ]"
                                    >
                                        {{ portfolio.status }}
                                    </span>
                                </div>
                                <!-- Progress bar para borradores -->
                                <div
                                    v-if="portfolio.status === 'borrador'"
                                    class="absolute right-0 bottom-0 left-0 h-1.5 bg-gray-200/50"
                                >
                                    <div
                                        class="h-1.5 bg-white transition-all duration-500"
                                        :style="{
                                            width: portfolio.progress + '%',
                                        }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Contenido de la tarjeta -->
                            <div class="p-6">
                                <div
                                    class="mb-3 flex items-start justify-between"
                                >
                                    <div class="min-w-0 flex-1">
                                        <h3
                                            class="mb-1 truncate text-lg font-semibold text-gray-900"
                                        >
                                            {{ portfolio.title }}
                                        </h3>
                                        <p
                                            class="mb-2 line-clamp-2 text-sm leading-relaxed text-gray-600"
                                        >
                                            {{ portfolio.description }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Información adicional -->
                                <div
                                    class="mb-4 flex items-center justify-between text-sm text-gray-500"
                                >
                                    <div class="flex items-center space-x-1">
                                        <Calendar class="h-4 w-4" />
                                        <span>{{ portfolio.createdAt }}</span>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div
                                    class="flex items-center justify-between border-t border-gray-100 pt-4"
                                >
                                    <div class="flex space-x-1">
                                        <button
                                            class="rounded-lg p-2 text-gray-400 transition-colors duration-200 hover:bg-blue-50 hover:text-[#005aeb]"
                                            title="Editar"
                                        >
                                            <Edit class="h-4 w-4" />
                                        </button>
                                        <button
                                            class="rounded-lg p-2 text-gray-400 transition-colors duration-200 hover:bg-green-50 hover:text-green-600"
                                            title="Previsualizar"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </button>
                                        <button
                                            class="rounded-lg p-2 text-gray-400 transition-colors duration-200 hover:bg-purple-50 hover:text-purple-600"
                                            title="Descargar"
                                        >
                                            <Download class="h-4 w-4" />
                                        </button>
                                    </div>
                                    <button
                                        @click="deletePortfolio(portfolio.id)"
                                        class="rounded-lg p-2 text-gray-400 transition-colors duration-200 hover:bg-red-50 hover:text-red-600"
                                        title="Eliminar"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Transiciones suaves */
.sidebar {
    transition: transform 0.3s ease-in-out;
}

/* Mejoras de scroll */
main {
    scroll-behavior: smooth;
}

/* Efectos hover mejorados */
.group:hover {
    transform: translateY(-2px);
}

/* Shadow más sutiles */
.shadow-xs {
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
}
</style>
