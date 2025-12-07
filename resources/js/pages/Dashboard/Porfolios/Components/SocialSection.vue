<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
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

// Input internos para mostrar solo el username
const linkedinUsername = ref('');
const githubUsername = ref('');

onMounted(() => {
    props.validation.clearErrors();
    // Extraer usernames de las URLs guardadas
    linkedinUsername.value = props.modelValue.linkedin.replace('linkedin.com/in/', '').trim();
    githubUsername.value = props.modelValue.github.replace('github.com/', '').trim();
});

// Actualizar LinkedIn
const updateLinkedin = (value: string) => {
    linkedinUsername.value = value;

    // Marcar como tocado
    props.validation.markAsTouched('linkedin');

    // Actualizar el formData con la URL completa
    const fullUrl = value.trim() ? `linkedin.com/in/${value.trim()}` : '';
    const updated = { ...props.modelValue, linkedin: fullUrl };
    emit('update:modelValue', updated);

    // Validar en tiempo real
    if (socialErrors.linkedin && value.trim()) {
        validateField('linkedin', fullUrl, false);
    } else if (!value.trim()) {
        // Si está vacío, limpiar error
        delete socialErrors.linkedin;
    }
};

// Actualizar GitHub
const updateGithub = (value: string) => {
    githubUsername.value = value;

    // Marcar como tocado
    props.validation.markAsTouched('github');

    // Actualizar el formData con la URL completa
    const fullUrl = value.trim() ? `github.com/${value.trim()}` : '';
    const updated = { ...props.modelValue, github: fullUrl };
    emit('update:modelValue', updated);

    // Validar en tiempo real
    if (socialErrors.github && value.trim()) {
        validateField('github', fullUrl, false);
    } else if (!value.trim()) {
        // Si está vacío, limpiar error
        delete socialErrors.github;
    }
};

// Actualizar Website
const updateWebsite = (value: string) => {
    // Marcar como tocado
    props.validation.markAsTouched('website');

    const updated = { ...props.modelValue, website: value };
    emit('update:modelValue', updated);

    // Validar en tiempo real (forceShow = false)
    if (socialErrors.website) {
        validateField('website', value, false);
    } else if (!value.trim()) {
        // Si está vacío, limpiar error
        delete socialErrors.website;
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
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">Redes Sociales</h1>
            <p class="text-lg text-gray-600">
                Conecta tus perfiles profesionales para que te encuentren fácilmente.
            </p>
        </div>

        <div class="space-y-6">
            <!-- LinkedIn -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    {{ fieldInfo.linkedin.label }}
                </label>
                <p class="mb-2 text-xs text-gray-500">{{ fieldInfo.linkedin.helper }}</p>
                <div class="flex">
                    <span class="inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm font-medium text-gray-600">
                        {{ fieldInfo.linkedin.prefix }}
                    </span>
                    <input
                        :value="linkedinUsername"
                        @input="updateLinkedin(($event.target as HTMLInputElement).value)"
                        type="text"
                        class="flex-1 rounded-r-lg border bg-gray-50 px-3 py-2 text-sm text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                        :class="
                            socialErrors.linkedin
                                ? 'border-red-500 focus:border-red-500 focus:ring-red-200'
                                : 'border-gray-300 focus:border-[#005aeb]'
                        "
                        :placeholder="fieldInfo.linkedin.placeholder"
                    />
                </div>
                <p v-if="socialErrors.linkedin" class="mt-1 text-sm text-red-500">
                    {{ socialErrors.linkedin }}
                </p>
            </div>

            <!-- GitHub -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    {{ fieldInfo.github.label }}
                </label>
                <p class="mb-2 text-xs text-gray-500">{{ fieldInfo.github.helper }}</p>
                <div class="flex">
                    <span class="inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm font-medium text-gray-600">
                        {{ fieldInfo.github.prefix }}
                    </span>
                    <input
                        :value="githubUsername"
                        @input="updateGithub(($event.target as HTMLInputElement).value)"
                        type="text"
                        class="flex-1 rounded-r-lg border bg-gray-50 px-3 py-2 text-sm text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                        :class="
                            socialErrors.github
                                ? 'border-red-500 focus:border-red-500 focus:ring-red-200'
                                : 'border-gray-300 focus:border-[#005aeb]'
                        "
                        :placeholder="fieldInfo.github.placeholder"
                    />
                </div>
                <p v-if="socialErrors.github" class="mt-1 text-sm text-red-500">
                    {{ socialErrors.github }}
                </p>
            </div>

            <!-- Website -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    {{ fieldInfo.website.label }}
                </label>
                <p class="mb-2 text-xs text-gray-500">{{ fieldInfo.website.helper }}</p>
                <input
                    :value="modelValue.website"
                    @input="updateWebsite(($event.target as HTMLInputElement).value)"
                    type="url"
                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-sm text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
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
