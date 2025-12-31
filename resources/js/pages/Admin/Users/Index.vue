<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Edit, Eye, Filter, Search, Trash2, UserCheck, UserX, X } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps<{
    users: {
        data: Array<{
            id: number;
            first_name: string;
            last_name: string;
            name: string;
            email: string;
            role: string;
            created_at: string;
            created_at_iso: string;
            status: string;
            avatar_url?: string | null;
            portfolios_count: number;
        }>;
        links: any;
        meta?: any;
    };
    roles: Array<{ id: number; name: string }>;
}>();

const searchQuery = ref('');
const deleteConfirm = ref<number | null>(null);
const showFilters = ref(false);

// Filtros avanzados
const roleFilter = ref('all');
const statusFilter = ref('all');
const dateFilter = ref('all');

const roleOptions = [
    { value: 'all', label: 'Todos los roles' },
    { value: 'admin', label: 'Administradores' },
    { value: 'user', label: 'Usuarios' },
];

const statusOptions = [
    { value: 'all', label: 'Todos los estados' },
    { value: 'active', label: 'Activos' },
    { value: 'inactive', label: 'Inactivos' },
];

const dateOptions = [
    { value: 'all', label: 'Cualquier fecha' },
    { value: 'today', label: 'Hoy' },
    { value: 'week', label: 'Última semana' },
    { value: 'month', label: 'Último mes' },
    { value: '3months', label: 'Últimos 3 meses' },
];

const activeFiltersCount = computed(() => {
    let count = 0;
    if (roleFilter.value !== 'all') count++;
    if (statusFilter.value !== 'all') count++;
    if (dateFilter.value !== 'all') count++;
    return count;
});

const clearFilters = () => {
    roleFilter.value = 'all';
    statusFilter.value = 'all';
    dateFilter.value = 'all';
    searchQuery.value = '';
};

const filteredUsers = computed(() => {
    let result = props.users.data;
    
    // Búsqueda por texto
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            user => 
                user.name.toLowerCase().includes(query) ||
                user.email.toLowerCase().includes(query)
        );
    }
    
    // Filtro por rol
    if (roleFilter.value !== 'all') {
        result = result.filter(user => user.role === roleFilter.value);
    }
    
    // Filtro por estado
    if (statusFilter.value !== 'all') {
        result = result.filter(user => user.status === statusFilter.value);
    }
    
    // Filtro por fecha
    if (dateFilter.value !== 'all') {
        const now = new Date();
        const startOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate()); // 00:00:00 hoy local

        result = result.filter(user => {
            let userDate;
            
            if (user.created_at_iso) {
                userDate = new Date(user.created_at_iso);
            } else {
                // Fallback robusto: intentar parsear la fecha formateada (ej: "30 Dec 2025")
                userDate = new Date(user.created_at);
            }

            // Si la fecha no es válida, no mostrar
            if (isNaN(userDate.getTime())) return false;
            
            if (dateFilter.value === 'today') {
                return userDate >= startOfDay;
            }
            if (dateFilter.value === 'week') {
                const weekAgo = new Date(startOfDay);
                weekAgo.setDate(weekAgo.getDate() - 7);
                return userDate >= weekAgo;
            }
            if (dateFilter.value === 'month') {
                const monthAgo = new Date(startOfDay);
                monthAgo.setMonth(monthAgo.getMonth() - 1);
                return userDate >= monthAgo;
            }
            if (dateFilter.value === '3months') {
                const threeMonthsAgo = new Date(startOfDay);
                threeMonthsAgo.setMonth(threeMonthsAgo.getMonth() - 3);
                return userDate >= threeMonthsAgo;
            }
            return true;
        });
    }
    
    return result;
});

const confirmDelete = (userId: number) => {
    deleteConfirm.value = userId;
};

const cancelDelete = () => {
    deleteConfirm.value = null;
};

const deleteUser = (userId: number) => {
    router.delete(`/admin/users/${userId}`, {
        preserveScroll: true,
        onSuccess: () => {
            deleteConfirm.value = null;
        },
    });
};

const getRoleBadgeClass = (role: string) => {
    return role === 'admin'
        ? 'bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400'
        : 'bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400';
};

const getStatusBadgeClass = (status: string) => {
    return status === 'active'
        ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400'
        : 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400';
};
</script>

<template>
    <Head title="Gestión de Usuarios" />

    <AdminLayout title="Gestión de Usuarios">
        <!-- Header con búsqueda y filtros -->
        <div class="mb-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <!-- Búsqueda -->
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar por nombre o email..."
                        class="w-full rounded-lg border border-gray-300 dark:border-slate-800 bg-white dark:bg-slate-900 py-2.5 pl-10 pr-4 text-sm text-gray-900 dark:text-white focus:border-[#005aeb] focus:outline-none focus:ring-1 focus:ring-[#005aeb]"
                    />
                </div>
                
                <!-- Botón filtros -->
                <button
                    @click="showFilters = !showFilters"
                    :class="[
                        'inline-flex items-center space-x-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition',
                        showFilters || activeFiltersCount > 0
                            ? 'border-[#005aeb] bg-blue-50 dark:bg-blue-900/20 text-[#005aeb] dark:text-blue-400'
                            : 'border-gray-300 dark:border-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800'
                    ]"
                >
                    <Filter class="h-4 w-4" />
                    <span>Filtros</span>
                    <span v-if="activeFiltersCount > 0" class="flex h-5 w-5 items-center justify-center rounded-full bg-[#005aeb] text-xs text-white">
                        {{ activeFiltersCount }}
                    </span>
                </button>
                
                <p class="text-sm text-gray-500 dark:text-slate-400">
                    {{ filteredUsers.length }} de {{ props.users.data.length }} usuarios
                </p>
            </div>
            
            <!-- Panel de filtros avanzados -->
            <div v-if="showFilters" class="rounded-xl border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50 p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-slate-200">Filtros Avanzados</h3>
                    <button
                        v-if="activeFiltersCount > 0"
                        @click="clearFilters"
                        class="text-sm text-[#005aeb] hover:underline"
                    >
                        Limpiar filtros
                    </button>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Filtro por rol -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Rol</label>
                        <select
                            v-model="roleFilter"
                            class="w-full rounded-lg border border-gray-300 dark:border-slate-800 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-[#005aeb] focus:outline-none focus:ring-1 focus:ring-[#005aeb]"
                        >
                            <option v-for="option in roleOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                    
                    <!-- Filtro por estado -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Estado</label>
                        <select
                            v-model="statusFilter"
                            class="w-full rounded-lg border border-gray-300 dark:border-slate-800 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-[#005aeb] focus:outline-none focus:ring-1 focus:ring-[#005aeb]"
                        >
                            <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                    
                    <!-- Filtro por fecha -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Fecha de registro</label>
                        <select
                            v-model="dateFilter"
                            class="w-full rounded-lg border border-gray-300 dark:border-slate-800 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-[#005aeb] focus:outline-none focus:ring-1 focus:ring-[#005aeb]"
                        >
                            <option v-for="option in dateOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="rounded-2xl bg-white dark:bg-slate-900 shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden text-gray-900 dark:text-slate-300">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
                    <thead class="bg-gray-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                Usuario
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                Rol
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                Portafolios
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                Registro
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-slate-800">
                        <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img 
                                        v-if="user.avatar_url" 
                                        :src="user.avatar_url" 
                                        :alt="user.name"
                                        class="h-10 w-10 rounded-full object-cover"
                                    />
                                    <div v-else class="flex h-10 w-10 items-center justify-center rounded-full bg-[#005aeb] text-white font-medium">
                                        {{ user.first_name?.charAt(0) }}{{ user.last_name?.charAt(0) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-slate-400">{{ user.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="[getRoleBadgeClass(user.role), 'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium capitalize']">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="[getStatusBadgeClass(user.status), 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium']">
                                    <UserCheck v-if="user.status === 'active'" class="mr-1 h-3 w-3" />
                                    <UserX v-else class="mr-1 h-3 w-3" />
                                    {{ user.status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                                {{ user.portfolios_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                                {{ user.created_at }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <Link
                                        :href="`/admin/users/${user.id}`"
                                        class="rounded-lg p-2 text-gray-400 dark:text-slate-500 transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400"
                                        title="Ver detalles"
                                    >
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                    <button
                                        @click="confirmDelete(user.id)"
                                        class="rounded-lg p-2 text-gray-400 dark:text-slate-500 transition-colors hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400"
                                        title="Eliminar"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty state -->
            <div v-if="filteredUsers.length === 0" class="px-6 py-12 text-center">
                <Search class="mx-auto h-12 w-12 text-gray-300 dark:text-slate-700" />
                <p class="mt-4 text-gray-500 dark:text-slate-400">No se encontraron usuarios</p>
            </div>
        </div>

        <!-- Modal de confirmación de eliminación -->
        <Teleport to="body">
            <div v-if="deleteConfirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl border border-gray-100 dark:border-slate-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">¿Eliminar usuario?</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-slate-400">
                        Esta acción eliminará permanentemente al usuario y todos sus portafolios. No se puede deshacer.
                    </p>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            @click="cancelDelete"
                            class="rounded-lg border border-gray-300 dark:border-slate-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 transition hover:bg-gray-50 dark:hover:bg-slate-800"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="deleteUser(deleteConfirm)"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
