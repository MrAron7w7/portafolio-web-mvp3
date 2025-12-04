<script setup lang="ts">
import Sidebar from '@/components/Dashboard/Sidebar.vue';
import { Menu } from 'lucide-vue-next';
import { ref } from 'vue';

const sidebarOpen = ref(false);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-gray-50/50">
        <!-- Sidebar -->
        <Sidebar :is-open="sidebarOpen" @close="sidebarOpen = false" />

        <!-- Sidebar Overlay para móvil -->
        <div
            v-if="sidebarOpen"
            class="bg-opacity-50 fixed inset-0 z-40 bg-black lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <!-- Contenido principal -->
        <div class="flex min-w-0 flex-1 flex-col overflow-y-auto transition-all duration-300 ease-in-out w-full">
            <slot :toggle-sidebar="toggleSidebar" :is-sidebar-open="sidebarOpen" />
            
            <!-- Botón flotante para móvil si no hay header que lo contenga (opcional, por ahora confiamos en que las páginas lo implementen o usen el slot) -->
            <button
                v-if="!sidebarOpen"
                @click="sidebarOpen = true"
                class="fixed bottom-4 right-4 z-50 rounded-full bg-[#005aeb] p-3 text-white shadow-lg lg:hidden"
            >
                <Menu class="h-6 w-6" />
            </button>
        </div>
    </div>
</template>
