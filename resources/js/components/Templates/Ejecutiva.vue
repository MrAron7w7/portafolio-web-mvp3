<script setup lang="ts">
import { Award, Briefcase, Target, TrendingUp, Users, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { ref } from 'vue';
import ImageGallery from '../Shared/ImageGallery.vue';

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
            achievements?: string[];
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

// Gallery state
const galleryOpen = ref(false);
const galleryImages = ref<string[]>([]);
const galleryStartIndex = ref(0);

function openGallery(images: string[], startIdx: number = 0) {
  galleryImages.value = images;
  galleryStartIndex.value = startIdx;
  galleryOpen.value = true;
}

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

const calculateDuration = (
    start: string,
    end: string | undefined,
    current: boolean | undefined,
) => {
    if (!start) return '';

    const startDate = new Date(start);
    const endDate = current ? new Date() : end ? new Date(end) : new Date();

    const months =
        (endDate.getFullYear() - startDate.getFullYear()) * 12 +
        (endDate.getMonth() - startDate.getMonth());

    const years = Math.floor(months / 12);
    const remainingMonths = months % 12;

    if (years > 0 && remainingMonths > 0) {
        return `${years}a ${remainingMonths}m`;
    } else if (years > 0) {
        return `${years}a`;
    } else {
        return `${remainingMonths}m`;
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 portfolio-container">
        <!-- Header Ejecutivo -->
        <header class="bg-gray-900 text-white">
            <div class="mx-auto max-w-6xl px-4 py-8 md:px-8 md:py-12">
                <div class="header-content-wrapper">
                    <!-- Información Principal -->
                    <div class="flex flex-1 flex-col gap-4">
                         <div class="header-profile-section">
                            <div v-if="data.personal.photo" class="h-24 w-24 md:h-32 md:w-32 shrink-0 overflow-hidden rounded-lg border-2 border-gray-700 bg-gray-800">
                                <img :src="data.personal.photo" :alt="data.personal.name" class="h-full w-full object-cover" />
                            </div>
                            <div class="header-text">
                                <h1 class="mb-2 font-bold header-name">
                                    {{ data.personal.name || 'Tu Nombre' }}
                                </h1>
                                <p class="text-gray-300 header-title">
                                    {{ data.personal.title || 'Director Ejecutivo' }}
                                </p>
                            </div>
                         </div>
                        <!-- Resumen Ejecutivo -->
                        <p
                            v-if="data.personal.summary"
                            class="max-w-2xl leading-relaxed text-gray-400 header-summary"
                        >
                            {{ data.personal.summary }}
                        </p>
                    </div>

                    <!-- Contacto Ejecutivo -->
                    <div class="header-contact">
                        <div class="space-y-2 text-sm">
                            <div
                                v-if="data.personal.email"
                                class="flex items-center contact-item-wrapper"
                            >
                                <span class="text-gray-300">{{
                                    data.personal.email
                                }}</span>
                            </div>
                            <div
                                v-if="data.personal.phone"
                                class="flex items-center contact-item-wrapper"
                            >
                                <span class="text-gray-300">{{
                                    data.personal.phone
                                }}</span>
                            </div>
                            <div
                                v-if="data.personal.location"
                                class="flex items-center contact-item-wrapper"
                            >
                                <span class="text-gray-300">{{
                                    data.personal.location
                                }}</span>
                            </div>
                            <div class="flex items-center social-links-wrapper gap-4 mt-2">
                                <a
                                    v-if="data.personal.linkedin"
                                    :href="ensureUrl(data.personal.linkedin)"
                                    target="_blank"
                                    class="flex items-center text-gray-300 transition-colors hover:text-white"
                                >
                                    <Linkedin class="mr-2 h-4 w-4" />
                                    <span>LinkedIn</span>
                                </a>
                                <a
                                    v-if="data.personal.github"
                                    :href="ensureUrl(data.personal.github)"
                                    target="_blank"
                                    class="flex items-center text-gray-300 transition-colors hover:text-white"
                                >
                                    <Github class="mr-2 h-4 w-4" />
                                    <span>GitHub</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main class="mx-auto max-w-6xl px-4 py-8 md:px-8 md:py-12">
            <div class="main-content-grid">
                <!-- Columna Principal -->
                <div class="lg:col-span-2">
                    <!-- Experiencia Ejecutiva -->
                    <section v-if="data.experience?.length" class="mb-8">
                        <div class="mb-6 flex items-center">
                            <Briefcase class="mr-3 h-6 w-6 text-gray-700" />
                            <h2 class="text-2xl font-bold text-gray-900">
                                Experiencia Ejecutiva
                            </h2>
                        </div>

                        <!-- Carousel Container -->
                        <div class="relative group">
                            <!-- Navigation Arrows - Elegant glassmorphism style -->
                            <button 
                                v-if="experienceIndex > 0"
                                @click="prevExperience"
                                class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-blue-200 text-blue-600 shadow-md hover:shadow-lg hover:bg-white hover:border-blue-400 transition-all duration-300 md:opacity-0 group-hover:opacity-100"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button 
                                v-if="experienceIndex < data.experience.length - 1"
                                @click="nextExperience"
                                class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-blue-200 text-blue-600 shadow-md hover:shadow-lg hover:bg-white hover:border-blue-400 transition-all duration-300 md:opacity-0 group-hover:opacity-100"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>

                            <!-- Experience Display (2 at a time) -->
                            <div class="carousel-card-grid">
                                <div
                                    v-for="(exp, index) in data.experience.slice(experienceIndex, experienceIndex + 2)"
                                    :key="index"
                                    class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all duration-300"
                                >
                                    <div class="mb-3 flex items-start justify-between">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">
                                                {{ exp.position }}
                                            </h3>
                                            <p class="text-lg font-semibold text-blue-600">
                                                {{ exp.company }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="block text-sm font-medium text-gray-700">
                                                {{ formatDate(exp.startDate) }} –
                                                {{
                                                    exp.current
                                                        ? 'Presente'
                                                        : formatDate(exp.endDate || '')
                                                }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ calculateDuration(exp.startDate, exp.endDate, exp.current) }}
                                            </span>
                                        </div>
                                    </div>

                                    <p class="mb-4 leading-relaxed text-gray-700">
                                        {{ exp.description }}
                                    </p>

                                    <!-- Logros -->
                                    <div
                                        v-if="exp.achievements?.length"
                                        class="rounded-lg bg-gray-50 p-4"
                                    >
                                        <h4 class="mb-2 flex items-center font-semibold text-gray-900">
                                            <Target class="mr-2 h-4 w-4 text-green-600" />
                                            Logros Clave
                                        </h4>
                                        <ul class="space-y-1">
                                            <li
                                                v-for="(achievement, achIndex) in exp.achievements"
                                                :key="achIndex"
                                                class="flex items-start text-sm text-gray-700"
                                            >
                                                <span class="mr-2 text-green-500">•</span>
                                                {{ achievement }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
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

                    <!-- Proyectos Estratégicos -->
                    <section v-if="data.projects?.length" class="mb-8">
                        <div class="mb-6 flex items-center">
                            <TrendingUp class="mr-3 h-6 w-6 text-gray-700" />
                            <h2 class="text-2xl font-bold text-gray-900">
                                Proyectos Estratégicos
                            </h2>
                        </div>

                        <!-- Carousel Container -->
                        <div class="relative group">
                            <!-- Navigation Arrows - Elegant glassmorphism style -->
                            <button 
                                v-if="projectIndex > 0"
                                @click="prevProject"
                                class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-blue-200 text-blue-600 shadow-md hover:shadow-lg hover:bg-white hover:border-blue-400 transition-all duration-300 md:opacity-0 group-hover:opacity-100"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button 
                                v-if="projectIndex < data.projects.length - 1"
                                @click="nextProject"
                                class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-blue-200 text-blue-600 shadow-md hover:shadow-lg hover:bg-white hover:border-blue-400 transition-all duration-300 md:opacity-0 group-hover:opacity-100"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>

                            <!-- Projects Display (2 at a time) -->
                            <div class="carousel-card-grid">
                                <div
                                    v-for="(project, index) in data.projects.slice(projectIndex, projectIndex + 2)"
                                    :key="index"
                                    class="rounded-lg border border-gray-200 bg-white p-6 transition-all duration-300 hover:shadow-md"
                                >
                                    <!-- Multi-image gallery grid -->
                                    <div v-if="project.images && project.images.length > 1" class="gallery-grid mb-4">
                                        <img 
                                            v-for="(img, imgIdx) in project.images.slice(0, 4)" 
                                            :key="imgIdx" 
                                            :src="img" 
                                            :alt="`${project.name} - ${Number(imgIdx) + 1}`"
                                            class="gallery-thumbnail cursor-pointer"
                                            @click="openGallery(project.images, Number(imgIdx))"
                                        />
                                        <div 
                                            v-if="project.images.length > 4" 
                                            class="gallery-more"
                                            @click="openGallery(project.images, 4)"
                                        >
                                            +{{ project.images.length - 4 }}
                                        </div>
                                    </div>
                                    <!-- Single image fallback -->
                                    <img
                                        v-else-if="project.image"
                                        :src="project.image"
                                        :alt="project.name"
                                        class="mb-4 h-48 w-full rounded object-cover cursor-pointer hover:opacity-90 transition-opacity"
                                        @click="openGallery([project.image], 0)"
                                    />
                                    <h3 class="mb-2 font-bold text-gray-900">
                                        {{ project.name }}
                                    </h3>
                                    <p class="mb-3 text-sm text-gray-700">
                                        {{ project.description }}
                                    </p>
                                    <div
                                        v-if="project.technologies?.length"
                                        class="flex flex-wrap gap-1"
                                    >
                                        <span
                                            v-for="tech in project.technologies"
                                            :key="tech"
                                            class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-700"
                                        >
                                            {{ tech }}
                                        </span>
                                    </div>
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

                    <!-- Image Gallery Modal -->
                    <ImageGallery 
                        v-if="galleryOpen" 
                        :images="galleryImages" 
                        :initialIndex="galleryStartIndex"
                        @close="galleryOpen = false"
                    />
                </div>

                <!-- Sidebar -->
                <div>
                    <!-- Competencias Ejecutivas -->
                    <section
                        v-if="
                            data.skills?.technical?.length ||
                            data.skills?.soft?.length
                        "
                        class="mb-8"
                    >
                        <div
                            class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                        >
                            <div class="mb-4 flex items-center">
                                <Users class="mr-2 h-5 w-5 text-gray-700" />
                                <h2 class="text-xl font-bold text-gray-900">
                                    Competencias
                                </h2>
                            </div>

                            <div
                                v-if="data.skills.technical?.length"
                                class="mb-4"
                            >
                                <h3 class="mb-2 font-semibold text-gray-700">
                                    Liderazgo Técnico
                                </h3>
                                <div class="space-y-2">
                                    <div
                                        v-for="skill in data.skills.technical"
                                        :key="skill.name"
                                        class="flex items-center"
                                    >
                                        <div
                                            class="mr-3 h-2 w-2 rounded-full bg-blue-600"
                                        ></div>
                                        <span class="text-sm text-gray-700">{{
                                            skill.name
                                        }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="data.skills.soft?.length">
                                <h3 class="mb-2 font-semibold text-gray-700">
                                    Habilidades Directivas
                                </h3>
                                <div class="space-y-2">
                                    <div
                                        v-for="skill in data.skills.soft"
                                        :key="skill.name"
                                        class="flex items-center"
                                    >
                                        <div
                                            class="mr-3 h-2 w-2 rounded-full bg-green-600"
                                        ></div>
                                        <span class="text-sm text-gray-700">{{
                                            skill.name
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Educación Ejecutiva -->
                    <section v-if="data.education?.length" class="mb-8">
                        <div
                            class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                        >
                            <div class="mb-4 flex items-center">
                                <Award class="mr-2 h-5 w-5 text-gray-700" />
                                <h2 class="text-xl font-bold text-gray-900">
                                    Formación Académica
                                </h2>
                            </div>

                            <div class="space-y-4">
                                <div
                                    v-for="(edu, index) in data.education"
                                    :key="index"
                                    class="border-l-2 border-blue-600 pl-4"
                                >
                                    <h3 class="font-semibold text-gray-900">
                                        {{ edu.degree }}
                                    </h3>
                                    <p class="text-sm text-blue-600">
                                        {{ edu.institution }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ formatDate(edu.startDate) }} -
                                        {{ formatDate(edu.endDate) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Certificaciones -->
                    <section v-if="data.certifications?.length" class="mb-8">
                        <div
                            class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                        >
                            <h2 class="mb-4 text-xl font-bold text-gray-900">
                                Certificaciones
                            </h2>
                            <div class="space-y-3">
                                <div
                                    v-for="(cert, index) in data.certifications"
                                    :key="index"
                                    class="flex items-start"
                                >
                                    <div class="mr-3 rounded bg-blue-100 p-1">
                                        <Award class="h-4 w-4 text-blue-600" />
                                    </div>
                                    <div>
                                        <h3
                                            class="text-sm font-semibold text-gray-900"
                                        >
                                            {{ cert.name }}
                                        </h3>
                                        <p class="text-xs text-gray-500">
                                            {{ cert.issuer }} • {{ cert.date }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Idiomas -->
                    <section v-if="data.languages?.length">
                        <div
                            class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                        >
                            <h2 class="mb-4 text-xl font-bold text-gray-900">
                                Idiomas
                            </h2>
                            <div class="space-y-2">
                                <div
                                    v-for="(lang, index) in data.languages"
                                    :key="index"
                                    class="flex items-center justify-between"
                                >
                                    <span class="text-sm text-gray-700">{{
                                        lang.name
                                    }}</span>
                                    <span
                                        class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600"
                                    >
                                        {{ lang.level }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>

        <!-- Footer Ejecutivo -->
        <footer class="border-t border-gray-200 bg-white py-6">
            <div class="mx-auto max-w-6xl px-8 text-center">
                <p class="text-sm text-gray-600">
                    {{ data.personal.name }} • Ejecutivo Profesional •
                    {{ new Date().getFullYear() }}
                </p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.portfolio-container {
    container-type: inline-size;
}

.transition {
    transition: all 0.3s ease;
}

/* Gallery Grid for Multiple Images */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
    position: relative;
}

.gallery-thumbnail {
    width: 100%;
    height: 6rem;
    object-fit: cover;
    border-radius: 0.25rem;
    border: 1px solid #e5e7eb;
    transition: transform 0.2s, box-shadow 0.2s;
}

.gallery-thumbnail:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.gallery-more {
    position: absolute;
    bottom: 0;
    right: 0;
    width: calc(50% - 0.25rem);
    height: 6rem;
    background: rgba(31, 41, 55, 0.8);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    border-radius: 0.25rem;
    cursor: pointer;
    transition: background 0.2s;
}

.gallery-more:hover {
    background: rgba(31, 41, 55, 0.95);
}

/* Container Queries for Layout */

/* Header Layout */
.header-content-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

@container (min-width: 48rem) {
    .header-content-wrapper {
        flex-direction: row;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0;
    }
}

.header-profile-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

@container (min-width: 30rem) {
    .header-profile-section {
        flex-direction: row;
        align-items: flex-start;
        gap: 1.5rem;
    }
}

.header-text {
    text-align: center;
}

@container (min-width: 30rem) {
    .header-text {
        text-align: left;
    }
}

.header-name {
    font-size: 1.5rem; /* 2xl */
    line-height: 2rem;
}

@container (min-width: 48rem) {
    .header-name {
        font-size: 2.25rem; /* 4xl */
        line-height: 2.5rem;
    }
}

.header-title {
    font-size: 1.125rem; /* lg */
    line-height: 1.75rem;
}

@container (min-width: 48rem) {
    .header-title {
        font-size: 1.25rem; /* xl */
        line-height: 1.75rem;
    }
}

.header-summary {
    text-align: center;
}

@container (min-width: 30rem) {
    .header-summary {
        text-align: left;
    }
}

.header-contact {
    text-align: center;
}

@container (min-width: 48rem) {
    .header-contact {
        text-align: right;
    }
}

.contact-item-wrapper {
    justify-content: center;
}

@container (min-width: 48rem) {
    .contact-item-wrapper {
        justify-content: flex-end;
    }
}

.social-links-wrapper {
    justify-content: center;
}

@container (min-width: 48rem) {
    .social-links-wrapper {
        justify-content: flex-end;
    }
}

/* Main Grid Layout */
.main-content-grid {
    display: grid;
    gap: 2rem;
    grid-template-columns: 1fr;
}

@container (min-width: 64rem) {
    .main-content-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

/* Carousel Grid Layout */
.carousel-card-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: 1fr;
}

@container (min-width: 48rem) {
    .carousel-card-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>
