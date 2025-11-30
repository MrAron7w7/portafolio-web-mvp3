<script setup lang="ts">
const props = defineProps<{
    modelValue: {
        linkedin: string;
        github: string;
        website: string;
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
                Redes Sociales
            </h1>
            <p class="text-lg text-gray-600">
                Conecta tus perfiles profesionales para que te encuentren fácilmente.
            </p>
        </div>

        <div class="space-y-6">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    LinkedIn
                </label>
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-gray-500 sm:text-sm">linkedin.com/in/</span>
                    </div>
                    <input
                        :value="modelValue.linkedin.replace('linkedin.com/in/', '')"
                        @input="updateField('linkedin', 'linkedin.com/in/' + ($event.target as HTMLInputElement).value)"
                        type="text"
                        class="block w-full rounded-lg border border-gray-300 pl-32 py-3 focus:border-[#005aeb] focus:ring-[#005aeb] sm:text-sm"
                        placeholder="usuario"
                    />
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    GitHub
                </label>
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-gray-500 sm:text-sm">github.com/</span>
                    </div>
                    <input
                        :value="modelValue.github.replace('github.com/', '')"
                        @input="updateField('github', 'github.com/' + ($event.target as HTMLInputElement).value)"
                        type="text"
                        class="block w-full rounded-lg border border-gray-300 pl-24 py-3 focus:border-[#005aeb] focus:ring-[#005aeb] sm:text-sm"
                        placeholder="usuario"
                    />
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Sitio Web Personal
                </label>
                <input
                    :value="modelValue.website"
                    @input="updateField('website', ($event.target as HTMLInputElement).value)"
                    type="url"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#005aeb] focus:ring-[#005aeb]"
                    placeholder="https://tu-sitio.com"
                />
            </div>
        </div>
    </div>
</template>
