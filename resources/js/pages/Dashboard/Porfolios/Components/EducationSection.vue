<script setup lang="ts">
import { Plus, Trash2, ChevronDown, AlertCircle, Sparkles } from 'lucide-vue-next';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useEducationValidation, type Education } from './Composables/useEducationValidation';

const props = defineProps<{
    modelValue: Education[];
    validation: any;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: Education[]];
}>();

const {
    errors: educationErrors,
    validateField,
    getErrorClass,
    reindexErrors,
    getCharCount,
} = props.validation;

// Índice del acordeón expandido
const expandedIndex = ref<number | null>(null);

// NUEVO: Estado de "NUEVOS ITEMS"
const newItems = ref(new Set<number>());
const lastAddedId = ref<number | null>(null);

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

onMounted(() => {
    props.validation.clearAllErrors();
});

// Auto-expandir nuevo item
watch(
    () => props.modelValue.length,
    (newLength, oldLength) => {
        if (newLength > oldLength && lastAddedId.value !== null) {
            const education = props.modelValue.find((e) => e.id === lastAddedId.value);
            if (education) {
                expandedIndex.value = lastAddedId.value;
            }
            lastAddedId.value = null;
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

// Agregar educación
const addEducation = () => {
    const newId = Date.now();
    lastAddedId.value = newId;

    // 1. Lo marcamos como nuevo
    newItems.value.add(newId);

    const newEducation: Education = {
        id: newId,
        degree: '',
        institution: '',
        startDate: '',
        endDate: '',
        description: '',
        current: false,
    };
    emit('update:modelValue', [...props.modelValue, newEducation]);

    // Quitar el estado "nuevo" automáticamente después de 3 segundos
    setTimeout(() => {
        newItems.value.delete(newId);
    }, 3000);
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
    const idToRemove = props.modelValue[index].id;
    newItems.value.delete(idToRemove);

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
const toggleEducation = (id: number) => {
    expandedIndex.value = expandedIndex.value === id ? null : id;
    if (newItems.value.has(id)) {
        newItems.value.delete(id);
    }
};

// Actualizar campo + validar
const updateEducation = (index: number, field: keyof Education, value: any) => {
    const id = props.modelValue[index].id;

    // Si el usuario edita, ya no es "nuevo"
    if (newItems.value.has(id)) {
        newItems.value.delete(id);
    }

    // Marcar campo como tocado al escribir
    props.validation.markAsTouched(index, field);

    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], [field]: value };
    emit('update:modelValue', updated);

    // Validar en tiempo real (forceShow = false)
    if (educationErrors[index]?.[field]) {
        validateField(index, field, value, updated[index], false);
    }
};

// Checkbox "Actualmente estudiando"
const handleCurrentChange = (index: number, checked: boolean) => {
    const id = props.modelValue[index].id;

    // Si el usuario edita, ya no es "nuevo"
    if (newItems.value.has(id)) {
        newItems.value.delete(id);
    }

    // Marcar como tocado
    props.validation.markAsTouched(index, 'current');

    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], current: checked };
    if (checked) {
        updated[index].endDate = '';
    }
    emit('update:modelValue', updated);
    validateField(index, 'current', checked, updated[index], false);
};

// Función para obtener clase de error del contenedor
const getContainerClass = (index: number, id: number) => {
    if (hasEducationError(index)) {
        return 'border-red-300 bg-red-50';
    }
    if (newItems.value.has(id)) {
        return 'border-emerald-300 bg-emerald-50';
    }
    return 'border-gray-200 bg-white';
};

// Verificar si una educación tiene errores
const hasEducationError = (index: number) => {
    const errors = educationErrors[index];
    return errors && Object.keys(errors).length > 0;
};

// Función para obtener clase de error con forceShow
const getErrorClassForInput = (index: number, field: keyof Education) => {
    return props.validation.getErrorClass(index, field, false);
};
</script>

<template>
    <div class="relative" ref="containerRef">
        <div class="mb-8">
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">Educación</h1>
            <p class="text-lg text-gray-600">
                Agrega tu historial educativo y formación profesional.
            </p>
        </div>

        <div class="space-y-3">
            <!-- Items del acordeón -->
            <div v-for="(education, index) in modelValue" :key="education.id"
                class="overflow-hidden rounded-lg border transition-all duration-500 ease-in-out"
                :class="getContainerClass(index, education.id)">
                <!-- Header del acordeón -->
                <button @click="toggleEducation(education.id)"
                    class="flex w-full items-center justify-between px-4 py-3 transition-colors hover:bg-gray-50/50"
                    type="button">
                    <div class="flex flex-1 items-center gap-3 text-left">
                        <ChevronDown
                            class="h-5 w-5 flex-shrink-0 text-gray-600 transition-transform duration-400 ease-out"
                            :class="{ 'rotate-180 transform': expandedIndex === education.id }" />

                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="text-base font-medium transition-colors duration-300" :class="{
                                    'text-red-700': hasEducationError(index),
                                    'text-emerald-700': newItems.has(education.id),
                                    'text-gray-900': !hasEducationError(index) && !newItems.has(education.id)
                                }">
                                    {{ education.degree || 'Nueva educación' }}
                                </span>

                                <!-- ICONO DE ERROR -->
                                <AlertCircle v-if="hasEducationError(index)"
                                    class="h-4 w-4 text-red-500 animate-pulse" />

                                <!-- ICONO DE NUEVO -->
                                <Sparkles v-if="newItems.has(education.id) && !hasEducationError(index)"
                                    class="h-4 w-4 text-emerald-500 animate-bounce" />
                            </div>
                            <span v-if="education.institution" class="text-sm text-gray-500">
                                en {{ education.institution }}
                            </span>
                        </div>
                    </div>

                    <button @click.stop="openDeleteConfirm(index, $event)" type="button"
                        class="flex-shrink-0 p-1.5 text-gray-400 transition-colors hover:text-red-500">
                        <Trash2 class="h-4 w-4" />
                    </button>
                </button>

                <!-- Contenido del acordeón -->
                <transition enter-active-class="transition-all duration-300 ease-out"
                    leave-active-class="transition-all duration-300 ease-in"
                    enter-from-class="max-h-0 opacity-0 overflow-hidden" enter-to-class="max-h-[1200px] opacity-100"
                    leave-from-class="max-h-[1200px] opacity-100" leave-to-class="max-h-0 opacity-0 overflow-hidden">
                    <div v-if="expandedIndex === education.id" class="border-t px-4 py-4 bg-white/50"
                        :class="hasEducationError(index) ? 'border-red-200' : 'border-gray-200'">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Título/Carrera -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Título/Carrera *
                                </label>
                                <input :value="education.degree" @input="
                                    updateEducation(
                                        index,
                                        'degree',
                                        ($event.target as HTMLInputElement).value
                                    )
                                    " type="text"
                                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                    :class="getErrorClassForInput(index, 'degree')"
                                    placeholder="Ej: Ingeniería en Informática" />
                                <p v-if="educationErrors[index]?.degree" class="mt-1 text-sm text-red-500">
                                    {{ educationErrors[index].degree }}
                                </p>
                            </div>

                            <!-- Institución -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Institución *
                                </label>
                                <input :value="education.institution" @input="
                                    updateEducation(
                                        index,
                                        'institution',
                                        ($event.target as HTMLInputElement).value
                                    )
                                    " type="text"
                                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                    :class="getErrorClassForInput(index, 'institution')"
                                    placeholder="Ej: Universidad Nacional" />
                                <p v-if="educationErrors[index]?.institution" class="mt-1 text-sm text-red-500">
                                    {{ educationErrors[index].institution }}
                                </p>
                            </div>

                            <!-- Fecha de inicio -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Fecha de inicio *
                                </label>
                                <input :value="education.startDate" @input="
                                    updateEducation(
                                        index,
                                        'startDate',
                                        ($event.target as HTMLInputElement).value
                                    )
                                    " type="month"
                                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                    :class="getErrorClassForInput(index, 'startDate')" />
                                <p v-if="educationErrors[index]?.startDate" class="mt-1 text-sm text-red-500">
                                    {{ educationErrors[index].startDate }}
                                </p>
                            </div>

                            <!-- Fecha de fin -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Fecha de fin
                                </label>
                                <input :value="education.endDate" @input="
                                    updateEducation(
                                        index,
                                        'endDate',
                                        ($event.target as HTMLInputElement).value
                                    )
                                    " type="month" :disabled="education.current"
                                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20 disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="getErrorClassForInput(index, 'endDate')" />
                                <p v-if="educationErrors[index]?.endDate" class="mt-1 text-sm text-red-500">
                                    {{ educationErrors[index].endDate }}
                                </p>
                            </div>

                            <!-- Checkbox actualmente estudiando -->
                            <div class="md:col-span-2">
                                <label class="flex cursor-pointer items-center space-x-3">
                                    <input :checked="education.current" @change="
                                        handleCurrentChange(
                                            index,
                                            ($event.target as HTMLInputElement).checked
                                        )
                                        " type="checkbox"
                                        class="h-4 w-4 cursor-pointer rounded border-gray-300 text-[#005aeb] focus:ring-[#005aeb]" />
                                    <span class="text-sm font-medium text-gray-700">
                                        Actualmente estudiando
                                    </span>
                                </label>
                            </div>


                        </div>
                    </div>
                </transition>
            </div>

            <!-- Botón agregar educación -->
            <button @click="addEducation"
                class="flex w-full items-center justify-center space-x-2 rounded-lg border-2 border-dashed border-gray-300 p-4 text-gray-600 transition-colors hover:border-[#005aeb] hover:text-[#005aeb]">
                <Plus class="h-5 w-5" />
                <span>Agregar nueva educación</span>
            </button>
        </div>

        <!-- Backdrop para cerrar popover -->
        <div v-if="deleteConfirmIndex !== null" @click="closeDeleteConfirm" class="fixed inset-0 z-40"></div>

        <!-- Popover de confirmación -->
        <transition enter-active-class="transition-all duration-200 ease-out"
            leave-active-class="transition-all duration-200 ease-in" enter-from-class="opacity-0 scale-95 translate-y-2"
            enter-to-class="opacity-100 scale-100 translate-y-0" leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-2">
            <div v-if="deleteConfirmIndex !== null"
                class="pointer-events-auto fixed z-50 w-56 rounded-lg border border-gray-200 bg-white p-4 shadow-2xl"
                :style="{
                    top: popoverPosition.top + 'px',
                    left: popoverPosition.left + 'px',
                }">
                <!-- Flecha arriba -->
                <div v-if="popoverPosition.positionY === 'below'"
                    class="absolute -top-2 h-4 w-4 rotate-45 border-l border-t border-gray-200 bg-white"
                    :style="{ left: popoverPosition.arrowLeft + 'px' }"></div>

                <!-- Flecha abajo -->
                <div v-if="popoverPosition.positionY === 'above'"
                    class="absolute -bottom-2 h-4 w-4 rotate-45 border-b border-r border-gray-200 bg-white"
                    :style="{ left: popoverPosition.arrowLeft + 'px' }"></div>

                <div class="relative">
                    <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                        <Trash2 class="h-5 w-5 text-red-600" />
                    </div>

                    <h4 class="mb-1 text-center text-sm font-semibold text-gray-900">
                        ¿Eliminar educación?
                    </h4>

                    <p class="mb-4 text-center text-xs text-gray-600">
                        Esta acción no se puede deshacer
                    </p>

                    <div class="flex gap-2">
                        <button @click.stop="closeDeleteConfirm"
                            class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button @click.stop="confirmDelete(deleteConfirmIndex)"
                            class="flex-1 rounded-lg bg-red-600 px-3 py-2 text-xs font-medium text-white transition-colors hover:bg-red-700">
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
