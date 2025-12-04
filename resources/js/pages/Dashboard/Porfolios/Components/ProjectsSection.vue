<script setup lang="ts">
import { Plus, Trash2, Upload, ChevronDown } from 'lucide-vue-next';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';


interface Project {
    id: number;
    name: string;
    description: string;
    image: string | null;
    link: string;
    technologies: string[];
}


interface ProjectErrors {
    name?: string;
    description?: string;
    link?: string;
    technologies?: string;
}


const props = defineProps<{
    modelValue: Project[];
}>();


const emit = defineEmits(['update:modelValue']);


// Input local para cada proyecto
const techInputs = ref<{ [key: number]: string }>({});
// Errores por proyecto
const projectErrors = ref<{ [key: number]: ProjectErrors }>({});
// Índice del proyecto expandido (solo uno)
const expandedIndex = ref<number | null>(null);
// Estado del popover
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
        // Expandir solo el último si hay proyectos
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


// Recalcular posición del popover (para scroll y al abrir)
const recalculatePopoverPosition = () => {
  if (!deleteConfirmButtonRef.value) return;

  const button = deleteConfirmButtonRef.value;
  const rect = button.getBoundingClientRect(); // respecto a la ventana

  const popoverWidth = 224; // w-56 = 224px
  const popoverHeight = 220; // altura aproximada del popover
  const spacing = 8;
  const windowPadding = 16; // Padding mínimo desde los bordes de la ventana

  // ===== POSICIÓN HORIZONTAL (respecto a la ventana) =====
  const buttonCenterX = rect.left + rect.width / 2;
  let popoverLeftPosition = buttonCenterX - popoverWidth / 2;

  // Verificar si se sale de la ventana por la derecha
  if (popoverLeftPosition + popoverWidth > window.innerWidth - windowPadding) {
    popoverLeftPosition = window.innerWidth - popoverWidth - windowPadding;
  }

  // Verificar si se sale de la ventana por la izquierda
  if (popoverLeftPosition < windowPadding) {
    popoverLeftPosition = windowPadding;
  }

  // ===== POSICIÓN VERTICAL (respecto a la ventana) =====
  const spaceBelow = window.innerHeight - rect.bottom;
  const spaceAbove = rect.top;

  let popoverTopPosition: number;
  let positionY: 'below' | 'above';
  let arrowTop: number;

  if (spaceBelow >= popoverHeight + spacing) {
    // Hay espacio debajo
    popoverTopPosition = rect.bottom + spacing;
    positionY = 'below';
    arrowTop = -8;
  } else if (spaceAbove >= popoverHeight + spacing) {
    // Hay espacio arriba
    popoverTopPosition = rect.top - popoverHeight + 32;
    positionY = 'above';
    arrowTop = popoverHeight - 4;
  } else {
    // No hay espacio, poner donde hay más espacio
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

  // ===== CALCULAR POSICIÓN DE LA FLECHA =====
  // La flecha debe apuntar al botón, considerando la posición del popover en la ventana
  let arrowLeft = buttonCenterX - popoverLeftPosition - 8;

  // Asegurar que la flecha no se salga del popover
  arrowLeft = Math.max(8, Math.min(arrowLeft, popoverWidth - 16));

  popoverPosition.value = {
    top: popoverTopPosition,
    left: popoverLeftPosition,
    arrowLeft,
    arrowTop,
    positionY,
  };
};


// Listener para scroll - recalcular posición del popover
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


const toggleProject = (index: number) => {
    // Si ya está expandido, colapsarlo; si no, expandirlo
    expandedIndex.value = expandedIndex.value === index ? null : index;
};


const removeProject = (index: number) => {
    const updated = [...props.modelValue];
    updated.splice(index, 1);
    emit('update:modelValue', updated);
    delete techInputs.value[index];
    delete projectErrors.value[index];
    // Si se elimina el expandido, colapsar
    if (expandedIndex.value === index) {
        expandedIndex.value = null;
    }
    closeDeleteConfirm();
};


const updateProject = (index: number, field: keyof Project, value: any) => {
    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], [field]: value };
    emit('update:modelValue', updated);
    validateField(index, field);
};


const handleImageUpload = (index: number, event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        if (input.files[0].size > 2 * 1024 * 1024) {
            if (!projectErrors.value[index]) projectErrors.value[index] = {};
            projectErrors.value[index].image = 'La imagen no debe superar 2MB';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            updateProject(index, 'image', e.target?.result as string);
            if (!projectErrors.value[index]) projectErrors.value[index] = {};
            delete projectErrors.value[index].image;
        };
        reader.readAsDataURL(input.files[0]);
    }
};


const validateField = (index: number, field: keyof Project) => {
    if (!projectErrors.value[index]) projectErrors.value[index] = {};

    const project = props.modelValue[index];

    switch (field) {
        case 'name':
            if (!project.name.trim()) {
                projectErrors.value[index].name = 'El nombre es requerido';
            } else if (project.name.trim().length < 3) {
                projectErrors.value[index].name = 'Mínimo 3 caracteres';
            } else {
                delete projectErrors.value[index].name;
            }
            break;

        case 'description':
            if (!project.description.trim()) {
                projectErrors.value[index].description = 'La descripción es requerida';
            } else if (project.description.trim().length < 10) {
                projectErrors.value[index].description = 'Mínimo 10 caracteres';
            } else if (project.description.length > 500) {
                projectErrors.value[index].description = 'Máximo 500 caracteres';
            } else {
                delete projectErrors.value[index].description;
            }
            break;

        case 'link':
            if (project.link) {
                const urlRegex = /^https?:\/\/.+\..+/;
                if (!urlRegex.test(project.link)) {
                    projectErrors.value[index].link = 'URL inválida (ej: https://ejemplo.com)';
                } else {
                    delete projectErrors.value[index].link;
                }
            } else {
                delete projectErrors.value[index].link;
            }
            break;

        case 'technologies':
            if (project.technologies.length === 0) {
                projectErrors.value[index].technologies = 'Agrega al menos una tecnología';
            } else {
                delete projectErrors.value[index].technologies;
            }
            break;
    }
};


const validateProject = (index: number): boolean => {
    const project = props.modelValue[index];
    projectErrors.value[index] = {};

    if (!project.name.trim()) {
        projectErrors.value[index].name = 'El nombre es requerido';
    } else if (project.name.trim().length < 3) {
        projectErrors.value[index].name = 'Mínimo 3 caracteres';
    }

    if (!project.description.trim()) {
        projectErrors.value[index].description = 'La descripción es requerida';
    } else if (project.description.trim().length < 10) {
        projectErrors.value[index].description = 'Mínimo 10 caracteres';
    } else if (project.description.length > 500) {
        projectErrors.value[index].description = 'Máximo 500 caracteres';
    }

    if (project.link) {
        const urlRegex = /^https?:\/\/.+\..+/;
        if (!urlRegex.test(project.link)) {
            projectErrors.value[index].link = 'URL inválida (ej: https://ejemplo.com)';
        }
    }

    if (project.technologies.length === 0) {
        projectErrors.value[index].technologies = 'Agrega al menos una tecnología';
    }

    return Object.keys(projectErrors.value[index]).length === 0;
};


const processTechnologies = (index: number) => {
    const input = techInputs.value[index] || '';
    if (!input.trim()) return;

    const newTechs = input
        .split(',')
        .map(tech => tech.trim())
        .filter(tech => tech !== '');
    
    const allTechs = [...props.modelValue[index].technologies, ...newTechs];
    updateProject(index, 'technologies', allTechs);
    techInputs.value[index] = '';
};


const handleTechKeydown = (index: number, event: KeyboardEvent) => {
    if (event.key === ',' || event.key === 'Enter') {
        event.preventDefault();
        processTechnologies(index);
    }
};


const removeTechnology = (index: number, techIndex: number) => {
    const updated = [...props.modelValue[index].technologies];
    updated.splice(techIndex, 1);
    updateProject(index, 'technologies', updated);
};


const getErrorClass = (index: number, field: keyof ProjectErrors) => {
    return projectErrors.value[index]?.[field] ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-[#005aeb]';
};


// Abrir popover de confirmación con posicionamiento respecto a la ventana
const openDeleteConfirm = (index: number, event: Event) => {
  const button = event.currentTarget as HTMLElement;

  // Si se vuelve a hacer click sobre el mismo índice, cerrar
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
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">
                Proyectos
            </h1>
            <p class="text-lg text-gray-600">
                Muestra tus mejores trabajos y proyectos realizados.
            </p>
        </div>


        <div class="space-y-3">
            <!-- Accordion Items -->
            <div
                v-for="(project, index) in modelValue"
                :key="project.id"
                class="rounded-lg border border-gray-200 overflow-hidden transition-all"
            >
                <!-- Accordion Header -->
                <button
                    @click="toggleProject(index)"
                    class="w-full flex items-center justify-between px-6 py-4 bg-gray-50 hover:bg-gray-100 transition-colors"
                >
                    <div class="flex items-center gap-3 flex-1 text-left">
                        <ChevronDown
                            class="h-5 w-5 text-gray-600 transition-transform"
                            :class="{ 'transform rotate-180': expandedIndex === index }"
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
                    enter-to-class="max-h-[900px] opacity-100"
                    leave-from-class="max-h-[900px] opacity-100"
                    leave-to-class="max-h-0 opacity-0"
                >
                    <div v-if="expandedIndex === index" class="px-6 py-6 bg-white border-t border-gray-200">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Imagen del proyecto -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Imagen del proyecto
                                </label>
                                <div
                                    class="relative flex h-48 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 transition-colors hover:bg-gray-100"
                                >
                                    <div v-if="!project.image" class="flex flex-col items-center justify-center pb-6 pt-5">
                                        <Upload class="mb-3 h-8 w-8 text-gray-400" />
                                        <p class="mb-2 text-sm text-gray-500">
                                            <span class="font-semibold">Haz clic para subir</span>
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG (MAX. 2MB)</p>
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
                                <p v-if="projectErrors[index]?.image" class="mt-1 text-sm text-red-500">
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
                                    @input="updateProject(index, 'name', ($event.target as HTMLInputElement).value)"
                                    @blur="validateField(index, 'name')"
                                    type="text"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors"
                                    :class="getErrorClass(index, 'name')"
                                    placeholder="Nombre del proyecto"
                                />
                                <p v-if="projectErrors[index]?.name" class="mt-1 text-sm text-red-500">
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
                                    @input="updateProject(index, 'link', ($event.target as HTMLInputElement).value)"
                                    @blur="validateField(index, 'link')"
                                    type="url"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors"
                                    :class="getErrorClass(index, 'link')"
                                    placeholder="https://..."
                                />
                                <p v-if="projectErrors[index]?.link" class="mt-1 text-sm text-red-500">
                                    {{ projectErrors[index].link }}
                                </p>
                            </div>


                            <!-- Tecnologías -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Tecnologías (presiona coma o enter para agregar) *
                                </label>
                                <div class="space-y-2">
                                    <input
                                        v-model="techInputs[index]"
                                        @keydown="handleTechKeydown(index, $event)"
                                        @blur="processTechnologies(index)"
                                        type="text"
                                        class="w-full rounded-lg border px-4 py-2 transition-colors"
                                        :class="getErrorClass(index, 'technologies')"
                                        placeholder="Escribe tecnología y presiona coma o enter..."
                                    />

                                    <p v-if="projectErrors[index]?.technologies" class="text-sm text-red-500">
                                        {{ projectErrors[index].technologies }}
                                    </p>

                                    <!-- Display de tags -->
                                    <div v-if="project.technologies.length" class="flex flex-wrap gap-2">
                                        <span
                                            v-for="(tech, techIndex) in project.technologies"
                                            :key="techIndex"
                                            class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-700"
                                        >
                                            {{ tech }}
                                            <button
                                                @click="removeTechnology(index, techIndex)"
                                                type="button"
                                                class="hover:text-blue-900"
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
                                    <span class="text-xs text-gray-500">
                                        ({{ project.description?.length ?? 0 }}/500)
                                    </span>
                                </label>
                                <textarea
                                    :value="project.description"
                                    @input="updateProject(index, 'description', ($event.target as HTMLInputElement).value)"
                                    @blur="validateField(index, 'description')"
                                    rows="3"
                                    class="w-full rounded-lg border px-4 py-2 transition-colors"
                                    :class="getErrorClass(index, 'description')"
                                    placeholder="Describe el proyecto..."
                                ></textarea>
                                <p v-if="projectErrors[index]?.description" class="mt-1 text-sm text-red-500">
                                    {{ projectErrors[index].description }}
                                </p>
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


        <!-- Backdrop para cerrar popover (bloquea interacción) -->
        <div
            v-if="deleteConfirmIndex !== null"
            @click="closeDeleteConfirm"
            class="fixed inset-0 z-40"
        ></div>


        <!-- Popover de confirmación (fixed respecto a la ventana) -->
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
                class="fixed w-56 bg-white rounded-lg shadow-2xl border border-gray-200 z-50 p-4 pointer-events-auto"
                :style="{
                    top: popoverPosition.top + 'px',
                    left: popoverPosition.left + 'px',
                }"
            >
                <!-- Flecha apuntando al botón (arriba o abajo según posición) -->
                <div
                    v-if="popoverPosition.positionY === 'below'"
                    class="absolute -top-2 w-4 h-4 bg-white border-t border-l border-gray-200 rotate-45"
                    :style="{ left: popoverPosition.arrowLeft + 'px' }"
                ></div>

                <div
                    v-if="popoverPosition.positionY === 'above'"
                    class="absolute -bottom-2 w-4 h-4 bg-white border-b border-r border-gray-200 rotate-45"
                    :style="{ left: popoverPosition.arrowLeft + 'px' }"
                ></div>

                <div class="relative">
                    <div
                        class="flex items-center justify-center w-10 h-10 mx-auto bg-red-100 rounded-full mb-3"
                    >
                        <Trash2 class="h-5 w-5 text-red-600" />
                    </div>

                    <h4 class="text-sm font-semibold text-gray-900 text-center mb-1">
                        ¿Eliminar proyecto?
                    </h4>

                    <p class="text-xs text-gray-600 text-center mb-4">
                        Esta acción no se puede deshacer
                    </p>

                    <div class="flex gap-2">
                        <button
                            @click.stop="closeDeleteConfirm"
                            class="flex-1 px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            @click.stop="confirmDelete(deleteConfirmIndex)"
                            class="flex-1 px-3 py-2 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>