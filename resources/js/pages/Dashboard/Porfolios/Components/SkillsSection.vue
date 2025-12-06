<script setup lang="ts">
import { Plus, Trash2, AlertCircle, Sparkles } from 'lucide-vue-next';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useSkillValidation, type Skill, type SkillsData } from './Composables/useSkillValidation';

const props = defineProps<{
    modelValue: SkillsData;
    validation: any;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: SkillsData];
}>();

const {
    errors: skillErrors,
    validateField,
    getErrorClass,
    reindexErrors,
    getLevelLabel,
} = props.validation;

// NUEVO: Estado de "NUEVOS ITEMS" para ambos tipos
const newItemsTechnical = ref(new Set<number>());
const newItemsSoft = ref(new Set<number>());
const lastAddedId = ref<{ type: 'technical' | 'soft'; id: number } | null>(null);

// Variables para el popover de eliminación
const deleteConfirmIndex = ref<{ type: 'technical' | 'soft'; index: number } | null>(null);
const popoverPosition = ref({
    top: 0,
    left: 0,
    arrowLeft: 0,
    arrowTop: 0,
    positionY: 'below' as 'below' | 'above',
});
const deleteConfirmButtonRef = ref<HTMLElement | null>(null);

onMounted(() => {
    props.validation.clearAllErrors();
});

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

// Agregar habilidad
const addSkill = (type: 'technical' | 'soft') => {
    const newSkillId = Date.now();
    lastAddedId.value = { type, id: newSkillId };

    // 1. Lo marcamos como nuevo
    if (type === 'technical') {
        newItemsTechnical.value.add(newSkillId);
    } else {
        newItemsSoft.value.add(newSkillId);
    }

    const newSkill: Skill = {
        id: newSkillId,
        name: '',
        level: 50,
        category: type,
    };

    const updated = { ...props.modelValue };
    updated[type] = [...updated[type], newSkill];
    emit('update:modelValue', updated);

    // Opcional: Quitar el estado "nuevo" automáticamente después de 3 segundos
    setTimeout(() => {
        if (type === 'technical') {
            newItemsTechnical.value.delete(newSkillId);
        } else {
            newItemsSoft.value.delete(newSkillId);
        }
    }, 3000);
};

// Abrir popover de confirmación
const openDeleteConfirm = (type: 'technical' | 'soft', index: number, event: Event) => {
    const button = event.currentTarget as HTMLElement;

    if (deleteConfirmIndex.value?.type === type && deleteConfirmIndex.value?.index === index) {
        closeDeleteConfirm();
        return;
    }

    deleteConfirmIndex.value = { type, index };
    deleteConfirmButtonRef.value = button;
    recalculatePopoverPosition();
};

// Confirmar eliminación
const confirmDelete = (deleteData: { type: 'technical' | 'soft'; index: number } | null) => {
    if (!deleteData) return;

    const { type, index } = deleteData;
    const idToRemove = props.modelValue[type][index].id;

    // Limpiar del set de nuevos
    if (type === 'technical') {
        newItemsTechnical.value.delete(idToRemove);
    } else {
        newItemsSoft.value.delete(idToRemove);
    }

    const updated = { ...props.modelValue };
    const newArray = [...updated[type]];
    newArray.splice(index, 1);
    updated[type] = newArray;
    emit('update:modelValue', updated);

    // Reindexar errores
    reindexErrors(type, index);

    closeDeleteConfirm();
};

// Actualizar habilidad + validar
const updateSkill = (type: 'technical' | 'soft', index: number, field: keyof Skill, value: any) => {
    const id = props.modelValue[type][index].id;

    // 2. Si el usuario edita, ya no es "nuevo"
    if (type === 'technical' && newItemsTechnical.value.has(id)) {
        newItemsTechnical.value.delete(id);
    } else if (type === 'soft' && newItemsSoft.value.has(id)) {
        newItemsSoft.value.delete(id);
    }

    // MODIFICADO: Marcar campo como tocado al escribir
    props.validation.markAsTouched(type, index, field);

    const updated = { ...props.modelValue };
    updated[type] = [
        ...updated[type].slice(0, index),
        { ...updated[type][index], [field]: value },
        ...updated[type].slice(index + 1),
    ];
    emit('update:modelValue', updated);

    // MODIFICADO: Validar en tiempo real (forceShow = false)
    if (skillErrors[type]?.[index]?.[field]) {
        validateField(type, index, field, value, false);
    }
};

// NUEVO: Función para obtener clase de error del contenedor
const getContainerClass = (type: 'technical' | 'soft', index: number, id: number) => {
    if (hasSkillError(type, index)) {
        return 'border-red-300 bg-red-50';
    }
    if (type === 'technical' && newItemsTechnical.value.has(id)) {
        return 'border-emerald-300 bg-emerald-50';
    }
    if (type === 'soft' && newItemsSoft.value.has(id)) {
        return 'border-emerald-300 bg-emerald-50';
    }
    return 'border-gray-200 bg-white';
};

// NUEVO: Verificar si una habilidad tiene errores
const hasSkillError = (type: 'technical' | 'soft', index: number) => {
    const errors = skillErrors[type]?.[index];
    return errors && Object.keys(errors).length > 0;
};

// NUEVO: Función para obtener clase de error con forceShow
const getErrorClassForInput = (type: 'technical' | 'soft', index: number, field: keyof any) => {
    return props.validation.getErrorClass(type, index, field, false);
};

// NUEVO: Verificar si es nuevo
const isNew = (type: 'technical' | 'soft', id: number) => {
    if (type === 'technical') {
        return newItemsTechnical.value.has(id);
    }
    return newItemsSoft.value.has(id);
};
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">Habilidades</h1>
            <p class="text-lg text-gray-600">
                Destaca tus competencias técnicas y habilidades blandas.
            </p>
        </div>

        <!-- HABILIDADES TÉCNICAS -->
        <div class="mb-12">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Habilidades Técnicas</h3>
                <button
                    @click="addSkill('technical')"
                    class="flex items-center space-x-1 text-sm font-medium text-[#005aeb] transition-colors hover:text-[#0047b2]"
                >
                    <Plus class="h-4 w-4" />
                    <span>Agregar</span>
                </button>
            </div>

            <div
                v-if="modelValue.technical.length === 0"
                class="rounded-lg border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500"
            >
                No has agregado habilidades técnicas
            </div>

            <div v-else class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div
                    v-for="(skill, index) in modelValue.technical"
                    :key="skill.id"
                    class="overflow-hidden rounded-lg border transition-all duration-500 ease-in-out"
                    :class="getContainerClass('technical', index, skill.id)"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between px-4 py-3">
                        <div class="flex flex-1 items-center gap-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-base font-medium transition-colors duration-300"
                                        :class="{
                                            'text-red-700': hasSkillError('technical', index),
                                            'text-emerald-700': isNew('technical', skill.id),
                                            'text-gray-900': !hasSkillError('technical', index) && !isNew('technical', skill.id)
                                        }"
                                    >
                                        {{ skill.name || 'Nueva habilidad' }}
                                    </span>

                                    <!-- ICONO DE ERROR -->
                                    <AlertCircle
                                        v-if="hasSkillError('technical', index)"
                                        class="h-4 w-4 text-red-500 animate-pulse"
                                    />

                                    <!-- ICONO DE NUEVO -->
                                    <Sparkles
                                        v-if="isNew('technical', skill.id) && !hasSkillError('technical', index)"
                                        class="h-4 w-4 text-emerald-500 animate-bounce"
                                    />
                                </div>
                                <span class="text-xs text-gray-500">{{ getLevelLabel(skill.level) }}</span>
                            </div>
                        </div>

                        <button
                            @click.stop="openDeleteConfirm('technical', index, $event)"
                            type="button"
                            class="flex-shrink-0 p-1.5 text-gray-400 transition-colors hover:text-red-500"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>

                    <!-- Contenido -->
                    <div
                        class="border-t px-4 py-4 bg-white/50"
                        :class="hasSkillError('technical', index) ? 'border-red-200' : 'border-gray-200'"
                    >
                        <!-- Nombre -->
                        <div class="mb-4">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Habilidad *</label>
                            <input
                                :value="skill.name"
                                @input="
                                    updateSkill(
                                        'technical',
                                        index,
                                        'name',
                                        ($event.target as HTMLInputElement).value
                                    )
                                "
                                type="text"
                                class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-sm text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                :class="getErrorClassForInput('technical', index, 'name')"
                                placeholder="Ej: JavaScript, React, Vue.js"
                            />
                            <p v-if="skillErrors.technical?.[index]?.name" class="mt-1 text-xs text-red-500">
                                {{ skillErrors.technical[index].name }}
                            </p>
                        </div>

                        <!-- Nivel -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Nivel: {{ skill.level }}%
                            </label>
                            <input
                                :value="skill.level"
                                @input="
                                    updateSkill(
                                        'technical',
                                        index,
                                        'level',
                                        parseInt(($event.target as HTMLInputElement).value)
                                    )
                                "
                                type="range"
                                min="0"
                                max="100"
                                class="w-full cursor-pointer"
                            />
                            <p v-if="skillErrors.technical?.[index]?.level" class="mt-1 text-xs text-red-500">
                                {{ skillErrors.technical[index].level }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HABILIDADES BLANDAS -->
        <div>
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Habilidades Blandas</h3>
                <button
                    @click="addSkill('soft')"
                    class="flex items-center space-x-1 text-sm font-medium text-[#005aeb] transition-colors hover:text-[#0047b2]"
                >
                    <Plus class="h-4 w-4" />
                    <span>Agregar</span>
                </button>
            </div>

            <div
                v-if="modelValue.soft.length === 0"
                class="rounded-lg border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500"
            >
                No has agregado habilidades blandas
            </div>

            <div v-else class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div
                    v-for="(skill, index) in modelValue.soft"
                    :key="skill.id"
                    class="overflow-hidden rounded-lg border transition-all duration-500 ease-in-out"
                    :class="getContainerClass('soft', index, skill.id)"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between px-4 py-3">
                        <div class="flex flex-1 items-center gap-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-base font-medium transition-colors duration-300"
                                        :class="{
                                            'text-red-700': hasSkillError('soft', index),
                                            'text-emerald-700': isNew('soft', skill.id),
                                            'text-gray-900': !hasSkillError('soft', index) && !isNew('soft', skill.id)
                                        }"
                                    >
                                        {{ skill.name || 'Nueva habilidad' }}
                                    </span>

                                    <!-- ICONO DE ERROR -->
                                    <AlertCircle
                                        v-if="hasSkillError('soft', index)"
                                        class="h-4 w-4 text-red-500 animate-pulse"
                                    />

                                    <!-- ICONO DE NUEVO -->
                                    <Sparkles
                                        v-if="isNew('soft', skill.id) && !hasSkillError('soft', index)"
                                        class="h-4 w-4 text-emerald-500 animate-bounce"
                                    />
                                </div>
                                <span class="text-xs text-gray-500">{{ getLevelLabel(skill.level) }}</span>
                            </div>
                        </div>

                        <button
                            @click.stop="openDeleteConfirm('soft', index, $event)"
                            type="button"
                            class="flex-shrink-0 p-1.5 text-gray-400 transition-colors hover:text-red-500"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>

                    <!-- Contenido -->
                    <div
                        class="border-t px-4 py-4 bg-white/50"
                        :class="hasSkillError('soft', index) ? 'border-red-200' : 'border-gray-200'"
                    >
                        <!-- Nombre -->
                        <div class="mb-4">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Habilidad *</label>
                            <input
                                :value="skill.name"
                                @input="
                                    updateSkill(
                                        'soft',
                                        index,
                                        'name',
                                        ($event.target as HTMLInputElement).value
                                    )
                                "
                                type="text"
                                class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-sm text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                :class="getErrorClassForInput('soft', index, 'name')"
                                placeholder="Ej: Comunicación, Liderazgo, Resolución de problemas"
                            />
                            <p v-if="skillErrors.soft?.[index]?.name" class="mt-1 text-xs text-red-500">
                                {{ skillErrors.soft[index].name }}
                            </p>
                        </div>

                        <!-- Nivel -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Nivel: {{ skill.level }}%
                            </label>
                            <input
                                :value="skill.level"
                                @input="
                                    updateSkill(
                                        'soft',
                                        index,
                                        'level',
                                        parseInt(($event.target as HTMLInputElement).value)
                                    )
                                "
                                type="range"
                                min="0"
                                max="100"
                                class="w-full cursor-pointer"
                            />
                            <p v-if="skillErrors.soft?.[index]?.level" class="mt-1 text-xs text-red-500">
                                {{ skillErrors.soft[index].level }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
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
                        ¿Eliminar habilidad?
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
