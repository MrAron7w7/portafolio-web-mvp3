<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import dashboard from '@/routes/dashboard';
import { computed } from 'vue';
import {
    Folder,
    LayoutTemplate,
    Wrench,
    X,
    Settings,
    HelpCircle,
    Bell,
    ChevronDown,
    LogOut
} from 'lucide-vue-next';
import { logout } from '@/routes';

defineProps<{
    isOpen: boolean;
}>();

const emit = defineEmits(['close']);

const page = usePage();
const authUser = page.props.auth.user;

const user = computed(() => ({
    name: authUser.name,
    email: authUser.email,
    avatar: authUser.name.charAt(0).toUpperCase(),
}));

const menuItems = computed(() => [
    {
        name: 'Mis proyectos',
        icon: Folder,
        href: '/dashboard',
        active: page.url === '/dashboard' || page.url === '/dashboard/'
    },
    {
        name: 'Mis plantillas',
        icon: LayoutTemplate,
        href: dashboard.template.url(),
        active: page.url.includes(dashboard.template.url())
    },
    {
        name: 'Mis herramientas',
        icon: Wrench,
        href: '#',
        active: false
    }
]);

const secondaryItems = [
    {
        name: 'Notificaciones',
        icon: Bell,
        href: '#',
        badge: 3
    },
    {
        name: 'Configuración',
        icon: Settings,
        href: '#'
    },
    {
        name: 'Ayuda',
        icon: HelpCircle,
        href: '#'
    }
];
</script>

<template>
    <aside :class="[
        'fixed inset-y-0 left-0 z-50 w-64 transform bg-white border-r border-gray-200/60 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0',
        isOpen ? 'translate-x-0' : '-translate-x-full'
    ]">
        <!-- Logo Area -->
        <div class="flex h-16 items-center justify-between px-6 border-b border-gray-100">
            <div class="flex items-center space-x-2">
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-[#005aeb] to-[#7B2FF7]">
                    <span class="text-lg font-bold text-white">P</span>
                </div>
                <span class="text-lg font-bold text-gray-900">Portafolio</span>
            </div>
            <button @click="emit('close')" class="lg:hidden rounded-lg p-1 text-gray-500 hover:bg-gray-100">
                <X class="h-5 w-5" />
            </button>
        </div>

        <!-- Navigation -->
        <div class="flex flex-col justify-between h-[calc(100vh-4rem)] p-4">
            <nav class="space-y-1">
                <div v-for="item in menuItems" :key="item.name">
                    <Link :href="item.href" :class="[
                        'flex items-center space-x-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200',
                        item.active
                            ? 'bg-blue-50 text-[#005aeb] shadow-sm ring-1 ring-blue-100'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    ]">
                    <component :is="item.icon" :class="[
                        'h-5 w-5 transition-colors',
                        item.active ? 'text-[#005aeb]' : 'text-gray-400 group-hover:text-gray-600'
                    ]" />
                    <span>{{ item.name }}</span>
                    </Link>
                </div>
            </nav>

            <!-- Secondary Navigation -->
            <div class="space-y-4">
                <div class="pt-4 border-t border-gray-100">
                    <div class="space-y-1">
                        <Link v-for="item in secondaryItems" :key="item.name" :href="item.href"
                            class="flex items-center space-x-3 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-gray-50 hover:text-gray-900">
                        <component :is="item.icon" class="h-5 w-5 text-gray-400" />
                        <span>{{ item.name }}</span>
                        <span v-if="item.badge"
                            class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-red-100 text-xs font-medium text-red-600">
                            {{ item.badge }}
                        </span>
                        </Link>
                    </div>
                </div>

                <!-- User Profile Summary (Optional, nice for sidebar) -->
                <!-- User Profile Footer -->
                <div class="border-t border-gray-100 pt-4 mt-auto">
                    <div class="flex items-center justify-between p-2 rounded-xl bg-gray-50 border border-gray-100">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#005aeb] to-[#7B2FF7] text-sm font-semibold text-white shadow-sm">
                                {{ user.avatar }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ user.name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                            </div>
                        </div>
                        <Link :href="logout()" method="post" as="button"
                            class="ml-2 rounded-lg p-2 text-red-600 transition-all duration-200" title="Cerrar sesión">
                        <LogOut class="h-5 w-5" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</template>
