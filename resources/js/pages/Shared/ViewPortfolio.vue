<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { Eye, Edit3, User, Mail, Phone, MapPin, Briefcase, GraduationCap, Code, FolderOpen, Globe, Linkedin, Github, ExternalLink, Lock, Save, Loader2, Check } from 'lucide-vue-next';

const props = defineProps<{
    portfolio: any;
    canEdit: boolean;
    isOwner: boolean;
    permission: 'view' | 'view_edit';
}>();

const page = usePage();
const currentUser = computed(() => page.props.auth?.user || null);

// Estado de edición
const isEditing = ref(false);
const isSaving = ref(false);
const editedData = ref<any>(null);

// Datos del portafolio
const templateData = computed(() => props.portfolio.template_data || {});
const personal = computed(() => templateData.value.personal || {});
const experience = computed(() => templateData.value.experience || []);
const education = computed(() => templateData.value.education || []);
const skills = computed(() => templateData.value.skills || { technical: [], soft: [] });
const projects = computed(() => templateData.value.projects || []);

// Nombre completo
const fullName = computed(() => {
    return `${personal.value.firstName || ''} ${personal.value.lastName || ''}`.trim() || 'Sin nombre';
});

// Iniciar edición
const startEditing = () => {
    if (!props.canEdit) return;
    editedData.value = JSON.parse(JSON.stringify(templateData.value));
    isEditing.value = true;
};

// Cancelar edición
const cancelEditing = () => {
    isEditing.value = false;
    editedData.value = null;
};

// Guardar cambios
const saveChanges = async () => {
    if (!editedData.value) return;
    
    isSaving.value = true;
    
    try {
        const response = await fetch(`/share/${props.portfolio.share_token}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                template_data: editedData.value,
            }),
        });

        if (response.ok) {
            isEditing.value = false;
            // Recargar la página para ver los cambios
            window.location.reload();
        } else {
            const error = await response.json();
            alert(error.error || 'Error al guardar');
        }
    } catch (err) {
        console.error('Error:', err);
        alert('Error al guardar los cambios');
    } finally {
        isSaving.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Logo y título -->
                    <div class="flex items-center space-x-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#005aeb]">
                            <span class="text-sm font-bold text-white">P</span>
                        </div>
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900">{{ portfolio.title }}</h1>
                            <p class="text-xs text-gray-500">
                                {{ isOwner ? 'Tu portafolio' : `Compartido por ${personal.firstName || 'Usuario'}` }}
                            </p>
                        </div>
                    </div>

                    <!-- Indicador de permisos y acciones -->
                    <div class="flex items-center space-x-3">
                        <!-- Badge de permiso -->
                        <div :class="[
                            'flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium',
                            canEdit ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'
                        ]">
                            <Edit3 v-if="canEdit" class="w-3.5 h-3.5" />
                            <Eye v-else class="w-3.5 h-3.5" />
                            {{ canEdit ? 'Puedes editar' : 'Solo lectura' }}
                        </div>

                        <!-- Botones de edición -->
                        <template v-if="canEdit && !isEditing">
                            <button
                                @click="startEditing"
                                class="flex items-center gap-2 px-4 py-2 bg-[#005aeb] text-white rounded-lg font-medium hover:bg-[#0048c4] transition-colors"
                            >
                                <Edit3 class="w-4 h-4" />
                                Editar
                            </button>
                        </template>

                        <template v-if="isEditing">
                            <button
                                @click="cancelEditing"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                @click="saveChanges"
                                :disabled="isSaving"
                                class="flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg font-medium hover:bg-green-600 transition-colors disabled:opacity-50"
                            >
                                <Loader2 v-if="isSaving" class="w-4 h-4 animate-spin" />
                                <Save v-else class="w-4 h-4" />
                                {{ isSaving ? 'Guardando...' : 'Guardar' }}
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido del portafolio -->
        <main class="container mx-auto px-6 py-8">
            <div class="max-w-4xl mx-auto">
                
                <!-- Sección Personal -->
                <section class="bg-white rounded-2xl shadow-sm p-8 mb-6">
                    <div class="flex items-start gap-6">
                        <!-- Foto -->
                        <div class="shrink-0">
                            <div v-if="personal.photo" class="w-24 h-24 rounded-full overflow-hidden bg-gray-100">
                                <img :src="personal.photo" :alt="fullName" class="w-full h-full object-cover" />
                            </div>
                            <div v-else class="w-24 h-24 rounded-full bg-gradient-to-br from-[#005aeb] to-[#7B2FF7] flex items-center justify-center">
                                <User class="w-10 h-10 text-white" />
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 mb-1">{{ fullName }}</h1>
                            <p class="text-lg text-[#005aeb] font-medium mb-4">{{ personal.title || 'Profesional' }}</p>
                            
                            <!-- Contacto -->
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <div v-if="personal.email" class="flex items-center gap-2">
                                    <Mail class="w-4 h-4" />
                                    <a :href="`mailto:${personal.email}`" class="hover:text-[#005aeb]">{{ personal.email }}</a>
                                </div>
                                <div v-if="personal.phone" class="flex items-center gap-2">
                                    <Phone class="w-4 h-4" />
                                    <span>{{ personal.phone }}</span>
                                </div>
                                <div v-if="personal.location" class="flex items-center gap-2">
                                    <MapPin class="w-4 h-4" />
                                    <span>{{ personal.location }}</span>
                                </div>
                            </div>

                            <!-- Links sociales -->
                            <div class="flex gap-3 mt-4">
                                <a v-if="personal.linkedin" :href="personal.linkedin" target="_blank" 
                                    class="p-2 bg-gray-100 rounded-lg hover:bg-blue-100 transition-colors">
                                    <Linkedin class="w-5 h-5 text-gray-600" />
                                </a>
                                <a v-if="personal.github" :href="personal.github" target="_blank"
                                    class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    <Github class="w-5 h-5 text-gray-600" />
                                </a>
                                <a v-if="personal.website" :href="personal.website" target="_blank"
                                    class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    <Globe class="w-5 h-5 text-gray-600" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div v-if="personal.summary" class="mt-6 pt-6 border-t border-gray-100">
                        <p class="text-gray-600 leading-relaxed">{{ personal.summary }}</p>
                    </div>
                </section>

                <!-- Experiencia -->
                <section v-if="experience.length > 0" class="bg-white rounded-2xl shadow-sm p-8 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <Briefcase class="w-6 h-6 text-[#005aeb]" />
                        Experiencia Laboral
                    </h2>
                    <div class="space-y-6">
                        <div v-for="(exp, index) in experience" :key="index" class="relative pl-6 border-l-2 border-gray-200">
                            <div class="absolute -left-2 top-0 w-4 h-4 rounded-full bg-[#005aeb]"></div>
                            <h3 class="font-semibold text-gray-900">{{ exp.position }}</h3>
                            <p class="text-[#005aeb] font-medium">{{ exp.company }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ exp.startDate }} - {{ exp.current ? 'Presente' : exp.endDate }}
                            </p>
                            <p v-if="exp.description" class="text-gray-600 mt-2">{{ exp.description }}</p>
                        </div>
                    </div>
                </section>

                <!-- Educación -->
                <section v-if="education.length > 0" class="bg-white rounded-2xl shadow-sm p-8 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <GraduationCap class="w-6 h-6 text-[#005aeb]" />
                        Formación Académica
                    </h2>
                    <div class="space-y-4">
                        <div v-for="(edu, index) in education" :key="index" class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-900">{{ edu.degree }}</h3>
                            <p class="text-[#005aeb]">{{ edu.institution }}</p>
                            <p class="text-sm text-gray-500">{{ edu.startDate }} - {{ edu.endDate }}</p>
                        </div>
                    </div>
                </section>

                <!-- Habilidades -->
                <section v-if="skills.technical?.length > 0 || skills.soft?.length > 0" class="bg-white rounded-2xl shadow-sm p-8 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <Code class="w-6 h-6 text-[#005aeb]" />
                        Habilidades
                    </h2>
                    
                    <div v-if="skills.technical?.length > 0" class="mb-6">
                        <h3 class="font-medium text-gray-700 mb-3">Técnicas</h3>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="skill in skills.technical" :key="skill.name"
                                class="px-3 py-1.5 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                {{ skill.name }}
                            </span>
                        </div>
                    </div>

                    <div v-if="skills.soft?.length > 0">
                        <h3 class="font-medium text-gray-700 mb-3">Blandas</h3>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="skill in skills.soft" :key="skill.name"
                                class="px-3 py-1.5 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                {{ skill.name }}
                            </span>
                        </div>
                    </div>
                </section>

                <!-- Proyectos -->
                <section v-if="projects.length > 0" class="bg-white rounded-2xl shadow-sm p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <FolderOpen class="w-6 h-6 text-[#005aeb]" />
                        Proyectos
                    </h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div v-for="(project, index) in projects" :key="index" 
                            class="p-4 border border-gray-200 rounded-lg hover:border-[#005aeb] transition-colors">
                            <h3 class="font-semibold text-gray-900">{{ project.title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ project.description }}</p>
                            <a v-if="project.url" :href="project.url" target="_blank"
                                class="inline-flex items-center gap-1 text-sm text-[#005aeb] mt-2 hover:underline">
                                Ver proyecto <ExternalLink class="w-3.5 h-3.5" />
                            </a>
                        </div>
                    </div>
                </section>

            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 bg-white py-6 mt-12">
            <div class="container mx-auto px-6 text-center text-sm text-gray-500">
                <p>Generado con <span class="font-semibold text-[#005aeb]">PortafolioAI</span></p>
            </div>
        </footer>
    </div>
</template>
