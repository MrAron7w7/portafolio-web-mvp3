<script setup lang="ts">
import { Upload } from 'lucide-vue-next';

const props = defineProps<{
    modelValue: {
        firstName: string;
        lastName: string;
        title: string;
        city: string;
        country: string;
        phone: string;
        email: string;
        photo: string | null;
        summary: string;
        website: string;
        linkedin: string;
        github: string;
    };
}>();

const emit = defineEmits(['update:modelValue']);

const handlePhotoUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            emit('update:modelValue', {
                ...props.modelValue,
                photo: e.target?.result as string,
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
};

const updateField = (field: string, value: string) => {
    emit('update:modelValue', {
        ...props.modelValue,
        [field]: value,
    });
};
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">
                Información Personal
            </h1>
            <p class="text-lg text-gray-600">
                Completa tu información básica para comenzar tu portafolio
                profesional.
            </p>
        </div>

        <!-- Foto de perfil -->
        <div class="mb-8 flex items-start space-x-6">
            <div class="relative">
                <div
                    class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-[#005aeb] to-[#7B2FF7] text-xl font-semibold text-white"
                >
                    <span v-if="!modelValue.photo">
                        {{ modelValue.firstName.charAt(0)
                        }}{{ modelValue.lastName.charAt(0) }}
                    </span>
                    <img
                        v-else
                        :src="modelValue.photo"
                        class="h-20 w-20 rounded-full object-cover"
                    />
                </div>
                <label
                    class="absolute -right-2 -bottom-2 cursor-pointer rounded-full border border-gray-300 bg-white p-1.5 shadow-sm transition-colors duration-200 hover:bg-gray-50"
                >
                    <Upload class="h-4 w-4 text-gray-600" />
                    <input
                        type="file"
                        class="hidden"
                        accept="image/*"
                        @change="handlePhotoUpload"
                    />
                </label>
            </div>
            <div>
                <h3 class="mb-1 font-semibold text-gray-900">Foto de perfil</h3>
                <p class="mb-3 text-sm text-gray-600">
                    Recomendado: 400x400 px, formato JPG o PNG
                </p>
                <button
                    v-if="modelValue.photo"
                    @click="updateField('photo', '')"
                    class="text-sm font-medium text-[#005aeb] hover:text-[#0048c4]"
                >
                    Eliminar foto
                </button>
            </div>
        </div>

        <!-- Formulario -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Nombre *
                </label>
                <input
                    :value="modelValue.firstName"
                    @input="updateField('firstName', ($event.target as HTMLInputElement).value)"
                    type="text"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                    placeholder="Tu nombre"
                />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Apellido *
                </label>
                <input
                    :value="modelValue.lastName"
                    @input="updateField('lastName', ($event.target as HTMLInputElement).value)"
                    type="text"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                    placeholder="Tu apellido"
                />
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Título profesional *
                </label>
                <input
                    :value="modelValue.title"
                    @input="updateField('title', ($event.target as HTMLInputElement).value)"
                    type="text"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                    placeholder="Ej: Diseñador UX/UI & Desarrollador Frontend"
                />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Ciudad
                </label>
                <input
                    :value="modelValue.city"
                    @input="updateField('city', ($event.target as HTMLInputElement).value)"
                    type="text"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                    placeholder="Tu ciudad"
                />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    País
                </label>
                <input
                    :value="modelValue.country"
                    @input="updateField('country', ($event.target as HTMLInputElement).value)"
                    type="text"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                    placeholder="Tu país"
                />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Teléfono
                </label>
                <input
                    :value="modelValue.phone"
                    @input="updateField('phone', ($event.target as HTMLInputElement).value)"
                    type="tel"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                    placeholder="+34 612 345 678"
                />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Email *
                </label>
                <input
                    :value="modelValue.email"
                    @input="updateField('email', ($event.target as HTMLInputElement).value)"
                    type="email"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                    placeholder="tu@email.com"
                />
            </div>
        </div>
    </div>
</template>
