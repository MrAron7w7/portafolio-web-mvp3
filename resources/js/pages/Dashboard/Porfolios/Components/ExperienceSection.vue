<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';

interface Experience {
    id: number;
    company: string;
    position: string;
    period: string;
    description: string;
    startDate: string;
    endDate: string;
    current: boolean;
}

const props = defineProps<{
    modelValue: Experience[];
}>();

const emit = defineEmits(['update:modelValue']);

const addExperience = () => {
    const newExperience = {
        id: Date.now(),
        company: '',
        position: '',
        period: '',
        description: '',
        startDate: '',
        endDate: '',
        current: false,
    };
    emit('update:modelValue', [...props.modelValue, newExperience]);
};

const removeExperience = (index: number) => {
    const updated = [...props.modelValue];
    updated.splice(index, 1);
    emit('update:modelValue', updated);
};

const updateExperience = (index: number, field: keyof Experience, value: any) => {
    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], [field]: value };
    emit('update:modelValue', updated);
};
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">
                Experiencia Laboral
            </h1>
            <p class="text-lg text-gray-600">
                Agrega tu historial profesional y experiencias relevantes.
            </p>
        </div>

        <div class="space-y-6">
            <div
                v-for="(exp, index) in modelValue"
                :key="exp.id"
                class="rounded-lg border border-gray-200 p-6"
            >
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Experiencia {{ index + 1 }}
                    </h3>
                    <button
                        @click="removeExperience(index)"
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
                            Empresa *
                        </label>
                        <input
                            :value="exp.company"
                            @input="updateExperience(index, 'company', ($event.target as HTMLInputElement).value)"
                            type="text"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                            placeholder="Nombre de la empresa"
                        />
                    </div>
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Cargo *
                        </label>
                        <input
                            :value="exp.position"
                            @input="updateExperience(index, 'position', ($event.target as HTMLInputElement).value)"
                            type="text"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                            placeholder="Tu posición"
                        />
                    </div>
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Fecha de inicio
                        </label>
                        <input
                            :value="exp.startDate"
                            @input="updateExperience(index, 'startDate', ($event.target as HTMLInputElement).value)"
                            type="month"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                        />
                    </div>
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Fecha de fin
                        </label>
                        <input
                            :value="exp.endDate"
                            @input="updateExperience(index, 'endDate', ($event.target as HTMLInputElement).value)"
                            type="month"
                            :disabled="exp.current"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb] disabled:opacity-50"
                        />
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center space-x-2">
                            <input
                                :checked="exp.current"
                                @change="updateExperience(index, 'current', ($event.target as HTMLInputElement).checked)"
                                type="checkbox"
                                class="rounded border-gray-300"
                            />
                            <span class="text-sm text-gray-700"
                                >Trabajo actual</span
                            >
                        </label>
                    </div>
                    <div class="md:col-span-2">
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Descripción
                        </label>
                        <textarea
                            :value="exp.description"
                            @input="updateExperience(index, 'description', ($event.target as HTMLInputElement).value)"
                            rows="3"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                            placeholder="Describe tus responsabilidades y logros..."
                        ></textarea>
                    </div>
                </div>
            </div>

            <button
                @click="addExperience"
                class="flex w-full items-center space-x-2 rounded-lg border-2 border-dashed border-gray-300 p-6 text-gray-600 transition-colors hover:border-[#005aeb] hover:text-[#005aeb]"
            >
                <Plus class="h-5 w-5" />
                <span>Agregar nueva experiencia</span>
            </button>
        </div>
    </div>
</template>
