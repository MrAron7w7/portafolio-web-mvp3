<script setup lang="ts">
import {
    Briefcase,
    Calendar,
    Github,
    Globe,
    Linkedin,
    Mail,
    MapPin,
    Phone,
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
            description?: string;
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

// Formatear fechas
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
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
        <!-- Header Principal -->
        <header class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-cyan-600 px-8 py-16 text-white">
            <!-- Decoración de fondo -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-white blur-3xl"></div>
                <div class="absolute bottom-0 left-0 h-48 w-48 rounded-full bg-white blur-3xl"></div>
            </div>

            <div class="relative mx-auto max-w-5xl">
                <div class="flex flex-col items-center text-center md:flex-row md:text-left">
                    <!-- Foto de perfil -->
                    <div v-if="data.personal.photo" class="mb-6 md:mr-8 md:mb-0">
                        <div class="h-32 w-32 overflow-hidden rounded-full border-4 border-white shadow-xl">
                            <img :src="data.personal.photo" :alt="data.personal.name"
                                class="h-full w-full object-cover" />
                        </div>
                    </div>

                    <!-- Información principal -->
                    <div class="flex-1">
                        <h1 class="mb-2 text-4xl font-bold md:text-5xl">
                            {{ data.personal.name || 'Tu Nombre' }}
                        </h1>
                        <p class="mb-4 text-xl text-blue-100 md:text-2xl">
                            {{ data.personal.title || 'Tu Título Profesional' }}
                        </p>

                        <!-- Contacto -->
                        <div class="flex flex-wrap items-center justify-center gap-4 md:justify-start">
                            <a v-if="data.personal.email" :href="`mailto:${data.personal.email}`"
                                class="flex items-center space-x-2 rounded-lg bg-white/10 px-3 py-2 backdrop-blur-sm transition hover:bg-white/20">
                                <Mail class="h-4 w-4" />
                                <span class="text-sm">{{
                                    data.personal.email
                                }}</span>
                            </a>

                            <a v-if="data.personal.phone" :href="`tel:${data.personal.phone}`"
                                class="flex items-center space-x-2 rounded-lg bg-white/10 px-3 py-2 backdrop-blur-sm transition hover:bg-white/20">
                                <Phone class="h-4 w-4" />
                                <span class="text-sm">{{
                                    data.personal.phone
                                }}</span>
                            </a>

                            <div v-if="data.personal.location"
                                class="flex items-center space-x-2 rounded-lg bg-white/10 px-3 py-2 backdrop-blur-sm">
                                <MapPin class="h-4 w-4" />
                                <span class="text-sm">{{
                                    data.personal.location
                                }}</span>
                            </div>
                        </div>

                        <!-- Links sociales -->
                        <div class="mt-4 flex items-center justify-center gap-3 md:justify-start">
                            <a v-if="data.personal.website" :href="data.personal.website" target="_blank"
                                class="rounded-lg bg-white/10 p-2 backdrop-blur-sm transition hover:bg-white/20">
                                <Globe class="h-5 w-5" />
                            </a>
                            <a v-if="data.personal.linkedin" :href="`https://${data.personal.linkedin}`" target="_blank"
                                class="rounded-lg bg-white/10 p-2 backdrop-blur-sm transition hover:bg-white/20">
                                <Linkedin class="h-5 w-5" />
                            </a>
                            <a v-if="data.personal.github" :href="`https://${data.personal.github}`" target="_blank"
                                class="rounded-lg bg-white/10 p-2 backdrop-blur-sm transition hover:bg-white/20">
                                <Github class="h-5 w-5" />
                            </a>


                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main class="mx-auto max-w-5xl px-8 py-12">
            <!-- Resumen Profesional -->
            <section v-if="data.personal.summary" class="mb-12">
                <div class="rounded-2xl bg-white p-8 shadow-md">
                    <h2 class="mb-4 text-2xl font-bold text-gray-900">
                        Perfil Profesional
                    </h2>
                    <p class="leading-relaxed text-gray-700">
                        {{ data.personal.summary }}
                    </p>
                    <p class="leading-relaxed text-gray-700">
                        {{ data.personal.description }}
                    </p>
                </div>
            </section>

            <div class="grid gap-8 lg:grid-cols-3">
                <!-- Columna Principal -->
                <div class="lg:col-span-2">
                    <!-- Experiencia Laboral -->
                    <section v-if="data.experience?.length" class="mb-8">
                        <h2 class="mb-6 flex items-center text-2xl font-bold text-gray-900">
                            <Briefcase class="mr-3 h-6 w-6 text-blue-600" />
                            Experiencia Laboral
                        </h2>

                        <div class="space-y-6">
                            <div v-for="(exp, index) in data.experience" :key="index"
                                class="relative rounded-2xl bg-white p-6 shadow-md transition hover:shadow-lg">
                                <!-- Línea temporal -->
                                <div class="absolute top-0 left-0 h-full w-1 bg-blue-600"></div>

                                <div class="relative pl-8">
                                    <div class="mb-2 flex flex-wrap items-start justify-between">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">
                                                {{ exp.position }}
                                            </h3>
                                            <p class="text-lg text-blue-600">
                                                {{ exp.company }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mb-3 flex items-center space-x-4 text-sm text-gray-600">
                                        <span class="flex items-center">
                                            <Calendar class="mr-1 h-4 w-4" />
                                            {{ formatDate(exp.startDate) }} -
                                            {{
                                                exp.current
                                                    ? 'Actualidad'
                                                    : formatDate(
                                                        exp.endDate || '',
                                                    )
                                            }}
                                        </span>
                                        <span class="text-gray-400">•</span>
                                        <span>{{
                                            calculateDuration(
                                                exp.startDate,
                                                exp.endDate,
                                                exp.current,
                                            )
                                        }}</span>
                                    </div>

                                    <p class="leading-relaxed text-gray-700">
                                        {{ exp.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Proyectos -->
                    <section v-if="data.projects?.length" class="mb-8">
                        <h2 class="mb-6 text-2xl font-bold text-gray-900">
                            Proyectos Destacados
                        </h2>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div v-for="(project, index) in data.projects" :key="index"
                                class="rounded-2xl bg-white p-6 shadow-md transition hover:shadow-lg">
                                <!-- Imagen del proyecto (opcional) -->
                                <img v-if="project.image" :src="project.image" :alt="project.name"
                                    class="mb-4 h-48 w-full rounded-lg object-cover" />

                                <h3 class="mb-2 text-lg font-bold text-gray-900">
                                    {{ project.name }}
                                </h3>

                                <p class="mb-3 text-sm text-gray-600">
                                    {{ project.description }}
                                </p>

                                <!-- Tecnologías -->
                                <div v-if="project.technologies?.length" class="mb-4 flex flex-wrap gap-2">
                                    <span v-for="tech in project.technologies" :key="tech"
                                        class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
                                        {{ tech }}
                                    </span>
                                </div>

                                <!-- Link/URL del proyecto - Elegante y compacto -->
                                <a v-if="project.link" :href="project.link" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 transition hover:text-blue-800 hover:underline">
                                    Ver proyecto
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </section>


                </div>

                <!-- Sidebar -->
                <div>
                    <!-- Habilidades -->
                    <section v-if="
                        data.skills?.technical?.length ||
                        data.skills?.soft?.length
                    " class="mb-8">
                        <div class="rounded-2xl bg-white p-6 shadow-md">
                            <h2 class="mb-4 text-xl font-bold text-gray-900">
                                Habilidades
                            </h2>

                            <div v-if="data.skills.technical?.length" class="mb-4">
                                <h3 class="mb-2 text-sm font-semibold text-gray-700">
                                    Técnicas
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="skill in data.skills.technical" :key="skill"
                                        class="rounded-lg bg-blue-50 px-3 py-1 text-sm text-blue-700">
                                        {{ skill.name }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="data.skills.soft?.length">
                                <h3 class="mb-2 text-sm font-semibold text-gray-700">
                                    Blandas
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="skill in data.skills.soft" :key="skill"
                                        class="rounded-lg bg-gray-50 px-3 py-1 text-sm text-gray-700">
                                        {{ skill.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Educación -->
                    <section v-if="data.education?.length" class="mb-8">
                        <div class="rounded-2xl bg-white p-6 shadow-md">
                            <h2 class="mb-4 text-xl font-bold text-gray-900">
                                Educación
                            </h2>

                            <div class="space-y-4">
                                <div v-for="(edu, index) in data.education" :key="index"
                                    class="border-l-2 border-blue-600 pl-4">
                                    <h3 class="font-semibold text-gray-900">
                                        {{ edu.degree }}
                                    </h3>
                                    <p class="text-sm text-blue-600">
                                        {{ edu.institution }}
                                    </p>
                                    <p class="text-xs text-gray-600">
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
                    <section v-if="data.languages?.length" class="mb-8">
                        <div class="rounded-2xl bg-white p-6 shadow-md">
                            <h2 class="mb-4 text-xl font-bold text-gray-900">
                                Idiomas
                            </h2>

                            <div class="space-y-3">
                                <div v-for="(lang, index) in data.languages" :key="index">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-900">{{ lang.name }}</span>
                                        <span class="text-sm text-gray-600">{{
                                            lang.level
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 bg-white py-8 text-center">
            <p class="text-gray-600">
                Generado con PortafolioAI • {{ new Date().getFullYear() }}
            </p>
        </footer>
    </div>
</template>

<style scoped>
/* Animaciones suaves */
.transition {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Efectos hover personalizados */
.shadow-md:hover {
    transform: translateY(-2px);
}
</style>
