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
    BellRing
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
    { name: 'Notificaciones', href: '/admin/notifications', icon: BellRing },
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
    <div class="flex min-h-screen bg-gray-50">
        <!-- Mobile Sidebar Overlay -->
        <div 
            v-if="mobileSidebarOpen" 
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            @click="mobileSidebarOpen = false"
        ></div>

        <!-- Sidebar -->
        <aside :class="[
            'fixed inset-y-0 left-0 z-50 flex flex-col bg-white border-r border-gray-200 transition-all duration-300',
            sidebarOpen ? 'w-64' : 'w-20',
            mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        ]">
            <!-- Logo -->
            <div class="flex h-16 items-center justify-between px-4 border-b border-gray-100">
                <Link href="/admin" class="flex items-center space-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#005aeb] to-[#3b82f6] shadow-lg shadow-blue-500/30">
                        <LayoutDashboard class="h-5 w-5 text-white" />
                    </div>
                    <span v-if="sidebarOpen" class="text-lg font-bold text-gray-900">Admin</span>
                </Link>
                <button 
                    @click="mobileSidebarOpen = false" 
                    class="lg:hidden rounded-lg p-1 text-gray-400 hover:bg-gray-100"
                >
                    <X class="h-5 w-5" />
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1 px-3 py-4">
                <p v-if="sidebarOpen" class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
                    Menú Principal
                </p>
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        'flex items-center space-x-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200',
                        isActive(item.href)
                            ? 'bg-blue-50 text-[#005aeb] shadow-sm'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    ]"
                >
                    <component 
                        :is="item.icon" 
                        :class="[
                            'h-5 w-5 flex-shrink-0 transition-colors',
                            isActive(item.href) ? 'text-[#005aeb]' : 'text-gray-400'
                        ]" 
                    />
                    <span v-if="sidebarOpen">{{ item.name }}</span>
                </Link>
            </nav>

            <!-- Footer -->
            <div class="border-t border-gray-100 p-4">
                <Link
                    href="/dashboard"
                    class="flex items-center space-x-3 rounded-xl px-3 py-3 text-sm font-medium text-gray-600 transition-all hover:bg-gray-50 hover:text-gray-900"
                >
                    <ChevronLeft class="h-5 w-5 text-gray-400" />
                    <span v-if="sidebarOpen">Volver al Dashboard</span>
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <div :class="[
            'flex-1 transition-all duration-300',
            sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'
        ]">
            <!-- Header -->
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 lg:px-6 shadow-sm">
                <div class="flex items-center space-x-4">
                    <!-- Mobile menu button -->
                    <button 
                        @click="mobileSidebarOpen = true"
                        class="lg:hidden rounded-lg p-2 text-gray-400 hover:bg-gray-100"
                    >
                        <Menu class="h-5 w-5" />
                    </button>
                    
                    <!-- Breadcrumb -->
                    <div class="flex items-center space-x-2 text-sm">
                        <span class="text-gray-400">Home</span>
                        <span class="text-gray-300">/</span>
                        <span class="font-medium text-gray-900">{{ title || 'Dashboard' }}</span>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- User -->
                    <div class="flex items-center space-x-3">
                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-medium text-gray-900">
                                {{ user.first_name }} {{ user.last_name }}
                            </p>
                            <p class="text-xs text-gray-500">Administrador</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-[#005aeb] to-[#3b82f6] text-white text-sm font-medium shadow-lg shadow-blue-500/20">
                            {{ user.first_name?.charAt(0) }}{{ user.last_name?.charAt(0) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 lg:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
