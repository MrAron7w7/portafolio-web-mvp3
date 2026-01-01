<script setup lang="ts">
import { Plus, Trash2, ChevronDown, AlertCircle, Sparkles } from 'lucide-vue-next';
import { ref, watch, computed, onMounted } from 'vue';
import { languageLevels, type Language } from './Composables/useLanguageValidation';

const props = withDefaults(
  defineProps<{
    modelValue?: Language[];
    validation: any;
  }>(),
  {
    modelValue: () => [],
  }
);

const emit = defineEmits<{
  'update:modelValue': [value: Language[]];
}>();

const expandedLanguage = ref<number | null>(null);
const lastAddedId = ref<number | null>(null);

// --- ESTADO DE "NUEVOS ITEMS" ---
const newItems = ref(new Set<number>());

const languages = computed(() => props.modelValue || []);

onMounted(() => {
  props.validation.clearAllErrors();
});

// Auto-expandir nuevo item
watch(
  () => languages.value.length,
  (newLength, oldLength) => {
    if (newLength > oldLength && lastAddedId.value !== null) {
      const language = languages.value.find((l) => l.id === lastAddedId.value);
      if (language) {
        expandedLanguage.value = language.id;
      }
      lastAddedId.value = null;
    }
  }
);

const addLanguage = () => {
  const newLanguageId = Date.now();
  lastAddedId.value = newLanguageId;

  // 1. Lo marcamos como nuevo
  newItems.value.add(newLanguageId);

  const newLanguage: Language = {
    id: newLanguageId,
    name: '',
    level: '',
  };

  const currentLanguages = [...languages.value, newLanguage];
  emit('update:modelValue', currentLanguages);
  
  // Opcional: Quitar el estado "nuevo" automáticamente después de 3 segundos
  setTimeout(() => {
     newItems.value.delete(newLanguageId);
  }, 3000);
};

const removeLanguage = (index: number) => {
  const idToRemove = languages.value[index].id;
  newItems.value.delete(idToRemove); // Limpiamos del set

  const currentLanguages = [...languages.value];
  currentLanguages.splice(index, 1);
  emit('update:modelValue', currentLanguages);
  props.validation.reindexErrors(index);
};

const updateLanguage = (index: number, field: keyof Language, value: any) => {
  const id = languages.value[index].id;
  
  // 2. Si el usuario edita, ya no es "nuevo"
  if (newItems.value.has(id)) {
    newItems.value.delete(id);
  }

  // MODIFICADO: Marcar campo como tocado al escribir
  props.validation.markAsTouched(index, field);

  const currentLanguages = [...languages.value];
  currentLanguages[index] = { ...currentLanguages[index], [field]: value };
  emit('update:modelValue', currentLanguages);

  // MODIFICADO: Validar en tiempo real (forceShow = false)
  if (props.validation.errors[index]?.[field]) {
    props.validation.validateField(index, field, value, false);
  }
};

const toggleLanguage = (id: number) => {
  expandedLanguage.value = expandedLanguage.value === id ? null : id;
  // Opcional: Si lo cierras, también podrías quitar el estado "nuevo"
  if (newItems.value.has(id)) {
      newItems.value.delete(id);
  }
};

const hasLanguageError = (index: number) => {
  const errors = props.validation.errors[index];
  return errors && Object.keys(errors).length > 0;
};

// MODIFICADO: Aceptar parámetro forceShow
const getContainerClass = (index: number, id: number, forceShow: boolean = false) => {
  if (hasLanguageError(index)) {
    return 'border-red-300 dark:border-red-500/50 bg-red-50 dark:bg-red-500/10';
  }
  if (newItems.value.has(id)) {
    return 'border-emerald-300 dark:border-emerald-500/50 bg-emerald-50 dark:bg-emerald-500/10';
  }
  return 'border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900';
};

// MODIFICADO: Función para obtener clase de error con forceShow
const getErrorClass = (index: number, field: keyof Language) => {
  return props.validation.getErrorClass(index, field, false);
};
</script>

<template>
  <div>
    <div class="mb-8">
      <h1 class="mb-3 text-2xl font-bold text-gray-900 dark:text-white lg:text-3xl">Idiomas</h1>
      <p class="text-lg text-gray-600 dark:text-slate-400">
        Destaca los idiomas que hablas y tu nivel de fluidez.
      </p>
    </div>

    <div>
      <div class="mb-4 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tus Idiomas</h3>
        <button
          @click="addLanguage"
          class="flex items-center space-x-1 text-sm font-medium text-[#005aeb] dark:text-blue-400 transition-colors hover:text-[#0047b2] dark:hover:text-blue-300"
        >
          <Plus class="h-4 w-4" />
          <span>Agregar</span>
        </button>
      </div>

      <div
        v-if="languages.length === 0"
        class="rounded-lg border border-dashed border-gray-300 dark:border-slate-800 p-4 text-center text-sm text-gray-500 dark:text-slate-500"
      >
        No has agregado idiomas
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="(language, index) in languages"
          :key="language.id"
          class="overflow-hidden rounded-lg border transition-all duration-500 ease-in-out"
          :class="getContainerClass(index, language.id)"
        >
          <!-- Header Acordeón -->
          <div
            role="button"
            tabindex="0"
            @click="toggleLanguage(language.id)"
            @keydown.enter="toggleLanguage(language.id)"
            class="flex w-full items-center justify-between px-4 py-3 transition-colors hover:bg-gray-50/50 dark:hover:bg-slate-800/50"
          >
            <div class="flex flex-1 items-center gap-3 text-left">
              <ChevronDown
                class="h-5 w-5 shrink-0 text-gray-600 transition-transform duration-400 ease-out"
                :class="{ 'rotate-180 transform': expandedLanguage === language.id }"
              />
              
              <div class="flex flex-col">
                <div class="flex items-center gap-2">
                  <span 
                    class="text-base font-medium transition-colors duration-300" 
                    :class="{
                        'text-red-700 dark:text-red-400': hasLanguageError(index),
                        'text-emerald-700 dark:text-emerald-400': newItems.has(language.id),
                        'text-gray-900 dark:text-white': !hasLanguageError(index) && !newItems.has(language.id)
                    }"
                  >
                    {{ language.name || 'Nuevo idioma' }}
                  </span>
                  
                  <!-- ICONO DE ERROR -->
                  <AlertCircle 
                    v-if="hasLanguageError(index)" 
                    class="h-4 w-4 text-red-500 animate-pulse" 
                  />

                  <!-- ICONO DE NUEVO -->
                  <Sparkles 
                    v-if="newItems.has(language.id) && !hasLanguageError(index)" 
                    class="h-4 w-4 text-emerald-500 animate-bounce" 
                  />
                </div>
                <span class="text-sm text-gray-500 dark:text-slate-400">{{ language.level }}</span>
              </div>
            </div>

            <button
              @click.stop="removeLanguage(index)"
              type="button"
              class="shrink-0 p-1.5 text-gray-400 dark:text-slate-500 transition-colors hover:text-red-500 dark:hover:text-red-400"
            >
              <Trash2 class="h-4 w-4" />
            </button>
          </div>

          <!-- Contenido Acordeón -->
          <transition
            enter-active-class="transition-all duration-400 ease-out"
            leave-active-class="transition-all duration-300 ease-in"
            enter-from-class="max-h-0 overflow-hidden opacity-0"
            enter-to-class="max-h-[300px] opacity-100"
            leave-from-class="max-h-[300px] opacity-100"
            leave-to-class="max-h-0 overflow-hidden opacity-0"
          >
            <div
              v-if="expandedLanguage === language.id"
              class="border-t px-4 py-4 bg-white/50 dark:bg-slate-900/50"
              :class="hasLanguageError(index) ? 'border-red-200 dark:border-red-500/30' : 'border-gray-200 dark:border-slate-800'"
            >
              <div class="space-y-4">
                
                <!-- Nombre -->
                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-slate-300">Idioma *</label>
                  <input
                    :value="language.name"
                    @input="updateLanguage(index, 'name', ($event.target as HTMLInputElement).value)"
                    type="text"
                    class="w-full rounded-lg border dark:border-slate-800 bg-gray-50 dark:bg-slate-800 px-3 py-2 text-base text-gray-900 dark:text-white transition-colors focus:border-[#005aeb] focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                    :class="getErrorClass(index, 'name')"
                    placeholder="Ej: Inglés"
                  />
                  <p v-if="validation.errors[index]?.name" class="mt-1 text-sm text-red-500">
                    {{ validation.errors[index].name }}
                  </p>
                </div>

                <!-- Nivel -->
                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-slate-300">Nivel *</label>
                  <select
                    :value="language.level"
                    @change="updateLanguage(index, 'level', ($event.target as HTMLSelectElement).value)"
                    class="w-full rounded-lg border dark:border-slate-800 bg-gray-50 dark:bg-slate-800 px-3 py-2 text-base text-gray-900 dark:text-white transition-colors focus:border-[#005aeb] focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                    :class="getErrorClass(index, 'level')"
                  >
                    <option value="" disabled>Selecciona un nivel</option>
                    <option v-for="level in languageLevels" :key="level" :value="level">
                      {{ level }}
                    </option>
                  </select>
                  <p v-if="validation.errors[index]?.level" class="mt-1 text-sm text-red-500">
                    {{ validation.errors[index].level }}
                  </p>
                </div>

              </div>
            </div>
          </transition>
        </div>
      </div>
    </div>
  </div>
</template>
