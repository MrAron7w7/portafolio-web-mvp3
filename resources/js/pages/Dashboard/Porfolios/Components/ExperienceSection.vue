<script setup lang="ts">
import { Plus, Trash2, ChevronDown } from 'lucide-vue-next';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useExperienceValidation, type Experience } from './Composables/useExperienceValidation';

const props = defineProps<{
    modelValue: Experience[];
}>();

const emit = defineEmits(['update:modelValue']);

// Usar el composable de validación
const {
    errors: experienceErrors,
    validateField,
    getErrorClass,
    reindexErrors,
    getCharCount,
} = useExperienceValidation();

// Índice del acordeón expandido
const expandedIndex = ref<number | null>(null);

// Variables para el popover de eliminación
const deleteConfirmIndex = ref<number | null>(null);
const popoverPosition = ref({
    top: 0,
    left: 0,
    arrowLeft: 0,
    arrowTop: 0,
    positionY: 'below' as 'below' | 'above',
});
const containerRef = ref<HTMLElement | null>(null);
const deleteConfirmButtonRef = ref<HTMLElement | null>(null);

// Abrir siempre la última experiencia agregada
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

// Cerrar popover
const closeDeleteConfirm = () => {
    deleteConfirmIndex.value = null;
    deleteConfirmButtonRef.value = null;
};

// Listener global (resize / ESC)
const handleGlobalClose = () => {
    closeDeleteConfirm();
};

const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Escape') {
        handleGlobalClose();
    }
};

// Recalcular posición del popover
const recalculatePopoverPosition = () => {
    if (!deleteConfirmButtonRef.value) return;

    const button = deleteConfirmButtonRef.value;
    const rect = button.getBoundingClientRect();

    const popoverWidth = 224;
    const popoverHeight = 220;
    const spacing = 8;
    const windowPadding = 16;

    // ===== POSICIÓN HORIZONTAL =====
    const buttonCenterX = rect.left + rect.width / 2;
    let popoverLeftPosition = buttonCenterX - popoverWidth / 2;

    if (popoverLeftPosition + popoverWidth > window.innerWidth - windowPadding) {
        popoverLeftPosition = window.innerWidth - popoverWidth - windowPadding;
    }

    if (popoverLeftPosition < windowPadding) {
        popoverLeftPosition = windowPadding;
    }

    // ===== POSICIÓN VERTICAL =====
    const spaceBelow = window.innerHeight - rect.bottom;
    const spaceAbove = rect.top;

    let popoverTopPosition: number;
    let positionY: 'below' | 'above';
    let arrowTop: number;

    if (spaceBelow >= popoverHeight + spacing) {
        popoverTopPosition = rect.bottom + spacing;
        positionY = 'below';
        arrowTop = -8;
    } else if (spaceAbove >= popoverHeight + spacing) {
        popoverTopPosition = rect.top - popoverHeight + 32;
        positionY = 'above';
        arrowTop = popoverHeight - 4;
    } else {
        if (spaceBelow > spaceAbove) {
            popoverTopPosition = rect.bottom + spacing;
            positionY = 'below';
            arrowTop = -8;
        } else {
            popoverTopPosition = rect.top - popoverHeight - spacing;
            positionY = 'above';
            arrowTop = popoverHeight - 4;
        }
    }

    // ===== POSICIÓN DE LA FLECHA =====
    let arrowLeft = buttonCenterX - popoverLeftPosition - 8;
    arrowLeft = Math.max(8, Math.min(arrowLeft, popoverWidth - 16));

    popoverPosition.value = {
        top: popoverTopPosition,
        left: popoverLeftPosition,
        arrowLeft,
        arrowTop,
        positionY,
    };
};

// Listener para scroll
const handleScroll = () => {
    if (deleteConfirmIndex.value !== null && deleteConfirmButtonRef.value) {
        recalculatePopoverPosition();
    }
};

onMounted(() => {
    window.addEventListener('resize', handleGlobalClose);
    window.addEventListener('scroll', handleScroll, true);
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('resize', handleGlobalClose);
    window.removeEventListener('scroll', handleScroll, true);
    window.removeEventListener('keydown', handleKeydown);
});

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

// Abrir popover de confirmación
const openDeleteConfirm = (index: number, event: Event) => {
    const button = event.currentTarget as HTMLElement;

    if (deleteConfirmIndex.value === index) {
        closeDeleteConfirm();
        return;
    }

    deleteConfirmIndex.value = index;
    deleteConfirmButtonRef.value = button;
    recalculatePopoverPosition();
};

// Confirmar eliminación
const confirmDelete = (index: number | null) => {
    if (index === null) return;
    const updated = [...props.modelValue];
    updated.splice(index, 1);
    emit('update:modelValue', updated);

    // Usar la función del composable para reindexar errores
    reindexErrors(index);

    if (expandedIndex.value === index) {
        expandedIndex.value = null;
    }
    closeDeleteConfirm();
};

// Toggle acordeón
const toggleExperience = (index: number) => {
    expandedIndex.value = expandedIndex.value === index ? null : index;
};

// Actualizar campo + validar
const updateExperience = (index: number, field: keyof Experience, value: any) => {
    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], [field]: value };
    emit('update:modelValue', updated);

    validateField(index, field, value, updated[index]);
};

// Checkbox "Trabajo actual"
const handleCurrentChange = (index: number, checked: boolean) => {
    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], current: checked };
    if (checked) {
        updated[index].endDate = '';
    }
    emit('update:modelValue', updated);
    validateField(index, 'current', checked, updated[index]);
};
</script>

<template>
    <div class="relative" ref="containerRef">
        <div class="mb-8">
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">
                Experiencia Laboral
            </h1>
            <p class="text-lg text-gray-600">
                Agrega tu historial profesional y experiencias relevantes.
            </p>
        </div>

        <div class="space-y-3">
            <!-- Items del acordeón -->
            <div
                v-for="(exp, index) in modelValue"
                :key="exp.id"
                class="overflow-hidden rounded-lg border border-gray-200 transition-all"
            >
                <!-- Header del acordeón -->
                <button
                    @click="toggleExperience(index)"
                    class="flex w-full items-center justify-between bg-gray-50 px-6 py-4 transition-colors hover:bg-gray-100"
                >
                    <div class="flex flex-1 items-center gap-3 text-left">
                        <ChevronDown
                            class="h-5 w-5 text-gray-600 transition-transform duration-300"
                            :class="{ 'rotate-180 transform': expandedIndex === index }"
                        />
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Experiencia {{ index + 1 }}
                            </h3>
                            <p v-if="exp.position" class="text-sm text-gray-600">
                                {{ exp.position }}
                                {{ exp.company ? `en ${exp.company}` : '' }}
                            </p>
                        </div>
                    </div>

                    <!-- Botón eliminar -->
                    <button
                        @click.stop="openDeleteConfirm(index, $event)"
                        class="rounded p-2 text-red-600 transition-colors hover:bg-red-50 hover:text-red-800"
                    >
                        <Trash2 class="h-4 w-4" />
                    </button>
                </button>

                <!-- Contenido del acordeón -->
                <transition
                    enter-active-class="transition-all duration-300 ease-out"
                    leave-active-class="transition-all duration-300 ease-in"
                    enter-from-class="max-h-0 opacity-0 overflow-hidden"
                    enter-to-class="max-h-[900px] opacity-100"
                    leave-from-class="max-h-[900px] opacity-100"
                    leave-to-class="max-h-0 opacity-0 overflow-hidden"
                >
                    <div
                        v-if="expandedIndex === index"
                        class="border-t border-gray-200 bg-white px-6 py-6"
                    >
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Empresa -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Empresa *
                                </label>
                                <input
                                    :value="exp.company"
                                    @input="
                                        updateExperience(
                                            index,
                                            'company',
                                            ($event.target as HTMLInputElement).value
                                        )
                                    "
                                    type="text"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors focus:ring-2"
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
                                    @input="
                                        updateExperience(
                                            index,
                                            'position',
                                            ($event.target as HTMLInputElement).value
                                        )
                                    "
                                    type="text"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors focus:ring-2"
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
                                    @input="
                                        updateExperience(
                                            index,
                                            'startDate',
                                            ($event.target as HTMLInputElement).value
                                        )
                                    "
                                    type="month"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors focus:ring-2"
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
                                    @input="
                                        updateExperience(
                                            index,
                                            'endDate',
                                            ($event.target as HTMLInputElement).value
                                        )
                                    "
                                    type="month"
                                    :disabled="exp.current"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors focus:ring-2 disabled:cursor-not-allowed disabled:opacity-50"
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
                                <label class="flex cursor-pointer items-center space-x-3">
                                    <input
                                        :checked="exp.current"
                                        @change="
                                            handleCurrentChange(
                                                index,
                                                ($event.target as HTMLInputElement).checked
                                            )
                                        "
                                        type="checkbox"
                                        class="h-4 w-4 cursor-pointer rounded border-gray-300 text-[#005aeb] focus:ring-[#005aeb]"
                                    />
                                    <span class="text-sm font-medium text-gray-700">
                                        Trabajo actual
                                    </span>
                                </label>
                            </div>

                            <!-- Descripción -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Descripción *
                                </label>
                                <p class="mb-2 text-xs text-gray-500">
                                    Describe tus responsabilidades, logros y tecnologías utilizadas.
                                </p>
                                <textarea
                                    :value="exp.description"
                                    @input="
                                        updateExperience(
                                            index,
                                            'description',
                                            ($event.target as HTMLTextAreaElement).value
                                        )
                                    "
                                    rows="4"
                                    class="w-full rounded-lg border px-4 py-3 transition-colors focus:ring-2"
                                    :class="getErrorClass(index, 'description')"
                                    placeholder="Describe tus responsabilidades, logros principales..."
                                ></textarea>
                                <div class="mt-1 flex items-center justify-between">
                                    <span
                                        v-if="experienceErrors[index]?.description"
                                        class="text-sm text-red-500"
                                    >
                                        {{ experienceErrors[index].description }}
                                    </span>
                                    <span v-else class="text-sm text-transparent">placeholder</span>
                                    <span
                                        class="text-xs"
                                        :class="[
                                            getCharCount(exp.description).isOverLimit
                                                ? 'font-medium text-red-500'
                                                : getCharCount(exp.description).isNearLimit
                                                  ? 'text-amber-500'
                                                  : 'text-gray-400',
                                        ]"
                                    >
                                        {{ getCharCount(exp.description).current }}/{{
                                            getCharCount(exp.description).max
                                        }}
                                    </span>
                                </div>
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

        <!-- Backdrop para cerrar popover -->
        <div
            v-if="deleteConfirmIndex !== null"
            @click="closeDeleteConfirm"
            class="fixed inset-0 z-40"
        ></div>

        <!-- Popover de confirmación -->
        <transition
            enter-active-class="transition-all duration-200 ease-out"
            leave-active-class="transition-all duration-200 ease-in"
            enter-from-class="opacity-0 scale-95 translate-y-2"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-2"
        >
            <div
                v-if="deleteConfirmIndex !== null"
                class="pointer-events-auto fixed z-50 w-56 rounded-lg border border-gray-200 bg-white p-4 shadow-2xl"
                :style="{
                    top: popoverPosition.top + 'px',
                    left: popoverPosition.left + 'px',
                }"
            >
                <!-- Flecha arriba -->
                <div
                    v-if="popoverPosition.positionY === 'below'"
                    class="absolute -top-2 h-4 w-4 rotate-45 border-l border-t border-gray-200 bg-white"
                    :style="{ left: popoverPosition.arrowLeft + 'px' }"
                ></div>

                <!-- Flecha abajo -->
                <div
                    v-if="popoverPosition.positionY === 'above'"
                    class="absolute -bottom-2 h-4 w-4 rotate-45 border-b border-r border-gray-200 bg-white"
                    :style="{ left: popoverPosition.arrowLeft + 'px' }"
                ></div>

                <div class="relative">
                    <div
                        class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-red-100"
                    >
                        <Trash2 class="h-5 w-5 text-red-600" />
                    </div>

                    <h4 class="mb-1 text-center text-sm font-semibold text-gray-900">
                        ¿Eliminar experiencia?
                    </h4>

                    <p class="mb-4 text-center text-xs text-gray-600">
                        Esta acción no se puede deshacer
                    </p>

                    <div class="flex gap-2">
                        <button
                            @click.stop="closeDeleteConfirm"
                            class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                        <button
                            @click.stop="confirmDelete(deleteConfirmIndex)"
                            class="flex-1 rounded-lg bg-red-600 px-3 py-2 text-xs font-medium text-white transition-colors hover:bg-red-700"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
