<script setup lang="ts">
    import { Plus, Trash2, ChevronDown, AlertCircle, Sparkles } from 'lucide-vue-next';
    import { ref, watch, onMounted } from 'vue';
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
    
    
    // Estado de "NUEVOS ITEMS" para ambos tipos
    const newItemsTechnical = ref(new Set<number>());
    const newItemsSoft = ref(new Set<number>());
    const lastAddedId = ref<{ type: 'technical' | 'soft'; id: number } | null>(null);
    
    
    // Estado de acordeón expandido (solo UNO a la vez, por ID)
    const expandedSkill = ref<number | null>(null);
    
    
    onMounted(() => {
        props.validation.clearAllErrors();
    });
    
    
    // Auto-expandir nuevo item
    watch(
        () => [props.modelValue.technical.length, props.modelValue.soft.length],
        () => {
            if (lastAddedId.value !== null) {
                const { type, id } = lastAddedId.value;
                const skills = props.modelValue[type];
                const skill = skills.find((s) => s.id === id);
                if (skill) {
                    expandedSkill.value = skill.id;
                }
                lastAddedId.value = null;
            }
        }
    );
    
    
    // Agregar habilidad
    const addSkill = (type: 'technical' | 'soft') => {
        const newSkillId = Date.now();
        lastAddedId.value = { type, id: newSkillId };
    
    
        // Marcar como nuevo
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
    
    
        // Quitar estado "nuevo" automáticamente en 3 segundos
        setTimeout(() => {
            if (type === 'technical') {
                newItemsTechnical.value.delete(newSkillId);
            } else {
                newItemsSoft.value.delete(newSkillId);
            }
        }, 3000);
    };
    
    
    // Eliminar habilidad
    const removeSkill = (type: 'technical' | 'soft', index: number) => {
        const idToRemove = props.modelValue[type][index].id;
    
    
        // Limpiar del set de nuevos
        if (type === 'technical') {
            newItemsTechnical.value.delete(idToRemove);
        } else {
            newItemsSoft.value.delete(idToRemove);
        }
    
    
        // Si se estaba mostrando ese acordeón, cerrarlo
        if (expandedSkill.value === idToRemove) {
            expandedSkill.value = null;
        }
    
    
        const updated = { ...props.modelValue };
        const newArray = [...updated[type]];
        newArray.splice(index, 1);
        updated[type] = newArray;
        emit('update:modelValue', updated);
    
    
        // Reindexar errores
        reindexErrors(type, index);
    };
    
    
    // Toggle acordeón - solo uno a la vez
    const toggleSkill = (id: number) => {
        if (expandedSkill.value === id) {
            expandedSkill.value = null;
        } else {
            expandedSkill.value = id;
            // Si lo abres, quitar estado "nuevo"
            if (newItemsTechnical.value.has(id)) {
                newItemsTechnical.value.delete(id);
            }
            if (newItemsSoft.value.has(id)) {
                newItemsSoft.value.delete(id);
            }
        }
    };
    
    
    // Actualizar habilidad + validar
    const updateSkill = (type: 'technical' | 'soft', index: number, field: keyof Skill, value: any) => {
        const id = props.modelValue[type][index].id;
    
    
        // Si el usuario edita, ya no es "nuevo"
        if (type === 'technical' && newItemsTechnical.value.has(id)) {
            newItemsTechnical.value.delete(id);
        } else if (type === 'soft' && newItemsSoft.value.has(id)) {
            newItemsSoft.value.delete(id);
        }
    
    
        // Marcar campo como tocado
        props.validation.markAsTouched(type, index, field);
    
    
        const updated = { ...props.modelValue };
        updated[type] = [
            ...updated[type].slice(0, index),
            { ...updated[type][index], [field]: value },
            ...updated[type].slice(index + 1),
        ];
        emit('update:modelValue', updated);
    
    
        // Validar en tiempo real
        if (skillErrors[type]?.[index]?.[field]) {
            validateField(type, index, field, value, false);
        }
    };
    
    
    // Verificar si una habilidad tiene errores
    const hasSkillError = (type: 'technical' | 'soft', index: number) => {
        const errors = skillErrors[type]?.[index];
        return errors && Object.keys(errors).length > 0;
    };
    
    
    // Obtener clase de contenedor
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
    
    
    // Verificar si es nuevo
    const isNew = (type: 'technical' | 'soft', id: number) => {
        if (type === 'technical') {
            return newItemsTechnical.value.has(id);
        }
        return newItemsSoft.value.has(id);
    };
    
    
    // Obtener clase de error para inputs
    const getErrorClassForInput = (type: 'technical' | 'soft', index: number, field: keyof any) => {
        return props.validation.getErrorClass(type, index, field, false);
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
    
    
                <div v-else class="space-y-3">
                    <div
                        v-for="(skill, index) in modelValue.technical"
                        :key="skill.id"
                        class="overflow-hidden rounded-lg border transition-all duration-500 ease-in-out"
                        :class="getContainerClass('technical', index, skill.id)"
                    >
                        <!-- Header Acordeón -->
                        <div
                            role="button"
                            tabindex="0"
                            @click="toggleSkill(skill.id)"
                            @keydown.enter="toggleSkill(skill.id)"
                            class="flex w-full items-center justify-between px-4 py-3 transition-colors hover:bg-gray-50/50"
                        >
                            <div class="flex flex-1 items-center gap-3 text-left">
                                <ChevronDown
                                    class="h-5 w-5 flex-shrink-0 text-gray-600 transition-transform duration-400 ease-out"
                                    :class="{ 'rotate-180 transform': expandedSkill === skill.id }"
                                />
                                
                                <div class="flex flex-col">
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
                                    <span class="text-sm text-gray-500">{{ getLevelLabel(skill.level) }}</span>
                                </div>
                            </div>
    
    
                            <button
                                @click.stop="removeSkill('technical', index)"
                                type="button"
                                class="flex-shrink-0 p-1.5 text-gray-400 transition-colors hover:text-red-500"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
    
    
                        <!-- Contenido Acordeón -->
                        <transition
                            enter-active-class="transition-all duration-400 ease-out"
                            leave-active-class="transition-all duration-300 ease-in"
                            enter-from-class="max-h-0 overflow-hidden opacity-0"
                            enter-to-class="max-h-[500px] opacity-100"
                            leave-from-class="max-h-[500px] opacity-100"
                            leave-to-class="max-h-0 overflow-hidden opacity-0"
                        >
                            <div
                                v-if="expandedSkill === skill.id"
                                class="border-t px-4 py-4 bg-white/50"
                                :class="hasSkillError('technical', index) ? 'border-red-200' : 'border-gray-200'"
                            >
                                <div class="space-y-4">
                                    <!-- Nombre -->
                                    <div>
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
                                            class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:border-[#005aeb] focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                            :class="getErrorClassForInput('technical', index, 'name')"
                                            placeholder="Ej: JavaScript, React, Vue.js"
                                        />
                                        <p v-if="skillErrors.technical?.[index]?.name" class="mt-1 text-sm text-red-500">
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
                                        <p v-if="skillErrors.technical?.[index]?.level" class="mt-1 text-sm text-red-500">
                                            {{ skillErrors.technical[index].level }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </transition>
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
    
    
                <div v-else class="space-y-3">
                    <div
                        v-for="(skill, index) in modelValue.soft"
                        :key="skill.id"
                        class="overflow-hidden rounded-lg border transition-all duration-500 ease-in-out"
                        :class="getContainerClass('soft', index, skill.id)"
                    >
                        <!-- Header Acordeón -->
                        <div
                            role="button"
                            tabindex="0"
                            @click="toggleSkill(skill.id)"
                            @keydown.enter="toggleSkill(skill.id)"
                            class="flex w-full items-center justify-between px-4 py-3 transition-colors hover:bg-gray-50/50"
                        >
                            <div class="flex flex-1 items-center gap-3 text-left">
                                <ChevronDown
                                    class="h-5 w-5 flex-shrink-0 text-gray-600 transition-transform duration-400 ease-out"
                                    :class="{ 'rotate-180 transform': expandedSkill === skill.id }"
                                />
                                
                                <div class="flex flex-col">
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
                                    <span class="text-sm text-gray-500">{{ getLevelLabel(skill.level) }}</span>
                                </div>
                            </div>
    
    
                            <button
                                @click.stop="removeSkill('soft', index)"
                                type="button"
                                class="flex-shrink-0 p-1.5 text-gray-400 transition-colors hover:text-red-500"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
    
    
                        <!-- Contenido Acordeón -->
                        <transition
                            enter-active-class="transition-all duration-400 ease-out"
                            leave-active-class="transition-all duration-300 ease-in"
                            enter-from-class="max-h-0 overflow-hidden opacity-0"
                            enter-to-class="max-h-[500px] opacity-100"
                            leave-from-class="max-h-[500px] opacity-100"
                            leave-to-class="max-h-0 overflow-hidden opacity-0"
                        >
                            <div
                                v-if="expandedSkill === skill.id"
                                class="border-t px-4 py-4 bg-white/50"
                                :class="hasSkillError('soft', index) ? 'border-red-200' : 'border-gray-200'"
                            >
                                <div class="space-y-4">
                                    <!-- Nombre -->
                                    <div>
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
                                            class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:border-[#005aeb] focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                                            :class="getErrorClassForInput('soft', index, 'name')"
                                            placeholder="Ej: Comunicación, Liderazgo, Resolución de problemas"
                                        />
                                        <p v-if="skillErrors.soft?.[index]?.name" class="mt-1 text-sm text-red-500">
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
                                        <p v-if="skillErrors.soft?.[index]?.level" class="mt-1 text-sm text-red-500">
                                            {{ skillErrors.soft[index].level }}
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
    