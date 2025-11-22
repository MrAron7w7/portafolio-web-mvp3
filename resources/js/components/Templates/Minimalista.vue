<script setup lang="ts">
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

const formatDate = (date: string) => {
    if (!date) return '';
    const [year, month] = date.split('-');
    return `${month}/${year}`;
};
</script>

<template>
    <div class="min-h-screen bg-white">
        <div class="mx-auto max-w-4xl px-8 py-16">
            <!-- Header Minimalista -->
            <header class="mb-16 border-b border-gray-900 pb-8">
                <h1
                    class="mb-2 font-serif text-5xl font-light tracking-tight text-gray-900"
                >
                    {{ data.personal.name || 'Tu Nombre' }}
                </h1>
                <p class="mb-6 text-xl font-light text-gray-600">
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
                        :href="data.personal.website"
                        target="_blank"
                        class="hover:text-gray-900"
                    >
                        Sitio Web
                    </a>
                    <a
                        v-if="data.personal.linkedin"
                        :href="data.personal.linkedin"
                        target="_blank"
                        class="hover:text-gray-900"
                    >
                        LinkedIn
                    </a>
                    <a
                        v-if="data.personal.github"
                        :href="data.personal.github"
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

                <div class="space-y-10">
                    <div v-for="(exp, index) in data.experience" :key="index">
                        <div class="mb-2 flex items-baseline justify-between">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ exp.position }}
                            </h3>
                            <span class="text-sm text-gray-500">
                                {{ formatDate(exp.startDate) }} –
                                {{
                                    exp.current
                                        ? 'Presente'
                                        : formatDate(exp.endDate || '')
                                }}
                            </span>
                        </div>
                        <p class="mb-3 text-gray-600">{{ exp.company }}</p>
                        <p class="leading-relaxed text-gray-700">
                            {{ exp.description }}
                        </p>
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
                            {{ data.skills.technical.join(' • ') }}
                        </p>
                    </div>

                    <div v-if="data.skills.soft?.length">
                        <h3
                            class="mb-2 text-sm font-medium tracking-wide text-gray-500 uppercase"
                        >
                            Competencias
                        </h3>
                        <p class="leading-relaxed text-gray-700">
                            {{ data.skills.soft.join(' • ') }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Proyectos -->
            <section v-if="data.projects?.length" class="mb-16">
                <h2 class="mb-8 font-serif text-2xl font-light text-gray-900">
                    Proyectos
                </h2>

                <div class="space-y-8">
                    <div v-for="(project, index) in data.projects" :key="index">
                        <h3 class="mb-2 text-lg font-medium text-gray-900">
                            {{ project.name }}
                        </h3>
                        <p class="mb-2 leading-relaxed text-gray-700">
                            {{ project.description }}
                        </p>
                        <p
                            v-if="project.technologies?.length"
                            class="text-sm text-gray-500"
                        >
                            {{ project.technologies.join(' • ') }}
                        </p>
                        <a
                            v-if="project.url"
                            :href="project.url"
                            target="_blank"
                            class="text-sm text-gray-900 underline"
                        >
                            Ver proyecto
                        </a>
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
