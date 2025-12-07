<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import dashboard from '@/routes/dashboard';
import { computed } from 'vue';
import {
    Folder,
    LayoutTemplate,
    Wrench,
    X,
    HelpCircle,
    Bell,
    LogOut,
    Settings2,
    ChevronRight
} from 'lucide-vue-next';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { logout } from '@/routes';

defineProps<{
    isOpen: boolean;
}>();

const emit = defineEmits(['close']);

const page = usePage();
const authUser = page.props.auth.user;

const user = computed(() => {
    const firstName = authUser.first_name || '';
    const lastName = authUser.last_name || '';
    const fullName = `${firstName} ${lastName}`.trim() || 'Usuario';
    const initials = `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase() || 'U';
    
    return {
        first_name: firstName,
        last_name: lastName,
        full_name: fullName,
        email: authUser.email || '',
        avatar_url: authUser.avatar_url || '',
        initials: initials,
    };
});

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

const settingsItems = computed(() => [
    {
        name: 'Ajustes de Cuenta',
        icon: Settings2,
        href: '/settings/general',
        active: page.url === '/settings/general'
    }
]);
</script>

<template>
    <aside :class="[
        'fixed inset-y-0 left-0 z-50 w-64 transform bg-white border-r border-gray-200/60 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0',
        isOpen ? 'translate-x-0' : '-translate-x-full'
    ]">
        <!-- Logo Area -->
        <div class="flex h-16 items-center justify-between px-6 border-b border-gray-100">
            <div class="flex items-center space-x-2 group cursor-pointer">
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-[#005aeb] to-[#7B2FF7] transition-transform duration-500 group-hover:rotate-[360deg] shadow-lg shadow-blue-500/30">
                    <span class="text-lg font-bold text-white">P</span>
                </div>
                <span
                    class="text-lg font-bold text-gray-900 bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 transition-all duration-300 group-hover:tracking-wider">
                    Portafolio
                </span>
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
                        'group flex items-center space-x-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-300 ease-out hover:scale-105 hover:shadow-lg hover:shadow-blue-500/10 hover:bg-white border border-transparent hover:border-blue-100 relative overflow-hidden',
                        item.active
                            ? 'bg-blue-50 text-[#005aeb] shadow-md shadow-blue-500/20 ring-1 ring-blue-100 scale-105'
                            : 'text-gray-600 hover:text-[#005aeb]'
                    ]">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-50/0 via-blue-50/50 to-blue-50/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                        <component :is="item.icon" :class="[
                            'h-5 w-5 transition-all duration-300 group-hover:rotate-12 group-hover:scale-110',
                            item.active ? 'text-[#005aeb] rotate-0' : 'text-gray-400 group-hover:text-[#005aeb]'
                        ]" />
                        <span class="relative z-10">{{ item.name }}</span>
                    </Link>
                </div>
            </nav>

            <!-- Secondary Navigation -->
            <div class="space-y-4">
                <div class="pt-4 border-t border-gray-100">
                    <div class="space-y-1">
                        <!-- Notificaciones -->
                        <Link href="#"
                            class="group flex items-center space-x-3 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 transition-all duration-300 hover:bg-gray-50 hover:text-gray-900 hover:translate-x-1">
                            <Bell class="h-5 w-5 text-gray-400 transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-12" />
                            <span>Notificaciones</span>
                            <span class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-red-100 text-xs font-medium text-red-600 animate-pulse">
                                3
                            </span>
                        </Link>

                        <!-- Configuración (Floating Dropdown) -->
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <button
                                    class="w-full group flex items-center justify-between space-x-3 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 transition-all duration-300 hover:bg-gray-50 hover:text-gray-900 hover:translate-x-1 outline-none">
                                    <div class="flex items-center space-x-3">
                                        <Settings2 class="h-5 w-5 text-gray-400 transition-transform duration-300 group-hover:rotate-90" />
                                        <span>Configuración</span>
                                    </div>
                                    <ChevronRight class="h-4 w-4 text-gray-400 opacity-0 group-hover:opacity-100 transition-all duration-300" />
                                </button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent side="right" align="start" :side-offset="20" class="w-56 p-2">
                                <DropdownMenuLabel class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Configuración
                                </DropdownMenuLabel>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem v-for="item in settingsItems" :key="item.name" as-child>
                                    <Link :href="item.href" :class="[
                                        'flex w-full items-center space-x-2 rounded-lg px-2 py-2 text-sm cursor-pointer',
                                        item.active ? 'bg-blue-50 text-[#005aeb]' : 'text-gray-600 hover:bg-gray-50'
                                    ]">
                                        <component :is="item.icon" class="h-4 w-4" />
                                        <span>{{ item.name }}</span>
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>

                        <!-- Ayuda -->
                        <Link href="#"
                            class="group flex items-center space-x-3 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 transition-all duration-300 hover:bg-gray-50 hover:text-gray-900 hover:translate-x-1">
                            <HelpCircle class="h-5 w-5 text-gray-400 transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-12" />
                            <span>Ayuda</span>
                        </Link>
                    </div>
                </div>

                <!-- User Profile Footer -->
                <div class="border-t border-gray-100 pt-4 mt-auto">
                    <div class="flex items-center justify-between p-2 rounded-xl bg-gray-50 border border-gray-100 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/10 hover:border-purple-100 hover:-translate-y-1 group">
                        <div class="flex items-center space-x-3 min-w-0">
                            <!-- Avatar con imagen o iniciales -->
                            <img 
                                v-if="user.avatar_url" 
                                :src="user.avatar_url" 
                                :alt="user.full_name"
                                class="h-9 w-9 shrink-0 rounded-full object-cover shadow-sm transition-transform duration-500 group-hover:scale-110 group-hover:rotate-12"
                                @error="(e) => (e.target as HTMLImageElement).style.display = 'none'"
                            >
                            <div 
                                v-if="!user.avatar_url"
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#005aeb] to-[#7B2FF7] text-sm font-semibold text-white shadow-sm transition-transform duration-500 group-hover:scale-110 group-hover:rotate-12"
                            >
                                {{ user.initials }}
                            </div>
                            <div class="min-w-0 flex-1 transition-all duration-300 group-hover:translate-x-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ user.full_name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                            </div>
                        </div>
                        <Link 
                            :href="logout()" 
                            method="post" 
                            as="button"
                            class="ml-2 rounded-lg p-2 text-black hover:text-red-600 transition-colors duration-200"
                            title="Cerrar sesión"
                        >
                            <LogOut class="h-5 w-5" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</template>