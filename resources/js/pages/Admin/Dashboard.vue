<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { 
    ArrowUpRight, 
    DollarSign, 
    Download, 
    FileText, 
    TrendingUp, 
    Users,
    MessageSquare,
    Eye,
    Smartphone 
} from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';

const props = defineProps<{
    stats: {
        total_users: number;
        total_portfolios: number;
        users_last_7_days: number;
        most_used_template: string;
        total_posts?: number;
        total_comments?: number;
        total_views?: number;
        top_device?: string;
        total_exports?: number;
    };
    recentUsers: Array<{
        id: number;
        name: string;
        email: string;
        created_at: string;
        days_ago: string;
    }>;
    chartData: Array<{
        month: string;
        users: number;
        portfolios: number;
    }>;
}>();

const maxValue = computed(() => {
    if (!props.chartData || props.chartData.length === 0) return 100;
    const max = Math.max(...props.chartData.map(d => d.users));
    return max > 0 ? max : 10;
});

const totalUsersInYear = computed(() => props.chartData?.reduce((sum, item) => sum + item.users, 0) || 0);
const totalPortfoliosInYear = computed(() => props.chartData?.reduce((sum, item) => sum + item.portfolios, 0) || 0);

const currentYear = new Date().getFullYear();
const startYear = 2025;
const availableYears = Array.from({ length: currentYear - startYear + 1 }, (_, i) => currentYear - i);

const selectedYear = ref(String(currentYear));

watch(selectedYear, (newYear) => {
    router.get('/admin/dashboard', { year: newYear }, {
        preserveState: true,
        preserveScroll: true,
        only: ['chartData'],
    });
});

const exportReport = (format = 'csv') => {
    // Usamos URL directa para evitar problemas de caché con Ziggy
    const url = `/admin/export?year=${selectedYear.value}&format=${format}`;
    console.log('Exporting from:', url);
    window.location.href = url;
};
</script>

<template>
    <Head title="Dashboard Admin" />

    <AdminLayout title="Dashboard">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Main Content - 3 columns -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Revenue Report Card -->
                <div class="rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-gray-100 dark:border-slate-700 p-6 transition-colors">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-sm font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                            Reporte de Actividad <span v-if="chartData">({{ totalUsersInYear }} usuarios, Max: {{ maxValue }})</span>
                        </h2>
                        <div class="flex items-center space-x-3">
                            <select
                                v-model="selectedYear"
                                class="rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-1.5 text-sm text-gray-600 dark:text-slate-300 focus:border-[#005aeb] focus:outline-none transition-colors"
                            >
                                <option 
                                    v-for="year in availableYears" 
                                    :key="year" 
                                    :value="String(year)"
                                    class="bg-white dark:bg-slate-900"
                                >
                                    {{ year }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Stats Summary -->
                        <div class="md:col-span-1 space-y-6">
                            <div>
                                <div class="flex items-center justify-between">
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.total_users + stats.total_portfolios }}</p>
                                    
                                    <!-- Export Dropdown Moved Here -->
                                    <div class="relative group">
                                        <button 
                                            class="text-xs bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-600 text-[#005aeb] font-medium px-2 py-1 rounded hover:bg-gray-50 dark:hover:bg-slate-800 flex items-center space-x-1 transition-colors"
                                        >
                                            <Download class="h-3 w-3" />
                                            <span>Exportar</span>
                                        </button>
                                        <!-- Dropdown with transparent bridge -->
                                        <div class="absolute left-0 top-full pt-1 w-32 hidden group-hover:block z-50">
                                            <div class="bg-white dark:bg-slate-900 rounded-lg shadow-lg border border-gray-100 dark:border-slate-700 py-1">
                                                <button 
                                                    @click="exportReport('csv')"
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 hover:text-[#005aeb] dark:hover:text-[#005aeb]"
                                                >
                                                    CSV
                                                </button>
                                                <button 
                                                    @click="exportReport('excel')"
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 hover:text-[#005aeb] dark:hover:text-[#005aeb]"
                                                >
                                                    Excel
                                                </button>
                                                <button 
                                                    @click="exportReport('pdf')"
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 hover:text-[#005aeb] dark:hover:text-[#005aeb]"
                                                >
                                                    PDF
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Total de registros</p>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-sm bg-[#005aeb]"></div>
                                    <span class="text-sm text-gray-600 dark:text-slate-300">Usuarios este mes</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.users_last_7_days * 4 }}</p>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-sm bg-[#3b82f6]"></div>
                                    <span class="text-sm text-gray-600 dark:text-slate-300">Portafolios este mes</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_portfolios }}</p>
                            </div>
                        </div>

                        <!-- Bar Chart -->
                        <div class="md:col-span-3">
                            <div class="h-64 flex items-end justify-between space-x-2">
                                <div 
                                    v-for="data in chartData" 
                                    :key="data.month"
                                    class="flex-1 flex flex-col justify-end items-center group relative h-full"
                                >
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-gray-800 dark:bg-slate-700 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10 shadow-lg">
                                        <div class="font-semibold">{{ data.month }}</div>
                                        <div>Usu: {{ data.users }}</div>
                                        <div>Port: {{ data.portfolios }}</div>
                                    </div>
                                    
                                    <div class="flex-1 w-full flex items-end justify-center space-x-1 pb-1">
                                        <!-- Users bar -->
                                        <div 
                                            class="w-3 bg-[#005aeb] rounded-t-sm transition-all duration-300 hover:bg-[#0048c4]"
                                            :style="{ height: data.users > 0 ? `${(data.users / maxValue) * 80}%` : '4px', opacity: data.users > 0 ? 1 : 0.1 }"
                                        ></div>
                                        <!-- Portfolios bar -->
                                        <div 
                                            class="w-3 bg-[#93c5fd] rounded-t-sm transition-all duration-300 hover:bg-[#60a5fa]"
                                            :style="{ height: data.portfolios > 0 ? `${(data.portfolios / maxValue) * 80}%` : '2px', opacity: data.portfolios > 0 ? 1 : 0.1 }"
                                        ></div>
                                    </div>
                                    <span class="text-xs text-gray-400 dark:text-slate-500 mt-2">{{ data.month }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Extended Metrics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Community Posts -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-700 relative overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                            <MessageSquare class="h-24 w-24 text-[#005aeb]" />
                        </div>
                        <div class="h-10 w-10 rounded-xl bg-purple-50 dark:bg-purple-900/10 flex items-center justify-center text-purple-600 dark:text-purple-400 mb-4">
                            <MessageSquare class="h-5 w-5" />
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-slate-400 mb-1">Comunidad</h3>
                        <div class="flex items-baseline space-x-2">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_posts || 0 }}</p>
                            <span class="text-xs text-gray-400 dark:text-slate-500">Posts</span>
                        </div>
                        <div class="flex items-baseline space-x-2 mt-1">
                            <p class="text-lg font-semibold text-gray-700 dark:text-slate-300">{{ stats.total_comments || 0 }}</p>
                            <span class="text-xs text-gray-400 dark:text-slate-500">Comentarios</span>
                        </div>
                    </div>

                    <!-- Analytics Stats -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-700 relative overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                            <Eye class="h-24 w-24 text-[#005aeb]" />
                        </div>
                        <div class="h-10 w-10 rounded-xl bg-amber-50 dark:bg-amber-900/10 flex items-center justify-center text-amber-600 dark:text-amber-400 mb-4">
                            <Eye class="h-5 w-5" />
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-slate-400 mb-1">Vistas Totales</h3>
                        <div class="flex items-baseline space-x-2">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_views || 0 }}</p>
                            <span class="text-xs text-gray-400 dark:text-slate-500">Global</span>
                        </div>
                        <div class="flex items-center mt-2 text-xs text-gray-500 dark:text-slate-400">
                             <Smartphone class="h-3 w-3 mr-1" />
                             Top: <span class="font-medium ml-1">{{ stats.top_device || 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Exports Stats -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-700 relative overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                            <Download class="h-24 w-24 text-[#005aeb]" />
                        </div>
                        <div class="h-10 w-10 rounded-xl bg-green-50 dark:bg-green-900/10 flex items-center justify-center text-green-600 dark:text-green-400 mb-4">
                            <Download class="h-5 w-5" />
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-slate-400 mb-1">Descargas</h3>
                        <div class="flex items-baseline space-x-2">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_exports || 0 }}</p>
                            <span class="text-xs text-gray-400 dark:text-slate-500">Reportes CSV</span>
                        </div>
                        <p class="text-sm text-green-600 dark:text-green-400 font-medium flex items-center mt-2">
                            <TrendingUp class="h-4 w-4 mr-1" />
                            Actividad reciente
                        </p>
                    </div>

                     <!-- System Health (Placeholder/Real) -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-slate-700 relative overflow-hidden group hover:shadow-md transition-shadow">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                            <Users class="h-24 w-24 text-[#005aeb]" />
                        </div>
                        <div class="h-10 w-10 rounded-xl bg-blue-50 dark:bg-blue-900/10 flex items-center justify-center text-blue-600 dark:text-blue-400 mb-4">
                            <Users class="h-5 w-5" />
                        </div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-slate-400 mb-1">Usuarios Activos</h3>
                         <div class="flex items-baseline space-x-2">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.users_last_7_days }}</p>
                            <span class="text-xs text-gray-400 dark:text-slate-500">Últimos 7 días</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-slate-700 rounded-full h-1.5 mt-3">
                            <div class="bg-blue-600 h-1.5 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                </div>

                <!-- Recent Users Table -->
                <div class="rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-gray-100 dark:border-slate-700">
                    <div class="border-b border-gray-100 dark:border-slate-700 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Usuarios Recientes</h2>
                    </div>

                    <div v-if="recentUsers.length > 0" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <div
                            v-for="user in recentUsers.slice(0, 5)"
                            :key="user.id"
                            class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors"
                        >
                            <div class="flex items-center space-x-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-linear-to-br from-[#005aeb] to-[#3b82f6] text-white font-medium">
                                    {{ user.name.charAt(0) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ user.name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-slate-400">{{ user.email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500 dark:text-slate-400">{{ user.days_ago }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="px-6 py-12 text-center">
                        <Users class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" />
                        <p class="mt-4 text-gray-500 dark:text-gray-400">No hay usuarios recientes</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar Stats - 1 column -->
            <div class="space-y-6">
                <!-- Total Users Card -->
                <div class="rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-gray-100 dark:border-slate-700 p-6 transition-colors">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Usuarios</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats.total_users }}</p>
                    <div class="flex items-center mt-2 text-sm">
                        <ArrowUpRight class="h-4 w-4 text-green-500" />
                        <span class="text-green-500 font-medium">+{{ stats.users_last_7_days }}</span>
                        <span class="text-gray-400 dark:text-gray-500 ml-1">última semana</span>
                    </div>

                    <!-- Mini chart ring -->
                    <div class="mt-4 flex items-center justify-center">
                        <div class="relative w-24 h-24">
                            <svg class="w-24 h-24 transform -rotate-90">
                                <circle cx="48" cy="48" r="40" stroke="#e5e7eb" class="dark:stroke-slate-700" stroke-width="8" fill="none" />
                                <circle 
                                    cx="48" cy="48" r="40" 
                                    stroke="#005aeb" 
                                    stroke-width="8" 
                                    fill="none"
                                    stroke-linecap="round"
                                    :stroke-dasharray="`${(stats.total_users / 100) * 251} 251`"
                                />
                            </svg>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-center space-x-4 text-xs">
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 rounded-full bg-gray-200 dark:bg-slate-600"></div>
                            <span class="text-gray-500 dark:text-gray-400">Inactivos</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 rounded-full bg-[#005aeb]"></div>
                            <span class="text-gray-500 dark:text-gray-400">Activos</span>
                        </div>
                    </div>
                </div>

                <!-- Total Portfolios Card -->
                <div class="rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-gray-100 dark:border-slate-700 p-6 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Portafolios</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats.total_portfolios }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/10">
                            <FileText class="h-6 w-6 text-[#005aeb]" />
                        </div>
                    </div>
                    <div class="flex items-center mt-2 text-sm">
                        <TrendingUp class="h-4 w-4 text-green-500" />
                        <span class="text-green-500 font-medium ml-1">+12%</span>
                        <span class="text-gray-400 dark:text-gray-500 ml-1">vs mes anterior</span>
                    </div>

                    <!-- Mini line chart -->
                    <div class="mt-4 h-16">
                        <svg class="w-full h-full" viewBox="0 0 200 60">
                            <path
                                d="M0,50 Q20,45 40,40 T80,35 T120,25 T160,30 T200,15"
                                fill="none"
                                stroke="#005aeb"
                                stroke-width="2"
                            />
                            <path
                                d="M0,50 Q20,45 40,40 T80,35 T120,25 T160,30 T200,15 L200,60 L0,60 Z"
                                fill="url(#gradient)"
                                opacity="0.1"
                            />
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#005aeb" />
                                    <stop offset="100%" stop-color="white" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>

                <!-- Most Used Template Card -->
                <div class="rounded-2xl bg-linear-to-br from-[#005aeb] to-[#3b82f6] shadow-lg p-6 text-white">
                    <p class="text-xs font-medium text-blue-200 uppercase tracking-wider">Plantilla Popular</p>
                    <p class="text-2xl font-bold mt-2 capitalize">{{ stats.most_used_template }}</p>
                    <p class="text-sm text-blue-200 mt-1">La más utilizada por usuarios</p>
                    
                    <div class="mt-4 flex items-center space-x-2">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full bg-white/20 border-2 border-white/30"></div>
                            <div class="w-8 h-8 rounded-full bg-white/20 border-2 border-white/30"></div>
                            <div class="w-8 h-8 rounded-full bg-white/20 border-2 border-white/30"></div>
                        </div>
                        <span class="text-sm text-blue-100">+{{ Math.floor(stats.total_portfolios * 0.4) }} usos</span>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
