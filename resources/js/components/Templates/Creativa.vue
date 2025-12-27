<script setup lang="ts">
import { Heart, Palette, Sparkles, Star, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps<{
    data: {
        personal: {
            name: string;
            title: string;
            email: string;
            phone: string;
            location: string;
            website?: string;
            linkedin?: string;
            github?: string;
            photo?: string;
            summary: string;
        };
        experience: Array<{
            company: string;
            position: string;
            startDate: string;
            endDate?: string;
            current?: boolean;
            description: string;
        }>;
        education: Array<any>;
        skills: {
            technical: string[];
            soft: string[];
        };
        projects: Array<any>;
        certifications: Array<any>;
        languages: Array<any>;
    };
    theme?: any;
}>();

// Carousel state
const projectIndex = ref(0);
const experienceIndex = ref(0);

const nextProject = () => {
    if (props.data.projects && projectIndex.value < props.data.projects.length - 1) {
        projectIndex.value++;
    }
};
const prevProject = () => {
    if (projectIndex.value > 0) {
        projectIndex.value--;
    }
};
const nextExperience = () => {
    if (props.data.experience && experienceIndex.value < props.data.experience.length - 1) {
        experienceIndex.value++;
    }
};
const prevExperience = () => {
    if (experienceIndex.value > 0) {
        experienceIndex.value--;
    }
};

const formatSkill = (skill: string | any) => {
    if (typeof skill === 'string') {
        try {
            if (skill.trim().startsWith('{')) {
                const parsed = JSON.parse(skill);
                return parsed.name || skill;
            }
        } catch (e) {
            return skill;
        }
    }
    return skill?.name || skill;
};

const ensureUrl = (url: string) => (url?.startsWith('http') ? url : `https://${url}`);

const formatDate = (date: string) => {
    if (!date) return '';
    const [year, month] = date.split('-');
    return `${month}/${year}`;
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50">
        <!-- Header Creativo -->
        <header
            class="relative overflow-hidden bg-gradient-to-r from-purple-600 to-pink-500 px-4 py-8 md:px-8 md:py-16 text-white"
        >
            <!-- Elementos decorativos -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-10 left-10">
                    <Palette class="h-24 w-24" />
                </div>
                <div class="absolute right-10 bottom-10">
                    <Sparkles class="h-24 w-24" />
                </div>
            </div>

            <div class="relative mx-auto max-w-4xl text-center">
                <!-- Foto de perfil circular -->
                <div v-if="data.personal.photo" class="mb-6 inline-block">
                    <div class="relative">
                        <div
                            class="h-24 w-24 md:h-32 md:w-32 overflow-hidden rounded-full border-4 border-white shadow-2xl"
                        >
                            <img
                                :src="data.personal.photo"
                                :alt="data.personal.name"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <div
                            class="absolute -right-2 -bottom-2 rounded-full bg-yellow-400 p-2"
                        >
                            <Star class="h-4 w-4 text-white" />
                        </div>
                    </div>
                </div>

                <h1 class="mb-2 md:mb-4 text-3xl md:text-5xl font-bold">
                    {{ data.personal.name || 'Tu Nombre' }}
                </h1>
                <p class="mb-4 md:mb-6 text-lg md:text-2xl text-purple-100">
                    {{ data.personal.title || 'Tu Título Creativo' }}
                </p>

                <!-- Contacto estilo creativo -->
                <div class="flex flex-wrap justify-center gap-2 md:gap-4">
                    <a
                        v-if="data.personal.email"
                        :href="`mailto:${data.personal.email}`"
                        class="rounded-full bg-white/20 px-4 py-2 md:px-6 md:py-3 text-sm md:text-base backdrop-blur-sm transition hover:bg-white/30"
                    >
                        ✉️ {{ data.personal.email }}
                    </a>
                    <div
                        v-if="data.personal.phone"
                        class="rounded-full bg-white/20 px-4 py-2 md:px-6 md:py-3 text-sm md:text-base backdrop-blur-sm"
                    >
                        📞 {{ data.personal.phone }}
                    </div>
                    <div
                        v-if="data.personal.location"
                        class="rounded-full bg-white/20 px-4 py-2 md:px-6 md:py-3 text-sm md:text-base backdrop-blur-sm"
                    >
                        📍 {{ data.personal.location }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main class="mx-auto max-w-4xl px-4 py-8 md:px-8 md:py-12">
            <!-- Resumen Creativo -->
            <section v-if="data.personal.summary" class="mb-12">
                <div class="relative rounded-3xl bg-white p-8 shadow-xl">
                    <div
                        class="absolute -top-4 left-8 rounded-full bg-pink-500 p-3"
                    >
                        <Heart class="h-6 w-6 text-white" />
                    </div>
                    <h2 class="mb-4 text-2xl font-bold text-gray-900">
                        Mi Historia
                    </h2>
                    <p class="text-lg leading-relaxed text-gray-700">
                        {{ data.personal.summary }}
                    </p>
                </div>
            </section>

            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Experiencia Creativa -->
                <section v-if="data.experience?.length" class="lg:col-span-2">
                    <h2
                        class="mb-8 text-center text-3xl font-bold text-gray-900"
                    >
                        🎨 Experiencia Profesional
                    </h2>

                    <!-- Carousel Container -->
                    <div class="relative group">
                        <!-- Navigation Arrows - Elegant glassmorphism style -->
                        <button 
                            v-if="experienceIndex > 0"
                            @click="prevExperience"
                            class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-purple-200 text-purple-500 shadow-md hover:shadow-lg hover:bg-white hover:text-purple-600 hover:border-purple-400 transition-all duration-300 opacity-0 group-hover:opacity-100"
                        >
                            <ChevronLeft class="h-5 w-5" />
                        </button>
                        <button 
                            v-if="experienceIndex < data.experience.length - 1"
                            @click="nextExperience"
                            class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-purple-200 text-purple-500 shadow-md hover:shadow-lg hover:bg-white hover:text-purple-600 hover:border-purple-400 transition-all duration-300 opacity-0 group-hover:opacity-100"
                        >
                            <ChevronRight class="h-5 w-5" />
                        </button>

                        <!-- Single Experience Display -->
                        <div
                            class="relative rounded-2xl bg-white p-6 shadow-lg transition-all duration-300"
                            :class="
                                experienceIndex % 2 === 0
                                    ? 'border-l-4 border-purple-500'
                                    : 'border-l-4 border-pink-500'
                            "
                        >
                            <div class="mb-3 flex items-start justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        {{ data.experience[experienceIndex].position }}
                                    </h3>
                                    <p class="text-lg text-purple-600">
                                        {{ data.experience[experienceIndex].company }}
                                    </p>
                                </div>
                                <span
                                    class="rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-1 text-sm text-white"
                                >
                                    {{ formatDate(data.experience[experienceIndex].startDate) }} –
                                    {{
                                        data.experience[experienceIndex].current
                                            ? 'Ahora'
                                            : formatDate(data.experience[experienceIndex].endDate || '')
                                    }}
                                </span>
                            </div>
                            <p class="leading-relaxed text-gray-700">
                                {{ data.experience[experienceIndex].description }}
                            </p>
                        </div>

                        <!-- Dots Indicator - Modern pill style -->
                        <div v-if="data.experience.length > 1" class="flex justify-center items-center gap-1.5 mt-6">
                            <button 
                                v-for="(_, idx) in data.experience" 
                                :key="idx"
                                @click="experienceIndex = idx"
                                class="rounded-full transition-all duration-300"
                                :class="idx === experienceIndex ? 'w-6 h-2 bg-gradient-to-r from-purple-500 to-pink-500' : 'w-2 h-2 bg-gray-300 hover:bg-purple-300'"
                            />
                        </div>
                    </div>
                </section>

                <!-- Habilidades Creativas -->
                <section
                    v-if="
                        data.skills?.technical?.length ||
                        data.skills?.soft?.length
                    "
                >
                    <div
                        class="rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 p-8 text-white"
                    >
                        <h2 class="mb-6 text-2xl font-bold">
                            Mis Superpoderes
                        </h2>

                        <div v-if="data.skills.technical?.length" class="mb-6">
                            <h3 class="mb-3 text-lg font-semibold">
                                💻 Habilidades Técnicas
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="skill in data.skills.technical"
                                    :key="skill"
                                    class="rounded-full bg-white/20 px-4 py-2 text-sm backdrop-blur-sm"
                                >
                                    {{ formatSkill(skill) }}
                                </span>
                            </div>
                        </div>

                        <div v-if="data.skills.soft?.length">
                            <h3 class="mb-3 text-lg font-semibold">
                                🌟 Habilidades Blandas
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="skill in data.skills.soft"
                                    :key="skill"
                                    class="rounded-full bg-white/20 px-4 py-2 text-sm backdrop-blur-sm"
                                >
                                    {{ formatSkill(skill) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Proyectos Creativos -->
                <section v-if="data.projects?.length">
                    <div class="rounded-2xl bg-white p-8 shadow-lg">
                        <h2 class="mb-6 text-2xl font-bold text-gray-900">
                            ✨ Proyectos Destacados
                        </h2>

                        <!-- Carousel Container -->
                        <div class="relative group">
                            <!-- Navigation Arrows - Elegant glassmorphism style -->
                            <button 
                                v-if="projectIndex > 0"
                                @click="prevProject"
                                class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-purple-200 text-purple-500 shadow-md hover:shadow-lg hover:bg-white hover:text-purple-600 hover:border-purple-400 transition-all duration-300 opacity-0 group-hover:opacity-100"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button 
                                v-if="projectIndex < data.projects.length - 1"
                                @click="nextProject"
                                class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-purple-200 text-purple-500 shadow-md hover:shadow-lg hover:bg-white hover:text-purple-600 hover:border-purple-400 transition-all duration-300 opacity-0 group-hover:opacity-100"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>

                            <!-- Single Project Display -->
                            <div class="rounded-xl border-2 border-dashed border-purple-200 p-6 transition-all duration-300 hover:border-purple-400">
                                <img 
                                    v-if="data.projects[projectIndex].image" 
                                    :src="data.projects[projectIndex].image" 
                                    :alt="data.projects[projectIndex].name" 
                                    class="mb-4 h-48 w-full rounded-lg object-cover"
                                />
                                <h3 class="text-lg font-bold text-gray-900">
                                    {{ data.projects[projectIndex].name }}
                                </h3>
                                <p class="mb-3 text-gray-600">
                                    {{ data.projects[projectIndex].description }}
                                </p>
                                <div
                                    v-if="data.projects[projectIndex].technologies?.length"
                                    class="flex flex-wrap gap-1"
                                >
                                    <span
                                        v-for="tech in data.projects[projectIndex].technologies"
                                        :key="tech"
                                        class="rounded-full bg-purple-100 px-2 py-1 text-xs text-purple-700"
                                    >
                                        {{ tech }}
                                    </span>
                                </div>
                            </div>

                            <!-- Dots Indicator - Modern pill style -->
                            <div v-if="data.projects.length > 1" class="flex justify-center items-center gap-1.5 mt-6">
                                <button 
                                    v-for="(_, idx) in data.projects" 
                                    :key="idx"
                                    @click="projectIndex = idx"
                                    class="rounded-full transition-all duration-300"
                                    :class="idx === projectIndex ? 'w-6 h-2 bg-gradient-to-r from-purple-500 to-pink-500' : 'w-2 h-2 bg-gray-300 hover:bg-purple-300'"
                                />
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Educación -->
                <section v-if="data.education?.length" class="lg:col-span-2">
                    <div class="rounded-2xl bg-white p-8 shadow-lg">
                        <h2 class="mb-6 text-2xl font-bold text-gray-900">
                            🎓 Trayectoria Académica
                        </h2>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div
                                v-for="(edu, index) in data.education"
                                :key="index"
                                class="rounded-xl bg-gradient-to-r from-purple-50 to-pink-50 p-6"
                            >
                                <h3 class="font-bold text-gray-900">
                                    {{ edu.degree }}
                                </h3>
                                <p class="text-purple-600">
                                    {{ edu.institution }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ formatDate(edu.startDate) }} -
                                    {{ formatDate(edu.endDate) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- Footer Creativo -->
        <footer class="border-t border-purple-200 bg-white py-8 text-center">
            <div class="mb-4 flex justify-center space-x-4">
                <a
                    v-if="data.personal.website"
                    :href="ensureUrl(data.personal.website)"
                    target="_blank"
                    class="rounded-full bg-purple-100 p-3 text-purple-600 transition hover:bg-purple-200"
                >
                    🌐 Sitio Web
                </a>
                <a
                    v-if="data.personal.linkedin"
                    :href="ensureUrl(data.personal.linkedin)"
                    target="_blank"
                    class="rounded-full bg-blue-100 p-3 text-blue-600 transition hover:bg-blue-200"
                >
                    💼 LinkedIn
                </a>
                <a
                    v-if="data.personal.github"
                    :href="ensureUrl(data.personal.github)"
                    target="_blank"
                    class="rounded-full bg-gray-100 p-3 text-gray-600 transition hover:bg-gray-200"
                >
                    💻 GitHub
                </a>
            </div>
            <p class="text-gray-600">
                Creado con 💜 • {{ new Date().getFullYear() }}
            </p>
        </footer>
    </div>
</template>

<style scoped>
.transition {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-scale:hover {
    transform: scale(1.05);
}
</style>
