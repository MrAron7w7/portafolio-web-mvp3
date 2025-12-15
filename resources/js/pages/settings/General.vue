<script setup lang="ts">
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

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
}

interface Props {
    user: User;
    status?: string;
}

const props = defineProps<Props>();

// Estado para controlar qué sección está en modo edición
const editingProfile = ref(false);
const editingPreferences = ref(false);
const editingNotifications = ref(false);
const showDeleteModal = ref(false);
const avatarPreview = ref<string | null>(null);

// Formulario de perfil
const profileForm = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    email: props.user.email,
    avatar: null as File | null,
});

// Formulario de preferencias
const preferencesForm = useForm({
    language: props.user.language,
    timezone: props.user.timezone,
});

// Formulario de notificaciones
const notificationsForm = useForm({
    email_notifications: props.user.email_notifications,
    marketing_emails: props.user.marketing_emails,
});

// Formulario de eliminación de cuenta
const deleteForm = useForm({
    password: '',
});

const handleAvatarChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (file) {
        // Validar tamaño (8MB)
        if (file.size > 8 * 1024 * 1024) {
            alert('La imagen no debe superar los 8MB');
            return;
        }

        // Validar tipo
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

const toggleEditNotifications = () => {
    editingNotifications.value = !editingNotifications.value;
};

const submitProfile = () => {
    // Para archivos, Laravel requiere POST con _method spoofing
    profileForm.post('/settings/general', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            editingProfile.value = false;
            avatarPreview.value = null;
            profileForm.avatar = null;
            // Recargar para ver la nueva imagen
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
</script>

<template>
    <Head title="Configuración General" />

    <DashboardLayout>
        <div class="p-6 max-w-5xl mx-auto">
            
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Configuración General</h1>
                <p class="text-gray-500 mt-1">Administra tu cuenta, preferencias y configuración de la aplicación.</p>
            </div>

            <!-- Success Message -->
            <div 
                v-if="status === 'general-updated'" 
                class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3"
            >
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-green-800 font-medium">Cambios guardados correctamente</span>
            </div>

            <div class="space-y-6">
                
                <!-- 1. PERFIL DE USUARIO -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Perfil de Usuario</h3>
                                <p class="text-sm text-gray-500">Tu información personal y datos de contacto</p>
                            </div>
                        </div>
                        <button 
                            @click="toggleEditProfile"
                            type="button"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition"
                            :class="editingProfile 
                                ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' 
                                : 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100'"
                        >
                            {{ editingProfile ? 'Cancelar' : 'Editar' }}
                        </button>
                    </div>

                    <form @submit.prevent="submitProfile" class="p-6">
                        <!-- Avatar Section -->
                        <div class="mb-6 flex items-center gap-6">
                            <div class="relative">
                                <img 
                                    :src="avatarPreview || user.avatar_url" 
                                    alt="Avatar" 
                                    class="w-20 h-20 rounded-full object-cover border-2 border-gray-200"
                                    @error="handleImageError"
                                >
                                <div 
                                    v-if="editingProfile"
                                    class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 rounded-full opacity-0 hover:opacity-100 transition cursor-pointer"
                                >
                                    <label class="cursor-pointer flex flex-col items-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-xs text-white mt-1">Cambiar</span>
                                        <input 
                                            type="file" 
                                            accept="image/*"
                                            @change="handleAvatarChange"
                                            class="hidden"
                                        >
                                    </label>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Foto de perfil</h4>
                                <p class="text-sm text-gray-500">JPG, PNG o GIF. Máx 8MB</p>
                                <p v-if="avatarPreview" class="text-xs text-green-600 mt-1">✓ Nueva imagen seleccionada</p>
                                
                                <!-- Botón para eliminar imagen -->
                                <button 
                                    v-if="editingProfile && user.avatar_url && !user.avatar_url.includes('ui-avatars.com')"
                                    @click="removeAvatar"
                                    type="button"
                                    class="mt-2 text-xs text-red-600 hover:text-red-700 font-medium flex items-center gap-1"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Eliminar foto de perfil
                                </button>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <!-- Nombre -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre
                                </label>
                                <input 
                                    v-if="editingProfile"
                                    v-model="profileForm.first_name"
                                    type="text" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    :class="{ 'border-red-500': profileForm.errors.first_name }"
                                >
                                <p v-else class="text-gray-900 py-2">{{ user.first_name }}</p>
                                <p v-if="profileForm.errors.first_name" class="mt-1 text-sm text-red-600">
                                    {{ profileForm.errors.first_name }}
                                </p>
                            </div>

                            <!-- Apellido -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Apellido
                                </label>
                                <input 
                                    v-if="editingProfile"
                                    v-model="profileForm.last_name"
                                    type="text" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    :class="{ 'border-red-500': profileForm.errors.last_name }"
                                >
                                <p v-else class="text-gray-900 py-2">{{ user.last_name }}</p>
                                <p v-if="profileForm.errors.last_name" class="mt-1 text-sm text-red-600">
                                    {{ profileForm.errors.last_name }}
                                </p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Correo electrónico
                                </label>
                                <input 
                                    v-if="editingProfile"
                                    v-model="profileForm.email"
                                    type="email" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    :class="{ 'border-red-500': profileForm.errors.email }"
                                >
                                <p v-else class="text-gray-900 py-2">{{ user.email }}</p>
                                <p v-if="profileForm.errors.email" class="mt-1 text-sm text-red-600">
                                    {{ profileForm.errors.email }}
                                </p>
                            </div>

                            <!-- Fecha de registro -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Miembro desde
                                </label>
                                <p class="text-gray-900 py-2">{{ user.created_at }}</p>
                            </div>

                            <!-- Last Login -->
                            <div v-if="user.last_login_at">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Último acceso
                                </label>
                                <p class="text-gray-900 py-2">{{ user.last_login_at }}</p>
                            </div>
                        </div>

                        <!-- Botón guardar -->
                        <div v-if="editingProfile" class="mt-6 flex justify-end gap-3">
                            <button 
                                type="button"
                                @click="toggleEditProfile"
                                class="px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition"
                                :disabled="profileForm.processing"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit"
                                class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 flex items-center gap-2"
                                :disabled="profileForm.processing"
                            >
                                <svg v-if="profileForm.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ profileForm.processing ? 'Guardando...' : 'Guardar cambios' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 2. PREFERENCIAS DE IDIOMA Y REGIÓN -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Idioma y Región</h3>
                                <p class="text-sm text-gray-500">Configura tu idioma y zona horaria</p>
                            </div>
                        </div>
                        <button 
                            @click="toggleEditPreferences"
                            type="button"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition"
                            :class="editingPreferences 
                                ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' 
                                : 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100'"
                        >
                            {{ editingPreferences ? 'Cancelar' : 'Editar' }}
                        </button>
                    </div>

                    <form @submit.prevent="submitPreferences" class="p-6 space-y-5">
                        <!-- Idioma -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Idioma de la aplicación
                            </label>
                            <select 
                                v-if="editingPreferences"
                                v-model="preferencesForm.language"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            >
                                <option value="es">Español</option>
                                <option value="en">English</option>
                                <option value="pt">Português</option>
                            </select>
                            <p v-else class="text-gray-900 py-2">
                                {{ user.language === 'es' ? 'Español' : user.language === 'en' ? 'English' : 'Português' }}
                            </p>
                        </div>

                        <!-- Zona horaria -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Zona horaria
                            </label>
                            <select 
                                v-if="editingPreferences"
                                v-model="preferencesForm.timezone"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            >
                                <option value="America/Lima">Lima (GMT-5)</option>
                                <option value="America/Mexico_City">Ciudad de México (GMT-6)</option>
                                <option value="America/New_York">Nueva York (GMT-5)</option>
                                <option value="Europe/Madrid">Madrid (GMT+1)</option>
                            </select>
                            <p v-else class="text-gray-900 py-2">{{ user.timezone }}</p>
                        </div>

                        <div v-if="editingPreferences" class="pt-4 flex justify-end gap-3">
                            <button 
                                type="button"
                                @click="toggleEditPreferences"
                                class="px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit"
                                class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition"
                            >
                                Guardar preferencias
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 3. NOTIFICACIONES -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Notificaciones</h3>
                                <p class="text-sm text-gray-500">Administra cómo y cuándo recibes notificaciones</p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitNotifications" class="p-6 space-y-4">
                        <!-- Toggle de notificaciones por email -->
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div>
                                <p class="font-medium text-gray-900">Notificaciones por email</p>
                                <p class="text-sm text-gray-500">Recibe actualizaciones importantes por correo</p>
                            </div>
                            <button 
                                @click="notificationsForm.email_notifications = !notificationsForm.email_notifications"
                                type="button"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                                :class="notificationsForm.email_notifications ? 'bg-indigo-600' : 'bg-gray-200'"
                            >
                                <span 
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                                    :class="notificationsForm.email_notifications ? 'translate-x-6' : 'translate-x-1'"
                                ></span>
                            </button>
                        </div>

                        <!-- Toggle de marketing -->
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <p class="font-medium text-gray-900">Notificaciones de marketing</p>
                                <p class="text-sm text-gray-500">Recibe noticias y ofertas especiales</p>
                            </div>
                            <button 
                                @click="notificationsForm.marketing_emails = !notificationsForm.marketing_emails"
                                type="button"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                                :class="notificationsForm.marketing_emails ? 'bg-indigo-600' : 'bg-gray-200'"
                            >
                                <span 
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                                    :class="notificationsForm.marketing_emails ? 'translate-x-6' : 'translate-x-1'"
                                ></span>
                            </button>
                        </div>

                        <div v-if="notificationsForm.isDirty" class="pt-4 flex justify-end">
                            <button 
                                type="submit"
                                class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition"
                            >
                                Guardar notificaciones
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 4. ACCESOS RÁPIDOS -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Accesos Rápidos</h3>
                                <p class="text-sm text-gray-500">Enlaces a otras configuraciones importantes</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <button 
                            type="button"
                            class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition group text-left"
                        >
                            <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center group-hover:bg-orange-200 transition">
                                <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Cambiar contraseña</p>
                                <p class="text-sm text-gray-500">Actualiza tu contraseña</p>
                            </div>
                        </button>

                        <button 
                            type="button"
                            class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition group text-left"
                        >
                            <div class="w-10 h-10 rounded-lg bg-pink-100 flex items-center justify-center group-hover:bg-pink-200 transition">
                                <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Apariencia</p>
                                <p class="text-sm text-gray-500">Tema y personalización</p>
                            </div>
                        </button>

                        <button 
                            type="button"
                            class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition group text-left"
                        >
                            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition">
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Autenticación 2FA</p>
                                <p class="text-sm text-gray-500">Seguridad adicional</p>
                            </div>
                        </button>

                        <button 
                            type="button"
                            class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition group text-left"
                        >
                            <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center group-hover:bg-teal-200 transition">
                                <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"/>
                                    <path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5zM15 11h2a1 1 0 110 2h-2v-2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Integraciones</p>
                                <p class="text-sm text-gray-500">Apps conectadas</p>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- 5. ZONA DE PELIGRO -->
                <div class="bg-white rounded-xl shadow-sm border border-red-200">
                    <div class="px-6 py-5 border-b border-red-100 bg-red-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-red-900">Zona de Peligro</h3>
                                <p class="text-sm text-red-600">Acciones irreversibles para tu cuenta</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-900">Eliminar cuenta</h4>
                                <p class="text-sm text-gray-600 mt-1">
                                    Una vez que elimines tu cuenta, no hay vuelta atrás. Por favor, ten cuidado.
                                </p>
                            </div>
                            <button 
                                @click="openDeleteModal"
                                type="button"
                                class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition ml-4"
                            >
                                Eliminar cuenta
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal de confirmación de eliminación -->
            <div 
                v-if="showDeleteModal" 
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
                @click.self="closeDeleteModal"
            >
                <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">¿Estás seguro?</h3>
                            <p class="text-sm text-gray-500">Esta acción no se puede deshacer</p>
                        </div>
                    </div>

                    <form @submit.prevent="deleteAccount">
                        <p class="text-sm text-gray-600 mb-4">
                            Para confirmar la eliminación de tu cuenta, ingresa tu contraseña:
                        </p>
                        
                        <input 
                            v-model="deleteForm.password"
                            type="password" 
                            placeholder="Tu contraseña"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent mb-1"
                            :class="{ 'border-red-500': deleteForm.errors.password }"
                        >
                        <p v-if="deleteForm.errors.password" class="text-sm text-red-600 mb-4">
                            {{ deleteForm.errors.password }}
                        </p>

                        <div class="flex gap-3 mt-6">
                            <button 
                                type="button"
                                @click="closeDeleteModal"
                                class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition"
                                :disabled="deleteForm.processing"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit"
                                class="flex-1 px-4 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition disabled:opacity-50"
                                :disabled="deleteForm.processing"
                            >
                                {{ deleteForm.processing ? 'Eliminando...' : 'Eliminar cuenta' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </DashboardLayout>
</template>