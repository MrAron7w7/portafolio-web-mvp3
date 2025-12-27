<script setup lang="ts">
import { Award, BookOpen, GraduationCap, Users, ChevronLeft, ChevronRight } from 'lucide-vue-next';
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
            technical: string[];
            soft: string[];
        };
        projects: Array<any>;
        certifications: Array<any>;
        languages: Array<any>;
        publications?: Array<{
            title: string;
            journal: string;
            date: string;
            link?: string;
        }>;
        research?: Array<{
            title: string;
            institution: string;
            description: string;
            period: string;
        }>;
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

const formatDate = (date: string) => {
    if (!date) return '';
    const [year, month] = date.split('-');
    const months = [
        'Ene',
        'Feb',
        'Mar',
        'Abr',
        'May',
        'Jun',
        'Jul',
        'Ago',
        'Sep',
        'Oct',
        'Nov',
        'Dic',
    ];
    return `${months[parseInt(month) - 1]} ${year}`;
};
</script>

<template>
    <div class="min-h-screen bg-white">
        <div class="mx-auto max-w-5xl px-4 py-8 md:px-8 md:py-12">
            <!-- Header Académico -->
            <header class="mb-8 md:mb-12 border-b border-gray-300 pb-6 md:pb-8">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 md:gap-0">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6">
                        <div
                            v-if="data.personal.photo"
                            class="h-24 w-24 md:h-32 md:w-32 shrink-0 overflow-hidden rounded-full border-4 border-gray-100 shadow-md"
                        >
                            <img
                                :src="data.personal.photo"
                                :alt="data.personal.name"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <div class="text-center sm:text-left">
                            <h1 class="mb-2 text-2xl md:text-4xl font-bold text-gray-900">
                                {{ data.personal.name || 'Tu Nombre' }}
                            </h1>
                            <p class="mb-2 md:mb-4 text-lg md:text-xl text-gray-600">
                                {{
                                    data.personal.title || 'Tu Título Académico'
                                }}
                            </p>
                        </div>
                    </div>
                    <div class="text-center md:text-right mt-2 md:mt-0">
                        <div class="space-y-1 text-sm text-gray-600">
                            <div
                                v-if="data.personal.email"
                                class="flex items-center justify-center md:justify-end"
                            >
                                <span>{{ data.personal.email }}</span>
                            </div>
                            <div
                                v-if="data.personal.phone"
                                class="flex items-center justify-center md:justify-end"
                            >
                                <span>{{ data.personal.phone }}</span>
                            </div>
                            <div
                                v-if="data.personal.location"
                                class="flex items-center justify-center md:justify-end"
                            >
                                <span>{{ data.personal.location }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-8">
                <!-- Columna Principal -->
                <div class="md:col-span-3">
                    <!-- Resumen Académico -->
                    <section v-if="data.personal.summary" class="mb-8">
                        <div class="mb-4 flex items-start">
                            <BookOpen class="mt-1 mr-3 h-5 w-5 text-blue-600" />
                            <h2 class="text-2xl font-bold text-gray-900">
                                Perfil Académico
                            </h2>
                        </div>
                        <p class="leading-relaxed text-gray-700">
                            {{ data.personal.summary }}
                        </p>
                    </section>
                    <!-- Experiencia Académica -->
                    <section v-if="data.experience?.length" class="mb-8">
                        <div class="mb-4 flex items-start">
                            <Users class="mt-1 mr-3 h-5 w-5 text-blue-600" />
                            <h2 class="text-2xl font-bold text-gray-900">
                                Experiencia Académica
                            </h2>
                        </div>

                        <!-- Carousel Container -->
                        <div class="relative group">
                            <!-- Navigation Arrows - Elegant glassmorphism style -->
                            <button 
                                v-if="experienceIndex > 0"
                                @click="prevExperience"
                                class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-blue-200 text-blue-600 shadow-md hover:shadow-lg hover:bg-white hover:border-blue-400 transition-all duration-300 opacity-0 group-hover:opacity-100"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button 
                                v-if="experienceIndex < data.experience.length - 1"
                                @click="nextExperience"
                                class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-blue-200 text-blue-600 shadow-md hover:shadow-lg hover:bg-white hover:border-blue-400 transition-all duration-300 opacity-0 group-hover:opacity-100"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>

                            <!-- Single Experience Display -->
                            <div class="border-l-4 border-blue-600 pl-4 py-4 bg-gray-50 rounded-r-lg transition-all duration-300">
                                <div class="mb-2 flex items-start justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ data.experience[experienceIndex].position }}
                                    </h3>
                                    <span class="text-sm whitespace-nowrap text-gray-500">
                                        {{ formatDate(data.experience[experienceIndex].startDate) }} –
                                        {{
                                            data.experience[experienceIndex].current
                                                ? 'Presente'
                                                : formatDate(data.experience[experienceIndex].endDate || '')
                                        }}
                                    </span>
                                </div>
                                <p class="mb-2 font-medium text-blue-600">
                                    {{ data.experience[experienceIndex].company }}
                                </p>
                                <p class="text-gray-700">
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
                                    :class="idx === experienceIndex ? 'w-6 h-2 bg-blue-600' : 'w-2 h-2 bg-gray-300 hover:bg-blue-300'"
                                />
                            </div>
                        </div>
                    </section>
                    
                    <!-- Proyectos Académicos -->
                    <section v-if="data.projects?.length" class="mb-8">
                        <div class="mb-4 flex items-start">
                            <Users class="mt-1 mr-3 h-5 w-5 text-blue-600" />
                            <h2 class="text-2xl font-bold text-gray-900">
                                Proyectos Destacados
                            </h2>
                        </div>

                        <!-- Carousel Container -->
                        <div class="relative group">
                            <!-- Navigation Arrows - Elegant glassmorphism style -->
                            <button 
                                v-if="projectIndex > 0"
                                @click="prevProject"
                                class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-blue-200 text-blue-600 shadow-md hover:shadow-lg hover:bg-white hover:border-blue-400 transition-all duration-300 opacity-0 group-hover:opacity-100"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button 
                                v-if="projectIndex < data.projects.length - 1"
                                @click="nextProject"
                                class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-blue-200 text-blue-600 shadow-md hover:shadow-lg hover:bg-white hover:border-blue-400 transition-all duration-300 opacity-0 group-hover:opacity-100"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>

                            <!-- Single Project Display -->
                            <div class="rounded-lg border border-gray-200 p-6 transition-all duration-300 hover:border-blue-300 hover:shadow-md">
                                <img
                                    v-if="data.projects[projectIndex].image"
                                    :src="data.projects[projectIndex].image"
                                    :alt="data.projects[projectIndex].name"
                                    class="mb-4 h-40 w-full rounded-md object-cover"
                                />
                                <h3 class="mb-2 font-bold text-gray-900">
                                    {{ data.projects[projectIndex].name }}
                                </h3>
                                <p class="mb-3 text-sm text-gray-700">
                                    {{ data.projects[projectIndex].description }}
                                </p>
                                <div
                                    v-if="data.projects[projectIndex].technologies?.length"
                                    class="flex flex-wrap gap-1"
                                >
                                    <span
                                        v-for="tech in data.projects[projectIndex].technologies"
                                        :key="tech"
                                        class="rounded bg-blue-50 px-2 py-1 text-xs text-blue-700"
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
                                    :class="idx === projectIndex ? 'w-6 h-2 bg-blue-600' : 'w-2 h-2 bg-gray-300 hover:bg-blue-300'"
                                />
                            </div>
                        </div>
                    </section>

                    <!-- Investigación -->
                    <section v-if="data.research?.length" class="mb-8">
                        <div class="mb-4 flex items-start">
                            <Award class="mt-1 mr-3 h-5 w-5 text-blue-600" />
                            <h2 class="text-2xl font-bold text-gray-900">
                                Investigación
                            </h2>
                        </div>
                        <div class="space-y-4">
                            <div
                                v-for="(research, index) in data.research"
                                :key="index"
                                class="rounded-lg bg-gray-50 p-4"
                            >
                                <h3 class="mb-1 font-semibold text-gray-900">
                                    {{ research.title }}
                                </h3>
                                <p class="mb-2 text-sm text-blue-600">
                                    {{ research.institution }} •
                                    {{ research.period }}
                                </p>
                                <p class="text-sm text-gray-700">
                                    {{ research.description }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Publicaciones -->
                    <section v-if="data.publications?.length" class="mb-8">
                        <h2 class="mb-4 text-2xl font-bold text-gray-900">
                            Publicaciones
                        </h2>
                        <div class="space-y-3">
                            <div
                                v-for="(pub, index) in data.publications"
                                :key="index"
                                class="border-b border-gray-200 pb-3"
                            >
                                <h3 class="font-semibold text-gray-900">
                                    {{ pub.title }}
                                </h3>
                                <p class="text-sm text-blue-600">
                                    {{ pub.journal }} • {{ pub.date }}
                                </p>
                                <a
                                    v-if="pub.link"
                                    :href="pub.link"
                                    target="_blank"
                                    class="text-sm text-gray-600 hover:text-blue-600"
                                >
                                    Enlace a la publicación
                                </a>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Sidebar -->
                <div class="col-span-1">
                    <!-- Educación -->
                    <section v-if="data.education?.length" class="mb-8">
                        <div class="mb-4 flex items-center">
                            <GraduationCap class="mr-2 h-5 w-5 text-blue-600" />
                            <h2 class="text-xl font-bold text-gray-900">
                                Educación
                            </h2>
                        </div>
                        <div class="space-y-4">
                            <div
                                v-for="(edu, index) in data.education"
                                :key="index"
                            >
                                <h3 class="text-sm font-semibold text-gray-900">
                                    {{ edu.degree }}
                                </h3>
                                <p class="text-xs text-blue-600">
                                    {{ edu.institution }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ formatDate(edu.startDate) }} -
                                    {{ formatDate(edu.endDate) }}
                                </p>
                                <p
                                    v-if="edu.field"
                                    class="text-xs text-gray-600"
                                >
                                    {{ edu.field }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Habilidades Académicas -->
                    <section v-if="data.skills?.technical?.length" class="mb-8">
                        <h2 class="mb-4 text-xl font-bold text-gray-900">
                            Competencias
                        </h2>
                        <div class="space-y-2">
                            <div
                                v-for="skill in data.skills.technical"
                                :key="skill"
                                class="flex items-center"
                            >
                                <div
                                    class="mr-2 h-2 w-2 rounded-full bg-blue-600"
                                ></div>
                                <span class="text-sm text-gray-700">{{
                                    skill
                                }}</span>
                            </div>
                        </div>
                    </section>

                    <!-- Idiomas -->
                    <section v-if="data.languages?.length" class="mb-8">
                        <h2 class="mb-4 text-xl font-bold text-gray-900">
                            Idiomas
                        </h2>
                        <div class="space-y-2">
                            <div
                                v-for="(lang, index) in data.languages"
                                :key="index"
                                class="flex justify-between"
                            >
                                <span class="text-sm text-gray-700">{{
                                    lang.name
                                }}</span>
                                <span class="text-sm text-gray-500">{{
                                    lang.level
                                }}</span>
                            </div>
                        </div>
                    </section>

                    <!-- Certificaciones -->
                    <section v-if="data.certifications?.length" class="mb-8">
                        <h2 class="mb-4 text-xl font-bold text-gray-900">
                            Certificaciones
                        </h2>
                        <div class="space-y-2">
                            <div
                                v-for="(cert, index) in data.certifications"
                                :key="index"
                            >
                                <h3 class="text-sm font-semibold text-gray-900">
                                    {{ cert.name }}
                                </h3>
                                <p class="text-xs text-gray-500">
                                    {{ cert.issuer }} • {{ cert.date }}
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&display=swap');

.font-serif {
    font-family: 'Merriweather', serif;
}
</style>
