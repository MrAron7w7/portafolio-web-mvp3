<script setup lang="ts">
import { onMounted } from 'vue';
import { useSocialValidation, type SocialData } from './Composables/useSocialValidation';

const props = defineProps<{
    modelValue: SocialData;
    validation: any;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: SocialData];
}>();

const {
    errors: socialErrors,
    validateField,
    getErrorClass,
    getFieldInfo,
} = props.validation;

onMounted(() => {
    props.validation.clearErrors();
});

// Actualizar campo genérico
const updateField = (field: keyof SocialData, value: string) => {
    // Marcar como tocado
    props.validation.markAsTouched(field);

    const updated = { ...props.modelValue, [field]: value };
    emit('update:modelValue', updated);

    // Validar en tiempo real
    if (socialErrors[field] || value.trim()) {
        validateField(field, value, false);
    } else if (!value.trim()) {
        // Si está vacío, limpiar error
        delete socialErrors[field];
    }
};

// Información de campos
const fieldInfo = {
    linkedin: getFieldInfo('linkedin'),
    github: getFieldInfo('github'),
    website: getFieldInfo('website'),
};
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="mb-3 text-2xl font-bold text-gray-900 dark:text-white lg:text-3xl">Redes Sociales</h1>
            <p class="text-lg text-gray-600 dark:text-slate-400">
                Conecta tus perfiles profesionales para que te encuentren fácilmente.
            </p>
        </div>

        <div class="space-y-6">
            <!-- LinkedIn -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-slate-300">
                    {{ fieldInfo.linkedin.label }}
                </label>
                <p class="mb-2 text-xs text-gray-500 dark:text-slate-500">{{ fieldInfo.linkedin.helper }}</p>
                <input
                    :value="modelValue.linkedin"
                    @input="updateField('linkedin', ($event.target as HTMLInputElement).value)"
                    type="text"
                    class="w-full rounded-lg border dark:border-slate-800 bg-gray-50 dark:bg-slate-800 px-3 py-2 text-sm text-gray-900 dark:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                    :class="getErrorClass('linkedin', false)"
                    :placeholder="fieldInfo.linkedin.placeholder"
                />
                <p v-if="socialErrors.linkedin" class="mt-1 text-sm text-red-500">
                    {{ socialErrors.linkedin }}
                </p>
            </div>

            <!-- GitHub -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-slate-300">
                    {{ fieldInfo.github.label }}
                </label>
                <p class="mb-2 text-xs text-gray-500 dark:text-slate-500">{{ fieldInfo.github.helper }}</p>
                <input
                    :value="modelValue.github"
                    @input="updateField('github', ($event.target as HTMLInputElement).value)"
                    type="text"
                    class="w-full rounded-lg border dark:border-slate-800 bg-gray-50 dark:bg-slate-800 px-3 py-2 text-sm text-gray-900 dark:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                    :class="getErrorClass('github', false)"
                    :placeholder="fieldInfo.github.placeholder"
                />
                <p v-if="socialErrors.github" class="mt-1 text-sm text-red-500">
                    {{ socialErrors.github }}
                </p>
            </div>

            <!-- Website -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-slate-300">
                    {{ fieldInfo.website.label }}
                </label>
                <p class="mb-2 text-xs text-gray-500 dark:text-slate-500">{{ fieldInfo.website.helper }}</p>
                <input
                    :value="modelValue.website"
                    @input="updateField('website', ($event.target as HTMLInputElement).value)"
                    type="url"
                    class="w-full rounded-lg border dark:border-slate-800 bg-gray-50 dark:bg-slate-800 px-3 py-2 text-sm text-gray-900 dark:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                    :class="getErrorClass('website', false)"
                    :placeholder="fieldInfo.website.placeholder"
                />
                <p v-if="socialErrors.website" class="mt-1 text-sm text-red-500">
                    {{ socialErrors.website }}
                </p>
            </div>
        </div>
    </div>
</template>
