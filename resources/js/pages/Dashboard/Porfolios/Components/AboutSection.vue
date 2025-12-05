<script setup lang="ts">
import { useAboutValidation } from './Composables/useAboutValidation';

const props = defineProps<{
    modelValue: {
        summary: string;
        description: string;
    };
}>();

const emit = defineEmits(['update:modelValue']);

const { errors, validateField, getCharCount } = useAboutValidation();

const updateField = (field: 'summary' | 'description', value: string) => {
    emit('update:modelValue', {
        ...props.modelValue,
        [field]: value,
    });
    // Validar en tiempo real
    validateField(field, value);
};

const handleBlur = (field: 'summary' | 'description') => {
    validateField(field, props.modelValue[field]);
};
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">
                Sobre Mí
            </h1>
            <p class="text-lg text-gray-600">
                Cuéntanos un poco más sobre ti, tu trayectoria y tus objetivos.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Resumen breve -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Resumen breve *
                </label>
                <p class="mb-2 text-xs text-gray-500">
                    Este texto aparecerá en la cabecera o sección principal.
                </p>
                <textarea
                    :value="modelValue.summary"
                    @input="updateField('summary', ($event.target as HTMLTextAreaElement).value)"
                    @blur="handleBlur('summary')"
                    rows="3"
                    class="w-full rounded-lg border px-4 py-3 transition-colors duration-200 focus:ring-2"
                    :class="[
                        errors.summary 
                            ? 'border-red-500 focus:border-red-500 focus:ring-red-200' 
                            : 'border-gray-300 focus:border-[#005aeb] focus:ring-[#005aeb]'
                    ]"
                    placeholder="Soy un desarrollador apasionado por..."
                ></textarea>
                <div class="mt-1 flex items-center justify-between">
                    <span 
                        v-if="errors.summary" 
                        class="text-sm text-red-500"
                    >
                        {{ errors.summary }}
                    </span>
                    <span v-else class="text-sm text-transparent">placeholder</span>
                    <span 
                        class="text-xs"
                        :class="[
                            getCharCount(modelValue.summary, 'summary').isOverLimit 
                                ? 'text-red-500 font-medium' 
                                : getCharCount(modelValue.summary, 'summary').isNearLimit 
                                    ? 'text-amber-500' 
                                    : 'text-gray-400'
                        ]"
                    >
                        {{ getCharCount(modelValue.summary, 'summary').current }}/{{ getCharCount(modelValue.summary, 'summary').max }}
                    </span>
                </div>
            </div>

            <!-- Biografía completa -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Biografía completa
                </label>
                <p class="mb-2 text-xs text-gray-500">
                    Una descripción más detallada para la sección "Sobre mí".
                </p>
                <textarea
                    :value="modelValue.description"
                    @input="updateField('description', ($event.target as HTMLTextAreaElement).value)"
                    @blur="handleBlur('description')"
                    rows="8"
                    class="w-full rounded-lg border px-4 py-3 transition-colors duration-200 focus:ring-2"
                    :class="[
                        errors.description 
                            ? 'border-red-500 focus:border-red-500 focus:ring-red-200' 
                            : 'border-gray-300 focus:border-[#005aeb] focus:ring-[#005aeb]'
                    ]"
                    placeholder="Comencé mi carrera en..."
                ></textarea>
                <div class="mt-1 flex items-center justify-between">
                    <span 
                        v-if="errors.description" 
                        class="text-sm text-red-500"
                    >
                        {{ errors.description }}
                    </span>
                    <span v-else class="text-sm text-transparent">placeholder</span>
                    <span 
                        class="text-xs"
                        :class="[
                            getCharCount(modelValue.description, 'description').isOverLimit 
                                ? 'text-red-500 font-medium' 
                                : getCharCount(modelValue.description, 'description').isNearLimit 
                                    ? 'text-amber-500' 
                                    : 'text-gray-400'
                        ]"
                    >
                        {{ getCharCount(modelValue.description, 'description').current }}/{{ getCharCount(modelValue.description, 'description').max }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
