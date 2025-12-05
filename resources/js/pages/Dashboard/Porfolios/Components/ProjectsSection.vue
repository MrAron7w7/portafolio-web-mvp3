<script setup lang="ts">
import { Plus, Trash2, Upload, ChevronDown } from 'lucide-vue-next';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useProjectValidation, type Project } from './Composables/useProjectValidation';

const props = defineProps<{
    modelValue: Project[];
}>();

const emit = defineEmits(['update:modelValue']);

// Usar el composable de validación
const {
    errors: projectErrors,
    validateField,
    validateImage,
    clearImageError,
    getErrorClass,
    reindexErrors,
    getCharCount,
} = useProjectValidation();

// Input local para cada proyecto (tecnologías)
const techInputs = ref<{ [key: number]: string }>({});

// Índice del proyecto expandido (solo uno)
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

// Watcher para expandir solo el último proyecto
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

// Agregar proyecto
const addProject = () => {
    const newProject: Project = {
        id: Date.now(),
        name: '',
        description: '',
        image: null,
        link: '',
        technologies: [],
    };
    emit('update:modelValue', [...props.modelValue, newProject]);
};

// Toggle acordeón
const toggleProject = (index: number) => {
    expandedIndex.value = expandedIndex.value === index ? null : index;
};

// Eliminar proyecto
const removeProject = (index: number) => {
    const updated = [...props.modelValue];
    updated.splice(index, 1);
    emit('update:modelValue', updated);
    delete techInputs.value[index];

    // Reindexar errores
    reindexErrors(index);

    if (expandedIndex.value === index) {
        expandedIndex.value = null;
    }
    closeDeleteConfirm();
};

// Actualizar proyecto
const updateProject = (index: number, field: keyof Project, value: any) => {
    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], [field]: value };
    emit('update:modelValue', updated);

    // Validar el campo
    validateField(index, field, value);
};

// Manejo de subida de imagen
const handleImageUpload = (index: number, event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Validar imagen usando el composable
        if (!validateImage(index, file)) {
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            updateProject(index, 'image', e.target?.result as string);
            clearImageError(index);
        };
        reader.readAsDataURL(file);
    }
};

// Procesar tecnologías
const processTechnologies = (index: number) => {
    const input = techInputs.value[index] || '';
    if (!input.trim()) return;

    const newTechs = input
        .split(',')
        .map((tech) => tech.trim())
        .filter((tech) => tech !== '');

    const allTechs = [...props.modelValue[index].technologies, ...newTechs];
    updateProject(index, 'technologies', allTechs);
    techInputs.value[index] = '';
};

// Manejo de teclado para tecnologías
const handleTechKeydown = (index: number, event: KeyboardEvent) => {
    if (event.key === ',' || event.key === 'Enter') {
        event.preventDefault();
        processTechnologies(index);
    }
};

// Eliminar tecnología
const removeTechnology = (index: number, techIndex: number) => {
    const updated = [...props.modelValue[index].technologies];
    updated.splice(techIndex, 1);
    updateProject(index, 'technologies', updated);
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
    removeProject(index);
};
</script>

<template>
    <div class="relative" ref="containerRef">
        <div class="mb-8">
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">Proyectos</h1>
            <p class="text-lg text-gray-600">
                Muestra tus mejores trabajos y proyectos realizados.
            </p>
        </div>

        <div class="space-y-3">
            <!-- Items del acordeón -->
            <div
                v-for="(project, index) in modelValue"
                :key="project.id"
                class="overflow-hidden rounded-lg border border-gray-200 transition-all"
            >
                <!-- Header del acordeón -->
                <button
                    @click="toggleProject(index)"
                    class="flex w-full items-center justify-between bg-gray-50 px-6 py-4 transition-colors hover:bg-gray-100"
                >
                    <div class="flex flex-1 items-center gap-3 text-left">
                        <ChevronDown
                            class="h-5 w-5 text-gray-600 transition-transform duration-300"
                            :class="{ 'rotate-180 transform': expandedIndex === index }"
                        />
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Proyecto {{ index + 1 }}
                            </h3>
                            <p v-if="project.name" class="text-sm text-gray-600">
                                {{ project.name }}
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
                    enter-to-class="max-h-[1100px] opacity-100"
                    leave-from-class="max-h-[1100px] opacity-100"
                    leave-to-class="max-h-0 opacity-0 overflow-hidden"
                >
                    <div
                        v-if="expandedIndex === index"
                        class="border-t border-gray-200 bg-white px-6 py-6"
                    >
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Imagen del proyecto -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Imagen del proyecto
                                </label>
                                <div
                                    class="relative flex h-48 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 transition-colors hover:bg-gray-100"
                                >
                                    <div
                                        v-if="!project.image"
                                        class="flex flex-col items-center justify-center pb-6 pt-5"
                                    >
                                        <Upload class="mb-3 h-8 w-8 text-gray-400" />
                                        <p class="mb-2 text-sm text-gray-500">
                                            <span class="font-semibold">Haz clic para subir</span>
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF, WebP (MAX. 2MB)</p>
                                    </div>
                                    <img
                                        v-else
                                        :src="project.image"
                                        class="h-full w-full rounded-lg object-cover"
                                    />
                                    <input
                                        type="file"
                                        class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                                        accept="image/*"
                                        @change="handleImageUpload(index, $event)"
                                    />
                                    <button
                                        v-if="project.image"
                                        @click.stop="updateProject(index, 'image', null)"
                                        class="absolute right-2 top-2 rounded-full bg-white p-1 shadow-sm hover:bg-gray-100"
                                    >
                                        <Trash2 class="h-4 w-4 text-red-500" />
                                    </button>
                                </div>
                                <p
                                    v-if="projectErrors[index]?.image"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ projectErrors[index].image }}
                                </p>
                            </div>

                            <!-- Nombre del proyecto -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Nombre del proyecto *
                                </label>
                                <input
                                    :value="project.name"
                                    @input="
                                        updateProject(
                                            index,
                                            'name',
                                            ($event.target as HTMLInputElement).value
                                        )
                                    "
                                    type="text"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors focus:ring-2"
                                    :class="getErrorClass(index, 'name')"
                                    placeholder="Nombre del proyecto"
                                />
                                <p
                                    v-if="projectErrors[index]?.name"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ projectErrors[index].name }}
                                </p>
                            </div>

                            <!-- Enlace del proyecto -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Enlace (URL)
                                </label>
                                <input
                                    :value="project.link"
                                    @input="
                                        updateProject(
                                            index,
                                            'link',
                                            ($event.target as HTMLInputElement).value
                                        )
                                    "
                                    type="url"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors focus:ring-2"
                                    :class="getErrorClass(index, 'link')"
                                    placeholder="https://..."
                                />
                                <p
                                    v-if="projectErrors[index]?.link"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ projectErrors[index].link }}
                                </p>
                            </div>

                            <!-- Tecnologías -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Tecnologías *
                                </label>
                                <p class="mb-2 text-xs text-gray-500">
                                    Presiona coma o enter para agregar cada tecnología.
                                </p>
                                <div class="space-y-2">
                                    <input
                                        v-model="techInputs[index]"
                                        @keydown="handleTechKeydown(index, $event)"
                                        @blur="processTechnologies(index)"
                                        type="text"
                                        class="w-full rounded-lg border px-4 py-2 transition-colors focus:ring-2"
                                        :class="getErrorClass(index, 'technologies')"
                                        placeholder="Escribe tecnología y presiona coma o enter..."
                                    />

                                    <p
                                        v-if="projectErrors[index]?.technologies"
                                        class="text-sm text-red-500"
                                    >
                                        {{ projectErrors[index].technologies }}
                                    </p>

                                    <!-- Display de tags -->
                                    <div
                                        v-if="project.technologies.length"
                                        class="flex flex-wrap gap-2"
                                    >
                                        <span
                                            v-for="(tech, techIndex) in project.technologies"
                                            :key="techIndex"
                                            class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-700"
                                        >
                                            {{ tech }}
                                            <button
                                                @click="removeTechnology(index, techIndex)"
                                                type="button"
                                                class="transition-colors hover:text-blue-900"
                                            >
                                                ✕
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Descripción *
                                </label>
                                <p class="mb-2 text-xs text-gray-500">
                                    Describe el proyecto, sus objetivos y tu rol en él.
                                </p>
                                <textarea
                                    :value="project.description"
                                    @input="
                                        updateProject(
                                            index,
                                            'description',
                                            ($event.target as HTMLTextAreaElement).value
                                        )
                                    "
                                    rows="4"
                                    class="w-full rounded-lg border px-4 py-3 transition-colors focus:ring-2"
                                    :class="getErrorClass(index, 'description')"
                                    placeholder="Describe el proyecto..."
                                ></textarea>
                                <div class="mt-1 flex items-center justify-between">
                                    <span
                                        v-if="projectErrors[index]?.description"
                                        class="text-sm text-red-500"
                                    >
                                        {{ projectErrors[index].description }}
                                    </span>
                                    <span v-else class="text-sm text-transparent">placeholder</span>
                                    <span
                                        class="text-xs"
                                        :class="[
                                            getCharCount(project.description).isOverLimit
                                                ? 'font-medium text-red-500'
                                                : getCharCount(project.description).isNearLimit
                                                  ? 'text-amber-500'
                                                  : 'text-gray-400',
                                        ]"
                                    >
                                        {{ getCharCount(project.description).current }}/{{
                                            getCharCount(project.description).max
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Botón agregar proyecto -->
            <button
                @click="addProject"
                class="flex w-full items-center justify-center space-x-2 rounded-lg border-2 border-dashed border-gray-300 p-4 text-gray-600 transition-colors hover:border-[#005aeb] hover:text-[#005aeb]"
            >
                <Plus class="h-5 w-5" />
                <span>Agregar nuevo proyecto</span>
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
                        ¿Eliminar proyecto?
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
