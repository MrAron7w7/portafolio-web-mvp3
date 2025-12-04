<script setup lang="ts">
import { Plus, Trash2, ChevronDown } from 'lucide-vue-next';
import { ref, watch, onMounted, onUnmounted } from 'vue';

interface Education {
  id: number;
  degree: string;
  institution: string;
  startDate: string;
  endDate: string;
  description: string;
  current: boolean;
}

interface EducationErrors {
  degree?: string;
  institution?: string;
  startDate?: string;
  endDate?: string;
  description?: string;
}

const props = defineProps<{
  modelValue: Education[];
}>();

const emit = defineEmits(['update:modelValue']);

// Estado de errores por índice
const educationErrors = ref<{ [key: number]: EducationErrors }>({});

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

// Abrir siempre la última formación agregada
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

// VALIDACIÓN de campo individual
const validateField = (index: number, field: keyof Education, value: any) => {
  if (!educationErrors.value[index]) {
    educationErrors.value[index] = {};
  }

  const edu = props.modelValue[index];

  switch (field) {
    case 'degree':
      if (!value.trim()) {
        educationErrors.value[index].degree = 'El título/carrera es requerido';
      } else if (value.trim().length < 3) {
        educationErrors.value[index].degree = 'Mínimo 3 caracteres';
      } else {
        delete educationErrors.value[index].degree;
      }
      break;

    case 'institution':
      if (!value.trim()) {
        educationErrors.value[index].institution = 'La institución es requerida';
      } else if (value.trim().length < 3) {
        educationErrors.value[index].institution = 'Mínimo 3 caracteres';
      } else {
        delete educationErrors.value[index].institution;
      }
      break;

    case 'startDate':
      if (!value) {
        educationErrors.value[index].startDate = 'La fecha de inicio es requerida';
      } else {
        if (edu.endDate && !edu.current && value > edu.endDate) {
          educationErrors.value[index].startDate =
            'La fecha de inicio no puede ser posterior a la de graduación';
          if (!educationErrors.value[index].endDate) {
            educationErrors.value[index].endDate =
              'La fecha de graduación no puede ser anterior a la de inicio';
          }
        } else {
          delete educationErrors.value[index].startDate;
          if (
            educationErrors.value[index].endDate ===
            'La fecha de graduación no puede ser anterior a la de inicio'
          ) {
            delete educationErrors.value[index].endDate;
          }
        }
      }
      break;

    case 'endDate':
      if (!edu.current) {
        if (!value) {
          educationErrors.value[index].endDate =
            'La fecha de graduación es requerida o marca "Actualmente estudiando"';
        } else if (edu.startDate && value < edu.startDate) {
          educationErrors.value[index].endDate =
            'La fecha de graduación no puede ser anterior a la de inicio';
          if (!educationErrors.value[index].startDate) {
            educationErrors.value[index].startDate =
              'La fecha de inicio no puede ser posterior a la de graduación';
          }
        } else {
          delete educationErrors.value[index].endDate;
          if (
            educationErrors.value[index].startDate ===
            'La fecha de inicio no puede ser posterior a la de graduación'
          ) {
            delete educationErrors.value[index].startDate;
          }
        }
      } else {
        delete educationErrors.value[index].endDate;
      }
      break;

    case 'description':
      if (!value.trim()) {
        delete educationErrors.value[index].description;
      } else if (value.trim().length < 10) {
        educationErrors.value[index].description = 'Mínimo 10 caracteres';
      } else if (value.length > 500) {
        educationErrors.value[index].description = 'Máximo 500 caracteres';
      } else {
        delete educationErrors.value[index].description;
      }
      break;

    case 'current':
      if (value) {
        delete educationErrors.value[index].endDate;
      } else {
        if (!edu.endDate) {
          educationErrors.value[index].endDate =
            'La fecha de graduación es requerida o marca "Actualmente estudiando"';
        }
      }
      break;
  }
};

// Clase de error para inputs
const getErrorClass = (index: number, field: keyof EducationErrors) => {
  return educationErrors.value[index]?.[field]
    ? 'border-red-500 focus:border-red-500'
    : 'border-gray-300 focus:border-[#005aeb]';
};

// Añadir formación
const addEducation = () => {
  const newEducation: Education = {
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
  delete educationErrors.value[index];

  if (expandedIndex.value === index) {
    expandedIndex.value = null;
  }
  closeDeleteConfirm();
};

// Toggle acordeón
const toggleEducation = (index: number) => {
  expandedIndex.value = expandedIndex.value === index ? null : index;
};

// Actualizar campo + validar
const updateEducation = (index: number, field: keyof Education, value: any) => {
  const updated = [...props.modelValue];
  updated[index] = { ...updated[index], [field]: value };
  emit('update:modelValue', updated);

  validateField(index, field, value);
};

// Checkbox "Actualmente estudiando"
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
  <div class="relative" ref="containerRef">
    <div class="mb-8">
      <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">
        Formación Académica
      </h1>
      <p class="text-lg text-gray-600">
        Agrega tu historial educativo y formación profesional.
      </p>
    </div>

    <div class="space-y-3">
      <!-- Items del acordeón -->
      <div
        v-for="(edu, index) in modelValue"
        :key="edu.id"
        class="rounded-lg border border-gray-200 overflow-hidden transition-all"
      >
        <!-- Header del acordeón -->
        <button
          @click="toggleEducation(index)"
          class="w-full flex items-center justify-between px-6 py-4 bg-gray-50 hover:bg-gray-100 transition-colors"
        >
          <div class="flex items-center gap-3 flex-1 text-left">
            <ChevronDown
              class="h-5 w-5 text-gray-600 transition-transform duration-400 ease-out"
              :class="{ 'transform rotate-180': expandedIndex === index }"
            />
            <div>
              <h3 class="text-lg font-semibold text-gray-900">
                Formación {{ index + 1 }}
              </h3>
              <p v-if="edu.degree" class="text-sm text-gray-600">
                {{ edu.degree }} {{ edu.institution ? `en ${edu.institution}` : '' }}
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

        <!-- Contenido del acordeón -->
        <transition
          enter-active-class="transition-all duration-400 ease-out"
          leave-active-class="transition-all duration-400 ease-in"
          enter-from-class="max-h-0 opacity-0 overflow-hidden"
          enter-to-class="max-h-[700px] opacity-100"
          leave-from-class="max-h-[700px] opacity-100"
          leave-to-class="max-h-0 opacity-0 overflow-hidden"
        >
          <div
            v-if="expandedIndex === index"
            class="px-6 py-6 bg-white border-t border-gray-200"
          >
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
              <!-- Título/Carrera -->
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                  Título/Carrera *
                </label>
                <input
                  :value="edu.degree"
                  @input="updateEducation(index, 'degree', ($event.target as HTMLInputElement).value)"
                  type="text"
                  class="w-full rounded-lg border px-4 py-2 transition-colors"
                  :class="getErrorClass(index, 'degree')"
                  placeholder="ej: Ingeniería de Sistemas"
                />
                <p
                  v-if="educationErrors[index]?.degree"
                  class="mt-1 text-sm text-red-500"
                >
                  {{ educationErrors[index].degree }}
                </p>
              </div>

              <!-- Institución -->
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                  Institución *
                </label>
                <input
                  :value="edu.institution"
                  @input="updateEducation(index, 'institution', ($event.target as HTMLInputElement).value)"
                  type="text"
                  class="w-full rounded-lg border px-4 py-2 transition-colors"
                  :class="getErrorClass(index, 'institution')"
                  placeholder="ej: Universidad Nacional"
                />
                <p
                  v-if="educationErrors[index]?.institution"
                  class="mt-1 text-sm text-red-500"
                >
                  {{ educationErrors[index].institution }}
                </p>
              </div>

              <!-- Fecha de inicio -->
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                  Fecha de inicio *
                </label>
                <input
                  :value="edu.startDate"
                  @input="updateEducation(index, 'startDate', ($event.target as HTMLInputElement).value)"
                  type="month"
                  class="w-full rounded-lg border px-4 py-2 transition-colors"
                  :class="getErrorClass(index, 'startDate')"
                />
                <p
                  v-if="educationErrors[index]?.startDate"
                  class="mt-1 text-sm text-red-500"
                >
                  {{ educationErrors[index].startDate }}
                </p>
              </div>

              <!-- Fecha de graduación -->
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                  Fecha de graduación
                </label>
                <input
                  :value="edu.endDate"
                  @input="updateEducation(index, 'endDate', ($event.target as HTMLInputElement).value)"
                  type="month"
                  :disabled="edu.current"
                  class="w-full rounded-lg border px-4 py-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  :class="getErrorClass(index, 'endDate')"
                />
                <p
                  v-if="educationErrors[index]?.endDate"
                  class="mt-1 text-sm text-red-500"
                >
                  {{ educationErrors[index].endDate }}
                </p>
              </div>

              <!-- Checkbox Actualmente estudiando -->
              <div class="md:col-span-2">
                <label class="flex items-center space-x-3">
                  <input
                    :checked="edu.current"
                    @change="handleCurrentChange(index, ($event.target as HTMLInputElement).checked)"
                    type="checkbox"
                    class="rounded border-gray-300 cursor-pointer w-4 h-4"
                  />
                  <span class="text-sm font-medium text-gray-700 cursor-pointer">
                    Actualmente estudiando
                  </span>
                </label>
              </div>
            </div>
          </div>
        </transition>
      </div>

      <!-- Botón agregar formación -->
      <button
        @click="addEducation"
        class="flex w-full items-center space-x-2 rounded-lg border-2 border-dashed border-gray-300 p-6 text-gray-600 transition-colors hover:border-[#005aeb] hover:text-[#005aeb]"
      >
        <Plus class="h-5 w-5" />
        <span>Agregar nueva formación</span>
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
        class="fixed w-56 bg-white rounded-lg shadow-2xl border border-gray-200 z-50 p-4 pointer-events-auto"
        :style="{
          top: popoverPosition.top + 'px',
          left: popoverPosition.left + 'px',
        }"
      >
        <!-- Flecha arriba -->
        <div
          v-if="popoverPosition.positionY === 'below'"
          class="absolute -top-2 w-4 h-4 bg-white border-t border-l border-gray-200 rotate-45"
          :style="{ left: popoverPosition.arrowLeft + 'px' }"
        ></div>

        <!-- Flecha abajo -->
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
            ¿Eliminar formación?
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