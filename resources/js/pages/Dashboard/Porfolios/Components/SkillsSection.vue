<script setup lang="ts">
import { Plus, Trash2, ChevronDown } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Skill {
    id: number;
    name: string;
    level: number;
    category: string;
}

const props = defineProps<{
    modelValue: {
        technical: Skill[];
        soft: Skill[];
    };
}>();

const emit = defineEmits(['update:modelValue']);

const expandedSkill = ref<number | null>(null);
const lastAddedId = ref<number | null>(null);

// Watcher para expandir automáticamente la última habilidad agregada (SOLO al agregar)
watch(
    () => [...props.modelValue.technical, ...props.modelValue.soft],
    (newSkills) => {
        // Solo si es diferente cantidad
        if (lastAddedId.value !== null) {
            // Buscar la habilidad con lastAddedId
            const skill = newSkills.find(s => s.id === lastAddedId.value);
            if (skill) {
                expandedSkill.value = skill.id;
            }
            lastAddedId.value = null;
        }
    },
    { deep: true }
);

const addSkill = (type: 'technical' | 'soft') => {
    const newSkillId = Date.now();
    lastAddedId.value = newSkillId; // Marcar que vamos a agregar

    const newSkill = {
        id: newSkillId,
        name: '',
        level: 50,
        category: type === 'technical' ? 'Technical' : 'Soft',
    };

    const updated = { ...props.modelValue };
    updated[type] = [...updated[type], newSkill];
    emit('update:modelValue', updated);
};

const removeSkill = (type: 'technical' | 'soft', index: number) => {
    const updated = { ...props.modelValue };
    updated[type] = [...updated[type]];
    updated[type].splice(index, 1);
    emit('update:modelValue', updated);
};

const updateSkill = (type: 'technical' | 'soft', index: number, field: keyof Skill, value: any) => {
    const updated = { ...props.modelValue };
    updated[type] = [...updated[type]];
    updated[type][index] = { ...updated[type][index], [field]: value };
    emit('update:modelValue', updated);
};

const toggleSkill = (id: number) => {
    expandedSkill.value = expandedSkill.value === id ? null : id;
};
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">
                Habilidades
            </h1>
            <p class="text-lg text-gray-600">
                Destaca tus competencias técnicas y habilidades blandas.
            </p>
        </div>

        <div class="space-y-8">
            <!-- Habilidades Técnicas -->
            <div>
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Habilidades Técnicas
                    </h3>
                    <button
                        @click="addSkill('technical')"
                        class="flex items-center space-x-1 text-sm font-medium text-blue-600 hover:text-blue-700"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Agregar</span>
                    </button>
                </div>

                <div v-if="modelValue.technical.length === 0" class="rounded-lg border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500">
                    No has agregado habilidades técnicas
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="(skill, index) in modelValue.technical"
                        :key="skill.id"
                        class="rounded-lg border border-gray-200 overflow-hidden"
                    >
                        <!-- Header Acordeón -->
                        <button
                            @click="toggleSkill(skill.id)"
                            class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                        >
                            <div class="flex items-center gap-3 flex-1 text-left">
                                <ChevronDown
                                    class="h-5 w-5 text-gray-600 transition-transform duration-400 ease-out flex-shrink-0"
                                    :class="{ 'transform rotate-180': expandedSkill === skill.id }"
                                />
                                <span class="text-base font-medium text-gray-900">
                                    {{ skill.name || 'Nueva habilidad' }}
                                </span>
                                <span class="text-sm text-gray-500">{{ skill.level }}%</span>
                            </div>
                            <button
                                @click.stop="removeSkill('technical', index)"
                                class="text-gray-400 hover:text-red-500 p-1.5 flex-shrink-0"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </button>

                        <!-- Contenido Acordeón -->
                        <transition
                            enter-active-class="transition-all duration-400 ease-out"
                            leave-active-class="transition-all duration-300 ease-in"
                            enter-from-class="max-h-0 opacity-0 overflow-hidden"
                            enter-to-class="max-h-[250px] opacity-100"
                            leave-from-class="max-h-[250px] opacity-100"
                            leave-to-class="max-h-0 opacity-0 overflow-hidden"
                        >
                            <div v-if="expandedSkill === skill.id" class="px-4 py-4 bg-white border-t border-gray-200">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Nombre
                                        </label>
                                        <input
                                            :value="skill.name"
                                            @input="updateSkill('technical', index, 'name', ($event.target as HTMLInputElement).value)"
                                            type="text"
                                            class="w-full text-base text-gray-900 bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none"
                                            placeholder="Ej: JavaScript"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Nivel: <span class="font-semibold text-blue-600">{{ skill.level }}%</span>
                                        </label>
                                        <input
                                            :value="skill.level"
                                            @input="updateSkill('technical', index, 'level', parseInt(($event.target as HTMLInputElement).value))"
                                            type="range"
                                            min="0"
                                            max="100"
                                            class="w-full h-2.5 cursor-pointer appearance-none rounded-lg bg-gray-200 accent-blue-500"
                                        />
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>

            <!-- Habilidades Blandas -->
            <div>
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Habilidades Blandas
                    </h3>
                    <button
                        @click="addSkill('soft')"
                        class="flex items-center space-x-1 text-sm font-medium text-blue-600 hover:text-blue-700"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Agregar</span>
                    </button>
                </div>

                <div v-if="modelValue.soft.length === 0" class="rounded-lg border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500">
                    No has agregado habilidades blandas
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="(skill, index) in modelValue.soft"
                        :key="skill.id"
                        class="rounded-lg border border-gray-200 overflow-hidden"
                    >
                        <!-- Header Acordeón -->
                        <button
                            @click="toggleSkill(skill.id)"
                            class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                        >
                            <div class="flex items-center gap-3 flex-1 text-left">
                                <ChevronDown
                                    class="h-5 w-5 text-gray-600 transition-transform duration-400 ease-out flex-shrink-0"
                                    :class="{ 'transform rotate-180': expandedSkill === skill.id }"
                                />
                                <span class="text-base font-medium text-gray-900">
                                    {{ skill.name || 'Nueva habilidad' }}
                                </span>
                                <span class="text-sm text-gray-500">{{ skill.level }}%</span>
                            </div>
                            <button
                                @click.stop="removeSkill('soft', index)"
                                class="text-gray-400 hover:text-red-500 p-1.5 flex-shrink-0"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </button>

                        <!-- Contenido Acordeón -->
                        <transition
                            enter-active-class="transition-all duration-400 ease-out"
                            leave-active-class="transition-all duration-300 ease-in"
                            enter-from-class="max-h-0 opacity-0 overflow-hidden"
                            enter-to-class="max-h-[250px] opacity-100"
                            leave-from-class="max-h-[250px] opacity-100"
                            leave-to-class="max-h-0 opacity-0 overflow-hidden"
                        >
                            <div v-if="expandedSkill === skill.id" class="px-4 py-4 bg-white border-t border-gray-200">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Nombre
                                        </label>
                                        <input
                                            :value="skill.name"
                                            @input="updateSkill('soft', index, 'name', ($event.target as HTMLInputElement).value)"
                                            type="text"
                                            class="w-full text-base text-gray-900 bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none"
                                            placeholder="Ej: Liderazgo"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Nivel: <span class="font-semibold text-blue-600">{{ skill.level }}%</span>
                                        </label>
                                        <input
                                            :value="skill.level"
                                            @input="updateSkill('soft', index, 'level', parseInt(($event.target as HTMLInputElement).value))"
                                            type="range"
                                            min="0"
                                            max="100"
                                            class="w-full h-2.5 cursor-pointer appearance-none rounded-lg bg-gray-200 accent-blue-500"
                                        />
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
