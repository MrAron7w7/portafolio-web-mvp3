<script setup lang="ts">
/**
 * Dashboard Layout - Filosofía "Claridad Vibrante"
 * - Estructura fluida y responsiva
 * - Transiciones suaves
 * - Custom scrollbar integrado
 */
import Sidebar from '@/components/Dashboard/Sidebar.vue';
import { Menu } from 'lucide-vue-next';
import { ref } from 'vue';

const sidebarOpen = ref(false);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
        <!-- Sidebar -->
        <Sidebar :is-open="sidebarOpen" @close="sidebarOpen = false" />

        <!-- Sidebar Overlay for mobile -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="sidebarOpen"
                class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false"
            ></div>
        </Transition>

        <!-- Main Content Area -->
        <div class="flex min-w-0 flex-1 flex-col overflow-y-auto">
            <slot :toggle-sidebar="toggleSidebar" :is-sidebar-open="sidebarOpen" />
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar for the entire layout */
:deep(*) {
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 transparent;
}

:deep(*)::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

:deep(*)::-webkit-scrollbar-track {
    background: transparent;
}

:deep(*)::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 3px;
}

:deep(*)::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}
</style>
