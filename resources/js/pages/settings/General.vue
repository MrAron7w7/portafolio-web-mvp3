<script setup lang="ts">
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import { useAppearance } from '@/composables/useAppearance';
import { route } from '@/utils/route';
import { 
    User as UserIcon, 
    Globe, 
    Bell, 
    Lock, 
    Palette, 
    Settings, 
    Trash2, 
    ShieldCheck,
    Moon,
    Sun,
    Layers as IntegrationIcon
} from 'lucide-vue-next';

const { appearance, updateAppearance } = useAppearance();

interface User {
    id: number;
    first_name: string;
    last_name: string;
    full_name: string;
    email: string;
    avatar_url: string;
    timezone: string;
    language: string;
    email_notifications: boolean;
    marketing_emails: boolean;
    status: string;
    created_at: string;
    last_login_at: string | null;
    two_factor_enabled: boolean;
}

interface Props {
    user: User;
    status?: string;
}

const props = defineProps<Props>();

// UI State
const editingProfile = ref(false);
const editingPreferences = ref(false);
const editingNotifications = ref(false);
const showDeleteModal = ref(false);
const avatarPreview = ref<string | null>(null);

// Forms
const profileForm = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    email: props.user.email,
    avatar: null as File | null,
});

const preferencesForm = useForm({
    language: props.user.language,
    timezone: props.user.timezone,
});

const notificationsForm = useForm({
    email_notifications: props.user.email_notifications,
    marketing_emails: props.user.marketing_emails,
});

const deleteForm = useForm({
    password: '',
});

const changePasswordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const isChangingPassword = ref(false);

const toggleChangePassword = () => {
    if (isChangingPassword.value) {
        changePasswordForm.reset();
        changePasswordForm.clearErrors();
    }
    isChangingPassword.value = !isChangingPassword.value;
};

const updatePassword = () => {
    changePasswordForm.put(route('settings.password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            isChangingPassword.value = false;
            changePasswordForm.reset();
            // Optional: Show success notification logic if needed
        },
        onError: () => {
            if (changePasswordForm.errors.password) {
                changePasswordForm.reset('password', 'password_confirmation');
            }
            if (changePasswordForm.errors.current_password) {
                changePasswordForm.reset('current_password');
            }
        },
    });
};

// Methods
const handleAvatarChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (file) {
        if (file.size > 8 * 1024 * 1024) {
            alert('La imagen no debe superar los 8MB');
            return;
        }
        if (!['image/jpeg', 'image/jpg', 'image/png', 'image/gif'].includes(file.type)) {
            alert('Solo se permiten imágenes JPG, PNG o GIF');
            return;
        }

        profileForm.avatar = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const handleImageError = (event: Event) => {
    const img = event.target as HTMLImageElement;
    img.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(props.user.full_name)}&background=4F46E5&color=fff&size=256`;
};

const toggleEditProfile = () => {
    if (editingProfile.value) {
        profileForm.reset();
        avatarPreview.value = null;
    }
    editingProfile.value = !editingProfile.value;
};

const toggleEditPreferences = () => {
    if (editingPreferences.value) {
        preferencesForm.reset();
    }
    editingPreferences.value = !editingPreferences.value;
};

const submitProfile = () => {
    profileForm.post('/settings/general', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            editingProfile.value = false;
            avatarPreview.value = null;
            profileForm.avatar = null;
            window.location.reload();
        },
    });
};

const submitPreferences = () => {
    preferencesForm.patch('/settings/general', {
        preserveScroll: true,
        onSuccess: () => {
            editingPreferences.value = false;
        },
    });
};

const submitNotifications = () => {
    notificationsForm.patch('/settings/general', {
        preserveScroll: true,
        onSuccess: () => {
            editingNotifications.value = false;
        },
    });
};

const openDeleteModal = () => {
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    deleteForm.reset();
};

const deleteAccount = () => {
    deleteForm.delete('/settings/general', {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
    });
};

const removeAvatar = () => {
    if (confirm('¿Estás seguro de eliminar tu foto de perfil?')) {
        profileForm.post('/settings/general/remove-avatar', {
            preserveScroll: true,
            onSuccess: () => {
                avatarPreview.value = null;
                window.location.reload();
            },
        });
    }
};



const enableTwoFactorForm = useForm({});
const disableTwoFactorForm = useForm({});

const toggleTwoFactor = () => {
    if (props.user.two_factor_enabled) {
        // Disable
        if (confirm('¿Estás seguro de desactivar la autenticación de dos factores?')) {
            disableTwoFactorForm.delete(route('user.email-two-factor.disable'), {
                preserveScroll: true,
                onSuccess: () => {
                    // Update user prop or reload logic handled by Inertia automatically
                },
            });
        }
    } else {
        // Enable
        enableTwoFactorForm.post(route('user.email-two-factor.enable'), {
            preserveScroll: true,
            onSuccess: () => {
                 // Update user prop or reload logic handled by Inertia automatically
            },
        });
    }
};
</script>


<template>
    <Head title="Configuración General" />

    <DashboardLayout>
        <div class="p-6 max-w-5xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Configuración General
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">
                        Administra tu perfil, preferencias de idioma y personalización para que tu experiencia sea única.
                    </p>
                </div>
            </div>

            <!-- Success Alert -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform -translate-y-2 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div 
                    v-if="status === 'general-updated' || status === 'password-updated'" 
                    class="p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-2xl flex items-center gap-3"
                >
                    <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <ShieldCheck class="w-5 h-5" />
                    </div>
                    <span class="text-emerald-800 dark:text-emerald-400 font-medium">
                        {{ status === 'password-updated' ? 'Contraseña actualizada correctamente' : 'Cambios guardados correctamente' }}
                    </span>
                </div>
            </Transition>

            <div class="grid grid-cols-1 gap-8">
                
                <!-- 1. PERFIL DE USUARIO -->
                <section class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden transition-all duration-300">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <UserIcon class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Perfil de Usuario</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Tus datos personales básicos</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button 
                                @click="toggleEditProfile"
                                type="button"
                                class="px-5 py-2.5 text-sm font-semibold rounded-xl transition-all active:scale-95 flex items-center gap-2"
                                :class="editingProfile 
                                    ? 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-600' 
                                    : 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200 dark:shadow-none'"
                            >
                                {{ editingProfile ? 'Cancelar' : 'Editar Perfil' }}
                            </button>
                        </div>
                    </div>

                    <form @submit.prevent="submitProfile" class="p-8">
                        <div class="flex flex-col md:flex-row gap-10">
                            <!-- Avatar Column -->
                            <div class="flex flex-col items-center gap-4">
                                <div class="relative group">
                                    <div class="w-32 h-32 rounded-full p-1 bg-linear-to-tr from-indigo-500 to-violet-500 shadow-xl">
                                        <img 
                                            :src="avatarPreview || user.avatar_url" 
                                            alt="Avatar" 
                                            class="w-full h-full rounded-full object-cover border-4 border-white dark:border-slate-900"
                                            @error="handleImageError"
                                        >
                                    </div>
                                    <label v-if="editingProfile" class="absolute inset-x-0 bottom-0 top-0 flex items-center justify-center bg-black/40 rounded-full opacity-0 group-hover:opacity-100 transition cursor-pointer backdrop-blur-[2px]">
                                        <div class="flex flex-col items-center text-white">
                                            <Camera class="w-6 h-6 mb-1" />
                                            <span class="text-[10px] font-bold uppercase tracking-wider">Cambiar</span>
                                        </div>
                                        <input type="file" accept="image/*" @change="handleAvatarChange" class="hidden">
                                    </label>
                                </div>
                                
                                <button 
                                    v-if="editingProfile && user.avatar_url && !user.avatar_url.includes('ui-avatars.com')"
                                    @click="removeAvatar"
                                    type="button"
                                    class="text-xs text-red-500 hover:text-red-700 font-bold transition-colors"
                                >
                                    Eliminar foto
                                </button>
                            </div>

                            <!-- Fields Column -->
                            <div class="flex-1 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Nombre</label>
                                        <input 
                                            v-if="editingProfile"
                                            v-model="profileForm.first_name"
                                            type="text" 
                                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition dark:text-white"
                                            :class="{ 'border-red-500': profileForm.errors.first_name }"
                                        >
                                        <p v-else class="text-slate-900 dark:text-white font-medium py-1 px-1">{{ user.first_name }}</p>
                                        <p v-if="profileForm.errors.first_name" class="text-xs text-red-500">{{ profileForm.errors.first_name }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Apellido</label>
                                        <input 
                                            v-if="editingProfile"
                                            v-model="profileForm.last_name"
                                            type="text" 
                                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition dark:text-white"
                                            :class="{ 'border-red-500': profileForm.errors.last_name }"
                                        >
                                        <p v-else class="text-slate-900 dark:text-white font-medium py-1 px-1">{{ user.last_name }}</p>
                                        <p v-if="profileForm.errors.last_name" class="text-xs text-red-500">{{ profileForm.errors.last_name }}</p>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Correo Electrónico</label>
                                    <input 
                                        v-if="editingProfile"
                                        v-model="profileForm.email"
                                        type="email" 
                                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition dark:text-white"
                                        :class="{ 'border-red-500': profileForm.errors.email }"
                                    >
                                    <p v-else class="text-slate-900 dark:text-white font-medium py-1 px-1">{{ user.email }}</p>
                                    <p v-if="profileForm.errors.email" class="text-xs text-red-500">{{ profileForm.errors.email }}</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                                    <div class="space-y-1">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">Miembro desde</span>
                                        <p class="text-slate-600 dark:text-slate-400 text-sm">{{ user.created_at }}</p>
                                    </div>
                                    <div v-if="user.last_login_at" class="space-y-1">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">Último acceso</span>
                                        <p class="text-slate-600 dark:text-slate-400 text-sm">{{ user.last_login_at }}</p>
                                    </div>
                                </div>

                                <!-- Password Change Section -->
                                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 transition-opacity duration-300" :class="{ 'opacity-50 grayscale': !editingProfile }">
                                    <button 
                                        @click="toggleChangePassword"
                                        type="button"
                                        :disabled="!editingProfile"
                                        class="text-indigo-600 dark:text-indigo-400 font-bold text-sm hover:underline flex items-center gap-2 disabled:cursor-not-allowed disabled:no-underline"
                                    >
                                        <Lock class="w-4 h-4" />
                                        Cambiar de contraseña
                                    </button>

                                    <div v-if="isChangingPassword" class="mt-4 space-y-4 bg-slate-50 dark:bg-slate-950 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 animate-in fade-in slide-in-from-top-2 duration-300">
                                        <div class="space-y-2">
                                            <label class="text-xs font-bold text-slate-500 uppercase">Contraseña Actual</label>
                                            <input 
                                                v-model="changePasswordForm.current_password"
                                                type="password" 
                                                placeholder="Tu contraseña actual"
                                                class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 transition-all dark:text-white text-sm"
                                            >
                                            <p v-if="changePasswordForm.errors.current_password" class="text-xs text-red-500 font-bold px-1">{{ changePasswordForm.errors.current_password }}</p>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label class="text-xs font-bold text-slate-500 uppercase">Nueva Contraseña</label>
                                                <input 
                                                    v-model="changePasswordForm.password"
                                                    type="password" 
                                                    placeholder="Nueva contraseña"
                                                    class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 transition-all dark:text-white text-sm"
                                                >
                                                <p v-if="changePasswordForm.errors.password" class="text-xs text-red-500 font-bold px-1">{{ changePasswordForm.errors.password }}</p>
                                            </div>

                                            <div class="space-y-2">
                                                <label class="text-xs font-bold text-slate-500 uppercase">Confirmar Contraseña</label>
                                                <input 
                                                    v-model="changePasswordForm.password_confirmation"
                                                    type="password" 
                                                    placeholder="Repite la nueva contraseña"
                                                    class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 transition-all dark:text-white text-sm"
                                                >
                                                <p v-if="changePasswordForm.errors.password_confirmation" class="text-xs text-red-500 font-bold px-1">{{ changePasswordForm.errors.password_confirmation }}</p>
                                            </div>
                                        </div>

                                        <div class="flex gap-3 pt-2">
                                            <button 
                                                type="button"
                                                @click="toggleChangePassword"
                                                class="px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition text-sm"
                                            >
                                                Cancelar
                                            </button>
                                            <button 
                                                type="button"
                                                @click="updatePassword"
                                                class="px-4 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 dark:shadow-none transition disabled:opacity-50 text-sm flex items-center gap-2"
                                                :disabled="changePasswordForm.processing"
                                            >
                                                <span v-if="changePasswordForm.processing" class="animate-spin text-white">⟳</span>
                                                {{ changePasswordForm.processing ? 'Guardando...' : 'Actualizar Contraseña' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <Transition
                                    enter-active-class="transition duration-300"
                                    enter-from-class="opacity-0 translate-y-2"
                                    enter-to-class="opacity-100 translate-y-0"
                                >
                                    <div v-if="editingProfile" class="flex justify-end pt-4">
                                        <button 
                                            type="submit"
                                            class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 dark:shadow-none disabled:opacity-50 flex items-center gap-2"
                                            :disabled="profileForm.processing"
                                        >
                                            <Settings v-if="profileForm.processing" class="animate-spin w-4 h-4" />
                                            {{ profileForm.processing ? 'Guardando...' : 'Guardar Cambios' }}
                                        </button>
                                    </div>
                                </Transition>
                            </div>
                        </div>
                    </form>
                </section>

                <!-- 2. IDIOMA Y REGIÓN -->
                <section class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden transition-all duration-300">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                <Globe class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Idioma y Región</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Localización y formato</p>
                            </div>
                        </div>
                        <button 
                            @click="toggleEditPreferences"
                            type="button"
                            class="px-5 py-2.5 text-sm font-semibold rounded-xl transition-all active:scale-95"
                            :class="editingPreferences 
                                ? 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-600' 
                                : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        >
                            {{ editingPreferences ? 'Cancelar' : 'Cambiar' }}
                        </button>
                    </div>

                    <form @submit.prevent="submitPreferences" class="p-8 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Idioma</label>
                                <select 
                                    v-if="editingPreferences"
                                    v-model="preferencesForm.language"
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition dark:text-white"
                                >
                                    <option value="es" class="dark:bg-slate-900">Español</option>
                                    <option value="en" class="dark:bg-slate-900">English</option>
                                    <option value="pt" class="dark:bg-slate-900">Português</option>
                                </select>
                                <div v-else class="flex items-center gap-3">
                                    <span class="text-2xl">🇪🇸</span>
                                    <p class="text-slate-900 dark:text-white font-medium">
                                        {{ user.language === 'es' ? 'Español' : user.language === 'en' ? 'English' : 'Português' }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Zona Horaria</label>
                                <select 
                                    v-if="editingPreferences"
                                    v-model="preferencesForm.timezone"
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition dark:text-white"
                                >
                                    <option value="America/Lima" class="dark:bg-slate-900">Lima (GMT-5)</option>
                                    <option value="America/Mexico_City" class="dark:bg-slate-900">Ciudad de México (GMT-6)</option>
                                    <option value="America/New_York" class="dark:bg-slate-900">Nueva York (GMT-5)</option>
                                    <option value="Europe/Madrid" class="dark:bg-slate-900">Madrid (GMT+1)</option>
                                </select>
                                <div v-else class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <p class="text-slate-900 dark:text-white font-medium">{{ user.timezone }}</p>
                                </div>
                            </div>
                        </div>

                        <Transition
                            enter-active-class="transition duration-300"
                            enter-from-class="opacity-0 translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                        >
                            <div v-if="editingPreferences" class="flex justify-end">
                                <button 
                                    type="submit"
                                    class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 dark:shadow-none"
                                >
                                    Guardar Preferencias
                                </button>
                            </div>
                        </Transition>
                    </form>
                </section>

                <!-- 3. APARIENCIA / TEMA -->
                <section class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden transition-all duration-300">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <Palette class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Apariencia</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Personaliza tu entorno de trabajo</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="flex items-center justify-between p-6 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-100 dark:border-slate-800 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-white dark:bg-slate-900 flex items-center justify-center border border-slate-200 dark:border-slate-800 shadow-sm transition-colors">
                                    <Moon v-if="appearance === 'dark'" class="w-6 h-6 text-indigo-500" />
                                    <Sun v-else class="w-6 h-6 text-amber-500" />
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">Modo Oscuro</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Reduce el cansancio visual y ahorra energía.</p>
                                </div>
                            </div>
                            
                            <button 
                                @click="updateAppearance(appearance === 'dark' ? 'light' : 'dark')"
                                type="button"
                                class="relative inline-flex h-8 w-14 items-center rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                                :class="appearance === 'dark' ? 'bg-indigo-600' : 'bg-slate-200'"
                            >
                                <span 
                                    class="inline-block h-6 w-6 transform rounded-full bg-white shadow-md transition-transform duration-300 ease-spring"
                                    :class="appearance === 'dark' ? 'translate-x-7' : 'translate-x-1'"
                                ></span>
                            </button>
                        </div>
                    </div>
                </section>

                <!-- 4. NOTIFICACIONES -->
                <section class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden transition-all duration-300">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <Bell class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Notificaciones</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Canales y alertas del sistema</p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitNotifications" class="p-8 space-y-2">
                        <!-- Email Notify -->
                        <div class="group flex items-center justify-between p-4 bg-transparent hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-2xl transition-all cursor-pointer" @click="notificationsForm.email_notifications = !notificationsForm.email_notifications">
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white transition-colors">Alertas por Email</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Cambios importantes en tu cuenta.</p>
                            </div>
                            <button type="button" class="relative inline-flex h-6 w-11 items-center rounded-full transition" :class="notificationsForm.email_notifications ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700'">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition" :class="notificationsForm.email_notifications ? 'translate-x-6' : 'translate-x-1'"></span>
                            </button>
                        </div>

                        <!-- Marketing Notify -->
                        <div class="group flex items-center justify-between p-4 bg-transparent hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-2xl transition-all cursor-pointer" @click="notificationsForm.marketing_emails = !notificationsForm.marketing_emails">
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white transition-colors">Marketing y Sugerencias</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Noticias y consejos del editor.</p>
                            </div>
                            <button type="button" class="relative inline-flex h-6 w-11 items-center rounded-full transition" :class="notificationsForm.marketing_emails ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700'">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition" :class="notificationsForm.marketing_emails ? 'translate-x-6' : 'translate-x-1'"></span>
                            </button>
                        </div>

                        <Transition
                            enter-active-class="transition duration-300"
                            enter-from-class="opacity-0 translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                        >
                            <div v-if="notificationsForm.isDirty" class="flex justify-end pt-6">
                                <button 
                                    type="submit"
                                    class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 dark:shadow-none"
                                >
                                    Guardar Notificaciones
                                </button>
                            </div>
                        </Transition>
                    </form>
                </section>

                <!-- 5. SEGURIDAD (New 2FA Section) -->
                <!-- <section class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden transition-all duration-300">
                     <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-teal-100 dark:bg-teal-500/10 flex items-center justify-center text-teal-600 dark:text-teal-400">
                                <ShieldCheck class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Seguridad</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Protección adicional de tu cuenta</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                         <div class="group flex items-center justify-between p-4 bg-transparent hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-2xl transition-all cursor-pointer" @click="toggleTwoFactor">
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white transition-colors">Doble Factor (2FA)</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Solicitar código de verificación al iniciar sesión.</p>
                            </div>
                             <div class="relative inline-flex h-6 w-11 items-center rounded-full transition" :class="user.two_factor_enabled ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700'">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition" :class="user.two_factor_enabled ? 'translate-x-6' : 'translate-x-1'"></span>
                            </div>
                        </div>
                    </div>
                </section> -->
                
                
                <!-- 6. ZONA DE PELIGRO -->
                <section class="bg-red-50/30 dark:bg-red-950/20 rounded-3xl border border-red-100 dark:border-red-900/30 overflow-hidden">
                    <div class="p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 rounded-2xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                                <Trash2 class="w-8 h-8" />
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-red-900 dark:text-red-300">Eliminar Cuenta</h4>
                                <p class="text-sm text-red-600/80 dark:text-red-400/80 mt-1 max-w-md">
                                    Esta acción eliminará todos tus portafolios, datos y configuraciones. No se puede deshacer.
                                </p>
                            </div>
                        </div>
                        <button 
                            @click="openDeleteModal"
                            type="button"
                            class="px-8 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition active:scale-95 shrink-0"
                        >
                            Eliminar definitivamente
                        </button>
                    </div>
                </section>

            </div>

            <!-- Delete Confirmation Modal -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="closeDeleteModal"></div>
                    
                    <div class="relative bg-white dark:bg-slate-900 w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100 dark:border-slate-800 animate-in zoom-in-95 duration-300">
                        <div class="p-8">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-500/10 flex items-center justify-center text-red-600 dark:text-red-400">
                                    <Trash2 class="w-6 h-6" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">¿Estás seguro?</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 tracking-tight">Escribe tu contraseña para confirmar</p>
                                </div>
                            </div>

                            <form @submit.prevent="deleteAccount" class="space-y-6">
                                <input 
                                    v-model="deleteForm.password"
                                    type="password" 
                                    placeholder="Confirmar con contraseña"
                                    class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl outline-none focus:ring-2 focus:ring-red-500 transition-all dark:text-white"
                                    autofocus
                                >
                                <p v-if="deleteForm.errors.password" class="text-xs text-red-500 font-bold px-1">{{ deleteForm.errors.password }}</p>

                                <div class="flex gap-4">
                                    <button 
                                        type="button"
                                        @click="closeDeleteModal"
                                        class="flex-1 px-4 py-4 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-2xl hover:bg-slate-200 dark:hover:bg-slate-700 transition"
                                    >
                                        Cancelar
                                    </button>
                                    <button 
                                        type="submit"
                                        class="flex-1 px-4 py-4 bg-red-600 text-white font-bold rounded-2xl hover:bg-red-700 shadow-xl shadow-red-200 dark:shadow-none transition disabled:opacity-50"
                                        :disabled="deleteForm.processing"
                                    >
                                        {{ deleteForm.processing ? 'Eliminando...' : 'Eliminar' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </Transition>

        </div>
    </DashboardLayout>
</template>

<style scoped>
.ease-spring {
    transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);
}
</style>