<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    notifications: Object
});

const toast = useToast();

// Formulario para nueva notificación
const form = useForm({
    type: 'info',
    title: '',
    message: '',
    is_global: true
});

const showForm = ref(false);

// Enviar notificación
const sendNotification = () => {
    form.post('/admin/notifications', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showForm.value = false;
            toast.success('¡Enviado!', 'Notificación enviada a todos los usuarios');
        },
        onError: () => {
            toast.error('Error', 'No se pudo enviar la notificación');
        }
    });
};

// Eliminar notificación
const deleteNotification = (id) => {
    if (confirm('¿Eliminar esta notificación?')) {
        router.delete(`/admin/notifications/${id}`, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Eliminado', 'Notificación eliminada correctamente');
            }
        });
    }
};
</script>

<template>
    <AdminLayout title="Notificaciones">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Notificaciones</h1>
                    <p class="mt-2 text-gray-600">Envía notificaciones a todos los usuarios</p>
                </div>
                
                <button
                    @click="showForm = !showForm"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 transition-colors flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nueva Notificación
                </button>
            </div>

            <!-- Formulario de nueva notificación -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 -translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-4"
            >
                <div v-if="showForm" class="mb-8 rounded-lg bg-white p-6 shadow-lg border-2 border-blue-500">
                    <h2 class="text-xl font-semibold mb-6">Enviar Notificación Global</h2>
                    
                    <form @submit.prevent="sendNotification" class="space-y-4">
                        <!-- Tipo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                            <div class="grid grid-cols-4 gap-2">
                                <button
                                    type="button"
                                    @click="form.type = 'info'"
                                    :class="[
                                        'px-4 py-2 rounded-lg font-medium transition-all',
                                        form.type === 'info'
                                            ? 'bg-blue-500 text-white scale-105'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    ]"
                                >
                                    💡 Info
                                </button>
                                <button
                                    type="button"
                                    @click="form.type = 'success'"
                                    :class="[
                                        'px-4 py-2 rounded-lg font-medium transition-all',
                                        form.type === 'success'
                                            ? 'bg-green-500 text-white scale-105'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    ]"
                                >
                                    ✅ Éxito
                                </button>
                                <button
                                    type="button"
                                    @click="form.type = 'warning'"
                                    :class="[
                                        'px-4 py-2 rounded-lg font-medium transition-all',
                                        form.type === 'warning'
                                            ? 'bg-yellow-500 text-white scale-105'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    ]"
                                >
                                    ⚠️ Alerta
                                </button>
                                <button
                                    type="button"
                                    @click="form.type = 'error'"
                                    :class="[
                                        'px-4 py-2 rounded-lg font-medium transition-all',
                                        form.type === 'error'
                                            ? 'bg-red-500 text-white scale-105'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    ]"
                                >
                                    ❌ Error
                                </button>
                            </div>
                        </div>

                        <!-- Título -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full rounded-lg border-gray-300"
                                placeholder="Título de la notificación"
                                required
                            />
                        </div>

                        <!-- Mensaje -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mensaje</label>
                            <textarea
                                v-model="form.message"
                                rows="3"
                                class="w-full rounded-lg border-gray-300"
                                placeholder="Escribe el mensaje aquí..."
                                required
                            ></textarea>
                        </div>

                        <!-- Checkbox Global -->
                        <div class="flex items-center gap-2">
                            <input
                                v-model="form.is_global"
                                type="checkbox"
                                id="is_global"
                                class="rounded border-gray-300 text-blue-600"
                            />
                            <label for="is_global" class="text-sm text-gray-700">
                                Enviar a todos los usuarios (notificación global)
                            </label>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end gap-3">
                            <button
                                type="button"
                                @click="showForm = false"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
                            >
                                <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Enviando...' : 'Enviar Notificación' }}
                            </button>
                        </div>
                    </form>
                </div>
            </Transition>

            <!-- Lista de notificaciones enviadas -->
            <div class="rounded-lg bg-white shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold">Historial de Notificaciones</h2>
                </div>
                
                <div class="divide-y divide-gray-200">
                    <div
                        v-for="notification in notifications.data"
                        :key="notification.id"
                        class="px-6 py-4 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <!-- Icono según tipo -->
                                <div :class="[
                                    'w-10 h-10 rounded-lg flex items-center justify-center text-xl',
                                    {
                                        'bg-blue-100': notification.type === 'info',
                                        'bg-green-100': notification.type === 'success',
                                        'bg-yellow-100': notification.type === 'warning',
                                        'bg-red-100': notification.type === 'error'
                                    }
                                ]">
                                    {{ notification.type === 'success' ? '✅' : notification.type === 'error' ? '❌' : notification.type === 'warning' ? '⚠️' : '💡' }}
                                </div>
                                
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ notification.title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ notification.message }}</p>
                                    <div class="flex items-center gap-4 mt-2">
                                        <span class="text-xs text-gray-500">
                                            {{ new Date(notification.created_at).toLocaleString('es-ES') }}
                                        </span>
                                        <span v-if="notification.is_global" class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded">
                                            🌐 Global
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón eliminar -->
                            <button
                                @click="deleteNotification(notification.id)"
                                class="text-gray-400 hover:text-red-600 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-if="notifications.data.length === 0" class="px-6 py-12 text-center">
                        <div class="text-6xl mb-4">📭</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay notificaciones</h3>
                        <p class="text-gray-500">Crea tu primera notificación para enviar a los usuarios</p>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="notifications.data.length > 0" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">
                            Mostrando {{ notifications.from }} a {{ notifications.to }} de {{ notifications.total }}
                        </span>
                        
                        <div class="flex gap-2">
                            <a
                                v-for="link in notifications.links"
                                :key="link.label"
                                :href="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1 rounded border text-sm',
                                    link.active
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                            ></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>