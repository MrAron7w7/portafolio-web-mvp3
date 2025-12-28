<script setup lang="ts">
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
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
    return `${month}/${year}`;
};
</script>

<template>
    <div class="min-h-screen bg-white font-sans text-gray-900 portfolio-container">
        <!-- Header Minimalista -->
        <header class="border-b border-gray-100 pt-16 pb-8 md:pt-24 md:pb-12">
            <div class="mx-auto max-w-5xl px-6">
                <!-- Main Header Layout -->
                <div class="header-layout flex flex-col md:flex-row md:items-center md:justify-between gap-8 md:gap-12">
                    <!-- Text Content (Left Side) -->
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="mb-3 text-5xl font-serif font-normal tracking-tight text-gray-900 md:text-7xl">
                            {{ data.personal.name || 'Tu Nombre' }}
                        </h1>

                        <!-- Photo Mobile Only (Between Name and Title) -->
                        <div v-if="data.personal.photo" class="mb-6 flex justify-center md:hidden">
                            <img :src="data.personal.photo" :alt="data.personal.name" class="h-32 w-32 rounded-full object-cover transition-all duration-500 hover:scale-105 shadow-sm" />
                        </div>

                        <p class="mb-6 text-xl font-light tracking-wide text-gray-500 font-sans">
                            {{ data.personal.title || 'Diseñador / Creativo' }}
                        </p>

                        <!-- Contact Info -->
                        <div class="flex flex-wrap justify-center md:justify-start gap-x-6 gap-y-2 text-sm text-gray-500 font-sans">
                            <div v-if="data.personal.email" class="contact-item group">
                                <span class="border-b border-transparent group-hover:border-gray-900 group-hover:text-gray-900 transition-all cursor-pointer">{{ data.personal.email }}</span>
                            </div>
                            <div v-if="data.personal.phone" class="contact-item">
                                <span>{{ data.personal.phone }}</span>
                            </div>
                            <div v-if="data.personal.location" class="contact-item">
                                <span>{{ data.personal.location }}</span>
                            </div>
                        </div>

                         <div class="mt-4 flex justify-center md:justify-start gap-4">
                            <a v-if="data.personal.linkedin" :href="ensureUrl(data.personal.linkedin)" target="_blank" class="text-gray-400 hover:text-black transition-colors transform hover:-translate-y-1">
                                <Linkedin class="h-5 w-5" />
                            </a>
                            <a v-if="data.personal.github" :href="ensureUrl(data.personal.github)" target="_blank" class="text-gray-400 hover:text-black transition-colors transform hover:-translate-y-1">
                                <Github class="h-5 w-5" />
                            </a>
                        </div>
                    </div>

                    <!-- Photo Desktop Only (Right Side) -->
                    <div v-if="data.personal.photo" class="hidden md:flex justify-center md:justify-end shrink-0">
                        <title>Profile Photo</title>
                        <img :src="data.personal.photo" :alt="data.personal.name" class="h-32 w-32 md:h-40 md:w-40 rounded-full object-cover transition-all duration-500 hover:scale-105 shadow-sm" />
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main class="mx-auto max-w-5xl px-6 py-8 md:py-12">
            <!-- Sobre Mí -->
            <section v-if="data.personal.summary" class="mb-16 md:mb-24">
                <p class="max-w-2xl text-xl font-light leading-relaxed text-gray-600 md:text-2xl">
                    {{ data.personal.summary }}
                </p>
            </section>

            <div class="main-grid">
                <!-- Columna Izquierda (Principal) -->
                <div class="span-2-lg">
                    <!-- Experiencia -->
                    <section v-if="data.experience?.length" class="mb-16 md:mb-24">
                        <h2 class="mb-10 text-2xl font-serif text-gray-900 md:text-3xl">
                            Experiencia
                        </h2>

                        <!-- Carousel Container -->
                        <div class="relative group">
                             <!-- Navigation Arrows - Minimalist style -->
                            <button 
                                v-if="experienceIndex > 0"
                                @click="prevExperience"
                                class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-black transition-all duration-300 md:opacity-0 group-hover:opacity-100 shadow-sm"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button 
                                v-if="experienceIndex < data.experience.length - 1"
                                @click="nextExperience"
                                class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-black transition-all duration-300 md:opacity-0 group-hover:opacity-100 shadow-sm"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>

                            <!-- Experience Display (Single view for detail) -->
                            <div class="group relative border-l border-gray-200 pl-8 transition-all duration-300 hover:border-gray-900">
                                <!-- Timeline Dot -->
                                <div class="absolute -left-[5px] top-2 h-2.5 w-2.5 rounded-full bg-gray-200 transition-colors duration-300 group-hover:bg-black ring-4 ring-white"></div>
                                
                                <div class="mb-3 flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-1">
                                    <h3 class="text-2xl font-serif text-gray-900">
                                        {{ data.experience[experienceIndex].position }}
                                    </h3>
                                    <span class="text-xs font-bold tracking-widest uppercase text-gray-400 group-hover:text-black transition-colors">
                                        {{ formatDate(data.experience[experienceIndex].startDate) }} –
                                        {{
                                            data.experience[experienceIndex].current
                                                ? 'Presente'
                                                : formatDate(data.experience[experienceIndex].endDate || '')
                                        }}
                                    </span>
                                </div>
                                <p class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-500">
                                    {{ data.experience[experienceIndex].company }}
                                </p>
                                <p class="leading-relaxed text-gray-600 font-light text-lg">
                                    {{ data.experience[experienceIndex].description }}
                                </p>
                            </div>


                             <!-- Minimal Dots -->
                             <div v-if="data.experience.length > 1" class="flex justify-center items-center gap-2 mt-8">
                                <button 
                                    v-for="(_, idx) in data.experience" 
                                    :key="idx"
                                    @click="experienceIndex = idx"
                                    class="transition-all duration-300 rounded-full"
                                    :class="idx === experienceIndex ? 'w-2 h-2 bg-black' : 'w-1.5 h-1.5 bg-gray-300 hover:bg-gray-400'"
                                />
                            </div>
                        </div>
                    </section>

                    <!-- Proyectos -->
                    <section v-if="data.projects?.length" class="mb-16 md:mb-24">
                        <h2 class="mb-10 text-2xl font-serif text-gray-900 md:text-3xl">
                            Proyectos Seleccionados
                        </h2>

                        <!-- Carousel Container -->
                        <div class="relative group">
                            <!-- Navigation Arrows - Minimalist style -->
                            <button 
                                v-if="projectIndex > 0"
                                @click="prevProject"
                                class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-black transition-all duration-300 md:opacity-0 group-hover:opacity-100 shadow-sm"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button 
                                v-if="projectIndex < data.projects.length - 1"
                                @click="nextProject"
                                class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-black transition-all duration-300 md:opacity-0 group-hover:opacity-100 shadow-sm"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>

                            <!-- Projects Grid (using cards-grid for container queries) -->
                            <div class="cards-grid">
                                <div
                                    v-for="(project, index) in data.projects.slice(projectIndex, projectIndex + 2)"
                                    :key="index"
                                    class="group/card block bg-gray-50 p-6 transition-colors hover:bg-gray-100"
                                >
                                    <!-- Multi-image gallery grid -->
                                    <div v-if="project.images && project.images.length > 1" class="gallery-grid mb-4">
                                        <img 
                                            v-for="(img, imgIdx) in project.images.slice(0, 4)" 
                                            :key="imgIdx" 
                                            :src="img" 
                                            :alt="`${project.name} - ${Number(imgIdx) + 1}`"
                                            class="gallery-thumbnail cursor-pointer transition-opacity hover:opacity-90"
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
                                        class="mb-4 w-full h-48 object-cover block cursor-pointer transition-all hover:opacity-90 shadow-sm"
                                        @click="openGallery([project.image], 0)"
                                    >
                                    
                                    <div class="flex items-baseline justify-between mb-2">
                                        <h3 class="text-xl font-serif text-gray-900 group-hover/card:text-black transition-colors">
                                            {{ project.name }}
                                        </h3>
                                        <a
                                            v-if="project.url"
                                            :href="project.url"
                                            target="_blank"
                                            class="text-xs font-bold uppercase tracking-wider text-gray-400 hover:text-black transition-colors border-b border-transparent hover:border-black pb-0.5"
                                        >
                                            Ver Proyecto
                                        </a>
                                    </div>
                                    
                                    <p class="mb-4 text-sm leading-relaxed text-gray-600">
                                        {{ project.description }}
                                    </p>
                                    
                                    <div
                                        v-if="project.technologies?.length"
                                        class="flex flex-wrap gap-2 text-xs text-gray-500"
                                    >
                                        <span v-for="tech in project.technologies" :key="tech">
                                            {{ tech }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                             <!-- Minimal Dots -->
                             <div v-if="data.projects.length > 1" class="flex justify-center items-center gap-2 mt-8">
                                <button 
                                    v-for="(_, idx) in data.projects" 
                                    :key="idx"
                                    @click="projectIndex = idx"
                                    class="transition-all duration-300 rounded-full"
                                    :class="idx === projectIndex ? 'w-2 h-2 bg-black' : 'w-1.5 h-1.5 bg-gray-300 hover:bg-gray-400'"
                                />
                            </div>
                        </div>
                    </section>

                    <!-- Habilidades (Moved inside main col) -->
                    <section
                        v-if="
                            data.skills?.technical?.length || data.skills?.soft?.length
                        "
                        class="mb-16 md:mb-24"
                    >
                        <h2 class="mb-10 text-2xl font-serif text-gray-900 md:text-3xl">
                            Habilidades
                        </h2>

                        <div class="space-y-8">
                            <div v-if="data.skills.technical?.length">
                                <h3
                                    class="mb-4 text-sm font-medium tracking-wide text-gray-500 uppercase"
                                >
                                    Técnicas
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    <span 
                                        v-for="skill in data.skills.technical" 
                                        :key="skill"
                                        class="px-3 py-1 bg-gray-50 text-gray-700 text-sm rounded-full border border-gray-100"
                                    >
                                        {{ typeof skill === 'string' ? skill : skill.name }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="data.skills.soft?.length">
                                <h3
                                    class="mb-4 text-sm font-medium tracking-wide text-gray-500 uppercase"
                                >
                                    Competencias
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    <span 
                                        v-for="skill in data.skills.soft" 
                                        :key="skill"
                                        class="px-3 py-1 bg-gray-50 text-gray-700 text-sm rounded-full border border-gray-100"
                                    >
                                        {{ typeof skill === 'string' ? skill : skill.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div> <!-- Close lg:col-span-2 -->

                <!-- Sidebar (Columna Derecha) -->
                <div class="space-y-16 md:space-y-24">
                     <!-- Educación -->
                    <section v-if="data.education?.length">
                        <h2 class="mb-8 text-xl font-serif text-gray-900">
                            Educación
                        </h2>

                        <div class="space-y-6">
                            <div v-for="(edu, index) in data.education" :key="index" class="group relative pl-4 border-l border-gray-200 hover:border-gray-400 transition-colors">
                                <!-- Dot -->
                                <div class="absolute -left-[5px] top-1.5 h-2.5 w-2.5 rounded-full bg-gray-100 group-hover:bg-gray-400 transition-colors ring-4 ring-white"></div>
                                
                                <div class="mb-1">
                                    <h3 class="text-lg font-serif text-gray-900 leading-tight">
                                        {{ edu.degree }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ formatDate(edu.startDate) }} – {{ formatDate(edu.endDate) }}
                                    </p>
                                </div>
                                <p class="text-sm text-gray-600 font-medium mt-2">{{ edu.institution }}</p>
                                <p v-if="edu.field" class="text-sm text-gray-500 mt-1">
                                    {{ edu.field }}
                                </p>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Image Gallery Modal (placed here to ensure it's in a valid location) -->
                    <ImageGallery 
                        v-if="galleryOpen" 
                        :images="galleryImages" 
                        :initialIndex="galleryStartIndex"
                        @close="galleryOpen = false"
                    />

                    <!-- Idiomas -->
                    <section v-if="data.languages?.length">
                        <h2 class="mb-8 text-xl font-serif text-gray-900">
                            Idiomas
                        </h2>

                        <div class="grid grid-cols-1 gap-4">
                            <div v-for="(lang, index) in data.languages" :key="index">
                                <span class="text-gray-900">{{ lang.name }}</span>
                                <span class="text-gray-500"> — {{ lang.level }}</span>
                            </div>
                        </div>
                    </section>
                </div>
            </div> <!-- Close main-grid -->
        </main>
    </div>
</template>

<style scoped>
/* Fuente serif para títulos */
@import url('https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;600&display=swap');

.font-serif {
    font-family: 'Crimson Text', serif;
}

.portfolio-container {
    container-type: inline-size;
    container-name: portfolio;
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

/* Container Queries for Responsive Layout */

/* Header Layout */
.header-layout {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

@container portfolio (min-width: 48rem) { /* md equivalent */
    .header-layout {
        flex-direction: row;
        align-items: flex-end;
        justify-content: space-between;
        gap: 0;
    }
}

.header-text {
    flex: 1;
    text-align: center;
}

@container portfolio (min-width: 48rem) {
    .header-text {
        text-align: left;
    }
}

.header-contact {
    text-align: center;
}

@container portfolio (min-width: 48rem) {
    .header-contact {
        text-align: right;
    }
}

.contact-item {
    display: flex;
    align-items: center;
    justify-content: center;
}

@container portfolio (min-width: 48rem) {
    .contact-item {
        justify-content: flex-end;
    }
}

.social-links {
    margin-top: 1rem;
    display: flex;
    justify-content: center;
    gap: 1rem;
}

@container portfolio (min-width: 48rem) {
    .social-links {
        justify-content: flex-end;
    }
}

/* Main Grid */
.main-grid {
    display: grid;
    gap: 3rem;
    grid-template-columns: 1fr;
}

@container portfolio (min-width: 64rem) { /* lg equivalent */
    .main-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

.span-2-lg {
    grid-column: span 1;
}

@container portfolio (min-width: 64rem) {
    .span-2-lg {
        grid-column: span 2;
    }
}

/* Cards Grid */
.cards-grid {
    display: grid;
    gap: 1.5rem;
    grid-template-columns: 1fr;
}

@container portfolio (min-width: 48rem) {
    .cards-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>
