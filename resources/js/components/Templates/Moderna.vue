<script setup lang="ts">
import { ref } from 'vue';
import ImageGallery from '../Shared/ImageGallery.vue';
import {
    Briefcase,
    Calendar,
    Github,
    Globe,
    Linkedin,
    Mail,
    MapPin,
    Phone,
    ChevronLeft,
    ChevronRight,
} from 'lucide-vue-next';



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
            photo: string;
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
            languages: Array<any>;
        certifications: Array<any>;
    };
    theme?: any;
}>();

// Carousel state
const galleryOpen = ref(false);
const galleryImages = ref<string[]>([]);
const galleryStartIndex = ref(0);

function openGallery(images: string[], startIdx: number = 0) {
  galleryImages.value = images;
  galleryStartIndex.value = startIdx;
  galleryOpen.value = true;
}
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


// Formatear fechas
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


// Calcular duración
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
        return `${years} año${years > 1 ? 's' : ''} ${remainingMonths} mes${remainingMonths > 1 ? 'es' : ''}`;
    } else if (years > 0) {
        return `${years} año${years > 1 ? 's' : ''}`;
    } else {
        return `${remainingMonths} mes${remainingMonths > 1 ? 'es' : ''}`;
    }
};
</script>
<template>
    <div class="portfolio-container">
        <!-- Header Principal -->
        <header class="portfolio-header">
            <!-- Decoración de fondo -->
            <div class="header-decoration">
                <div class="decoration-blob decoration-blob-1"></div>
                <div class="decoration-blob decoration-blob-2"></div>
            </div>


            <div class="header-content">
                <div class="header-inner">
                    <!-- Foto de perfil -->
                    <div v-if="data.personal.photo" class="profile-photo-wrapper">
                        <div class="profile-photo">
                            <img :src="data.personal.photo" :alt="data.personal.name"
                                class="profile-image" />
                        </div>
                    </div>


                    <!-- Información principal -->
                    <div class="header-info">
                        <h1 class="header-name">
                            {{ data.personal.name || 'Tu Nombre' }}
                        </h1>
                        <p class="header-title">
                            {{ data.personal.title || 'Tu Título Profesional' }}
                        </p>


                        <!-- Contacto -->
                        <div class="contact-links">
                            <a v-if="data.personal.email" :href="`mailto:${data.personal.email}`"
                                class="contact-item">
                                <Mail class="contact-icon" />
                                <span class="contact-text">{{
                                    data.personal.email
                                }}</span>
                            </a>


                            <a v-if="data.personal.phone" :href="`tel:${data.personal.phone}`"
                                class="contact-item">
                                <Phone class="contact-icon" />
                                <span class="contact-text">{{
                                    data.personal.phone
                                }}</span>
                            </a>


                            <div v-if="data.personal.location"
                                class="contact-item">
                                <MapPin class="contact-icon" />
                                <span class="contact-text">{{
                                    data.personal.location
                                }}</span>
                            </div>
                        </div>


                        <!-- Links sociales -->
                        <div class="social-links">
                            <a v-if="data.personal.website" :href="ensureUrl(data.personal.website)" target="_blank"
                                class="social-icon">
                                <Globe class="icon" />
                            </a>
                            <a v-if="data.personal.linkedin" :href="ensureUrl(data.personal.linkedin)" target="_blank"
                                class="social-icon">
                                <Linkedin class="icon" />
                            </a>
                            <a v-if="data.personal.github" :href="ensureUrl(data.personal.github)" target="_blank"
                                class="social-icon">
                                <Github class="icon" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>


        <!-- Contenido Principal -->
        <main class="portfolio-main">
            <!-- Resumen Profesional -->
            <section v-if="data.personal.summary" class="summary-section">
                <div class="summary-card">
                    <h2 class="section-title">
                        Perfil Profesional
                    </h2>
                    <p class="summary-text">
                        {{ data.personal.summary }}
                    </p>
          
                </div>
            </section>


            <div class="content-grid">
                <!-- Columna Principal -->
                <div class="main-column">
                    <!-- Experiencia Laboral -->
                    <section v-if="data.experience?.length" class="experience-section">
                        <h2 class="section-title-with-icon">
                            <Briefcase class="section-icon" />
                            Experiencia Laboral
                        </h2>

                        <!-- Carousel Container -->
                        <div class="relative group">
                            <!-- Navigation Arrows - Elegant glassmorphism style -->
                            <button 
                                v-if="experienceIndex > 0"
                                @click="prevExperience"
                                class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-gray-200 text-gray-600 shadow-md hover:shadow-lg hover:bg-white hover:text-blue-600 hover:border-blue-300 transition-all duration-300 opacity-0 group-hover:opacity-100"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button 
                                v-if="experienceIndex < data.experience.length - 1"
                                @click="nextExperience"
                                class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-gray-200 text-gray-600 shadow-md hover:shadow-lg hover:bg-white hover:text-blue-600 hover:border-blue-300 transition-all duration-300 opacity-0 group-hover:opacity-100"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>

                            <!-- Single Experience Display -->
                            <div class="experience-card transition-all duration-300">
                                <div class="timeline-indicator"></div>
                                <div class="experience-content">
                                    <div class="experience-header">
                                        <div>
                                            <h3 class="experience-position">
                                                {{ data.experience[experienceIndex].position }}
                                            </h3>
                                            <p class="experience-company">
                                                {{ data.experience[experienceIndex].company }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="experience-meta">
                                        <span class="meta-item">
                                            <Calendar class="meta-icon" />
                                            {{ formatDate(data.experience[experienceIndex].startDate) }} -
                                            {{
                                                data.experience[experienceIndex].current
                                                    ? 'Actualidad'
                                                    : formatDate(data.experience[experienceIndex].endDate || '')
                                            }}
                                        </span>
                                        <span class="meta-separator">•</span>
                                        <span class="meta-item">{{
                                            calculateDuration(
                                                data.experience[experienceIndex].startDate,
                                                data.experience[experienceIndex].endDate,
                                                data.experience[experienceIndex].current,
                                            )
                                        }}</span>
                                    </div>

                                    <p class="experience-description">
                                        {{ data.experience[experienceIndex].description }}
                                    </p>
                                </div>
                            </div>

                            <!-- Dots Indicator - Modern pill style -->
                            <div v-if="data.experience.length > 1" class="flex justify-center items-center gap-1.5 mt-6">
                                <button 
                                    v-for="(_, idx) in data.experience" 
                                    :key="idx"
                                    @click="experienceIndex = idx"
                                    class="rounded-full transition-all duration-300"
                                    :class="idx === experienceIndex ? 'w-6 h-2 bg-blue-500' : 'w-2 h-2 bg-gray-300 hover:bg-gray-400'"
                                />
                            </div>
                        </div>
                    </section>


                    <!-- Proyectos -->
                    <section v-if="data.projects?.length" class="projects-section">
                        <h2 class="section-title-with-icon">
                            <Briefcase class="section-icon" />
                            Proyectos Destacados
                        </h2>

                        <!-- Carousel Container -->
                        <div class="relative group">
                            <!-- Navigation Arrows - Elegant glassmorphism style -->
                            <button 
                                v-if="projectIndex > 0"
                                @click="prevProject"
                                class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-gray-200 text-gray-600 shadow-md hover:shadow-lg hover:bg-white hover:text-blue-600 hover:border-blue-300 transition-all duration-300 opacity-0 group-hover:opacity-100"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button 
                                v-if="projectIndex < data.projects.length - 1"
                                @click="nextProject"
                                class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm border border-gray-200 text-gray-600 shadow-md hover:shadow-lg hover:bg-white hover:text-blue-600 hover:border-blue-300 transition-all duration-300 opacity-0 group-hover:opacity-100"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>

                            <!-- Single Project Display -->
                            <div class="project-card transition-all duration-300">
                                <!-- Multi-image gallery grid -->
                                <div v-if="data.projects[projectIndex].images && data.projects[projectIndex].images.length > 1" class="gallery-grid">
                                    <img 
                                        v-for="(img, idx) in data.projects[projectIndex].images.slice(0, 4)" 
                                        :key="idx" 
                                        :src="img" 
                                        :alt="`${data.projects[projectIndex].name} - ${Number(idx) + 1}`"
                                        class="gallery-thumbnail cursor-pointer"
                                        @click="openGallery(data.projects[projectIndex].images, Number(idx))"
                                    />
                                    <div 
                                        v-if="data.projects[projectIndex].images.length > 4" 
                                        class="gallery-more"
                                        @click="openGallery(data.projects[projectIndex].images, 4)"
                                    >
                                        +{{ data.projects[projectIndex].images.length - 4 }}
                                    </div>
                                </div>
                                <!-- Single image fallback -->
                                <img 
                                    v-else-if="data.projects[projectIndex].image" 
                                    :src="data.projects[projectIndex].image" 
                                    :alt="data.projects[projectIndex].name"
                                    class="project-image cursor-pointer" 
                                    @click="openGallery([data.projects[projectIndex].image], 0)"
                                />

                                <h3 class="project-name">
                                    {{ data.projects[projectIndex].name }}
                                </h3>

                                <p class="project-description">
                                    {{ data.projects[projectIndex].description }}
                                </p>

                                <!-- Tecnologías -->
                                <div v-if="data.projects[projectIndex].technologies?.length" class="technologies-list">
                                    <span v-for="tech in data.projects[projectIndex].technologies" :key="tech"
                                        class="technology-tag">
                                        {{ tech }}
                                    </span>
                                </div>

                                <!-- Link/URL del proyecto -->
                                <a v-if="data.projects[projectIndex].link" :href="data.projects[projectIndex].link" target="_blank" rel="noopener noreferrer"
                                    class="project-link">
                                    Ver proyecto
                                    <svg class="link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                </a>
                            </div>

                            <!-- Dots Indicator - Modern pill style -->
                            <div v-if="data.projects.length > 1" class="flex justify-center items-center gap-1.5 mt-6">
                                <button 
                                    v-for="(_, idx) in data.projects" 
                                    :key="idx"
                                    @click="projectIndex = idx"
                                    class="rounded-full transition-all duration-300"
                                    :class="idx === projectIndex ? 'w-6 h-2 bg-blue-500' : 'w-2 h-2 bg-gray-300 hover:bg-gray-400'"
                                />
                            </div>
                        </div>

                        <!-- Image Gallery Modal -->
                        <ImageGallery 
                            v-if="galleryOpen" 
                            :images="galleryImages" 
                            :initialIndex="galleryStartIndex"
                            @close="galleryOpen = false"
                        />
                    </section>
                </div>


                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Habilidades -->
                    <section v-if="
                        data.skills?.technical?.length ||
                        data.skills?.soft?.length
                    " class="sidebar-section">
                        <div class="sidebar-card">
                            <h2 class="sidebar-title">
                                Habilidades
                            </h2>


                            <div v-if="data.skills.technical?.length" class="skills-group">
                                <h3 class="skills-group-title">
                                    Técnicas
                                </h3>
                                <div class="skills-tags">
                                    <span v-for="skill in data.skills.technical" :key="skill.name"
                                        class="skill-tag skill-tag-technical">
                                        {{ skill.name }}
                                    </span>
                                </div>
                            </div>


                            <div v-if="data.skills.soft?.length" class="skills-group">
                                <h3 class="skills-group-title">
                                    Blandas
                                </h3>
                                <div class="skills-tags">
                                    <span v-for="skill in data.skills.soft" :key="skill.name"
                                        class="skill-tag skill-tag-soft">
                                        {{ skill.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>


                    <!-- Educación -->
                    <section v-if="data.education?.length" class="sidebar-section">
                        <div class="sidebar-card">
                            <h2 class="sidebar-title">
                                Educación
                            </h2>


                            <div class="education-list">
                                <div v-for="(edu, index) in data.education" :key="index"
                                    class="education-item">
                                    <h3 class="education-degree">
                                        {{ edu.degree }}
                                    </h3>
                                    <p class="education-institution">
                                        {{ edu.institution }}
                                    </p>
                                    <p class="education-dates">
                                        {{ formatDate(edu.startDate) }} -
                                        {{
                                            edu.current
                                                ? 'Actualidad'
                                                : formatDate(
                                                    edu.endDate || '',
                                                )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>


                    <!-- Idiomas -->
                    <section v-if="data.languages?.length" class="sidebar-section">
                        <div class="sidebar-card">
                            <h2 class="sidebar-title">
                                Idiomas
                            </h2>


                            <div class="languages-list">
                                <div v-for="(lang, index) in data.languages" :key="index"
                                    class="language-item">
                                    <span class="language-name">{{ lang.name }}</span>
                                    <span class="language-level">{{
                                        lang.level
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>


        <!-- Footer -->
        <footer class="portfolio-footer">
            <p class="footer-text">
                Generado con PortafolioAI • {{ new Date().getFullYear() }}
            </p>
        </footer>
    </div>
</template>
<style scoped>
/* ============================================
   CONTAINER SETUP - RESPONSIVE AL CONTENEDOR
   ============================================ */
.portfolio-container {
    container-type: inline-size;
    min-height: 100vh;
    background: linear-gradient(135deg, rgb(248 250 252 / 1) 0%, rgb(219 234 254 / 1) 100%);
}


/* ============================================
   HEADER
   ============================================ */
.portfolio-header {
    position: relative;
    overflow: hidden;
    background: linear-gradient(90deg, rgb(31, 86, 204) 0%, rgb(51, 120, 247) 100%);
    padding: clamp(2rem, 8vw, 4rem) clamp(1.5rem, 5vw, 2rem);
    color: white;
}


.header-decoration {
    position: absolute;
    inset: 0;
    opacity: 0.1;
    pointer-events: none;
}


.decoration-blob {
    position: absolute;
    border-radius: 50%;
    background: white;
    filter: blur(3rem);
}


.decoration-blob-1 {
    top: 0;
    right: 0;
    height: 16rem;
    width: 16rem;
}


.decoration-blob-2 {
    bottom: 0;
    left: 0;
    height: 12rem;
    width: 12rem;
}


.header-content {
    position: relative;
    margin: 0 auto;
    max-width: 80rem;
}


.header-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: clamp(1rem, 3vw, 2rem);
}


@container (min-width: 30rem) {
    .header-inner {
        flex-direction: row;
        text-align: left;
        align-items: flex-start;
    }
}


/* ============================================
   PROFILE PHOTO - OPTIMIZADO
   ============================================ */
.profile-photo-wrapper {
    flex-shrink: 0;
    margin-bottom: clamp(1rem, 3vw, 1.5rem);
    display: flex;
    justify-content: center;
}


@container (min-width: 30rem) {
    .profile-photo-wrapper {
        margin-right: clamp(2rem, 4vw, 2.5rem);
        margin-bottom: 0;
        justify-content: flex-start;
    }
}


.profile-photo {
    height: clamp(7rem, 14vw, 9rem);
    width: clamp(7rem, 14vw, 9rem);
    overflow: hidden;
    border-radius: 50%;
    border: 5px solid white;
    box-shadow: 0 10px 30px -5px rgb(0 0 0 / 0.2),
                0 0 0 1px rgb(255 255 255 / 0.3);
    position: relative;
    flex-shrink: 0;
    background: linear-gradient(135deg, rgb(219 234 254 / 1) 0%, rgb(191 219 254 / 1) 100%);
}


.profile-photo::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: radial-gradient(circle at 30% 30%, rgb(255 255 255 / 0.3), transparent 50%);
    pointer-events: none;
    z-index: 1;
}


.profile-image {
    height: 100%;
    width: 100%;
    object-fit: cover;
    position: relative;
    z-index: 0;
}


.header-info {
    flex: 1;
}


.header-name {
    margin: 0;
    font-size: clamp(1.875rem, 6vw, 3rem);
    font-weight: 700;
    margin-bottom: clamp(0.25rem, 1vw, 0.5rem);
    line-height: 1.2;
}


.header-title {
    margin: 0 0 clamp(0.5rem, 2vw, 1rem) 0;
    font-size: clamp(0.875rem, 3vw, 1.25rem);
    color: rgb(219 234 254 / 1);
}


.contact-links {
    display: flex;
    flex-wrap: wrap;
    gap: clamp(0.5rem, 2vw, 1rem);
    justify-content: center;
    margin-bottom: clamp(0.5rem, 2vw, 1rem);
}


@container (min-width: 30rem) {
    .contact-links {
        justify-content: flex-start;
    }
}


.contact-item {
    display: flex;
    align-items: center;
    gap: clamp(0.25rem, 1vw, 0.5rem);
    border-radius: 0.5rem;
    background: rgb(255 255 255 / 0.1);
    padding: clamp(0.25rem, 1vw, 0.5rem) clamp(0.5rem, 2vw, 0.75rem);
    backdrop-filter: blur(0.5rem);
    transition: background 150ms cubic-bezier(0.4, 0, 0.2, 1);
    font-size: clamp(0.65rem, 2vw, 0.875rem);
    text-decoration: none;
    color: white;
}


.contact-item:hover {
    background: rgb(255 255 255 / 0.2);
}


.contact-icon {
    height: clamp(0.875rem, 2vw, 1rem);
    width: clamp(0.875rem, 2vw, 1rem);
    flex-shrink: 0;
}


.contact-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: clamp(6rem, 15vw, 100%);
}


.social-links {
    display: flex;
    gap: clamp(0.5rem, 2vw, 0.75rem);
    justify-content: center;
}


@container (min-width: 30rem) {
    .social-links {
        justify-content: flex-start;
    }
}


.social-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
    background: rgb(255 255 255 / 0.1);
    padding: clamp(0.375rem, 1.5vw, 0.5rem);
    backdrop-filter: blur(0.5rem);
    transition: background 150ms cubic-bezier(0.4, 0, 0.2, 1);
}


.social-icon:hover {
    background: rgb(255 255 255 / 0.2);
}


.social-icon .icon {
    height: clamp(0.875rem, 2vw, 1.25rem);
    width: clamp(0.875rem, 2vw, 1.25rem);
}


/* ============================================
   MAIN CONTENT
   ============================================ */
.portfolio-main {
    margin: 0 auto;
    max-width: 80rem;
    padding: clamp(1.5rem, 4vw, 3rem) clamp(1.5rem, 3vw, 2rem);
}


.summary-section {
    margin-bottom: clamp(1.5rem, 4vw, 3rem);
}


.summary-card {
    border-radius: 1rem;
    background: white;
    padding: clamp(1.5rem, 4vw, 2rem);
    box-shadow: 0 1px 3px rgb(0 0 0 / 0.1), 0 1px 2px rgb(0 0 0 / 0.06);
}


.section-title {
    margin: 0 0 clamp(0.75rem, 2vw, 1rem) 0;
    font-size: clamp(1.25rem, 3vw, 1.5rem);
    font-weight: 700;
    color: rgb(17 24 39 / 1);
}


.summary-text {
    margin: 0 0 clamp(0.75rem, 2vw, 1rem) 0;
    line-height: 1.625;
    color: rgb(55 65 81 / 1);
    font-size: clamp(0.875rem, 2.5vw, 1rem);
}


.summary-text:last-child {
    margin-bottom: 0;
}


.content-grid {
    display: grid;
    gap: clamp(1.5rem, 3vw, 2rem);
}


@container (min-width: 60rem) {
    .content-grid {
        grid-template-columns: 1fr minmax(16rem, 20rem);
    }
}


/* ============================================
   MAIN COLUMN
   ============================================ */
.main-column {
    display: flex;
    flex-direction: column;
    gap: clamp(1.5rem, 3vw, 2rem);
}


.experience-section,
.projects-section {
    margin: 0;
}


.section-title-with-icon {
    display: flex;
    align-items: center;
    margin: 0 0 clamp(1rem, 2vw, 1.5rem) 0;
    font-size: clamp(1.25rem, 3vw, 1.5rem);
    font-weight: 700;
    color: rgb(17 24 39 / 1);
}


.section-icon {
    margin-right: clamp(0.5rem, 1vw, 0.75rem);
    height: clamp(1.25rem, 2.5vw, 1.5rem);
    width: clamp(1.25rem, 2.5vw, 1.5rem);
    color: rgb(37 99 235 / 1);
}


/* Experience */
.experience-list {
    display: flex;
    flex-direction: column;
    gap: clamp(1rem, 2vw, 1.5rem);
}


.experience-card {
    position: relative;
    border-radius: 1rem;
    background: white;
    padding: clamp(1rem, 3vw, 1.5rem);
    padding-left: clamp(1.5rem, 4vw, 2rem);
    box-shadow: 0 1px 3px rgb(0 0 0 / 0.1), 0 1px 2px rgb(0 0 0 / 0.06);
    transition: box-shadow 250ms cubic-bezier(0.4, 0, 0.2, 1);
}


.experience-card:hover {
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -1px rgb(0 0 0 / 0.06);
}


.timeline-indicator {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: clamp(2px, 0.5vw, 4px);
    background: rgb(37 99 235 / 1);
    border-radius: 1rem 0 0 1rem;
}


.experience-content {
    display: flex;
    flex-direction: column;
}


.experience-header {
    margin-bottom: clamp(0.5rem, 1vw, 0.75rem);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: clamp(0.5rem, 1vw, 1rem);
}


.experience-position {
    margin: 0;
    font-size: clamp(1rem, 2.5vw, 1.25rem);
    font-weight: 700;
    color: rgb(17 24 39 / 1);
}


.experience-company {
    margin: clamp(0.125rem, 0.5vw, 0.25rem) 0 0 0;
    font-size: clamp(0.875rem, 2vw, 1rem);
    color: rgb(37 99 235 / 1);
}


.experience-meta {
    display: flex;
    flex-wrap: wrap;
    gap: clamp(0.5rem, 1vw, 1rem);
    align-items: center;
    margin-bottom: clamp(0.5rem, 1vw, 0.75rem);
    font-size: clamp(0.75rem, 2vw, 0.875rem);
    color: rgb(75 85 99 / 1);
}


.meta-item {
    display: flex;
    align-items: center;
    gap: clamp(0.25rem, 0.5vw, 0.375rem);
    white-space: nowrap;
}


.meta-icon {
    height: clamp(0.875rem, 1.5vw, 1rem);
    width: clamp(0.875rem, 1.5vw, 1rem);
    flex-shrink: 0;
}


.meta-separator {
    color: rgb(209 213 219 / 1);
}


.experience-description {
    margin: 0;
    line-height: 1.625;
    color: rgb(55 65 81 / 1);
    font-size: clamp(0.8rem, 2.2vw, 0.95rem);
}


/* Projects */
.projects-grid {
    display: grid;
    gap: clamp(1rem, 2vw, 1.5rem);
    grid-template-columns: repeat(auto-fill, minmax(18rem, 1fr));
}


.project-card {
    border-radius: 1rem;
    background: white;
    padding: clamp(1rem, 3vw, 1.5rem);
    box-shadow: 0 1px 3px rgb(0 0 0 / 0.1), 0 1px 2px rgb(0 0 0 / 0.06);
    transition: box-shadow 250ms cubic-bezier(0.4, 0, 0.2, 1),
                transform 250ms cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}


.project-card:hover {
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -1px rgb(0 0 0 / 0.06);
    transform: translateY(-2px);
}


.project-image {
    margin-bottom: clamp(0.75rem, 2vw, 1rem);
    height: clamp(8rem, 20vw, 12rem);
    width: 100%;
    border-radius: 0.5rem;
    object-fit: cover;
    transition: transform 0.2s, box-shadow 0.2s;
}

.project-image:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Gallery Grid for Multiple Images */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
    margin-bottom: clamp(0.75rem, 2vw, 1rem);
    position: relative;
}

.gallery-thumbnail {
    width: 100%;
    height: clamp(5rem, 12vw, 7rem);
    object-fit: cover;
    border-radius: 0.5rem;
    transition: transform 0.2s, box-shadow 0.2s;
}

.gallery-thumbnail:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.gallery-more {
    position: absolute;
    bottom: 0;
    right: 0;
    width: calc(50% - 0.25rem);
    height: clamp(5rem, 12vw, 7rem);
    background: rgba(0, 0, 0, 0.6);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: background 0.2s;
}

.gallery-more:hover {
    background: rgba(0, 0, 0, 0.8);
}


.project-name {
    margin: 0 0 clamp(0.5rem, 1vw, 0.75rem) 0;
    font-size: clamp(1rem, 2.5vw, 1.125rem);
    font-weight: 700;
    color: rgb(17 24 39 / 1);
}


.project-description {
    margin: 0 0 clamp(0.5rem, 1vw, 0.75rem) 0;
    font-size: clamp(0.8rem, 2vw, 0.875rem);
    color: rgb(75 85 99 / 1);
    line-height: 1.5;
    flex-grow: 1;
}


.technologies-list {
    display: flex;
    flex-wrap: wrap;
    gap: clamp(0.375rem, 1vw, 0.5rem);
    margin-bottom: clamp(0.75rem, 2vw, 1rem);
}


.technology-tag {
    border-radius: 9999px;
    background: rgb(219 234 254 / 1);
    padding: clamp(0.25rem, 1vw, 0.375rem) clamp(0.5rem, 1.5vw, 0.75rem);
    font-size: clamp(0.7rem, 1.8vw, 0.8rem);
    font-weight: 500;
    color: rgb(37 99 235 / 1);
    white-space: nowrap;
}


.project-link {
    display: inline-flex;
    align-items: center;
    gap: clamp(0.25rem, 1vw, 0.375rem);
    font-size: clamp(0.8rem, 2vw, 0.875rem);
    font-weight: 600;
    color: rgb(37 99 235 / 1);
    text-decoration: none;
    transition: color 150ms cubic-bezier(0.4, 0, 0.2, 1);
    margin-top: auto;
}


.project-link:hover {
    color: rgb(29 78 216 / 1);
    text-decoration: underline;
}


.link-icon {
    height: clamp(0.875rem, 2vw, 1rem);
    width: clamp(0.875rem, 2vw, 1rem);
}


/* ============================================
   SIDEBAR
   ============================================ */
.sidebar {
    display: flex;
    flex-direction: column;
    gap: clamp(1.5rem, 3vw, 2rem);
}


.sidebar-section {
    margin: 0;
}


.sidebar-card {
    border-radius: 1rem;
    background: white;
    padding: clamp(1rem, 3vw, 1.5rem);
    box-shadow: 0 1px 3px rgb(0 0 0 / 0.1), 0 1px 2px rgb(0 0 0 / 0.06);
}


.sidebar-title {
    margin: 0 0 clamp(0.75rem, 2vw, 1rem) 0;
    font-size: clamp(1rem, 2.5vw, 1.125rem);
    font-weight: 700;
    color: rgb(17 24 39 / 1);
}


/* Skills */
.skills-group {
    margin-bottom: clamp(0.75rem, 2vw, 1rem);
}


.skills-group:last-child {
    margin-bottom: 0;
}


.skills-group-title {
    margin: 0 0 clamp(0.5rem, 1vw, 0.75rem) 0;
    font-size: clamp(0.8rem, 2vw, 0.875rem);
    font-weight: 600;
    color: rgb(55 65 81 / 1);
}


.skills-tags {
    display: flex;
    flex-wrap: wrap;
    gap: clamp(0.375rem, 1vw, 0.5rem);
}


.skill-tag {
    border-radius: 0.375rem;
    padding: clamp(0.25rem, 0.8vw, 0.375rem) clamp(0.5rem, 1vw, 0.75rem);
    font-size: clamp(0.75rem, 1.8vw, 0.875rem);
    font-weight: 500;
    white-space: nowrap;
}


.skill-tag-technical {
    background: rgb(219 234 254 / 1);
    color: rgb(37 99 235 / 1);
}


.skill-tag-soft {
    background: rgb(243 244 246 / 1);
    color: rgb(55 65 81 / 1);
}


/* Education */
.education-list {
    display: flex;
    flex-direction: column;
    gap: clamp(0.75rem, 2vw, 1rem);
}


.education-item {
    border-left: 2px solid rgb(37 99 235 / 1);
    padding-left: clamp(0.75rem, 2vw, 1rem);
}


.education-degree {
    margin: 0 0 clamp(0.125rem, 0.5vw, 0.25rem) 0;
    font-size: clamp(0.875rem, 2vw, 1rem);
    font-weight: 600;
    color: rgb(17 24 39 / 1);
}


.education-institution {
    margin: 0 0 clamp(0.125rem, 0.5vw, 0.25rem) 0;
    font-size: clamp(0.8rem, 1.8vw, 0.875rem);
    color: rgb(37 99 235 / 1);
}


.education-dates {
    margin: 0;
    font-size: clamp(0.75rem, 1.5vw, 0.8rem);
    color: rgb(107 114 128 / 1);
}


/* Languages */
.languages-list {
    display: flex;
    flex-direction: column;
    gap: clamp(0.5rem, 1.5vw, 0.75rem);
}


.language-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: clamp(0.5rem, 1vw, 1rem);
}


.language-name {
    font-size: clamp(0.875rem, 2vw, 1rem);
    font-weight: 600;
    color: rgb(17 24 39 / 1);
}


.language-level {
    font-size: clamp(0.8rem, 1.8vw, 0.875rem);
    color: rgb(107 114 128 / 1);
}


/* ============================================
   FOOTER
   ============================================ */
.portfolio-footer {
    border-top: 1px solid rgb(229 231 235 / 1);
    background: white;
    padding: clamp(1.5rem, 3vw, 2rem) clamp(1.5rem, 3vw, 2rem);
    text-align: center;
}


.footer-text {
    margin: 0;
    font-size: clamp(0.8rem, 2vw, 0.875rem);
    color: rgb(75 85 99 / 1);
}


/* ============================================
   TRANSITIONS & ANIMATIONS
   ============================================ */
* {
    transition: background-color 150ms cubic-bezier(0.4, 0, 0.2, 1),
                color 150ms cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
