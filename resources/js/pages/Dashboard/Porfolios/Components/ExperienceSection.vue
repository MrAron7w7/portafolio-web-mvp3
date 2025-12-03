<script setup lang="ts">
import { Plus, Trash2, ChevronDown } from 'lucide-vue-next';
import { ref, watch } from 'vue';

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

interface ExperienceErrors {
    company?: string;
    position?: string;
    startDate?: string;
    endDate?: string;
    description?: string;
}

const props = defineProps<{
    modelValue: Experience[];
}>();

const emit = defineEmits(['update:modelValue']);

// Estado
const experienceErrors = ref<{ [key: number]: ExperienceErrors }>({});
const expandedIndex = ref<number | null>(null);
const showDeleteModal = ref(false);
const deleteIndexToConfirm = ref<number | null>(null);

// Watcher para expandir solo el último
watch(
    () => props.modelValue.length,
    (newLength) => {
        if (newLength > 0) {
            expandedIndex.value = newLength - 1;
        } else {
            expandedIndex.value = null;
        }
    }
);

// Validar campo individual en tiempo real
const validateField = (index: number, field: keyof Experience, value: any) => {
    if (!experienceErrors.value[index]) {
        experienceErrors.value[index] = {};
    }

    const experience = props.modelValue[index];

    switch (field) {
        case 'company':
            if (!value.trim()) {
                experienceErrors.value[index].company = 'La empresa es requerida';
            } else if (value.trim().length < 3) {
                experienceErrors.value[index].company = 'Mínimo 3 caracteres';
            } else {
                delete experienceErrors.value[index].company;
            }
            break;

        case 'position':
            if (!value.trim()) {
                experienceErrors.value[index].position = 'El cargo es requerido';
            } else if (value.trim().length < 3) {
                experienceErrors.value[index].position = 'Mínimo 3 caracteres';
            } else {
                delete experienceErrors.value[index].position;
            }
            break;

        case 'startDate':
            if (!value) {
                experienceErrors.value[index].startDate = 'La fecha de inicio es requerida';
            } else {
                // Validar que startDate no sea mayor a endDate
                if (experience.endDate && !experience.current && value > experience.endDate) {
                    experienceErrors.value[index].startDate = 'La fecha de inicio no puede ser posterior a la de fin';
                    // También mostrar error en endDate
                    if (!experienceErrors.value[index].endDate) {
                        experienceErrors.value[index].endDate = 'La fecha de fin no puede ser anterior a la de inicio';
                    }
                } else {
                    delete experienceErrors.value[index].startDate;
                    // Limpiar error de endDate si ya no es necesario
                    if (experienceErrors.value[index].endDate === 'La fecha de fin no puede ser anterior a la de inicio') {
                        delete experienceErrors.value[index].endDate;
                    }
                }
            }
            break;

        case 'endDate':
            if (!experience.current) {
                if (!value) {
                    experienceErrors.value[index].endDate = 'La fecha de fin es requerida o marca "Trabajo actual"';
                } else if (experience.startDate && value < experience.startDate) {
                    experienceErrors.value[index].endDate = 'La fecha de fin no puede ser anterior a la de inicio';
                    // También mostrar error en startDate
                    if (!experienceErrors.value[index].startDate) {
                        experienceErrors.value[index].startDate = 'La fecha de inicio no puede ser posterior a la de fin';
                    }
                } else {
                    delete experienceErrors.value[index].endDate;
                    // Limpiar error de startDate si ya no es necesario
                    if (experienceErrors.value[index].startDate === 'La fecha de inicio no puede ser posterior a la de fin') {
                        delete experienceErrors.value[index].startDate;
                    }
                }
            } else {
                delete experienceErrors.value[index].endDate;
            }
            break;

        case 'description':
            if (!value.trim()) {
                experienceErrors.value[index].description = 'La descripción es requerida';
            } else if (value.trim().length < 10) {
                experienceErrors.value[index].description = 'Mínimo 10 caracteres';
            } else if (value.length > 500) {
                experienceErrors.value[index].description = 'Máximo 500 caracteres';
            } else {
                delete experienceErrors.value[index].description;
            }
            break;

        case 'current':
            if (value) {
                delete experienceErrors.value[index].endDate;
            } else {
                // Si desmarcas "trabajo actual", valida endDate
                if (!experience.endDate) {
                    experienceErrors.value[index].endDate = 'La fecha de fin es requerida o marca "Trabajo actual"';
                }
            }
            break;
    }
};

// Obtener clase de error
const getErrorClass = (index: number, field: keyof ExperienceErrors) => {
    return experienceErrors.value[index]?.[field]
        ? 'border-red-500 focus:border-red-500'
        : 'border-gray-300 focus:border-[#005aeb]';
};

// Agregar experiencia
const addExperience = () => {
    const newExperience: Experience = {
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

// Toggle accordion
const toggleExperience = (index: number) => {
    expandedIndex.value = expandedIndex.value === index ? null : index;
};

// Abrir modal de eliminación
const openDeleteModal = (index: number) => {
    deleteIndexToConfirm.value = index;
    showDeleteModal.value = true;
};

// Confirmar eliminación
const confirmDelete = () => {
    if (deleteIndexToConfirm.value !== null) {
        const updated = [...props.modelValue];
        updated.splice(deleteIndexToConfirm.value, 1);
        emit('update:modelValue', updated);
        delete experienceErrors.value[deleteIndexToConfirm.value];
        if (expandedIndex.value === deleteIndexToConfirm.value) {
            expandedIndex.value = null;
        }
    }
    closeDeleteModal();
};

// Cerrar modal
const closeDeleteModal = () => {
    showDeleteModal.value = false;
    deleteIndexToConfirm.value = null;
};

// Remover experiencia
const removeExperience = (index: number) => {
    openDeleteModal(index);
};

// Actualizar experiencia
const updateExperience = (index: number, field: keyof Experience, value: any) => {
    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], [field]: value };
    emit('update:modelValue', updated);

    // Validar inmediatamente después de actualizar
    validateField(index, field, value);
};

// Actualizar y validar checkbox
const handleCurrentChange = (index: number, checked: boolean) => {
    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], current: checked };
    if (checked) {
        updated[index].endDate = '';
    }
    emit('update:modelValue', updated);
    validateField(index, 'current', checked);
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

        <div class="space-y-3">
            <!-- Accordion Items -->
            <div
                v-for="(exp, index) in modelValue"
                :key="exp.id"
                class="rounded-lg border border-gray-200 overflow-hidden transition-all"
            >
                <!-- Accordion Header -->
                <button
                    @click="toggleExperience(index)"
                    class="w-full flex items-center justify-between px-6 py-4 bg-gray-50 hover:bg-gray-100 transition-colors"
                >
                    <div class="flex items-center gap-3 flex-1 text-left">
                        <ChevronDown
                            class="h-5 w-5 text-gray-600 transition-transform"
                            :class="{ 'transform rotate-180': expandedIndex === index }"
                        />
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Experiencia {{ index + 1 }}
                            </h3>
                            <p v-if="exp.position" class="text-sm text-gray-600">
                                {{ exp.position }} {{ exp.company ? `en ${exp.company}` : '' }}
                            </p>
                        </div>
                    </div>

                    <!-- Botón eliminar -->
                    <button
                        @click.stop="removeExperience(index)"
                        class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded transition-colors"
                    >
                        <Trash2 class="h-4 w-4" />
                    </button>
                </button>

                <!-- Accordion Content -->
                <transition
                    enter-active-class="transition-all duration-300 ease-out"
                    leave-active-class="transition-all duration-300 ease-in"
                    enter-from-class="max-h-0 opacity-0"
                    enter-to-class="max-h-[800px] opacity-100"
                    leave-from-class="max-h-[800px] opacity-100"
                    leave-to-class="max-h-0 opacity-0"
                >
                    <div
                        v-if="expandedIndex === index"
                        class="px-6 py-6 bg-white border-t border-gray-200"
                    >
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Empresa -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Empresa *
                                </label>
                                <input
                                    :value="exp.company"
                                    @input="updateExperience(index, 'company', ($event.target as HTMLInputElement).value)"
                                    type="text"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors"
                                    :class="getErrorClass(index, 'company')"
                                    placeholder="Nombre de la empresa"
                                />
                                <p
                                    v-if="experienceErrors[index]?.company"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ experienceErrors[index].company }}
                                </p>
                            </div>

                            <!-- Cargo -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Cargo *
                                </label>
                                <input
                                    :value="exp.position"
                                    @input="updateExperience(index, 'position', ($event.target as HTMLInputElement).value)"
                                    type="text"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors"
                                    :class="getErrorClass(index, 'position')"
                                    placeholder="Tu posición"
                                />
                                <p
                                    v-if="experienceErrors[index]?.position"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ experienceErrors[index].position }}
                                </p>
                            </div>

                            <!-- Fecha de inicio -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Fecha de inicio *
                                </label>
                                <input
                                    :value="exp.startDate"
                                    @input="updateExperience(index, 'startDate', ($event.target as HTMLInputElement).value)"
                                    type="month"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors"
                                    :class="getErrorClass(index, 'startDate')"
                                />
                                <p
                                    v-if="experienceErrors[index]?.startDate"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ experienceErrors[index].startDate }}
                                </p>
                            </div>

                            <!-- Fecha de fin -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Fecha de fin
                                </label>
                                <input
                                    :value="exp.endDate"
                                    @input="updateExperience(index, 'endDate', ($event.target as HTMLInputElement).value)"
                                    type="month"
                                    :disabled="exp.current"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    :class="getErrorClass(index, 'endDate')"
                                />
                                <p
                                    v-if="experienceErrors[index]?.endDate"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ experienceErrors[index].endDate }}
                                </p>
                            </div>

                            <!-- Checkbox trabajo actual -->
                            <div class="md:col-span-2">
                                <label class="flex items-center space-x-3">
                                    <input
                                        :checked="exp.current"
                                        @change="handleCurrentChange(index, ($event.target as HTMLInputElement).checked)"
                                        type="checkbox"
                                        class="rounded border-gray-300 cursor-pointer w-4 h-4"
                                    />
                                    <span class="text-sm font-medium text-gray-700 cursor-pointer">
                                        Trabajo actual
                                    </span>
                                </label>
                            </div>

                            <!-- Descripción -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Descripción *
                                    <span class="text-xs text-gray-500">
                                        ({{ exp.description.length }}/500)
                                    </span>
                                </label>
                                <textarea
                                    :value="exp.description"
                                    @input="updateExperience(index, 'description', ($event.target as HTMLInputElement).value)"
                                    rows="3"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors"
                                    :class="getErrorClass(index, 'description')"
                                    placeholder="Describe tus responsabilidades y logros..."
                                ></textarea>
                                <p
                                    v-if="experienceErrors[index]?.description"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ experienceErrors[index].description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Botón agregar experiencia -->
            <button
                @click="addExperience"
                class="flex w-full items-center justify-center space-x-2 rounded-lg border-2 border-dashed border-gray-300 p-4 text-gray-600 transition-colors hover:border-[#005aeb] hover:text-[#005aeb]"
            >
                <Plus class="h-5 w-5" />
                <span>Agregar nueva experiencia</span>
            </button>
        </div>

        <!-- Modal de confirmación -->
        <Teleport to="body">
            <transition
                enter-active-class="transition-opacity duration-300"
                leave-active-class="transition-opacity duration-300"
                enter-from-class="opacity-0"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="showDeleteModal"
                    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                    @click.self="closeDeleteModal"
                >
                    <transition
                        enter-active-class="transition-all duration-300"
                        leave-active-class="transition-all duration-300"
                        enter-from-class="opacity-0 scale-95"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showDeleteModal" class="bg-white rounded-lg shadow-lg p-6 max-w-sm mx-4">
                            <div
                                class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4"
                            >
                                <Trash2 class="h-6 w-6 text-red-600" />
                            </div>

                            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">
                                Eliminar experiencia
                            </h3>

                            <p class="text-sm text-gray-600 text-center mb-6">
                                ¿Estás seguro de que deseas eliminar esta experiencia laboral?
                            </p>

                            <div class="flex gap-3">
                                <button
                                    @click="closeDeleteModal"
                                    class="flex-1 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors"
                                >
                                    Cancelar
                                </button>
                                <button
                                    @click="confirmDelete"
                                    class="flex-1 px-4 py-2 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition-colors"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>
            </transition>
        </Teleport>
    </div>
</template>
