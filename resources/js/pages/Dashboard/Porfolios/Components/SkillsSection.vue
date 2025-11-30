<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';

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

const addSkill = (type: 'technical' | 'soft') => {
    const newSkill = {
        id: Date.now(),
        name: '',
        level: 80,
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

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Habilidades Técnicas -->
            <div>
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Habilidades Técnicas
                    </h3>
                    <button
                        @click="addSkill('technical')"
                        class="flex items-center space-x-1 text-sm font-medium text-[#005aeb] hover:text-[#0048c4]"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Agregar</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <div
                        v-for="(skill, index) in modelValue.technical"
                        :key="skill.id"
                        class="flex items-center space-x-3 rounded-lg border border-gray-200 p-3"
                    >
                        <div class="flex-1">
                            <input
                                :value="skill.name"
                                @input="updateSkill('technical', index, 'name', ($event.target as HTMLInputElement).value)"
                                type="text"
                                class="w-full rounded border-gray-300 px-2 py-1 text-sm focus:border-[#005aeb] focus:ring-1 focus:ring-[#005aeb]"
                                placeholder="Ej: JavaScript"
                            />
                        </div>
                        <div class="w-24">
                            <input
                                :value="skill.level"
                                @input="updateSkill('technical', index, 'level', parseInt(($event.target as HTMLInputElement).value))"
                                type="range"
                                min="0"
                                max="100"
                                class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-200 accent-[#005aeb]"
                            />
                        </div>
                        <button
                            @click="removeSkill('technical', index)"
                            class="text-gray-400 hover:text-red-500"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                    <div
                        v-if="modelValue.technical.length === 0"
                        class="rounded-lg border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500"
                    >
                        No has agregado habilidades técnicas
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
                        class="flex items-center space-x-1 text-sm font-medium text-[#005aeb] hover:text-[#0048c4]"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Agregar</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <div
                        v-for="(skill, index) in modelValue.soft"
                        :key="skill.id"
                        class="flex items-center space-x-3 rounded-lg border border-gray-200 p-3"
                    >
                        <div class="flex-1">
                            <input
                                :value="skill.name"
                                @input="updateSkill('soft', index, 'name', ($event.target as HTMLInputElement).value)"
                                type="text"
                                class="w-full rounded border-gray-300 px-2 py-1 text-sm focus:border-[#005aeb] focus:ring-1 focus:ring-[#005aeb]"
                                placeholder="Ej: Liderazgo"
                            />
                        </div>
                        <div class="w-24">
                            <input
                                :value="skill.level"
                                @input="updateSkill('soft', index, 'level', parseInt(($event.target as HTMLInputElement).value))"
                                type="range"
                                min="0"
                                max="100"
                                class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-200 accent-[#005aeb]"
                            />
                        </div>
                        <button
                            @click="removeSkill('soft', index)"
                            class="text-gray-400 hover:text-red-500"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                    <div
                        v-if="modelValue.soft.length === 0"
                        class="rounded-lg border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500"
                    >
                        No has agregado habilidades blandas
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
