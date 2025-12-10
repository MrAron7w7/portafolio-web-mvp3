<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { 
    ArrowUpRight, 
    ArrowDownRight,
    Calendar,
    Download, 
    FileText, 
    TrendingUp, 
    Users 
} from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    monthlyStats: Array<{
        month: string;
        users: number;
        portfolios: number;
    }>;
    currentMonth: {
        users: number;
        portfolios: number;
    };
    templateStats: Array<{
        template_type: string;
        total: number;
    }>;
}>();

// Período seleccionado
const selectedPeriod = ref('year');

// Colores para templates
const templateColors = [
    { bg: 'bg-blue-500', light: 'bg-blue-100' },
    { bg: 'bg-green-500', light: 'bg-green-100' },
    { bg: 'bg-purple-500', light: 'bg-purple-100' },
    { bg: 'bg-amber-500', light: 'bg-amber-100' },
    { bg: 'bg-pink-500', light: 'bg-pink-100' },
    { bg: 'bg-cyan-500', light: 'bg-cyan-100' },
];

// Calcular totales
const totalUsers = props.monthlyStats?.reduce((acc, s) => acc + s.users, 0) || 0;
const totalPortfolios = props.monthlyStats?.reduce((acc, s) => acc + s.portfolios, 0) || 0;
const maxMonthlyUsers = Math.max(...(props.monthlyStats?.map(s => s.users) || [1]));
const maxMonthlyPortfolios = Math.max(...(props.monthlyStats?.map(s => s.portfolios) || [1]));
</script>

<template>
    <Head title="Reportes" />

    <AdminLayout title="Reportes">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Reportes Estadísticos</h2>
                <p class="text-sm text-gray-500 mt-1">Analiza el crecimiento de la plataforma</p>
            </div>
            <div class="flex items-center space-x-3">
                <select
                    v-model="selectedPeriod"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm focus:border-[#005aeb] focus:outline-none"
                >
                    <option value="month">Este mes</option>
                    <option value="quarter">Este trimestre</option>
                    <option value="year">Este año</option>
                </select>
                <button class="inline-flex items-center space-x-2 rounded-lg bg-[#005aeb] px-4 py-2 text-sm font-medium text-white hover:bg-[#0048c4] transition">
                    <Download class="h-4 w-4" />
                    <span>Exportar PDF</span>
                </button>
            </div>
        </div>

        <!-- Stats Cards Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Users -->
            <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Usuarios</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ totalUsers }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50">
                        <Users class="h-6 w-6 text-[#005aeb]" />
                    </div>
                </div>
                <div class="flex items-center mt-3 text-sm">
                    <ArrowUpRight class="h-4 w-4 text-green-500" />
                    <span class="text-green-500 font-medium">+12.5%</span>
                    <span class="text-gray-400 ml-1">vs período anterior</span>
                </div>
            </div>

            <!-- Total Portfolios -->
            <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Portafolios</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ totalPortfolios }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50">
                        <FileText class="h-6 w-6 text-green-600" />
                    </div>
                </div>
                <div class="flex items-center mt-3 text-sm">
                    <ArrowUpRight class="h-4 w-4 text-green-500" />
                    <span class="text-green-500 font-medium">+8.2%</span>
                    <span class="text-gray-400 ml-1">vs período anterior</span>
                </div>
            </div>

            <!-- Users This Month -->
            <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Usuarios Este Mes</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ currentMonth?.users || 0 }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50">
                        <Calendar class="h-6 w-6 text-purple-600" />
                    </div>
                </div>
                <div class="flex items-center mt-3 text-sm">
                    <TrendingUp class="h-4 w-4 text-green-500" />
                    <span class="text-green-500 font-medium">Activo</span>
                </div>
            </div>

            <!-- Portfolios This Month -->
            <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Portafolios Este Mes</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ currentMonth?.portfolios || 0 }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50">
                        <TrendingUp class="h-6 w-6 text-amber-600" />
                    </div>
                </div>
                <div class="flex items-center mt-3 text-sm">
                    <ArrowDownRight class="h-4 w-4 text-red-500" />
                    <span class="text-red-500 font-medium">-3.1%</span>
                    <span class="text-gray-400 ml-1">vs mes anterior</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Chart -->
            <div class="lg:col-span-2 rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Crecimiento Mensual</h3>
                    <div class="flex items-center space-x-4 text-sm">
                        <div class="flex items-center space-x-2">
                            <div class="h-3 w-3 rounded-full bg-[#005aeb]"></div>
                            <span class="text-gray-600">Usuarios</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="h-3 w-3 rounded-full bg-green-500"></div>
                            <span class="text-gray-600">Portafolios</span>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="h-64 flex items-end justify-between space-x-3">
                    <div
                        v-for="stat in monthlyStats"
                        :key="stat.month"
                        class="flex-1 flex flex-col items-center"
                    >
                        <div class="w-full flex items-end justify-center space-x-1 h-52">
                            <!-- Users bar -->
                            <div 
                                class="w-5 bg-[#005aeb] rounded-t-md transition-all duration-300 hover:bg-[#0048c4]"
                                :style="{ height: `${Math.max((stat.users / maxMonthlyUsers) * 100, 5)}%` }"
                                :title="`Usuarios: ${stat.users}`"
                            ></div>
                            <!-- Portfolios bar -->
                            <div 
                                class="w-5 bg-green-500 rounded-t-md transition-all duration-300 hover:bg-green-600"
                                :style="{ height: `${Math.max((stat.portfolios / maxMonthlyPortfolios) * 100, 5)}%` }"
                                :title="`Portafolios: ${stat.portfolios}`"
                            ></div>
                        </div>
                        <span class="text-xs text-gray-400 mt-2">{{ stat.month }}</span>
                    </div>
                </div>
            </div>

            <!-- Template Stats -->
            <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Plantillas Usadas</h3>

                <div v-if="templateStats && templateStats.length > 0" class="space-y-4">
                    <div
                        v-for="(template, index) in templateStats"
                        :key="template.template_type"
                        class="flex items-center justify-between"
                    >
                        <div class="flex items-center space-x-3">
                            <div :class="[templateColors[index % templateColors.length].bg, 'h-3 w-3 rounded-full']"></div>
                            <span class="text-sm font-medium text-gray-700 capitalize">
                                {{ template.template_type }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div 
                                    :class="[templateColors[index % templateColors.length].bg]"
                                    class="h-full rounded-full transition-all duration-500"
                                    :style="{ width: `${(template.total / (templateStats[0]?.total || 1)) * 100}%` }"
                                ></div>
                            </div>
                            <span class="text-sm text-gray-500 w-8 text-right">{{ template.total }}</span>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-8">
                    <FileText class="mx-auto h-10 w-10 text-gray-300" />
                    <p class="mt-2 text-sm text-gray-500">Sin datos de plantillas</p>
                </div>

                <!-- Donut chart visualization -->
                <div v-if="templateStats && templateStats.length > 0" class="mt-6 flex justify-center">
                    <div class="relative w-32 h-32">
                        <svg class="w-32 h-32 transform -rotate-90">
                            <circle cx="64" cy="64" r="50" stroke="#e5e7eb" stroke-width="12" fill="none" />
                            <circle 
                                cx="64" cy="64" r="50" 
                                stroke="#005aeb" 
                                stroke-width="12" 
                                fill="none"
                                stroke-linecap="round"
                                :stroke-dasharray="`${(templateStats[0]?.total / totalPortfolios) * 314} 314`"
                            />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900">{{ templateStats.length }}</p>
                                <p class="text-xs text-gray-500">plantillas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
