<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { 
    ArrowUpRight, 
    DollarSign, 
    Download, 
    FileText, 
    TrendingUp, 
    Users 
} from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    stats: {
        total_users: number;
        total_portfolios: number;
        users_last_7_days: number;
        most_used_template: string;
    };
    recentUsers: Array<{
        id: number;
        name: string;
        email: string;
        created_at: string;
        days_ago: string;
    }>;
}>();

// Datos del gráfico (mock por ahora)
const monthlyData = [
    { month: 'Ene', users: 12, portfolios: 8 },
    { month: 'Feb', users: 19, portfolios: 14 },
    { month: 'Mar', users: 15, portfolios: 11 },
    { month: 'Abr', users: 25, portfolios: 18 },
    { month: 'May', users: 22, portfolios: 16 },
    { month: 'Jun', users: 30, portfolios: 22 },
    { month: 'Jul', users: 28, portfolios: 20 },
    { month: 'Ago', users: 35, portfolios: 26 },
    { month: 'Sep', users: 32, portfolios: 24 },
    { month: 'Oct', users: 40, portfolios: 30 },
    { month: 'Nov', users: 38, portfolios: 28 },
    { month: 'Dic', users: 45, portfolios: 35 },
];

const maxValue = Math.max(...monthlyData.map(d => d.users));

const selectedYear = ref('2024');
</script>

<template>
    <Head title="Dashboard Admin" />

    <AdminLayout title="Dashboard">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Main Content - 3 columns -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Revenue Report Card -->
                <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                            Reporte de Actividad
                        </h2>
                        <div class="flex items-center space-x-3">
                            <select
                                v-model="selectedYear"
                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm text-gray-600 focus:border-[#005aeb] focus:outline-none"
                            >
                                <option value="2024">2024</option>
                                <option value="2023">2023</option>
                            </select>
                            <button class="text-sm text-[#005aeb] font-medium hover:underline flex items-center space-x-1">
                                <Download class="h-4 w-4" />
                                <span>Exportar</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Stats Summary -->
                        <div class="md:col-span-1 space-y-6">
                            <div>
                                <p class="text-3xl font-bold text-gray-900">{{ stats.total_users + stats.total_portfolios }}</p>
                                <p class="text-sm text-gray-500 mt-1">Total de registros</p>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-sm bg-[#005aeb]"></div>
                                    <span class="text-sm text-gray-600">Usuarios este mes</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.users_last_7_days * 4 }}</p>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-sm bg-[#3b82f6]"></div>
                                    <span class="text-sm text-gray-600">Portafolios este mes</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total_portfolios }}</p>
                            </div>
                        </div>

                        <!-- Bar Chart -->
                        <div class="md:col-span-3">
                            <div class="h-64 flex items-end justify-between space-x-2">
                                <div 
                                    v-for="data in monthlyData" 
                                    :key="data.month"
                                    class="flex-1 flex flex-col items-center"
                                >
                                    <div class="w-full flex flex-col items-center space-y-1">
                                        <!-- Users bar -->
                                        <div 
                                            class="w-4 bg-[#005aeb] rounded-t-sm transition-all duration-300 hover:bg-[#0048c4]"
                                            :style="{ height: `${(data.users / maxValue) * 180}px` }"
                                        ></div>
                                        <!-- Portfolios bar (smaller, behind) -->
                                        <div 
                                            class="w-4 bg-[#93c5fd] rounded-t-sm -mt-1"
                                            :style="{ height: `${(data.portfolios / maxValue) * 120}px` }"
                                        ></div>
                                    </div>
                                    <span class="text-xs text-gray-400 mt-2">{{ data.month }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Users Table -->
                <div class="rounded-2xl bg-white shadow-sm border border-gray-100">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-900">Usuarios Recientes</h2>
                    </div>

                    <div v-if="recentUsers.length > 0" class="divide-y divide-gray-100">
                        <div
                            v-for="user in recentUsers.slice(0, 5)"
                            :key="user.id"
                            class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex items-center space-x-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-[#005aeb] to-[#3b82f6] text-white font-medium">
                                    {{ user.name.charAt(0) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ user.name }}</p>
                                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">{{ user.days_ago }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="px-6 py-12 text-center">
                        <Users class="mx-auto h-12 w-12 text-gray-300" />
                        <p class="mt-4 text-gray-500">No hay usuarios recientes</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar Stats - 1 column -->
            <div class="space-y-6">
                <!-- Total Users Card -->
                <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Usuarios</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.total_users }}</p>
                    <div class="flex items-center mt-2 text-sm">
                        <ArrowUpRight class="h-4 w-4 text-green-500" />
                        <span class="text-green-500 font-medium">+{{ stats.users_last_7_days }}</span>
                        <span class="text-gray-400 ml-1">última semana</span>
                    </div>

                    <!-- Mini chart ring -->
                    <div class="mt-4 flex items-center justify-center">
                        <div class="relative w-24 h-24">
                            <svg class="w-24 h-24 transform -rotate-90">
                                <circle cx="48" cy="48" r="40" stroke="#e5e7eb" stroke-width="8" fill="none" />
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
                            <div class="w-2 h-2 rounded-full bg-gray-200"></div>
                            <span class="text-gray-500">Inactivos</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 rounded-full bg-[#005aeb]"></div>
                            <span class="text-gray-500">Activos</span>
                        </div>
                    </div>
                </div>

                <!-- Total Portfolios Card -->
                <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Portafolios</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.total_portfolios }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50">
                            <FileText class="h-6 w-6 text-[#005aeb]" />
                        </div>
                    </div>
                    <div class="flex items-center mt-2 text-sm">
                        <TrendingUp class="h-4 w-4 text-green-500" />
                        <span class="text-green-500 font-medium ml-1">+12%</span>
                        <span class="text-gray-400 ml-1">vs mes anterior</span>
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
                <div class="rounded-2xl bg-gradient-to-br from-[#005aeb] to-[#3b82f6] shadow-lg p-6 text-white">
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
