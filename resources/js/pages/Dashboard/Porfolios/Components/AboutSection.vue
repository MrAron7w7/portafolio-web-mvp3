<script setup lang="ts">
const props = defineProps<{
    modelValue: {
        summary: string;
        description: string;
    };
}>();

const emit = defineEmits(['update:modelValue']);

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
                Sobre Mí
            </h1>
            <p class="text-lg text-gray-600">
                Cuéntanos un poco más sobre ti, tu trayectoria y tus objetivos.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Resumen breve *
                </label>
                <p class="mb-2 text-xs text-gray-500">
                    Este texto aparecerá en la cabecera o sección principal.
                </p>
                <textarea
                    :value="modelValue.summary"
                    @input="updateField('summary', ($event.target as HTMLInputElement).value)"
                    rows="3"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                    placeholder="Soy un desarrollador apasionado por..."
                ></textarea>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Biografía completa
                </label>
                <p class="mb-2 text-xs text-gray-500">
                    Una descripción más detallada para la sección "Sobre mí".
                </p>
                <textarea
                    :value="modelValue.description"
                    @input="updateField('description', ($event.target as HTMLInputElement).value)"
                    rows="8"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-[#005aeb] focus:ring-2 focus:ring-[#005aeb]"
                    placeholder="Comencé mi carrera en..."
                ></textarea>
            </div>
        </div>
    </div>
</template>
