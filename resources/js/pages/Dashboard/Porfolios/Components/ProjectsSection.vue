<script setup lang="ts">
import { Plus, Trash2, ChevronDown, AlertCircle, Sparkles, X, Star, Image as ImageIcon } from 'lucide-vue-next';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useProjectValidation, type Project } from './Composables/useProjectValidation';

const props = defineProps<{
    modelValue: Project[];
    validation: any;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: Project[]];
}>();

const {
    errors: projectErrors,
    validateField,
    validateImage,
    clearImageError,
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
const deleteConfirmButtonRef = ref<HTMLElement | null>(null);

// Variables para input de tecnologías
const techInput = ref<{ [key: number]: string }>({});

onMounted(() => {
    props.validation.clearAllErrors();
});

// Auto-expandir nuevo item
watch(
    () => props.modelValue.length,
    (newLength, oldLength) => {
        if (newLength > oldLength && lastAddedId.value !== null) {
            const project = props.modelValue.find((p) => p.id === lastAddedId.value);
            if (project) {
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

    const buttonCenterX = rect.left + rect.width / 2;
    let popoverLeftPosition = buttonCenterX - popoverWidth / 2;

    if (popoverLeftPosition + popoverWidth > window.innerWidth - windowPadding) {
        popoverLeftPosition = window.innerWidth - popoverWidth - windowPadding;
    }

    if (popoverLeftPosition < windowPadding) {
        popoverLeftPosition = windowPadding;
    }

    const spaceBelow = window.innerHeight - rect.bottom;
    const spaceAbove = rect.top;

    let popoverTopPosition: number;
    let positionY: 'below' | 'above';

    if (spaceBelow >= popoverHeight + spacing) {
        popoverTopPosition = rect.bottom + spacing;
        positionY = 'below';
    } else if (spaceAbove >= popoverHeight + spacing) {
        popoverTopPosition = rect.top - popoverHeight + 32;
        positionY = 'above';
    } else {
        if (spaceBelow > spaceAbove) {
            popoverTopPosition = rect.bottom + spacing;
            positionY = 'below';
        } else {
            popoverTopPosition = rect.top - popoverHeight - spacing;
            positionY = 'above';
        }
    }

    let arrowLeft = buttonCenterX - popoverLeftPosition - 8;
    arrowLeft = Math.max(8, Math.min(arrowLeft, popoverWidth - 16));

    popoverPosition.value = {
        top: popoverTopPosition,
        left: popoverLeftPosition,
        arrowLeft,
        arrowTop: 0,
        positionY,
    };
};

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
    const newProjectId = Date.now();
    lastAddedId.value = newProjectId;

    newItems.value.add(newProjectId);

    const newProject: Project = {
        id: newProjectId,
        name: '',
        description: '',
        image: null,
        link: '',
        technologies: [],
    };
    emit('update:modelValue', [...props.modelValue, newProject]);

    setTimeout(() => {
        newItems.value.delete(newProjectId);
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

    reindexErrors(index);

    if (expandedIndex.value === index) {
        expandedIndex.value = null;
    }
    closeDeleteConfirm();
};

// Toggle acordeón
const toggleProject = (id: number) => {
    expandedIndex.value = expandedIndex.value === id ? null : id;
    if (newItems.value.has(id)) {
        newItems.value.delete(id);
    }
};

// Actualizar proyecto + validar
const updateProject = (index: number, field: keyof Project, value: any) => {
    const id = props.modelValue[index].id;

    if (newItems.value.has(id)) {
        newItems.value.delete(id);
    }

    props.validation.markAsTouched(index, field);

    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], [field]: value };
    emit('update:modelValue', updated);

    if (projectErrors[index]?.[field]) {
        validateField(index, field, value, false);
    }
};

// Manejar upload de imagen principal
const handleImageUpload = (index: number, event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (!file) return;

    const id = props.modelValue[index].id;
    if (newItems.value.has(id)) {
        newItems.value.delete(id);
    }

    props.validation.markAsTouched(index, 'image');

    if (validateImage(index, file)) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const updated = [...props.modelValue];
            const newImage = e.target?.result as string;
            
            // Si no hay array images, inicializarlo con la nueva imagen
            let currentImages = updated[index].images || [];
            
            // Si la imagen no está en el array, agregarla
            if (!currentImages.includes(newImage)) {
                // Si la imagen anterior existe y no está en el array, añadirla primero
                if (updated[index].image && !currentImages.includes(updated[index].image)) {
                    currentImages = [updated[index].image, ...currentImages];
                }
                // Añadir la nueva al principio
                currentImages = [newImage, ...currentImages];
            }

            updated[index] = {
                ...updated[index],
                image: newImage,
                images: currentImages
            };
            emit('update:modelValue', updated);
        };
        reader.readAsDataURL(file);
    }
};

// Eliminar imagen principal
const removeImage = (index: number) => {
    clearImageError(index);
    const updated = [...props.modelValue];
    // No eliminamos del array images, solo quitamos la principal visualmente o seteamos otra?
    // Mejor lógica: set null la image principal.
    updated[index] = { ...updated[index], image: null };
    emit('update:modelValue', updated);
};

// Manejar upload de galería (múltiples imágenes)
const handleGalleryUpload = (index: number, event: Event) => {
    const input = event.target as HTMLInputElement;
    const files = input.files;

    if (!files || files.length === 0) return;

    const id = props.modelValue[index].id;
    if (newItems.value.has(id)) {
        newItems.value.delete(id);
    }

    // Validar y procesar cada archivo
    const updated = [...props.modelValue];
    let currentImages = updated[index].images ? [...updated[index].images!] : [];
    
    // Si hay una imagen principal y no está en el array, agregarla primero
    if (updated[index].image && !currentImages.includes(updated[index].image)) {
        currentImages.unshift(updated[index].image!);
    }

    let processedCount = 0;
    const totalFiles = files.length;

    Array.from(files).forEach(file => {
        if (validateImage(index, file)) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const result = e.target?.result as string;
                if (!currentImages.includes(result)) {
                    currentImages.push(result);
                }
                
                processedCount++;
                
                // Si es el último archivo procesado, actualizar estado
                if (processedCount === totalFiles) { // Simplificado: en prod se usaría Promise.all
                     // Si no había imagen principal, usar la primera de las nuevas
                    let mainImage = updated[index].image;
                    if (!mainImage && currentImages.length > 0) {
                        mainImage = currentImages[0];
                    }

                    updated[index] = {
                        ...updated[index],
                        images: currentImages,
                        image: mainImage
                    };
                    emit('update:modelValue', updated);
                }
            };
            reader.readAsDataURL(file);
        } else {
            processedCount++;
        }
    });
};

// Eliminar imagen de la galería
const removeGalleryImage = (index: number, imgIndex: number) => {
    const updated = [...props.modelValue];
    const project = updated[index];
    const images = project.images ? [...project.images] : [];
    const removedImage = images[imgIndex];
    
    // Eliminar del array
    images.splice(imgIndex, 1);
    
    // Si la imagen eliminada era la principal, asignar otra o null
    let mainImage = project.image;
    if (mainImage === removedImage) {
        mainImage = images.length > 0 ? images[0] : null;
    }

    updated[index] = {
        ...project,
        images: images,
        image: mainImage
    };
    emit('update:modelValue', updated);
};

// Establecer imagen principal desde la galería
const setMainImage = (index: number, imgUrl: string) => {
    const updated = [...props.modelValue];
    updated[index] = {
        ...updated[index],
        image: imgUrl
    };
    emit('update:modelValue', updated);
};

// Agregar tecnología
const addTechnology = (index: number, event: KeyboardEvent) => {
    const input = event.target as HTMLInputElement;
    const value = input.value.trim();
    const key = (event as any).key;

    if ((key === 'Enter' || key === ',') && value) {
        event.preventDefault();

        const id = props.modelValue[index].id;
        if (newItems.value.has(id)) {
            newItems.value.delete(id);
        }

        props.validation.markAsTouched(index, 'technologies');

        const updated = [...props.modelValue];
        const newTechs = [...updated[index].technologies];
        if (!newTechs.includes(value)) {
            newTechs.push(value);
        }
        updated[index] = { ...updated[index], technologies: newTechs };
        emit('update:modelValue', updated);

        techInput.value[index] = '';

        if (projectErrors[index]?.technologies) {
            validateField(index, 'technologies', newTechs, false);
        }
    }
};

// Eliminar tecnología
const removeTechnology = (index: number, tech: string) => {
    const updated = [...props.modelValue];
    updated[index] = {
        ...updated[index],
        technologies: updated[index].technologies.filter((t) => t !== tech),
    };
    emit('update:modelValue', updated);
};

// NUEVO: Función para obtener clase de error del contenedor
const getContainerClass = (index: number, id: number) => {
    if (hasProjectError(index)) {
        return 'border-red-300 bg-red-50';
    }
    if (newItems.value.has(id)) {
        return 'border-emerald-300 bg-emerald-50';
    }
    return 'border-gray-200 bg-white';
};

// NUEVO: Verificar si un proyecto tiene errores
const hasProjectError = (index: number) => {
    const errors = projectErrors[index];
    return errors && Object.keys(errors).length > 0;
};

// NUEVO: Función para obtener clase de error con forceShow
const getErrorClassForInput = (index: number, field: keyof any) => {
    return props.validation.getErrorClass(index, field, false);
};
</script>

<template>
    <div>
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
                class="overflow-hidden rounded-lg border transition-all duration-500 ease-in-out"
                :class="getContainerClass(index, project.id)"
            >
                <!-- Header del acordeón -->
                <div
                    @click="toggleProject(project.id)"
                    class="flex w-full items-center justify-between px-4 py-3 transition-colors hover:bg-gray-50/50 cursor-pointer"
                    role="button"
                    tabindex="0"
                >
                    <div class="flex flex-1 items-center gap-3 text-left">
                        <ChevronDown
                            class="h-5 w-5 flex-shrink-0 text-gray-600 transition-transform duration-400 ease-out"
                            :class="{ 'rotate-180 transform': expandedIndex === project.id }"
                        />

                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-base font-medium transition-colors duration-300"
                                    :class="{
                                        'text-red-700': hasProjectError(index),
                                        'text-emerald-700': newItems.has(project.id),
                                        'text-gray-900': !hasProjectError(index) && !newItems.has(project.id)
                                    }"
                                >
                                    {{ project.name || 'Nuevo proyecto' }}
                                </span>

                                <!-- ICONO DE ERROR -->
                                <AlertCircle
                                    v-if="hasProjectError(index)"
                                    class="h-4 w-4 text-red-500 animate-pulse"
                                />

                                <!-- ICONO DE NUEVO -->
                                <Sparkles
                                    v-if="newItems.has(project.id) && !hasProjectError(index)"
                                    class="h-4 w-4 text-emerald-500 animate-bounce"
                                />
                            </div>
                            <span v-if="project.technologies.length > 0" class="text-xs text-gray-500">
                                {{ project.technologies.slice(0, 2).join(', ') }}
                                <span v-if="project.technologies.length > 2">, +{{ project.technologies.length - 2 }}</span>
                            </span>
                        </div>
                    </div>

                    <button
                        @click.stop="openDeleteConfirm(index, $event)"
                        type="button"
                        class="flex-shrink-0 p-1.5 text-gray-400 transition-colors hover:text-red-500"
                    >
                        <Trash2 class="h-4 w-4" />
                    </button>
                </div>

                <!-- Contenido del acordeón -->
                <transition
                    enter-active-class="transition-all duration-300 ease-out"
                    leave-active-class="transition-all duration-300 ease-in"
                    enter-from-class="max-h-0 opacity-0 overflow-hidden"
                    enter-to-class="max-h-[1200px] opacity-100"
                    leave-from-class="max-h-[1200px] opacity-100"
                    leave-to-class="max-h-0 opacity-0 overflow-hidden"
                >
                    <div
                        v-if="expandedIndex === project.id"
                        class="border-t px-4 py-4 bg-white/50"
                        :class="hasProjectError(index) ? 'border-red-200' : 'border-gray-200'"
                    >
                        <div class="space-y-6">
                            <!-- Imagen -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Imagen del proyecto</label>
                                <div
                                    v-if="!project.image"
                                    class="relative rounded-lg border-2 border-dashed border-gray-300 p-6 text-center"
                                >
                                    <input
                                        type="file"
                                        accept="image/png,image/jpeg,image/gif,image/webp"
                                        @change="handleImageUpload(index, $event)"
                                        class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                                    />
                                    <div class="pointer-events-none">
                                        <p class="text-sm font-medium text-gray-700">Haz clic para subir</p>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF, WebP (MAX. 2MB)</p>
                                    </div>
                                </div>
                                <div v-else class="relative">
                                    <img
                                        :src="project.image"
                                        alt="Project preview"
                                        class="w-full rounded-lg object-cover max-h-48"
                                    />
                                    <button
                                        @click="removeImage(index)"
                                        type="button"
                                        class="absolute right-2 top-2 rounded-full bg-red-500 p-2 text-white transition-colors hover:bg-red-600"
                                    >
                                        <X class="h-4 w-4" />
                                    </button>
                                </div>
                                <p v-if="projectErrors[index]?.image" class="mt-1 text-sm text-red-500">
                                    {{ projectErrors[index].image }}
                                </p>
                            </div>

                            <!-- Galería de imágenes -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Galería de imágenes</label>
                                <p class="mb-3 text-xs text-gray-500">
                                    Sube múltiples imágenes para crear una galería. Puedes elegir cuál será la portada haciendo clic en la estrella.
                                </p>
                                
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-5">
                                    <!-- Botón Upload -->
                                    <div class="relative flex aspect-square cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 text-gray-400 transition-colors hover:border-[#005aeb] hover:bg-[#005aeb]/5 hover:text-[#005aeb]">
                                         <input
                                            type="file"
                                            multiple
                                            accept="image/png,image/jpeg,image/gif,image/webp"
                                            @change="handleGalleryUpload(index, $event)"
                                            class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0"
                                        />
                                        <ImageIcon class="mb-1 h-6 w-6" />
                                        <span class="text-xs font-medium">Agregar</span>
                                    </div>

                                    <!-- Miniaturas -->
                                    <div 
                                         v-for="(img, imgIdx) in (project.images || [])" 
                                         :key="imgIdx"
                                         class="group relative aspect-square overflow-hidden rounded-lg border border-gray-200 bg-gray-100"
                                    >
                                        <img :src="img" class="h-full w-full object-cover" />
                                        
                                        <!-- Overlay Acciones -->
                                        <div class="absolute inset-0 flex items-center justify-center gap-2 bg-black/50 opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                                            <!-- Hacer Principal -->
                                            <button
                                                @click="setMainImage(index, img)"
                                                type="button"
                                                class="rounded-full bg-white p-1.5 text-yellow-500 hover:bg-yellow-50 shadow-sm transition-transform hover:scale-110"
                                                :title="project.image === img ? 'Es la portada actual' : 'Usar como portada'"
                                            >
                                                <Star class="h-3.5 w-3.5" :fill="project.image === img ? 'currentColor' : 'none'" />
                                            </button>
                                            
                                            <!-- Eliminar -->
                                            <button
                                                @click="removeGalleryImage(index, imgIdx)"
                                                type="button"
                                                class="rounded-full bg-white p-1.5 text-red-500 hover:bg-red-50 shadow-sm transition-transform hover:scale-110"
                                                title="Eliminar imagen"
                                            >
                                                <X class="h-3.5 w-3.5" />
                                            </button>
                                        </div>

                                         <!-- Badge Portada -->
                                        <div v-if="project.image === img" class="absolute left-1 top-1 rounded bg-yellow-400 px-1.5 py-0.5 text-[0.6rem] font-bold text-yellow-900 shadow-sm z-10 pointer-events-none">
                                            PORTADA
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Nombre -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Nombre del proyecto *</label>
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
                                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-sm text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                    :class="getErrorClassForInput(index, 'name')"
                                    placeholder="Nombre del proyecto"
                                />
                                <p v-if="projectErrors[index]?.name" class="mt-1 text-xs text-red-500">
                                    {{ projectErrors[index].name }}
                                </p>
                            </div>

                            <!-- Link -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Enlace (opcional)</label>
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
                                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-sm text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                    :class="getErrorClassForInput(index, 'link')"
                                    placeholder="https://ejemplo.com"
                                />
                                <p v-if="projectErrors[index]?.link" class="mt-1 text-xs text-red-500">
                                    {{ projectErrors[index].link }}
                                </p>
                            </div>

                            <!-- Tecnologías -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Tecnologías *</label>
                                <p class="mb-2 text-xs text-gray-500">Presiona coma o enter para agregar cada tecnología.</p>
                                <input
                                    :value="techInput[index] || ''"
                                    @input="techInput[index] = ($event.target as HTMLInputElement).value"
                                    @keydown="addTechnology(index, $event)"
                                    type="text"
                                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-sm text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                    :class="getErrorClassForInput(index, 'technologies')"
                                    placeholder="Vue.js, Tailwind CSS..."
                                />
                                <p v-if="projectErrors[index]?.technologies" class="mt-1 text-xs text-red-500">
                                    {{ projectErrors[index].technologies }}
                                </p>
                                <div v-if="project.technologies.length > 0" class="mt-3 flex flex-wrap gap-2">
                                    <span
                                        v-for="tech in project.technologies"
                                        :key="tech"
                                        class="inline-flex items-center gap-2 rounded-full bg-[#005aeb]/10 px-3 py-1 text-xs font-medium text-[#005aeb]"
                                    >
                                        {{ tech }}
                                        <button
                                            @click="removeTechnology(index, tech)"
                                            type="button"
                                            class="text-[#005aeb] hover:text-[#0047b2]"
                                        >
                                            <X class="h-3 w-3" />
                                        </button>
                                    </span>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Descripción *</label>
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
                                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-sm text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                    :class="getErrorClassForInput(index, 'description')"
                                    placeholder="Describe brevemente el proyecto..."
                                ></textarea>
                                <div class="mt-1 flex items-center justify-between">
                                    <span
                                        v-if="projectErrors[index]?.description"
                                        class="text-xs text-red-500"
                                    >
                                        {{ projectErrors[index].description }}
                                    </span>
                                    <span v-else class="text-xs text-transparent">placeholder</span>
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
