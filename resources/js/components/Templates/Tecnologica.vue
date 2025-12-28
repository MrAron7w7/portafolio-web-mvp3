<script setup lang="ts">
import { Cloud, Code, Cpu, Database, Server, Terminal, ChevronLeft, ChevronRight } from 'lucide-vue-next';
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
            technologies?: string[];
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

const techIcons: { [key: string]: any } = {
    JavaScript: Code,
    TypeScript: Code,
    Python: Terminal,
    Java: Cpu,
    React: Code,
    Vue: Code,
    'Node.js': Server,
    AWS: Cloud,
    Azure: Cloud,
    Docker: Server,
    Kubernetes: Server,
    MySQL: Database,
    PostgreSQL: Database,
    MongoDB: Database,
};
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-gray-900 to-blue-900 text-white"
    >
        <!-- Header Tecnológico -->
        <header class="relative overflow-hidden border-b border-cyan-900/50 bg-slate-950 p-6 md:p-12">
            <!-- Efectos de fondo tecnológico -->
            <div class="absolute inset-0 opacity-10">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-500 to-cyan-500 blur-3xl"
                ></div>
            </div>

            <!-- Grid Background -->
            <div class="bg-grid-white absolute inset-0 opacity-5"></div>

            <div class="relative mx-auto max-w-6xl">
                <div class="header-layout">
                    <div class="header-text-section">
                        <h1
                            class="mb-2 md:mb-3 bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-3xl md:text-5xl font-bold text-transparent"
                        >
                            {{ data.personal.name || 'Tu Nombre' }}
                        </h1>
                        <p class="mb-4 md:mb-6 text-lg md:text-2xl text-gray-300">
                            {{
                                data.personal.title ||
                                'Desarrollador de Software'
                            }}
                        </p>

                        <!-- Stack tecnológico -->
                        <div
                            v-if="data.skills?.technical?.length"
                            class="flex flex-wrap gap-2"
                        >
                            <span
                                v-for="tech in data.skills.technical.slice(
                                    0,
                                    5,
                                )"
                                :key="tech.name"
                                class="rounded-full border border-blue-400/30 bg-blue-400/10 px-3 py-1.5 md:px-4 md:py-2 text-xs md:text-sm backdrop-blur-sm"
                            >
                                {{ tech.name }}
                            </span>
                        </div>
                    </div>

                   <!-- Profile Photo -->
                    <div v-if="data.personal.photo" class="banner-photo">
                        <div class="relative h-40 w-40">
                             <div class="absolute inset-0 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 blur-lg opacity-50"></div>
                             <div class="relative h-full w-full overflow-hidden rounded-full border-2 border-cyan-500/50 p-1 bg-gray-900">
                                <img 
                                    :src="data.personal.photo" 
                                    :alt="data.personal.name" 
                                    class="h-full w-full rounded-full object-cover"
                                />
                             </div>
                        </div>
                    </div>

                    <!-- Contacto Tecnológico -->
                    <div class="header-contact-section">
                        <div class="space-y-2 text-sm text-gray-300">
                            <div
                                v-if="data.personal.email"
                                class="contact-item-row"
                            >
                                <span>{{ data.personal.email }}</span>
                            </div>
                            <div
                                v-if="data.personal.phone"
                                class="contact-item-row"
                            >
                                <span>{{ data.personal.phone }}</span>
                            </div>
                            <div
                                v-if="data.personal.location"
                                class="contact-item-row"
                            >
                                <span>{{ data.personal.location }}</span>
                            </div>
                            <div class="social-links-row">
                                <a
                                    v-if="data.personal.github"
                                    :href="ensureUrl(data.personal.github)"
                                    target="_blank"
                                    class="rounded-lg bg-white/10 p-2 backdrop-blur-sm transition hover:bg-white/20"
                                >
                                    <Code class="h-5 w-5" />
                                </a>
                                <a
                                    v-if="data.personal.linkedin"
                                    :href="ensureUrl(data.personal.linkedin)"
                                    target="_blank"
                                    class="rounded-lg bg-white/10 p-2 backdrop-blur-sm transition hover:bg-white/20"
                                >
                                    <Terminal class="h-5 w-5" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main class="relative mx-auto max-w-6xl px-4 py-8 md:px-8 md:py-12">
            <!-- Resumen Tecnológico -->
            <section v-if="data.personal.summary" class="mb-12">
                <div
                    class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-8 backdrop-blur-sm"
                >
                    <h2 class="mb-4 flex items-center text-2xl font-bold">
                        <Terminal class="mr-3 h-6 w-6 text-cyan-400" />
                        Perfil Técnico
                    </h2>
                    <p class="leading-relaxed text-gray-300">
                        {{ data.personal.summary }}
                    </p>
                </div>
            </section>

            <div class="main-grid">
                <!-- Columna Principal -->
                <div class="lg:col-span-2">
                    <!-- Experiencia Tecnológica -->
                    <section v-if="data.experience?.length" class="mb-8">
                        <h2 class="mb-6 flex items-center text-2xl font-bold">
                            <Server class="mr-3 h-6 w-6 text-cyan-400" />
                            Experiencia Técnica
                        </h2>

                        <!-- Carousel Container -->
                        <div class="relative group">
                            <!-- Navigation Arrows - Elegant glassmorphism style for dark theme -->
                            <button 
                                v-if="experienceIndex > 0"
                                @click="prevExperience"
                                class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-gray-800/90 backdrop-blur-sm border border-cyan-400/30 text-cyan-400 shadow-md hover:shadow-cyan-400/20 hover:shadow-lg hover:bg-gray-800 hover:border-cyan-400 transition-all duration-300 md:opacity-0 group-hover:opacity-100"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button 
                                v-if="experienceIndex < data.experience.length - 1"
                                @click="nextExperience"
                                class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-gray-800/90 backdrop-blur-sm border border-cyan-400/30 text-cyan-400 shadow-md hover:shadow-cyan-400/20 hover:shadow-lg hover:bg-gray-800 hover:border-cyan-400 transition-all duration-300 md:opacity-0 group-hover:opacity-100"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>

                            <!-- Experience Display (2 at a time on lg) -->
                            <div class="cards-grid">
                                <div
                                    v-for="(exp, index) in data.experience.slice(experienceIndex, experienceIndex + 2)"
                                    :key="index"
                                    class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm transition-all duration-300"
                                >
                                    <div
                                        class="mb-4 flex items-start justify-between"
                                    >
                                        <div class="flex-1">
                                            <h3
                                                class="text-lg font-bold text-gray-100"
                                            >
                                                {{ exp.position }}
                                            </h3>
                                            <p class="text-blue-400">
                                                {{ exp.company }}
                                            </p>
                                        </div>
                                        <div
                                            class="text-right text-xs text-gray-400"
                                        >
                                            <p>
                                                {{ formatDate(exp.startDate) }}
                                                -
                                            </p>
                                            <p>
                                                {{
                                                    exp.current
                                                        ? 'Presente'
                                                        : formatDate(
                                                              exp.endDate || '',
                                                          )
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <p class="text-sm leading-relaxed text-gray-300">
                                        {{ exp.description }}
                                    </p>

                                    <!-- Tecnologías usadas -->
                                    <div
                                        v-if="exp.technologies?.length"
                                        class="flex flex-wrap gap-2 mt-4"
                                    >
                                        <span
                                            v-for="tech in exp.technologies"
                                            :key="tech"
                                            class="rounded-full border border-cyan-400/30 bg-cyan-400/10 px-3 py-1 text-xs"
                                        >
                                            {{ tech }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Dots Indicator - Modern pill style -->
                            <div v-if="data.experience.length > 2" class="flex justify-center items-center gap-1.5 mt-6">
                                <button 
                                    v-for="(_, idx) in Math.ceil(data.experience.length / 2)" 
                                    :key="idx"
                                    @click="experienceIndex = idx * 2"
                                    class="rounded-full transition-all duration-300"
                                    :class="idx * 2 === experienceIndex ? 'w-6 h-2 bg-blue-500' : 'w-2 h-2 bg-gray-600 hover:bg-gray-500'"
                                />
                            </div>
                        </div>
                    </section>

                    <!-- Proyectos Técnicos -->
                    <section v-if="data.projects?.length" class="mb-8">
                        <div class="mb-6 flex items-center">
                            <Code class="mr-3 h-6 w-6 text-cyan-400" />
                            <h2 class="text-2xl font-bold text-gray-900">
                                <span class="text-gray-100">Proyectos</span>
                                <span class="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent"> Destacados</span>
                            </h2>
                        </div>

                        <!-- Carousel Container -->
                        <div class="relative group">
                            <!-- Navigation Arrows - Elegant glassmorphism style for dark theme -->
                            <button 
                                v-if="projectIndex > 0"
                                @click="prevProject"
                                class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-gray-800/90 backdrop-blur-sm border border-cyan-400/30 text-cyan-400 shadow-md hover:shadow-cyan-400/20 hover:shadow-lg hover:bg-gray-800 hover:border-cyan-400 transition-all duration-300 md:opacity-0 group-hover:opacity-100"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button 
                                v-if="projectIndex < data.projects.length - 1"
                                @click="nextProject"
                                class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-gray-800/90 backdrop-blur-sm border border-cyan-400/30 text-cyan-400 shadow-md hover:shadow-cyan-400/20 hover:shadow-lg hover:bg-gray-800 hover:border-cyan-400 transition-all duration-300 md:opacity-0 group-hover:opacity-100"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>

                           <!-- Projects Display (2 at a time) -->
                            <div class="cards-grid">
                                <div
                                    v-for="(project, index) in data.projects.slice(projectIndex, projectIndex + 2)"
                                    :key="index"
                                    class="group/card relative rounded-2xl border border-blue-400/20 bg-gray-900 overflow-hidden transition-all duration-300 hover:border-cyan-400/50 hover:shadow-lg hover:shadow-cyan-400/10"
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
                                    <div v-else-if="project.image" class="mb-4 overflow-hidden rounded-lg border border-cyan-500/30">
                                        <img 
                                            :src="project.image" 
                                            :alt="project.name" 
                                            class="h-48 w-full object-cover transition-transform hover:scale-105 cursor-pointer"
                                            @click="openGallery([project.image], 0)"
                                        />
                                    </div>
                                    <h3 class="mb-2 font-bold">
                                        {{ project.name }}
                                    </h3>
                                    <p class="mb-3 text-sm text-gray-400">
                                        {{ project.description }}
                                    </p>

                                    <div
                                        v-if="project.technologies?.length"
                                        class="flex flex-wrap gap-1"
                                    >
                                        <span
                                            v-for="tech in project.technologies"
                                            :key="tech"
                                            class="rounded-full bg-cyan-400/20 px-2 py-1 text-xs text-cyan-300"
                                        >
                                            {{ tech }}
                                        </span>
                                    </div>

                                    <a
                                        v-if="project.url"
                                        :href="project.url"
                                        target="_blank"
                                        class="mt-3 inline-block text-sm text-cyan-400 hover:text-cyan-300"
                                    >
                                        Ver código →
                                    </a>
                                </div>
                            </div>

                            <!-- Dots Indicator - Modern pill style -->
                            <div v-if="data.projects.length > 1" class="flex justify-center items-center gap-1.5 mt-6">
                                <button 
                                    v-for="(_, idx) in data.projects" 
                                    :key="idx"
                                    @click="projectIndex = idx"
                                    class="rounded-full transition-all duration-300"
                                    :class="idx === projectIndex ? 'w-6 h-2 bg-cyan-400' : 'w-2 h-2 bg-gray-600 hover:bg-cyan-400/50'"
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
                    <!-- Stack Tecnológico -->
                    <section v-if="data.skills?.technical?.length" class="mb-8">
                        <div
                            class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm"
                        >
                            <h2
                                class="mb-4 flex items-center text-xl font-bold"
                            >
                                <Cpu class="mr-2 h-5 w-5 text-cyan-400" />
                                Tech Stack
                            </h2>

                            <div class="space-y-4">
                                <div
                                    v-for="skill in data.skills.technical"
                                    :key="skill.name"
                                    class="flex items-center justify-between"
                                >
                                    <div class="flex items-center">
                                        <component
                                            :is="techIcons[skill.name] || Code"
                                            class="mr-3 h-4 w-4 text-cyan-400"
                                        />
                                        <span class="text-sm font-medium">{{ skill.name }}</span>
                                    </div>
                                    <div
                                        class="h-1 w-16 rounded-full bg-gray-700 overflow-hidden"
                                    >
                                        <div
                                            class="h-full rounded-full bg-cyan-400"
                                            :style="{ width: skill.level ? skill.level + '%' : '85%' }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Habilidades Blandas -->
                    <section v-if="data.skills?.soft?.length" class="mb-8">
                        <div
                            class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm"
                        >
                            <h2
                                class="mb-4 flex items-center text-xl font-bold"
                            >
                                <Users class="mr-2 h-5 w-5 text-purple-400" />
                                Soft Skills
                            </h2>

                            <div class="space-y-3">
                                <div
                                    v-for="skill in data.skills.soft"
                                    :key="skill.name"
                                    class="flex flex-col"
                                >
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium">{{ skill.name }}</span>
                                        <span v-if="skill.level" class="text-xs text-gray-400">{{ skill.level }}%</span>
                                    </div>
                                    <div
                                        class="h-1 w-full rounded-full bg-gray-700 overflow-hidden"
                                    >
                                        <div
                                            class="h-full rounded-full bg-purple-400"
                                            :style="{ width: skill.level ? skill.level + '%' : '100%' }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Educación Técnica -->
                    <section v-if="data.education?.length" class="mb-8">
                        <div
                            class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm"
                        >
                            <h2 class="mb-4 text-xl font-bold">Educación</h2>

                            <div class="space-y-4">
                                <div
                                    v-for="(edu, index) in data.education"
                                    :key="index"
                                    class="border-l-2 border-cyan-400 pl-4"
                                >
                                    <h3 class="font-semibold">
                                        {{ edu.degree }}
                                    </h3>
                                    <p class="text-sm text-cyan-400">
                                        {{ edu.institution }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ formatDate(edu.startDate) }} -
                                        {{ formatDate(edu.endDate) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Certificaciones Técnicas -->
                    <section v-if="data.certifications?.length" class="mb-8">
                        <div
                            class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm"
                        >
                            <h2 class="mb-4 text-xl font-bold">
                                Certificaciones
                            </h2>

                            <div class="space-y-3">
                                <div
                                    v-for="(cert, index) in data.certifications"
                                    :key="index"
                                    class="flex items-start"
                                >
                                    <div
                                        class="mr-3 rounded bg-cyan-400/20 p-1"
                                    >
                                        <Cloud class="h-4 w-4 text-cyan-400" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold">
                                            {{ cert.name }}
                                        </h3>
                                        <p class="text-xs text-gray-400">
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
                            class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm"
                        >
                            <h2 class="mb-4 text-xl font-bold">Idiomas</h2>

                            <div class="space-y-2">
                                <div
                                    v-for="(lang, index) in data.languages"
                                    :key="index"
                                    class="flex items-center justify-between"
                                >
                                    <span class="text-sm">{{ lang.name }}</span>
                                    <span
                                        class="rounded bg-cyan-400/20 px-2 py-1 text-xs text-cyan-300"
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

        <!-- Footer Tecnológico -->
        <footer class="border-t border-blue-400/20 py-8 text-center">
            <p class="text-gray-400">
                Desarrollado con 💻 • {{ new Date().getFullYear() }}
            </p>
        </footer>
    </div>
</template>

<style scoped>
.bg-grid-white {
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
    background-size: 50px 50px;
}

.transition {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
    border-radius: 0.5rem;
    border: 1px solid rgba(34, 211, 238, 0.3);
    transition: transform 0.2s, border-color 0.2s;
}

.gallery-thumbnail:hover {
    transform: scale(1.03);
    border-color: rgba(34, 211, 238, 0.6);
}

.gallery-more {
    position: absolute;
    bottom: 0;
    right: 0;
    width: calc(50% - 0.25rem);
    height: 6rem;
    background: rgba(0, 0, 0, 0.7);
    color: #22d3ee;
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
    background: rgba(0, 0, 0, 0.85);
}
.portfolio-container {
    container-type: inline-size;
}

.bg-grid-white {
    background-image: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23fff' fill-opacity='0.1' fill-rule='evenodd'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E");
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
    border: 1px solid rgba(56, 189, 248, 0.3); /* sky-400/30 */
    transition: transform 0.2s, box-shadow 0.2s;
}

.gallery-thumbnail:hover {
    transform: scale(1.02);
    box-shadow: 0 0 15px rgba(34, 211, 238, 0.2); /* cyan-400/20 */
    border-color: rgba(34, 211, 238, 0.6);
}

.gallery-more {
    position: absolute;
    bottom: 0;
    right: 0;
    width: calc(50% - 0.25rem);
    height: 6rem;
    background: rgba(15, 23, 42, 0.8); /* slate-900/80 */
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
    background: rgba(15, 23, 42, 0.95);
}

/* Container Queries */
.header-layout {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

@container (min-width: 64rem) { /* lg equivalent */
    .header-layout {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 0;
    }
}

.header-text-section {
    text-align: left;
}

.header-contact-section {
    text-align: left;
}

@container (min-width: 64rem) {
    .header-contact-section {
        text-align: right;
    }
}

.banner-photo {
    display: block;
    margin: 1.5rem auto;
    flex-shrink: 0;
}

@container (min-width: 64rem) {
    .banner-photo {
        margin: 0 2rem;
    }
}

.contact-item-row {
    display: flex;
    align-items: center;
}

@container (min-width: 64rem) {
    .contact-item-row {
        justify-content: flex-end;
    }
}

.social-links-row {
    margin-top: 0.75rem;
    display: flex;
    gap: 0.75rem;
}

@container (min-width: 64rem) {
    .social-links-row {
        justify-content: flex-end;
    }
}

/* Grids */
.main-grid {
    display: grid;
    gap: 2rem;
    grid-template-columns: 1fr;
}

@container (min-width: 64rem) {
    .main-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

.cards-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: 1fr;
}

@container (min-width: 48rem) {
    .cards-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>
```
