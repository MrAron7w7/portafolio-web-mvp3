<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';

interface Education {
    id: number;
    degree: string;
    institution: string;
    startDate: string;
    endDate: string;
    description: string;
    current: boolean;
}

const props = defineProps<{
    modelValue: Education[];
}>();

const emit = defineEmits(['update:modelValue']);

const addEducation = () => {
    const newEducation = {
        id: Date.now(),
        degree: '',
        institution: '',
        startDate: '',
        endDate: '',
        description: '',
        current: false,
    };
    emit('update:modelValue', [...props.modelValue, newEducation]);
};

const removeEducation = (index: number) => {
    const updated = [...props.modelValue];
    updated.splice(index, 1);
    emit('update:modelValue', updated);
};

const updateEducation = (index: number, field: keyof Education, value: any) => {
    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], [field]: value };
    emit('update:modelValue', updated);
};
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">
                Formación Académica
            </h1>
            <p class="text-lg text-gray-600">
                Agrega tu historial educativo y formación profesional.
            </p>
        </div>

        <div class="space-y-6">
            <div
                v-for="(edu, index) in modelValue"
                :key="edu.id"
                class="rounded-lg border border-gray-200 p-6"
            >
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Formación {{ index + 1 }}
                    </h3>
                    <button
                        @click="removeEducation(index)"
                        class="text-red-600 hover:text-red-800"
                    >
                        <Trash2 class="h-4 w-4" />
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Título/Carrera *
                        </label>
                        <input
                            :value="edu.degree"
                            @input="updateEducation(index, 'degree', ($event.target as HTMLInputElement).value)"
                            type="text"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                            placeholder="ej: Ingeniería de Sistemas"
                        />
                    </div>
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Institución *
                        </label>
                        <input
                            :value="edu.institution"
                            @input="updateEducation(index, 'institution', ($event.target as HTMLInputElement).value)"
                            type="text"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                            placeholder="ej: Universidad Nacional"
                        />
                    </div>
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Fecha de inicio
                        </label>
                        <input
                            :value="edu.startDate"
                            @input="updateEducation(index, 'startDate', ($event.target as HTMLInputElement).value)"
                            type="month"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                        />
                    </div>
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Fecha de graduación
                        </label>
                        <input
                            :value="edu.endDate"
                            @input="updateEducation(index, 'endDate', ($event.target as HTMLInputElement).value)"
                            type="month"
                            :disabled="edu.current"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb] disabled:opacity-50"
                        />
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center space-x-2">
                            <input
                                :checked="edu.current"
                                @change="updateEducation(index, 'current', ($event.target as HTMLInputElement).checked)"
                                type="checkbox"
                                class="rounded border-gray-300"
                            />
                            <span class="text-sm text-gray-700"
                                >Actualmente estudiando</span
                            >
                        </label>
                    </div>
                    <div class="md:col-span-2">
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Descripción (opcional)
                        </label>
                        <textarea
                            :value="edu.description"
                            @input="updateEducation(index, 'description', ($event.target as HTMLInputElement).value)"
                            rows="3"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                            placeholder="ej: Mención en Seguridad Informática, Promedio: 4.2/5.0"
                        ></textarea>
                    </div>
                </div>
            </div>

            <button
                @click="addEducation"
                class="flex w-full items-center space-x-2 rounded-lg border-2 border-dashed border-gray-300 p-6 text-gray-600 transition-colors hover:border-[#005aeb] hover:text-[#005aeb]"
            >
                <Plus class="h-5 w-5" />
                <span>Agregar nueva formación</span>
            </button>
        </div>
    </div>
</template>
