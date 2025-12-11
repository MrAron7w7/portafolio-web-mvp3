<script setup lang="ts">
import { ref, onMounted } from 'vue';
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
</script>


<template>
    <div>
        <div class="mb-12">
            <h1 class="mb-3 text-3xl font-bold text-gray-900 lg:text-4xl">Información Personal</h1>
            <p class="text-lg text-gray-600">
                Completa tu información básica para comenzar tu portafolio profesional.
            </p>
        </div>


        <div class="space-y-8">
            <!-- Foto de Perfil -->
            <div class="flex items-start space-x-6">
                <div>
                    <div v-if="!photoPreview" class="relative rounded-full border-2 border-dashed border-gray-300 w-30 h-30 flex items-center justify-center transition-colors hover:border-[#005aeb]">
                        <input
                            type="file"
                            accept="image/png,image/jpeg"
                            @change="handlePhotoUpload"
                            class="absolute inset-0 h-full w-full cursor-pointer opacity-0 rounded-full"
                        />
                        <div class="pointer-events-none text-center">
                            <div class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 mb-1">
                                <svg class="h-3 w-3 text-[#005aeb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-900">Subir foto</p>
                        </div>
                    </div>


                    <div v-else class="relative inline-block">
                        <img
                            :src="photoPreview"
                            alt="Profile preview"
                            class="h-30 w-30 rounded-full border-3 border-[#005aeb] object-cover shadow-lg"
                        />
                        <button
                            @click="removePhoto"
                            type="button"
                            class="absolute -right-0 -top-0 flex h-7 w-7 items-center justify-center rounded-full bg-red-500 text-white shadow-lg transition-all hover:bg-red-600 hover:scale-110"
                        >
                            ✕
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
                <input
                    :value="modelValue.title"
                    @input="updateField('title', ($event.target as HTMLInputElement).value)"
                    type="text"
                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                    :class="getErrorClass('title', false)"
                    placeholder="Ej: Desarrollador Web, Diseñador UX, Ingeniero en Software"
                />
                <p v-if="personalErrors.title" class="mt-2 text-sm text-red-500">
                    {{ personalErrors.title }}
                </p>
                <p class="mt-2 text-sm text-gray-500">Este título se mostrará en la sección principal de tu perfil</p>
            </div>


            <!-- Email -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Email *</label>
                <input
                    :value="modelValue.email"
                    @input="updateField('email', ($event.target as HTMLInputElement).value)"
                    type="email"
                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                    :class="getErrorClass('email', false)"
                    placeholder="tu@email.com"
                />
                <p v-if="personalErrors.email" class="mt-2 text-sm text-red-500">
                    {{ personalErrors.email }}
                </p>
            </div>


            <!-- Teléfono -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Teléfono</label>
                <input
                    :value="modelValue.phone"
                    @input="updateField('phone', ($event.target as HTMLInputElement).value)"
                    type="tel"
                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                    :class="getErrorClass('phone', false)"
                    placeholder="+1 (555) 123-4567"
                />
                <p v-if="personalErrors.phone" class="mt-2 text-sm text-red-500">
                    {{ personalErrors.phone }}
                </p>
                <p class="mt-2 text-sm text-gray-500">Opcional - Número al que puedes ser llamado</p>
            </div>


            <!-- Ciudad -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Ciudad</label>
                <input
                    :value="modelValue.city"
                    @input="updateField('city', ($event.target as HTMLInputElement).value)"
                    type="text"
                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/200"
                    placeholder="Ej: Madrid"
                />
            </div>


            <!-- País -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">País</label>
                <input
                    :value="modelValue.country"
                    @input="updateField('country', ($event.target as HTMLInputElement).value)"
                    type="text"
                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                    placeholder="Ej: España"
                />
            </div>
        </div>
    </div>
</template>
