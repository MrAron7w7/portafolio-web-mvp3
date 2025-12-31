<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, FileText, Mail, Save, User } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    user: {
        id: number;
        first_name: string;
        last_name: string;
        name: string;
        email: string;
        role: string;
        created_at: string;
        status: string;
    };
    portfolios: Array<{
        id: number;
        title: string;
        template_type: string;
        is_public: boolean;
        created_at: string;
    }>;
    roles: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    status: props.user.status,
    role: props.user.role,
});

const isEditing = ref(false);

const saveChanges = () => {
    form.put(`/admin/users/${props.user.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        },
    });
};

const cancelEdit = () => {
    form.reset();
    isEditing.value = false;
};
</script>

<template>
    <Head :title="`Usuario: ${user.name}`" />

    <AdminLayout :title="`Usuario: ${user.name}`">
        <!-- Back button -->
        <div class="mb-6">
            <Link
                href="/admin/users"
                class="inline-flex items-center space-x-2 text-sm text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors"
            >
                <ArrowLeft class="h-4 w-4" />
                <span>Volver a usuarios</span>
            </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Información del usuario -->
            <div class="lg:col-span-2">
                <div class="rounded-2xl bg-white dark:bg-slate-900 shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Información del Usuario</h2>
                        <button
                            v-if="!isEditing"
                            @click="isEditing = true"
                            class="text-sm text-[#005aeb] hover:underline dark:text-blue-400"
                        >
                            Editar
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Avatar y nombre -->
                        <div class="flex items-center space-x-4 pb-4 border-b border-gray-100 dark:border-slate-800">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#005aeb] text-white text-xl font-medium">
                                {{ user.first_name?.charAt(0) }}{{ user.last_name?.charAt(0) }}
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ user.name }}</h3>
                                <p class="text-gray-500 dark:text-slate-400">{{ user.email }}</p>
                            </div>
                        </div>

                        <!-- Campos editables -->
                        <div v-if="isEditing" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Nombre</label>
                                    <input
                                        v-model="form.first_name"
                                        type="text"
                                        class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-[#005aeb] focus:outline-none focus:ring-1 focus:ring-[#005aeb]"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Apellido</label>
                                    <input
                                        v-model="form.last_name"
                                        type="text"
                                        class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-[#005aeb] focus:outline-none focus:ring-1 focus:ring-[#005aeb]"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Rol</label>
                                    <select
                                        v-model="form.role"
                                        class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-[#005aeb] focus:outline-none focus:ring-1 focus:ring-[#005aeb]"
                                    >
                                        <option v-for="role in roles" :key="role.id" :value="role.name">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Estado</label>
                                    <select
                                        v-model="form.status"
                                        class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-[#005aeb] focus:outline-none focus:ring-1 focus:ring-[#005aeb]"
                                    >
                                        <option value="active">Activo</option>
                                        <option value="inactive">Inactivo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3 pt-4">
                                <button
                                    @click="cancelEdit"
                                    class="rounded-lg border border-gray-300 dark:border-slate-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 transition hover:bg-gray-50 dark:hover:bg-slate-800"
                                >
                                    Cancelar
                                </button>
                                <button
                                    @click="saveChanges"
                                    :disabled="form.processing"
                                    class="inline-flex items-center space-x-2 rounded-lg bg-[#005aeb] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#0048c4] disabled:opacity-50"
                                >
                                    <Save class="h-4 w-4" />
                                    <span>Guardar</span>
                                </button>
                            </div>
                        </div>

                        <!-- Vista de solo lectura -->
                        <div v-else class="space-y-3">
                            <div class="flex justify-between py-2">
                                <span class="text-sm text-gray-500 dark:text-slate-400">Rol</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ user.role }}</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-sm text-gray-500 dark:text-slate-400">Estado</span>
                                <span :class="[
                                    'text-sm font-medium',
                                    user.status === 'active' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                                ]">
                                    {{ user.status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-sm text-gray-500 dark:text-slate-400">Registrado</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ user.created_at }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Portafolios del usuario -->
            <div class="lg:col-span-1">
                <div class="rounded-2xl bg-white dark:bg-slate-900 shadow-sm border border-gray-100 dark:border-slate-800 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Portafolios ({{ portfolios.length }})
                    </h2>

                    <div v-if="portfolios.length > 0" class="space-y-3">
                        <div
                            v-for="portfolio in portfolios"
                            :key="portfolio.id"
                            class="rounded-lg border border-gray-100 dark:border-slate-800 p-3 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors"
                        >
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white text-sm">{{ portfolio.title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400 capitalize">{{ portfolio.template_type }}</p>
                                </div>
                                <span :class="[
                                    'text-xs px-2 py-0.5 rounded-full',
                                    portfolio.is_public 
                                        ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400' 
                                        : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400'
                                ]">
                                    {{ portfolio.is_public ? 'Público' : 'Privado' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8">
                        <FileText class="mx-auto h-10 w-10 text-gray-300 dark:text-slate-600" />
                        <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">Sin portafolios</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
