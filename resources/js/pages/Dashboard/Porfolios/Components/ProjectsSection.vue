<script setup lang="ts">
import { Plus, Trash2, Upload } from 'lucide-vue-next';

interface Project {
    id: number;
    title: string;
    description: string;
    image: string | null;
    link: string;
    tags: string[];
}

const props = defineProps<{
    modelValue: Project[];
}>();

const emit = defineEmits(['update:modelValue']);

const addProject = () => {
    const newProject = {
        id: Date.now(),
        title: '',
        description: '',
        image: null,
        link: '',
        tags: [],
    };
    emit('update:modelValue', [...props.modelValue, newProject]);
};

const removeProject = (index: number) => {
    const updated = [...props.modelValue];
    updated.splice(index, 1);
    emit('update:modelValue', updated);
};

const updateProject = (index: number, field: keyof Project, value: any) => {
    const updated = [...props.modelValue];
    updated[index] = { ...updated[index], [field]: value };
    emit('update:modelValue', updated);
};

const handleImageUpload = (index: number, event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            updateProject(index, 'image', e.target?.result as string);
        };
        reader.readAsDataURL(input.files[0]);
    }
};

const updateTags = (index: number, tagsString: string) => {
    const tags = tagsString.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
    updateProject(index, 'tags', tags);
};
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">
                Proyectos
            </h1>
            <p class="text-lg text-gray-600">
                Muestra tus mejores trabajos y proyectos realizados.
            </p>
        </div>

        <div class="space-y-8">
            <div
                v-for="(project, index) in modelValue"
                :key="project.id"
                class="rounded-lg border border-gray-200 p-6"
            >
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Proyecto {{ index + 1 }}
                    </h3>
                    <button
                        @click="removeProject(index)"
                        class="text-red-600 hover:text-red-800"
                    >
                        <Trash2 class="h-4 w-4" />
                    </button>
                </div>

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
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Título del proyecto *
                        </label>
                        <input
                            :value="project.title"
                            @input="updateProject(index, 'title', ($event.target as HTMLInputElement).value)"
                            type="text"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                            placeholder="Nombre del proyecto"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Enlace (URL)
                        </label>
                        <input
                            :value="project.link"
                            @input="updateProject(index, 'link', ($event.target as HTMLInputElement).value)"
                            type="url"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                            placeholder="https://..."
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Tecnologías (separadas por coma)
                        </label>
                        <input
                            :value="project.tags.join(', ')"
                            @input="updateTags(index, ($event.target as HTMLInputElement).value)"
                            type="text"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                            placeholder="Vue, Laravel, TailwindCSS..."
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Descripción
                        </label>
                        <textarea
                            :value="project.description"
                            @input="updateProject(index, 'description', ($event.target as HTMLInputElement).value)"
                            rows="3"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-[#005aeb]"
                            placeholder="Describe el proyecto..."
                        ></textarea>
                    </div>
                </div>
            </div>

            <button
                @click="addProject"
                class="flex w-full items-center space-x-2 rounded-lg border-2 border-dashed border-gray-300 p-6 text-gray-600 transition-colors hover:border-[#005aeb] hover:text-[#005aeb]"
            >
                <Plus class="h-5 w-5" />
                <span>Agregar nuevo proyecto</span>
            </button>
        </div>
    </div>
</template>
