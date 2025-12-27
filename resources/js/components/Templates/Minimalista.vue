<script setup lang="ts">
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { ref } from 'vue';

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
            technical: Array<any>;
            soft: Array<any>;
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

const ensureUrl = (url: string) => (url?.startsWith('http') ? url : `https://${url}`);

const formatDate = (date: string) => {
    if (!date) return '';
    const [year, month] = date.split('-');
    return `${month}/${year}`;
};
</script>

<template>
    <div class="min-h-screen bg-white">
        <div class="mx-auto max-w-4xl px-4 py-8 md:px-8 md:py-16">
            <!-- Header Minimalista -->
            <header class="mb-8 md:mb-16 border-b border-gray-900 pb-6 md:pb-8">
                <div class="mb-6" v-if="data.personal.photo">
                    <img 
                        :src="data.personal.photo" 
                        :alt="data.personal.name" 
                        class="h-24 w-24 md:h-32 md:w-32 object-cover grayscale"
                    >
                </div>

                <h1
                    class="mb-2 font-serif text-3xl md:text-5xl font-light tracking-tight text-gray-900"
                >
                    {{ data.personal.name || 'Tu Nombre' }}
                </h1>
                <p class="mb-4 md:mb-6 text-lg md:text-xl font-light text-gray-600">
                    {{ data.personal.title || 'Tu Título Profesional' }}
                </p>

                <!-- Contacto minimalista -->
                <div
                    class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-600"
                >
                    <a
                        v-if="data.personal.email"
                        :href="`mailto:${data.personal.email}`"
                        class="hover:text-gray-900"
                    >
                        {{ data.personal.email }}
                    </a>
                    <span v-if="data.personal.phone">{{
                        data.personal.phone
                    }}</span>
                    <span v-if="data.personal.location">{{
                        data.personal.location
                    }}</span>
                    <a
                        v-if="data.personal.website"
                        :href="ensureUrl(data.personal.website)"
                        target="_blank"
                        class="hover:text-gray-900"
                    >
                        Sitio Web
                    </a>
                    <a
                        v-if="data.personal.linkedin"
                        :href="ensureUrl(data.personal.linkedin)"
                        target="_blank"
                        class="hover:text-gray-900"
                    >
                        LinkedIn
                    </a>
                    <a
                        v-if="data.personal.github"
                        :href="ensureUrl(data.personal.github)"
                        target="_blank"
                        class="hover:text-gray-900"
                    >
                        GitHub
                    </a>
                </div>
            </header>

            <!-- Resumen -->
            <section v-if="data.personal.summary" class="mb-16">
                <p class="font-serif text-lg leading-relaxed text-gray-700">
                    {{ data.personal.summary }}
                </p>
            </section>

            <!-- Experiencia -->
            <section v-if="data.experience?.length" class="mb-16">
                <h2 class="mb-8 font-serif text-2xl font-light text-gray-900">
                    Experiencia
                </h2>

                <!-- Carousel Container -->
                <div class="relative group">
                    <!-- Navigation Arrows - Elegant glassmorphism style -->
                    <button 
                        v-if="experienceIndex > 0"
                        @click="prevExperience"
                        class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-gray-200 text-gray-600 shadow-md hover:shadow-lg hover:bg-white hover:text-gray-900 hover:border-gray-400 transition-all duration-300 opacity-0 group-hover:opacity-100"
                    >
                        <ChevronLeft class="h-5 w-5" />
                    </button>
                    <button 
                        v-if="experienceIndex < data.experience.length - 1"
                        @click="nextExperience"
                        class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-gray-200 text-gray-600 shadow-md hover:shadow-lg hover:bg-white hover:text-gray-900 hover:border-gray-400 transition-all duration-300 opacity-0 group-hover:opacity-100"
                    >
                        <ChevronRight class="h-5 w-5" />
                    </button>

                    <!-- Single Experience Display -->
                    <div class="p-6 border border-gray-200 rounded-lg transition-all duration-300">
                        <div class="mb-2 flex items-baseline justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ data.experience[experienceIndex].position }}
                            </h3>
                            <span class="text-sm text-gray-500">
                                {{ formatDate(data.experience[experienceIndex].startDate) }} –
                                {{
                                    data.experience[experienceIndex].current
                                        ? 'Presente'
                                        : formatDate(data.experience[experienceIndex].endDate || '')
                                }}
                            </span>
                        </div>
                        <p class="mb-3 text-gray-600">{{ data.experience[experienceIndex].company }}</p>
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
                            :class="idx === experienceIndex ? 'w-6 h-2 bg-gray-900' : 'w-2 h-2 bg-gray-300 hover:bg-gray-500'"
                        />
                    </div>
                </div>
            </section>

            <!-- Educación -->
            <section v-if="data.education?.length" class="mb-16">
                <h2 class="mb-8 font-serif text-2xl font-light text-gray-900">
                    Educación
                </h2>

                <div class="space-y-6">
                    <div v-for="(edu, index) in data.education" :key="index">
                        <div class="mb-2 flex items-baseline justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ edu.degree }}
                            </h3>
                            <span class="text-sm text-gray-500">
                                {{ formatDate(edu.startDate) }} –
                                {{ formatDate(edu.endDate) }}
                            </span>
                        </div>
                        <p class="text-gray-600">{{ edu.institution }}</p>
                        <p v-if="edu.field" class="text-sm text-gray-500">
                            {{ edu.field }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Habilidades -->
            <section
                v-if="
                    data.skills?.technical?.length || data.skills?.soft?.length
                "
                class="mb-16"
            >
                <h2 class="mb-8 font-serif text-2xl font-light text-gray-900">
                    Habilidades
                </h2>

                <div class="space-y-4">
                    <div v-if="data.skills.technical?.length">
                        <h3
                            class="mb-2 text-sm font-medium tracking-wide text-gray-500 uppercase"
                        >
                            Técnicas
                        </h3>
                        <p class="leading-relaxed text-gray-700">
                            {{ data.skills.technical.map((s: any) => s.name).join(' • ') }}
                        </p>
                    </div>

                    <div v-if="data.skills.soft?.length">
                        <h3
                            class="mb-2 text-sm font-medium tracking-wide text-gray-500 uppercase"
                        >
                            Competencias
                        </h3>
                        <p class="leading-relaxed text-gray-700">
                            {{ data.skills.soft.map((s: any) => s.name).join(' • ') }}
                        </p>

                    </div>
                </div>
            </section>

            <!-- Proyectos -->
            <section v-if="data.projects?.length" class="mb-16">
                <h2 class="mb-8 font-serif text-2xl font-light text-gray-900">
                    Proyectos
                </h2>

                <!-- Carousel Container -->
                <div class="relative group">
                    <!-- Navigation Arrows - Elegant glassmorphism style -->
                    <button 
                        v-if="projectIndex > 0"
                        @click="prevProject"
                        class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-gray-200 text-gray-600 shadow-md hover:shadow-lg hover:bg-white hover:text-gray-900 hover:border-gray-400 transition-all duration-300 opacity-0 group-hover:opacity-100"
                    >
                        <ChevronLeft class="h-5 w-5" />
                    </button>
                    <button 
                        v-if="projectIndex < data.projects.length - 1"
                        @click="nextProject"
                        class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-gray-200 text-gray-600 shadow-md hover:shadow-lg hover:bg-white hover:text-gray-900 hover:border-gray-400 transition-all duration-300 opacity-0 group-hover:opacity-100"
                    >
                        <ChevronRight class="h-5 w-5" />
                    </button>

                    <!-- Single Project Display -->
                    <div class="transition-all duration-300">
                        <img 
                            v-if="data.projects[projectIndex].image" 
                            :src="data.projects[projectIndex].image" 
                            :alt="data.projects[projectIndex].name" 
                            class="mb-4 w-full h-64 object-cover grayscale block"
                        >
                        <h3 class="mb-2 text-lg font-medium text-gray-900">
                            {{ data.projects[projectIndex].name }}
                        </h3>
                        <p class="mb-2 leading-relaxed text-gray-700">
                            {{ data.projects[projectIndex].description }}
                        </p>
                        <p
                            v-if="data.projects[projectIndex].technologies?.length"
                            class="text-sm text-gray-500"
                        >
                            {{ data.projects[projectIndex].technologies.join(' • ') }}
                        </p>
                        <a
                            v-if="data.projects[projectIndex].url"
                            :href="data.projects[projectIndex].url"
                            target="_blank"
                            class="text-sm text-gray-900 underline"
                        >
                            Ver proyecto
                        </a>
                    </div>

                    <!-- Dots Indicator - Modern pill style -->
                    <div v-if="data.projects.length > 1" class="flex justify-center items-center gap-1.5 mt-6">
                        <button 
                            v-for="(_, idx) in data.projects" 
                            :key="idx"
                            @click="projectIndex = idx"
                            class="rounded-full transition-all duration-300"
                            :class="idx === projectIndex ? 'w-6 h-2 bg-gray-900' : 'w-2 h-2 bg-gray-300 hover:bg-gray-500'"
                        />
                    </div>
                </div>
            </section>

            <!-- Idiomas -->
            <section v-if="data.languages?.length">
                <h2 class="mb-8 font-serif text-2xl font-light text-gray-900">
                    Idiomas
                </h2>

                <div class="grid grid-cols-2 gap-4">
                    <div v-for="(lang, index) in data.languages" :key="index">
                        <span class="text-gray-900">{{ lang.name }}</span>
                        <span class="text-gray-500"> — {{ lang.level }}</span>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<style scoped>
/* Fuente serif para títulos */
@import url('https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;600&display=swap');

.font-serif {
    font-family: 'Crimson Text', serif;
}
</style>
