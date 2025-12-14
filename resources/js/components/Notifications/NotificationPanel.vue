<script setup lang="ts">
import { ref, onMounted,onUnmounted, computed, watch } from 'vue';
import axios from 'axios';

interface Notification {
    id: number;
    type: 'info' | 'success' | 'warning' | 'error';
    title: string;
    message: string;
    is_read: boolean;
    created_at: string;
}

const isOpen = ref(false);
const notifications = ref<Notification[]>([]);
const loading = ref(false);
const containerRef = ref<HTMLElement | null>(null);

const emit = defineEmits<{
    (e: 'update:unread', value: number): void;
}>();

const unreadCount = computed(() =>
    notifications.value.filter(n => !n.is_read).length
);

watch(unreadCount, (value) => {
    emit('update:unread', value);
});
//observador de la ventana notificaciones para cerrarlo con un click fuera 
watch(isOpen, (newValue) => {
    if (newValue) {
        setTimeout(() => {
            document.addEventListener('click', closeOnClickOutside);
        }, 0);
    } else {
        document.removeEventListener('click', closeOnClickOutside);
    }
});

const toggle = () => {
    isOpen.value = !isOpen.value;
};

const fetchNotifications = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/notifications');
        notifications.value = data;
    } finally {
        loading.value = false;
    }
};

const markAsRead = async (id: number) => {
    const n = notifications.value.find(n => n.id === id);

    if (!n || n.is_read) return;

    await axios.post(`/api/notifications/${id}/read`);
    n.is_read = true;
};

const closeOnClickOutside = (e: MouseEvent) => {
    if (!isOpen.value) return;

    if (containerRef.value && containerRef.value.contains(e.target as Node)) {
        return;
    }

    isOpen.value = false;
};

onMounted(() => {
    fetchNotifications();
});

onUnmounted(() => {
    document.removeEventListener('click', closeOnClickOutside);
});

</script>

<template>
    <div class="relative"  ref="containerRef">
        <!-- Trigger -->
        <button @click="toggle"
            class="group flex w-full items-center space-x-3 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all">
            <slot name="trigger" />
        </button>

        <!-- Panel -->
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <div v-if="isOpen" class="absolute left-full bottom-0 ml-3 w-[420px] max-h-[70vh] min-h-[180px] rounded-xl bg-white shadow-2xl
                border border-gray-200 z-50 flex flex-col">
                <!-- Header -->
                <div class="p-4 border-b">
                    <h3 class="text-sm font-semibold">Notificaciones</h3>
                </div>

                <!-- Body -->
                <div class="max-h-80 flex-1 overflow-y-auto">
                    <div v-if="loading" class="p-4 text-sm text-gray-500">
                        Cargando…
                    </div>

                    <div v-for="n in notifications" :key="n.id" @click="markAsRead(n.id)" :class="[
                        'cursor-pointer px-4 py-3 border-b last:border-0 transition-colors flex gap-3',
                        n.is_read
                            ? 'bg-gray-50 hover:bg-gray-100'
                            : 'bg-blue-50 hover:bg-blue-100'
                    ]">
                        <div class="pt-1 shrink-0">
                            <span v-if="!n.is_read" class="block h-2 w-2 rounded-full bg-blue-500" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <p class="text-sm font-medium text-gray-900 leading-tight">
                                    {{ n.title }}
                                </p>

                                <span v-if="n.is_read"
                                    class="text-[10px] uppercase tracking-wide text-gray-400 shrink-0 mt-0.5">
                                    Leída
                                </span>
                            </div>

                            <p class="text-xs text-gray-600 break-words whitespace-normal leading-relaxed">
                                {{ n.message }}
                            </p>

                            <span class="text-[10px] text-gray-400 mt-2 block">
                                {{ new Date(n.created_at).toLocaleString() }}
                            </span>
                        </div>
                    </div>

                    <!-- Empty -->
                    <div v-if="!loading && notifications.length === 0" class="p-6 text-center text-sm text-gray-500">
                        No tienes notificaciones
                    </div>
                </div>
            </div>
        </Transition>

    </div>
</template>
