<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BarChart3,
    ChevronLeft,
    LayoutDashboard,
    Menu,
    Settings,
    Users,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    title?: string;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);

const navigation = [
    { name: 'Dashboard', href: '/admin', icon: LayoutDashboard },
    { name: 'Usuarios', href: '/admin/users', icon: Users },
    { name: 'Reportes', href: '/admin/reports', icon: BarChart3 },
    { name: 'Contenido del sitio', href: '/admin/settings', icon: Settings },
];

const sidebarOpen = ref(true);
const mobileSidebarOpen = ref(false);

const isActive = (href: string) => {
    if (href === '/admin') {
        return page.url === '/admin' || page.url === '/admin/';
    }
    return page.url.startsWith(href);
};
</script>

<template>
    <div class="flex min-h-screen bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
        <!-- Mobile Sidebar Overlay -->
        <div 
            v-if="mobileSidebarOpen" 
            class="fixed inset-0 z-40 bg-black/50 lg:hidden backdrop-blur-sm"
            @click="mobileSidebarOpen = false"
        ></div>

        <!-- Sidebar -->
        <aside :class="[
            'fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transition-all duration-300',
            sidebarOpen ? 'w-64' : 'w-20',
            mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        ]">
            <!-- Logo -->
            <div class="flex h-16 items-center justify-between px-4 border-b border-slate-100 dark:border-slate-800">
                <Link href="/admin" class="flex items-center space-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-[#005aeb] to-[#3b82f6] shadow-lg shadow-blue-500/30">
                        <LayoutDashboard class="h-5 w-5 text-white" />
                    </div>
                    <span v-if="sidebarOpen" class="text-lg font-bold text-slate-900 dark:text-white">Admin</span>
                </Link>
                <button 
                    @click="mobileSidebarOpen = false" 
                    class="lg:hidden rounded-lg p-1 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800"
                >
                    <X class="h-5 w-5" />
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1 px-3 py-4 overflow-y-auto overflow-x-hidden custom-scrollbar">
                <p v-if="sidebarOpen" class="px-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">
                    Menú Principal
                </p>
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        'flex items-center space-x-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200',
                        isActive(item.href)
                            ? 'bg-blue-50 dark:bg-blue-900/20 text-[#005aeb] dark:text-blue-400 shadow-sm'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-200'
                    ]"
                >
                    <component 
                        :is="item.icon" 
                        :class="[
                            'h-5 w-5 shrink-0 transition-colors',
                            isActive(item.href) ? 'text-[#005aeb] dark:text-blue-400' : 'text-slate-400 dark:text-slate-500'
                        ]" 
                    />
                    <span v-if="sidebarOpen" class="truncate">{{ item.name }}</span>
                </Link>
            </nav>

            <!-- Footer -->
            <div class="border-t border-slate-100 dark:border-slate-800 p-4">
                <Link
                    href="/dashboard"
                    class="flex items-center space-x-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-600 dark:text-slate-400 transition-all hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-200"
                >
                    <ChevronLeft class="h-5 w-5 text-slate-400 dark:text-slate-500" />
                    <span v-if="sidebarOpen" class="truncate">Dashboard</span>
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <div :class="[
            'flex-1 transition-all duration-300',
            sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'
        ]">
            <!-- Header -->
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md px-4 lg:px-6 shadow-sm">
                <div class="flex items-center space-x-4">
                    <!-- Mobile menu button -->
                    <button 
                        @click="mobileSidebarOpen = true"
                        class="lg:hidden rounded-lg p-2 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800"
                    >
                        <Menu class="h-5 w-5" />
                    </button>
                    
                    <!-- Breadcrumb -->
                    <div class="flex items-center space-x-2 text-sm">
                        <span class="text-slate-400 dark:text-slate-500">Home</span>
                        <span class="text-slate-300 dark:text-slate-700">/</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ title || 'Dashboard' }}</span>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- User -->
                    <div class="flex items-center space-x-3">
                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-medium text-slate-900 dark:text-white">
                                {{ user.first_name }} {{ user.last_name }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Administrador</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-linear-to-br from-[#005aeb] to-[#3b82f6] text-white text-sm font-medium shadow-lg shadow-blue-500/20">
                            {{ user.first_name?.charAt(0) }}{{ user.last_name?.charAt(0) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 lg:p-6 min-h-[calc(100vh-4rem)]">
                <slot />
            </main>
        </div>
    </div>
</template>
