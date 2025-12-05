<script setup lang="ts">
import { useSocialValidation, type SocialData } from './Composables/useSocialValidation';

const props = defineProps<{
    modelValue: SocialData;
}>();

const emit = defineEmits(['update:modelValue']);

// Usar el composable de validación
const { errors: socialErrors, validateField, getErrorClass, extractUsername, buildUrl } = useSocialValidation();

// Actualizar campo
const updateField = (field: keyof SocialData, value: string) => {
    emit('update:modelValue', {
        ...props.modelValue,
        [field]: value,
    });

    // Validar en tiempo real
    validateField(field, value);
};

// Manejar blur para validación final
const handleBlur = (field: keyof SocialData) => {
    validateField(field, props.modelValue[field]);
};
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">Redes Sociales</h1>
            <p class="text-lg text-gray-600">
                Conecta tus perfiles profesionales para que te encuentren fácilmente.
            </p>
        </div>

        <div class="space-y-6">
            <!-- LinkedIn -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">LinkedIn</label>
                <p class="mb-2 text-xs text-gray-500">Tu perfil de LinkedIn (solo nombre de usuario)</p>
                <div class="relative rounded-lg shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-sm text-gray-500">linkedin.com/in/</span>
                    </div>
                    <input
                        :value="extractUsername('linkedin', modelValue.linkedin)"
                        @input="
                            updateField('linkedin', buildUrl('linkedin', ($event.target as HTMLInputElement).value))
                        "
                        @blur="handleBlur('linkedin')"
                        type="text"
                        class="block w-full rounded-lg border pl-32 py-3 pr-4 transition-colors focus:ring-2"
                        :class="getErrorClass('linkedin')"
                        placeholder="usuario"
                    />
                </div>
                <p v-if="socialErrors.linkedin" class="mt-1 text-sm text-red-500">
                    {{ socialErrors.linkedin }}
                </p>
            </div>

            <!-- GitHub -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">GitHub</label>
                <p class="mb-2 text-xs text-gray-500">Tu perfil de GitHub (solo nombre de usuario)</p>
                <div class="relative rounded-lg shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-sm text-gray-500">github.com/</span>
                    </div>
                    <input
                        :value="extractUsername('github', modelValue.github)"
                        @input="
                            updateField('github', buildUrl('github', ($event.target as HTMLInputElement).value))
                        "
                        @blur="handleBlur('github')"
                        type="text"
                        class="block w-full rounded-lg border pl-24 py-3 pr-4 transition-colors focus:ring-2"
                        :class="getErrorClass('github')"
                        placeholder="usuario"
                    />
                </div>
                <p v-if="socialErrors.github" class="mt-1 text-sm text-red-500">
                    {{ socialErrors.github }}
                </p>
            </div>

            <!-- Sitio Web Personal -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Sitio Web Personal</label>
                <p class="mb-2 text-xs text-gray-500">Tu sitio web personal o portafolio</p>
                <input
                    :value="modelValue.website"
                    @input="updateField('website', ($event.target as HTMLInputElement).value)"
                    @blur="handleBlur('website')"
                    type="url"
                    class="w-full rounded-lg border px-4 py-3 transition-colors focus:ring-2"
                    :class="getErrorClass('website')"
                    placeholder="https://tu-sitio.com"
                />
                <p v-if="socialErrors.website" class="mt-1 text-sm text-red-500">
                    {{ socialErrors.website }}
                </p>
            </div>
        </div>
    </div>
</template>
