<script setup lang="ts">
import { Award, BookOpen, GraduationCap, Users, ChevronLeft, ChevronRight } from 'lucide-vue-next';
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
            technical: Array<string | { name: string; level?: number }>;
            soft: Array<string | { name: string; level?: number }>;
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

const ensureUrl = (url: string) => {
    if (!url.startsWith('http://') && !url.startsWith('https://')) {
        return `https://${url}`;
    }
    return url;
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 font-serif text-gray-800 portfolio-container">
        <!-- Header Académico -->
        <header class="border-b-4 border-blue-900 bg-white">
            <div class="mx-auto max-w-6xl px-4 py-12 md:px-8">
                <div class="header-layout">
                    <!-- Foto -->
                    <div v-if="data.personal.photo" class="header-photo-container">
                        <div class="h-40 w-40 overflow-hidden rounded-xl border-4 border-blue-100 shadow-xl">
                            <img :src="data.personal.photo" :alt="data.personal.name" class="h-full w-full object-cover" />
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="header-info">
                        <h1 class="header-name">
                            {{ data.personal.name || 'Tu Nombre' }}
                        </h1>
                        <p class="header-title">
                            {{ data.personal.title || 'Título Académico / Profesional' }}
                        </p>

                        <!-- Contacto -->
                        <div class="header-contact-grid">
                            <div
                                v-if="data.personal.email"
                                class="flex items-center text-gray-600"
                            >
                                <Mail class="mr-2 h-4 w-4 text-blue-600" />
                                <span>{{ data.personal.email }}</span>
                            </div>
                            <div
                                v-if="data.personal.phone"
                                class="flex items-center text-gray-600"
                            >
                                <Phone class="mr-2 h-4 w-4 text-blue-600" />
                                <span>{{ data.personal.phone }}</span>
                            </div>
                            <div
                                v-if="data.personal.location"
                                class="flex items-center text-gray-600"
                            >
                                <MapPin class="mr-2 h-4 w-4 text-blue-600" />
                                <span>{{ data.personal.location }}</span>
                            </div>
                        </div>

                        <!-- Redes -->
                        <div class="social-links">
                            <a
                                v-if="data.personal.linkedin"
                                :href="ensureUrl(data.personal.linkedin)"
                                target="_blank"
                                class="rounded-full bg-blue-50 p-2 text-blue-700 transition hover:bg-blue-100"
                            >
                                <Linkedin class="h-5 w-5" />
                            </a>
                            <a
                                v-if="data.personal.github"
                                :href="ensureUrl(data.personal.github)"
                                target="_blank"
                                class="rounded-full bg-gray-50 p-2 text-gray-700 transition hover:bg-gray-100"
                            >
                                <Github class="h-5 w-5" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main class="mx-auto max-w-6xl px-4 py-12 md:px-8">
            <div class="main-grid">
                <!-- Columna Izquierda (Principal) -->
                <div class="lg:col-span-2">
                    <!-- Resumen -->
                    <section v-if="data.personal.summary" class="mb-12">
                        <div class="mb-4 flex items-start">
                            <BookOpen class="mt-1 mr-3 h-5 w-5 text-blue-600" />
                            <h2 class="text-2xl font-bold text-gray-900">
                                Perfil Profesional
                            </h2>
                        </div>
                        <p
                            class="rounded-r-lg border-l-4 border-blue-600 bg-white p-6 leading-relaxed text-gray-700 shadow-sm"
                        >
                            {{ data.personal.summary }}
                        </p>
                    </section>

                    <!-- Experiencia -->
                    <section v-if="data.experience?.length" class="mb-12">
                        <div class="mb-4 flex items-start">
                            <Briefcase class="mt-1 mr-3 h-5 w-5 text-blue-600" />
                            <h2 class="text-2xl font-bold text-gray-900">
                                Trayectoria Profesional
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
                    <section v-if="data.projects?.length" class="mb-12">
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
                                class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-blue-200 text-blue-600 shadow-md hover:shadow-lg hover:bg-white hover:border-blue-400 transition-all duration-300 md:opacity-0 group-hover:opacity-100"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button 
                                v-if="projectIndex < data.projects.length - 2"
                                @click="nextProject"
                                class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-blue-200 text-blue-600 shadow-md hover:shadow-lg hover:bg-white hover:border-blue-400 transition-all duration-300 md:opacity-0 group-hover:opacity-100"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>

                            <!-- Projects Display (2 at a time) -->
                            <div class="cards-grid">
                                <div
                                    v-for="(project, index) in data.projects.slice(projectIndex, projectIndex + 2)"
                                    :key="index"
                                    class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all duration-300"
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
                                        class="mb-4 h-40 w-full rounded-md object-cover cursor-pointer hover:opacity-90 transition-opacity"
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
                                            class="rounded bg-blue-50 px-2 py-1 text-xs text-blue-700"
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
                                v-for="(skill, index) in data.skills.technical"
                                :key="typeof skill === 'string' ? skill : skill.name || index"
                                class="flex items-center"
                            >
                                <div
                                    class="mr-2 h-2 w-2 rounded-full bg-blue-600"
                                ></div>
                                <span class="text-sm text-gray-700">{{
                                    typeof skill === 'string' ? skill : skill.name
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
        </main>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&display=swap');

.font-serif {
    font-family: 'Merriweather', serif;
}

.portfolio-container {
    container-type: inline-size;
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
    align-items: center;
    gap: 2rem;
}

@container (min-width: 48rem) { /* md equivalent */
    .header-layout {
        flex-direction: row;
        align-items: flex-start;
    }
}

.header-photo-container {
    flex-shrink: 0;
}

.header-info {
    flex: 1;
    text-align: center;
}

@container (min-width: 48rem) {
    .header-info {
        text-align: left;
    }
}

.header-name {
    margin-bottom: 0.5rem;
    font-family: serif;
    font-weight: 700;
    font-size: 1.875rem; /* 3xl */
    color: #111827; /* gray-900 */
}

@container (min-width: 48rem) {
    .header-name {
        font-size: 3rem; /* 5xl */
    }
}

.header-title {
    margin-bottom: 1rem;
    font-size: 1.25rem; /* xl */
    color: #2563eb; /* blue-600 */
}

@container (min-width: 48rem) {
    .header-title {
        font-size: 1.5rem; /* 2xl */
    }
}

.header-contact-grid {
    margin-top: 1.5rem;
    display: grid;
    gap: 0.5rem;
    grid-template-columns: 1fr;
    justify-items: center;
}

@container (min-width: 40rem) {
    .header-contact-grid {
        grid-template-columns: repeat(2, 1fr);
        justify-items: start;
        gap: 1rem;
    }
}

.social-links {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
    gap: 1rem;
}

@container (min-width: 48rem) {
    .social-links {
        justify-content: flex-start;
    }
}

/* Main Grid */
.main-grid {
    display: grid;
    gap: 2rem;
    grid-template-columns: 1fr;
}

@container (min-width: 64rem) { /* lg equivalent */
    .main-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

/* Cards Grid (Projects) */
.cards-grid {
    display: grid;
    gap: 1.5rem;
    grid-template-columns: 1fr;
}

@container (min-width: 48rem) {
    .cards-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>
```
