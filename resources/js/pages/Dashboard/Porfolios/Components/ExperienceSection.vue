<script setup lang="ts">
import { Plus, Trash2, ChevronDown, AlertCircle, Sparkles } from 'lucide-vue-next';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useExperienceValidation, type Experience } from './Composables/useExperienceValidation';
import { getDetailedDescriptionSuggestions } from './Composables/experienceSuggestions';


const props = defineProps<{
    modelValue: Experience[];
    validation: any;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: Experience[]];
}>();

const {
    errors: experienceErrors,
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
            const experience = props.modelValue.find((e) => e.id === lastAddedId.value);
            if (experience) {
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

// Agregar experiencia
const addExperience = () => {
    const newExperienceId = Date.now();
    lastAddedId.value = newExperienceId;

    // 1. Lo marcamos como nuevo
    newItems.value.add(newExperienceId);

    const newExperience: Experience = {
        id: newExperienceId,
        company: '',
        position: '',
        period: '',
        description: '',
        startDate: '',
        endDate: '',
        current: false,
    };
    emit('update:modelValue', [...props.modelValue, newExperience]);

    // Opcional: Quitar el estado "nuevo" automáticamente después de 3 segundos
    setTimeout(() => {
        newItems.value.delete(newExperienceId);
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
    newItems.value.delete(idToRemove); // Limpiamos del set

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
const toggleExperience = (id: number) => {
    expandedIndex.value = expandedIndex.value === id ? null : id;
    // Opcional: Si lo cierras, también podrías quitar el estado "nuevo"
    if (newItems.value.has(id)) {
        newItems.value.delete(id);
    }
};

// Actualizar campo + validar
const updateExperience = (index: number, field: keyof Experience, value: any) => {
    const id = props.modelValue[index].id;

    // 2. Si el usuario edita, ya no es "nuevo"
    if (newItems.value.has(id)) {
        newItems.value.delete(id);
    }

    // MODIFICADO: Marcar campo como tocado al escribir
    props.validation.markAsTouched(index, field);

    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], [field]: value };
    emit('update:modelValue', updated);

    // MODIFICADO: Validar en tiempo real (forceShow = false)
    if (experienceErrors[index]?.[field]) {
        validateField(index, field, value, updated[index], false);
    }
};

// Checkbox "Trabajo actual"
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

// NUEVO: Función para obtener clase de error del contenedor (igual a LanguageSection)
const getContainerClass = (index: number, id: number) => {
    if (hasExperienceError(index)) {
        return 'border-red-300 bg-red-50';
    }
    if (newItems.value.has(id)) {
        return 'border-emerald-300 bg-emerald-50';
    }
    return 'border-gray-200 bg-white';
};

// NUEVO: Verificar si una experiencia tiene errores
const hasExperienceError = (index: number) => {
    const errors = experienceErrors[index];
    return errors && Object.keys(errors).length > 0;
};

// NUEVO: Función para obtener clase de error con forceShow
const getErrorClassForInput = (index: number, field: keyof Experience) => {
    return props.validation.getErrorClass(index, field, false);
};



// ==========================================
// LÓGICA DE AYUDA PARA LA DESCRIPCIÓN
// ==========================================

// 1. Calcular el color del badge de calidad según la longitud
const getQualityBadge = (text: string | undefined) => {
    const length = (text || '').trim().length;
    if (length === 0) return 'hidden'; // No mostrar si está vacío
    if (length < 30) return 'bg-gray-100 text-gray-600 border-gray-200';
    if (length < 80) return 'bg-yellow-50 text-yellow-700 border-yellow-200';
    if (length < 150) return 'bg-blue-50 text-blue-700 border-blue-200';
    return 'bg-emerald-50 text-emerald-700 border-emerald-200';
};

// 2. Calcular el texto del badge de calidad
const getQualityText = (text: string | undefined) => {
    const length = (text || '').trim().length;
    if (length === 0) return '';
    if (length < 30) return 'Muy corta';
    if (length < 80) return 'Podría expandirse';
    if (length < 150) return 'Buena descripción';
    return 'Excelente detalle';
};


// 4. Generar sugerencias de autocompletado basadas en el cargo (VERSIÓN EXPANDIDA)
const getDescriptionSuggestions = (experience: Experience) => {
    return getDetailedDescriptionSuggestions(experience);
};

// 5. Función para aplicar una sugerencia
const applySuggestion = (index: number, text: string) => {
    const current = props.modelValue[index].description;
    const combined = current ? current + ' ' + text : text;
    updateExperience(index, 'description', combined);
};

// 6. Mostrar sugerencias solo si está vacío o casi vacío
const shouldShowSuggestions = (description: string, position: string) => {
    return (!description || description.trim().length < 15) && position.trim().length > 3;
};



// ==========================================
// ESTADO PARA PANEL DE SUGERENCIAS
// ==========================================

// Track si el panel de sugerencias está expandido por index
const expandedSuggestions = ref<{ [key: number]: boolean }>({});

// Track la última sugerencia aplicada (solo para mostrar label visual)
const lastAppliedSuggestion = ref<{ [key: number]: string }>({});


// ==========================================
// FUNCIONES PARA MANEJAR SUGERENCIAS
// ==========================================

// Toggle el panel de sugerencias
const toggleSuggestionsPanel = (index: number) => {
    expandedSuggestions.value[index] = !expandedSuggestions.value[index];
};

// Cerrar panel de sugerencias
const closeSuggestionsPanel = (index: number) => {
    expandedSuggestions.value[index] = false;
};

// Aplicar sugerencia CON tracking del label (visual only)
const applySuggestionWithLabel = (index: number, text: string, label: string) => {
    const current = props.modelValue[index].description;
    const combined = current ? current + ' ' + text : text;
    
    // Actualizar descripción
    updateExperience(index, 'description', combined);
    
    // Guardar el label (solo visual, NO se envía en form)
    lastAppliedSuggestion.value[index] = label;
    
    // Cerrar panel después de seleccionar
    setTimeout(() => {
        closeSuggestionsPanel(index);
    }, 300);
};

// Limpiar el label visual
const clearLastSuggestion = (index: number) => {
    delete lastAppliedSuggestion.value[index];
};

// Limpiar label cuando el usuario borra la descripción
watch(
    () => props.modelValue.map((e, i) => e.description),
    (descriptions) => {
        descriptions.forEach((desc, i) => {
            if (!desc || desc.trim().length === 0) {
                delete lastAppliedSuggestion.value[i];
            }
        });
    },
    { deep: true }
);
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
            <div v-for="(exp, index) in modelValue" :key="exp.id"
                class="overflow-hidden rounded-lg border transition-all duration-500 ease-in-out"
                :class="getContainerClass(index, exp.id)">
                <!-- Header del acordeón -->
                <div @click="toggleExperience(exp.id)"
                    class="flex w-full items-center justify-between px-4 py-3 transition-colors hover:bg-gray-50/50 cursor-pointer"
                    role="button"
                    tabindex="0">
                    <div class="flex flex-1 items-center gap-3 text-left">
                        <ChevronDown
                            class="h-5 w-5 flex-shrink-0 text-gray-600 transition-transform duration-400 ease-out"
                            :class="{ 'rotate-180 transform': expandedIndex === exp.id }" />

                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="text-base font-medium transition-colors duration-300" :class="{
                                    'text-red-700': hasExperienceError(index),
                                    'text-emerald-700': newItems.has(exp.id),
                                    'text-gray-900': !hasExperienceError(index) && !newItems.has(exp.id)
                                }">
                                    {{ exp.position || exp.company || 'Nueva experiencia' }}
                                </span>

                                <!-- ICONO DE ERROR -->
                                <AlertCircle v-if="hasExperienceError(index)"
                                    class="h-4 w-4 text-red-500 animate-pulse" />

                                <!-- ICONO DE NUEVO -->
                                <Sparkles v-if="newItems.has(exp.id) && !hasExperienceError(index)"
                                    class="h-4 w-4 text-emerald-500 animate-bounce" />
                            </div>
                            <span v-if="exp.company" class="text-sm text-gray-500">{{ exp.company }}</span>
                        </div>
                    </div>

                    <button @click.stop="openDeleteConfirm(index, $event)" type="button"
                        class="flex-shrink-0 p-1.5 text-gray-400 transition-colors hover:text-red-500">
                        <Trash2 class="h-4 w-4" />
                    </button>
                </div>

                <!-- Contenido del acordeón -->
                <transition enter-active-class="transition-all duration-300 ease-out"
                    leave-active-class="transition-all duration-300 ease-in"
                    enter-from-class="max-h-0 opacity-0 overflow-hidden" enter-to-class="max-h-[900px] opacity-100"
                    leave-from-class="max-h-[900px] opacity-100" leave-to-class="max-h-0 opacity-0 overflow-hidden">
                    <div v-if="expandedIndex === exp.id" class="border-t px-4 py-4 bg-white/50"
                        :class="hasExperienceError(index) ? 'border-red-200' : 'border-gray-200'">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Empresa -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Empresa *
                                </label>
                                <input :value="exp.company" @input="
                                    updateExperience(
                                        index,
                                        'company',
                                        ($event.target as HTMLInputElement).value
                                    )
                                    " type="text"
                                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                    :class="getErrorClassForInput(index, 'company')"
                                    placeholder="Nombre de la empresa" />
                                <p v-if="experienceErrors[index]?.company" class="mt-1 text-sm text-red-500">
                                    {{ experienceErrors[index].company }}
                                </p>
                            </div>

                            <!-- Cargo -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Cargo *
                                </label>
                                <input :value="exp.position" @input="
                                    updateExperience(
                                        index,
                                        'position',
                                        ($event.target as HTMLInputElement).value
                                    )
                                    " type="text"
                                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                    :class="getErrorClassForInput(index, 'position')" placeholder="Tu posición" />
                                <p v-if="experienceErrors[index]?.position" class="mt-1 text-sm text-red-500">
                                    {{ experienceErrors[index].position }}
                                </p>
                            </div>

                            <!-- Fecha de inicio -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Fecha de inicio *
                                </label>
                                <input :value="exp.startDate" @input="
                                    updateExperience(
                                        index,
                                        'startDate',
                                        ($event.target as HTMLInputElement).value
                                    )
                                    " type="month"
                                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                    :class="getErrorClassForInput(index, 'startDate')" />
                                <p v-if="experienceErrors[index]?.startDate" class="mt-1 text-sm text-red-500">
                                    {{ experienceErrors[index].startDate }}
                                </p>
                            </div>

                            <!-- Fecha de fin -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Fecha de fin
                                </label>
                                <input :value="exp.endDate" @input="
                                    updateExperience(
                                        index,
                                        'endDate',
                                        ($event.target as HTMLInputElement).value
                                    )
                                    " type="month" :disabled="exp.current"
                                    class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20 disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="getErrorClassForInput(index, 'endDate')" />
                                <p v-if="experienceErrors[index]?.endDate" class="mt-1 text-sm text-red-500">
                                    {{ experienceErrors[index].endDate }}
                                </p>
                            </div>

                            <!-- Checkbox trabajo actual -->
                            <div class="md:col-span-2">
                                <label class="flex cursor-pointer items-center space-x-3">
                                    <input :checked="exp.current" @change="
                                        handleCurrentChange(
                                            index,
                                            ($event.target as HTMLInputElement).checked
                                        )
                                        " type="checkbox"
                                        class="h-4 w-4 cursor-pointer rounded border-gray-300 text-[#005aeb] focus:ring-[#005aeb]" />
                                    <span class="text-sm font-medium text-gray-700">
                                        Trabajo actual
                                    </span>
                                </label>
                            </div>

                            <!-- Descripción -->

                        <!-- Descripción MEJORADA con Autocompletado y Botón -->
<div class="md:col-span-2">
    <div class="flex items-center justify-between mb-2">
        <label class="block text-sm font-medium text-gray-700">
            Descripción *
        </label>
        
        <!-- Badge de calidad -->
        <span 
            v-if="exp.description"
            class="text-xs px-2 py-0.5 rounded-full border font-medium transition-all duration-300"
            :class="getQualityBadge(exp.description)"
        >
            {{ getQualityText(exp.description) }}
        </span>
    </div>

    <!-- Ayuda contextual pequeña -->
    <p class="mb-3 text-xs text-gray-500">
        Describe responsabilidades, logros, tecnologías y métricas.
    </p>

    <!-- Container del textarea con botón -->
    <div class="relative">
        <textarea 
            :value="exp.description" 
            @input="updateExperience(index, 'description', ($event.target as HTMLTextAreaElement).value)"
            placeholder="Describe tus responsabilidades, logros principales..." 
            rows="8"
            class="w-full rounded-lg border bg-gray-50 px-3 py-2 pr-10 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
            :class="getErrorClassForInput(index, 'description')"
        ></textarea>

        <!-- Botón flotante de autocompletar (Sparkles) -->
        <button
            v-if="exp.position.trim().length > 0"
            @click="toggleSuggestionsPanel(index)"
            type="button"
            class="absolute bottom-3 right-3 p-2 rounded-lg bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 transition-all text-blue-600"
            title="Abrir sugerencias de autocompletado"
        >
            <Sparkles class="h-4 w-4" />
        </button>
    </div>

    <!-- Etiqueta del último suggestion aplicado (Visual indicator, NO se envía) -->
    <div 
        v-if="lastAppliedSuggestion[index]" 
        class="mt-2 inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-xs text-emerald-700"
    >
        <span class="font-medium">✓</span>
        <span>{{ lastAppliedSuggestion[index] }}</span>
        <button
            @click="clearLastSuggestion(index)"
            type="button"
            class="ml-1 hover:text-emerald-900"
        >
            ✕
        </button>
    </div>

    <!-- Error o contador -->
    <div class="mt-2 flex items-center justify-between">
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
            {{ getCharCount(exp.description).current }}/{{ getCharCount(exp.description).max }}
        </span>
    </div>

    <!-- Panel de Sugerencias (Dropdown estilo modal) -->
    <transition
        enter-active-class="transition-all duration-200 ease-out"
        leave-active-class="transition-all duration-200 ease-in"
        enter-from-class="opacity-0 scale-95 max-h-0"
        enter-to-class="opacity-100 scale-100 max-h-96"
        leave-from-class="opacity-100 scale-100 max-h-96"
        leave-to-class="opacity-0 scale-95 max-h-0"
    >
        <div 
            v-if="expandedSuggestions[index]"
            class="mt-3 space-y-3 p-3 rounded-lg bg-gradient-to-b from-blue-50 to-blue-25 border border-blue-200 shadow-lg overflow-hidden"
        >
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-700 flex items-center gap-1">
                    <Sparkles class="h-3.5 w-3.5 text-blue-500" />
                    Sugerencias inteligentes
                </p>
                <button
                    @click="toggleSuggestionsPanel(index)"
                    type="button"
                    class="text-gray-400 hover:text-gray-600 text-lg leading-none"
                >
                    ✕
                </button>
            </div>

            <!-- Grid de sugerencias -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <button 
                    v-for="(suggestion, i) in getDescriptionSuggestions(exp)" 
                    :key="i"
                    @click="applySuggestionWithLabel(index, suggestion.text, suggestion.label)"
                    type="button"
                    class="text-left px-3 py-2.5 rounded-lg bg-white border border-blue-100 hover:border-blue-400 hover:bg-blue-50 transition-all text-xs text-gray-700 font-medium flex flex-col gap-1 group"
                >
                    <span class="text-blue-600 group-hover:text-blue-700">{{ suggestion.label }}</span>
                    <span class="text-gray-500 text-xs leading-snug line-clamp-2">{{ suggestion.text }}</span>
                </button>
            </div>

 
        </div>
    </transition>
</div>


                        </div>
                    </div>
                </transition>
            </div>

            <!-- Botón agregar experiencia -->
            <button @click="addExperience"
                class="flex w-full items-center justify-center space-x-2 rounded-lg border-2 border-dashed border-gray-300 p-4 text-gray-600 transition-colors hover:border-[#005aeb] hover:text-[#005aeb]">
                <Plus class="h-5 w-5" />
                <span>Agregar nueva experiencia</span>
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
                        ¿Eliminar experiencia?
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
