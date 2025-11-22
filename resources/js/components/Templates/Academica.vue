<script setup lang="ts">
import { Award, BookOpen, GraduationCap, Users } from 'lucide-vue-next';

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
        <div class="mx-auto max-w-5xl px-8 py-12">
            <!-- Header Académico -->
            <header class="mb-12 border-b border-gray-300 pb-8">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="mb-2 text-4xl font-bold text-gray-900">
                            {{ data.personal.name || 'Tu Nombre' }}
                        </h1>
                        <p class="mb-4 text-xl text-gray-600">
                            {{ data.personal.title || 'Tu Título Académico' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="space-y-1 text-sm text-gray-600">
                            <div
                                v-if="data.personal.email"
                                class="flex items-center justify-end"
                            >
                                <span>{{ data.personal.email }}</span>
                            </div>
                            <div
                                v-if="data.personal.phone"
                                class="flex items-center justify-end"
                            >
                                <span>{{ data.personal.phone }}</span>
                            </div>
                            <div
                                v-if="data.personal.location"
                                class="flex items-center justify-end"
                            >
                                <span>{{ data.personal.location }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-4 gap-8">
                <!-- Columna Principal -->
                <div class="col-span-3">
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
                        <div class="space-y-6">
                            <div
                                v-for="(exp, index) in data.experience"
                                :key="index"
                                class="border-l-2 border-blue-600 pl-4"
                            >
                                <div
                                    class="mb-2 flex items-start justify-between"
                                >
                                    <h3
                                        class="text-lg font-semibold text-gray-900"
                                    >
                                        {{ exp.position }}
                                    </h3>
                                    <span
                                        class="text-sm whitespace-nowrap text-gray-500"
                                    >
                                        {{ formatDate(exp.startDate) }} –
                                        {{
                                            exp.current
                                                ? 'Presente'
                                                : formatDate(exp.endDate || '')
                                        }}
                                    </span>
                                </div>
                                <p class="mb-2 font-medium text-blue-600">
                                    {{ exp.company }}
                                </p>
                                <p class="text-gray-700">
                                    {{ exp.description }}
                                </p>
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
