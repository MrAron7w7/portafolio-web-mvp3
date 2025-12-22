<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { User, RefreshCw, Upload, Trash2, Mail, Phone, MapPin, Globe } from 'lucide-vue-next';
import { usePersonalValidation, type PersonalData } from './Composables/usePersonalValidation';


const props = defineProps<{
    modelValue: PersonalData;
    validation: any;
}>();


const emit = defineEmits<{
    'update:modelValue': [value: PersonalData];
}>();


const {
    errors: personalErrors,
    validateField,
    getErrorClass,
} = props.validation;


const photoPreview = ref<string | null>(null);


onMounted(() => {
    props.validation.clearErrors();
    if (props.modelValue.photo) {
        photoPreview.value = props.modelValue.photo;
    }
});


// Actualizar campo + validar
const updateField = (field: keyof PersonalData, value: any) => {
    // Marcar como tocado al escribir
    if (field !== 'photo') {
        props.validation.markAsTouched(field);
    }


    const updated = { ...props.modelValue, [field]: value };
    emit('update:modelValue', updated);


    // Validar en tiempo real (forceShow = false)
    if (personalErrors[field as keyof typeof personalErrors]) {
        validateField(field as keyof typeof personalErrors, value, false);
    }
};


// Manejar upload de foto
const handlePhotoUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];


    if (!file) return;


    const reader = new FileReader();
    reader.onload = (e) => {
        const base64 = e.target?.result as string;
        photoPreview.value = base64;
        updateField('photo', base64);
    };
    reader.readAsDataURL(file);
};


// Eliminar foto
const removePhoto = () => {
    photoPreview.value = null;
    updateField('photo', null);
};

// Sincronizar con datos del perfil de usuario (auth)
const syncWithProfile = () => {
    const page = usePage();
    const user = (page.props.auth as any)?.user;
    
    if (user) {
        if (!props.modelValue.firstName && user.first_name) updateField('firstName', user.first_name);
        if (!props.modelValue.lastName && user.last_name) updateField('lastName', user.last_name);
        if (!props.modelValue.email && user.email) updateField('email', user.email);
        
        // Si no hay foto y el usuario tiene avatar_url
        if (!photoPreview.value && user.avatar_url) {
            photoPreview.value = user.avatar_url;
            updateField('photo', user.avatar_url);
        }
    }
};
</script>


<template>
    <div>
        <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="mb-3 text-3xl font-bold text-gray-900 lg:text-4xl">Información Personal</h1>
                <p class="text-lg text-gray-600">
                    Completa tu información básica para comenzar tu portafolio profesional.
                </p>
            </div>
            <button 
                type="button"
                @click="syncWithProfile"
                class="flex items-center gap-2 px-4 py-2.5 bg-white border border-indigo-200 text-indigo-600 rounded-xl font-semibold text-sm hover:bg-indigo-50 hover:border-indigo-300 transition-all shadow-sm hover:shadow group"
            >
                <RefreshCw class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500" />
                <span>Sincronizar con mi Perfil</span>
            </button>
        </div>


        <div class="space-y-8">
            <!-- Foto de Perfil -->
            <div class="flex items-start space-x-6">
                <div>
                    <div v-if="!photoPreview" class="relative rounded-full border-2 border-dashed border-gray-300 w-32 h-32 flex items-center justify-center transition-colors hover:border-indigo-500 bg-gray-50/50">
                        <input
                            type="file"
                            accept="image/png,image/jpeg"
                            @change="handlePhotoUpload"
                            class="absolute inset-0 h-full w-full cursor-pointer opacity-0 rounded-full z-10"
                        />
                        <div class="pointer-events-none text-center p-4">
                            <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 mb-2">
                                <Upload class="h-5 w-5 text-indigo-600" />
                            </div>
                            <p class="text-xs font-bold text-gray-900">Subir foto</p>
                        </div>
                    </div>


                    <div v-else class="relative group">
                        <img
                            :src="photoPreview"
                            alt="Profile preview"
                            class="h-32 w-32 rounded-full border-4 border-indigo-500 object-cover shadow-xl transition-transform group-hover:scale-105"
                        />
                        <button
                            @click="removePhoto"
                            type="button"
                            class="absolute -right-1 -top-1 flex h-9 w-9 items-center justify-center rounded-full bg-red-500 text-white shadow-lg transition-all hover:bg-red-600 hover:scale-110 border-2 border-white"
                        >
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Foto de Perfil</h2>
                    <p class="mt-1 text-sm text-gray-600">Recomendado: 400x400 px, formato JPG o PNG</p>
                </div>
            </div>


            <!-- Nombre y Apellido -->
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Nombre *</label>
                    <input
                        :value="modelValue.firstName"
                        @input="updateField('firstName', ($event.target as HTMLInputElement).value)"
                        type="text"
                        class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                        :class="getErrorClass('firstName', false)"
                        placeholder="Tu nombre"
                    />
                    <p v-if="personalErrors.firstName" class="mt-2 text-sm text-red-500">
                        {{ personalErrors.firstName }}
                    </p>
                </div>


                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Apellido *</label>
                    <input
                        :value="modelValue.lastName"
                        @input="updateField('lastName', ($event.target as HTMLInputElement).value)"
                        type="text"
                        class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                        :class="getErrorClass('lastName', false)"
                        placeholder="Tu apellido"
                    />
                    <p v-if="personalErrors.lastName" class="mt-2 text-sm text-red-500">
                        {{ personalErrors.lastName }}
                    </p>
                </div>
            </div>


            <!-- Título Profesional -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Título Profesional *</label>
                <div class="relative">
                    <User class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        :value="modelValue.title"
                        @input="updateField('title', ($event.target as HTMLInputElement).value)"
                        type="text"
                        class="w-full rounded-lg border bg-gray-50 pl-10 pr-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        :class="getErrorClass('title', false)"
                        placeholder="Ej: Desarrollador Web, Diseñador UX, Ingeniero en Software"
                    />
                </div>
                <p v-if="personalErrors.title" class="mt-2 text-sm text-red-500">
                    {{ personalErrors.title }}
                </p>
                <p class="mt-2 text-xs text-gray-500">Este título se mostrará en la sección principal de tu perfil</p>
            </div>


            <!-- Email -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Email *</label>
                <div class="relative">
                    <Mail class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        :value="modelValue.email"
                        @input="updateField('email', ($event.target as HTMLInputElement).value)"
                        type="email"
                        class="w-full rounded-lg border bg-gray-50 pl-10 pr-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        :class="getErrorClass('email', false)"
                        placeholder="tu@email.com"
                    />
                </div>
                <p v-if="personalErrors.email" class="mt-2 text-sm text-red-500">
                    {{ personalErrors.email }}
                </p>
            </div>


            <!-- Teléfono -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Teléfono</label>
                <div class="relative">
                    <Phone class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        :value="modelValue.phone"
                        @input="updateField('phone', ($event.target as HTMLInputElement).value)"
                        type="tel"
                        class="w-full rounded-lg border bg-gray-50 pl-10 pr-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        :class="getErrorClass('phone', false)"
                        placeholder="+1 (555) 123-4567"
                    />
                </div>
                <p v-if="personalErrors.phone" class="mt-2 text-sm text-red-500">
                    {{ personalErrors.phone }}
                </p>
                <p class="mt-2 text-xs text-gray-500">Opcional - Para que puedan contactarte directamente</p>
            </div>


            <!-- Ciudad y País -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Ciudad</label>
                    <div class="relative">
                        <MapPin class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input
                            :value="modelValue.city"
                            @input="updateField('city', ($event.target as HTMLInputElement).value)"
                            type="text"
                            class="w-full rounded-lg border bg-gray-50 pl-10 pr-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            placeholder="Ej: Madrid"
                        />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">País</label>
                    <div class="relative">
                        <Globe class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input
                            :value="modelValue.country"
                            @input="updateField('country', ($event.target as HTMLInputElement).value)"
                            type="text"
                            class="w-full rounded-lg border bg-gray-50 pl-10 pr-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            placeholder="Ej: España"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
