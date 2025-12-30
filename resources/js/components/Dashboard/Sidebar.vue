<script setup lang="ts">
/**
 * Sidebar Component - Filosofía "Claridad Vibrante"
 * - Sidebar Inteligente con ítems activos en Indigo-50
 * - Micro-interacciones sutiles
 * - Navegación simplificada
 */
import { Link, usePage } from '@inertiajs/vue3';
// import dashboard from '@/routes/dashboard';
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
    ChevronRight,
    Shield,
    MessageCircle,
    Sparkles,
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

// Detectar si el usuario es admin
const isAdmin = computed(() => {
    const roles = (page.props.auth as any).roles || [];
    return roles.includes('admin');
});

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
        name: 'Plantillas',
        icon: LayoutTemplate,
        href: '/dashboard/plantillas',
        active: page.url.includes('/dashboard/plantillas')
    },
    {
        name: 'Herramientas',
        icon: Wrench,
        href: '#',
        active: false
    },
    {
        name: 'Comunidad',
        icon: MessageCircle,
        href: '/dashboard/comunidad',
        active: page.url === '/dashboard/comunidad'
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
        'fixed inset-y-0 left-0 z-50 w-72 transform border-r border-slate-200/60 bg-white transition-transform duration-300 ease-out lg:static lg:translate-x-0',
        isOpen ? 'translate-x-0' : '-translate-x-full'
    ]">
        <!-- Logo Area -->
        <div class="flex h-16 items-center justify-between border-b border-slate-100 px-6">
            <div class="flex items-center gap-3 group cursor-pointer">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-indigo-600 to-violet-600 shadow-lg shadow-indigo-500/25 transition-transform duration-500 group-hover:rotate-360">
                    <Sparkles class="h-5 w-5 text-white" />
                </div>
                <span class="text-lg font-black tracking-tight bg-linear-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent">
                    PortafolioAI
                </span>
            </div>
            <button 
                @click="emit('close')" 
                class="rounded-xl p-2 text-slate-400 transition-all hover:bg-slate-100 hover:text-slate-600 lg:hidden"
            >
                <X class="h-5 w-5" />
            </button>
        </div>

        <!-- Navigation -->
        <div class="flex h-[calc(100vh-4rem)] flex-col justify-between p-4">
            <nav class="space-y-1">
                <div v-for="item in menuItems" :key="item.name">
                    <Link 
                        :href="item.href" 
                        :class="[
                            'group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-200',
                            item.active
                                ? 'bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-500/10'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <component 
                            :is="item.icon" 
                            :class="[
                                'h-5 w-5 transition-all duration-200 group-hover:scale-110',
                                item.active ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600'
                            ]" 
                        />
                        <span>{{ item.name }}</span>
                        <!-- Active indicator -->
                        <div 
                            v-if="item.active" 
                            class="ml-auto h-1.5 w-1.5 rounded-full bg-indigo-500"
                        ></div>
                    </Link>
                </div>
            </nav>

            <!-- Secondary Navigation -->
            <div class="space-y-4">
                <div class="border-t border-slate-100 pt-4">
                    <div class="space-y-1">
                        <!-- Panel Admin (solo para admins) -->
                        <Link 
                            v-if="isAdmin" 
                            href="/admin"
                            :class="[
                                'group flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-medium transition-all duration-200',
                                $page.url.startsWith('/admin')
                                    ? 'bg-violet-50 text-violet-700'
                                    : 'text-slate-600 hover:bg-violet-50 hover:text-violet-700'
                            ]"
                        >
                            <Shield 
                                class="h-5 w-5 transition-transform duration-200 group-hover:scale-110" 
                                :class="[
                                    $page.url.startsWith('/admin') ? 'text-violet-600' : 'text-slate-400 group-hover:text-violet-600'
                                ]" 
                            />
                            <span>Panel Admin</span>
                        </Link>

                        <!-- Notificaciones -->
                        <Link 
                            href="#"
                            class="group flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:text-slate-900"
                        >
                            <Bell class="h-5 w-5 text-slate-400 transition-transform duration-200 group-hover:scale-110" />
                            <span>Notificaciones</span>
                            <span class="ml-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-red-100 px-1.5 text-xs font-semibold text-red-600">
                                3
                            </span>
                        </Link>

                        <!-- Configuración (Floating Dropdown) -->
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <button class="w-full group flex items-center justify-between gap-3 rounded-2xl px-4 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:text-slate-900 outline-none">
                                    <div class="flex items-center gap-3">
                                        <Settings2 class="h-5 w-5 text-slate-400 transition-transform duration-300 group-hover:rotate-90" />
                                        <span>Configuración</span>
                                    </div>
                                    <ChevronRight class="h-4 w-4 text-slate-400 opacity-0 transition-all duration-200 group-hover:opacity-100" />
                                </button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent side="right" align="start" :side-offset="20" class="w-56 rounded-2xl border-slate-200 p-2 shadow-xl">
                                <DropdownMenuLabel class="px-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    Configuración
                                </DropdownMenuLabel>
                                <DropdownMenuSeparator class="bg-slate-100" />
                                <DropdownMenuItem v-for="item in settingsItems" :key="item.name" as-child>
                                    <Link 
                                        :href="item.href" 
                                        :class="[
                                            'flex w-full cursor-pointer items-center gap-2 rounded-xl px-2 py-2 text-sm',
                                            item.active ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50'
                                        ]"
                                    >
                                        <component :is="item.icon" class="h-4 w-4" />
                                        <span>{{ item.name }}</span>
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>

                        <!-- Ayuda -->
                        <Link 
                            href="#"
                            class="group flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:text-slate-900"
                        >
                            <HelpCircle class="h-5 w-5 text-slate-400 transition-transform duration-200 group-hover:scale-110" />
                            <span>Ayuda</span>
                        </Link>
                    </div>
                </div>

                <!-- User Profile Footer -->
                <div class="mt-auto border-t border-slate-100 pt-4">
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-3 transition-all duration-200 hover:bg-slate-100 group">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Avatar -->
                            <img 
                                v-if="user.avatar_url" 
                                :src="user.avatar_url" 
                                :alt="user.full_name"
                                class="h-10 w-10 shrink-0 rounded-xl object-cover shadow-sm transition-transform duration-200 group-hover:scale-105"
                                @error="(e) => (e.target as HTMLImageElement).style.display = 'none'"
                            >
                            <div 
                                v-if="!user.avatar_url"
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-indigo-600 to-violet-600 text-sm font-semibold text-white shadow-sm transition-transform duration-200 group-hover:scale-105"
                            >
                                {{ user.initials }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ user.full_name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ user.email }}</p>
                            </div>
                        </div>
                        <Link 
                            :href="logout()" 
                            method="post" 
                            as="button"
                            class="rounded-xl p-2 text-slate-400 transition-all duration-200 hover:bg-red-50 hover:text-red-500"
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