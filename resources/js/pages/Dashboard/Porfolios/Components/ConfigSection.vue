<script setup lang="ts">
const props = defineProps<{
    modelValue: {
        theme: string;
        is_public: boolean;
    };
}>();

const emit = defineEmits(['update:modelValue']);

const updateField = (field: string, value: any) => {
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
                Configuración
            </h1>
            <p class="text-lg text-gray-600">
                Ajusta las preferencias de tu portafolio.
            </p>
        </div>

        <div class="space-y-8">
            <!-- Visibilidad -->
            <div class="rounded-lg border border-gray-200 p-6">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Visibilidad</h3>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">Portafolio Público</p>
                        <p class="text-sm text-gray-500">
                            Permite que cualquiera vea tu portafolio con tu enlace único.
                        </p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input
                            type="checkbox"
                            :checked="modelValue.is_public"
                            @change="updateField('is_public', ($event.target as HTMLInputElement).checked)"
                            class="peer sr-only"
                        />
                        <div
                            class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#005aeb] peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300"
                        ></div>
                    </label>
                </div>
            </div>

            <!-- Tema (Placeholder para futura implementación) -->
            <div class="rounded-lg border border-gray-200 p-6 opacity-50">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Tema (Próximamente)</h3>
                <p class="text-sm text-gray-500">
                    La personalización de colores y fuentes estará disponible pronto.
                </p>
            </div>
        </div>
    </div>
</template>
