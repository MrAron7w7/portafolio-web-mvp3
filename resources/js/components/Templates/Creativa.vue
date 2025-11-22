<script setup lang="ts">
import { Heart, Palette, Sparkles, Star } from 'lucide-vue-next';

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
    <div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50">
        <!-- Header Creativo -->
        <header
            class="relative overflow-hidden bg-gradient-to-r from-purple-600 to-pink-500 px-8 py-16 text-white"
        >
            <!-- Elementos decorativos -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-10 left-10">
                    <Palette class="h-24 w-24" />
                </div>
                <div class="absolute right-10 bottom-10">
                    <Sparkles class="h-24 w-24" />
                </div>
            </div>

            <div class="relative mx-auto max-w-4xl text-center">
                <!-- Foto de perfil circular -->
                <div v-if="data.personal.photo" class="mb-6 inline-block">
                    <div class="relative">
                        <div
                            class="h-32 w-32 overflow-hidden rounded-full border-4 border-white shadow-2xl"
                        >
                            <img
                                :src="data.personal.photo"
                                :alt="data.personal.name"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <div
                            class="absolute -right-2 -bottom-2 rounded-full bg-yellow-400 p-2"
                        >
                            <Star class="h-4 w-4 text-white" />
                        </div>
                    </div>
                </div>

                <h1 class="mb-4 text-5xl font-bold">
                    {{ data.personal.name || 'Tu Nombre' }}
                </h1>
                <p class="mb-6 text-2xl text-purple-100">
                    {{ data.personal.title || 'Tu Título Creativo' }}
                </p>

                <!-- Contacto estilo creativo -->
                <div class="flex flex-wrap justify-center gap-4">
                    <a
                        v-if="data.personal.email"
                        :href="`mailto:${data.personal.email}`"
                        class="rounded-full bg-white/20 px-6 py-3 backdrop-blur-sm transition hover:bg-white/30"
                    >
                        ✉️ {{ data.personal.email }}
                    </a>
                    <div
                        v-if="data.personal.phone"
                        class="rounded-full bg-white/20 px-6 py-3 backdrop-blur-sm"
                    >
                        📞 {{ data.personal.phone }}
                    </div>
                    <div
                        v-if="data.personal.location"
                        class="rounded-full bg-white/20 px-6 py-3 backdrop-blur-sm"
                    >
                        📍 {{ data.personal.location }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main class="mx-auto max-w-4xl px-8 py-12">
            <!-- Resumen Creativo -->
            <section v-if="data.personal.summary" class="mb-12">
                <div class="relative rounded-3xl bg-white p-8 shadow-xl">
                    <div
                        class="absolute -top-4 left-8 rounded-full bg-pink-500 p-3"
                    >
                        <Heart class="h-6 w-6 text-white" />
                    </div>
                    <h2 class="mb-4 text-2xl font-bold text-gray-900">
                        Mi Historia
                    </h2>
                    <p class="text-lg leading-relaxed text-gray-700">
                        {{ data.personal.summary }}
                    </p>
                </div>
            </section>

            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Experiencia Creativa -->
                <section v-if="data.experience?.length" class="lg:col-span-2">
                    <h2
                        class="mb-8 text-center text-3xl font-bold text-gray-900"
                    >
                        🎨 Experiencia Profesional
                    </h2>

                    <div class="space-y-6">
                        <div
                            v-for="(exp, index) in data.experience"
                            :key="index"
                            class="relative rounded-2xl bg-white p-6 shadow-lg transition-transform hover:scale-105"
                            :class="
                                index % 2 === 0
                                    ? 'border-l-4 border-purple-500'
                                    : 'border-l-4 border-pink-500'
                            "
                        >
                            <div class="mb-3 flex items-start justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        {{ exp.position }}
                                    </h3>
                                    <p class="text-lg text-purple-600">
                                        {{ exp.company }}
                                    </p>
                                </div>
                                <span
                                    class="rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-1 text-sm text-white"
                                >
                                    {{ formatDate(exp.startDate) }} –
                                    {{
                                        exp.current
                                            ? 'Ahora'
                                            : formatDate(exp.endDate || '')
                                    }}
                                </span>
                            </div>
                            <p class="leading-relaxed text-gray-700">
                                {{ exp.description }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Habilidades Creativas -->
                <section
                    v-if="
                        data.skills?.technical?.length ||
                        data.skills?.soft?.length
                    "
                >
                    <div
                        class="rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 p-8 text-white"
                    >
                        <h2 class="mb-6 text-2xl font-bold">
                            Mis Superpoderes
                        </h2>

                        <div v-if="data.skills.technical?.length" class="mb-6">
                            <h3 class="mb-3 text-lg font-semibold">
                                💻 Habilidades Técnicas
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="skill in data.skills.technical"
                                    :key="skill"
                                    class="rounded-full bg-white/20 px-4 py-2 text-sm backdrop-blur-sm"
                                >
                                    {{ skill }}
                                </span>
                            </div>
                        </div>

                        <div v-if="data.skills.soft?.length">
                            <h3 class="mb-3 text-lg font-semibold">
                                🌟 Habilidades Blandas
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="skill in data.skills.soft"
                                    :key="skill"
                                    class="rounded-full bg-white/20 px-4 py-2 text-sm backdrop-blur-sm"
                                >
                                    {{ skill }}
                                </span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Proyectos Creativos -->
                <section v-if="data.projects?.length">
                    <div class="rounded-2xl bg-white p-8 shadow-lg">
                        <h2 class="mb-6 text-2xl font-bold text-gray-900">
                            ✨ Proyectos Destacados
                        </h2>

                        <div class="space-y-4">
                            <div
                                v-for="(project, index) in data.projects"
                                :key="index"
                                class="rounded-xl border-2 border-dashed border-purple-200 p-4 transition hover:border-purple-400"
                            >
                                <h3 class="font-bold text-gray-900">
                                    {{ project.name }}
                                </h3>
                                <p class="mb-2 text-sm text-gray-600">
                                    {{ project.description }}
                                </p>
                                <div
                                    v-if="project.technologies?.length"
                                    class="flex flex-wrap gap-1"
                                >
                                    <span
                                        v-for="tech in project.technologies"
                                        :key="tech"
                                        class="rounded-full bg-purple-100 px-2 py-1 text-xs text-purple-700"
                                    >
                                        {{ tech }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Educación -->
                <section v-if="data.education?.length" class="lg:col-span-2">
                    <div class="rounded-2xl bg-white p-8 shadow-lg">
                        <h2 class="mb-6 text-2xl font-bold text-gray-900">
                            🎓 Trayectoria Académica
                        </h2>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div
                                v-for="(edu, index) in data.education"
                                :key="index"
                                class="rounded-xl bg-gradient-to-r from-purple-50 to-pink-50 p-6"
                            >
                                <h3 class="font-bold text-gray-900">
                                    {{ edu.degree }}
                                </h3>
                                <p class="text-purple-600">
                                    {{ edu.institution }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ formatDate(edu.startDate) }} -
                                    {{ formatDate(edu.endDate) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- Footer Creativo -->
        <footer class="border-t border-purple-200 bg-white py-8 text-center">
            <div class="mb-4 flex justify-center space-x-4">
                <a
                    v-if="data.personal.website"
                    :href="data.personal.website"
                    target="_blank"
                    class="rounded-full bg-purple-100 p-3 text-purple-600 transition hover:bg-purple-200"
                >
                    🌐 Sitio Web
                </a>
                <a
                    v-if="data.personal.linkedin"
                    :href="data.personal.linkedin"
                    target="_blank"
                    class="rounded-full bg-blue-100 p-3 text-blue-600 transition hover:bg-blue-200"
                >
                    💼 LinkedIn
                </a>
                <a
                    v-if="data.personal.github"
                    :href="data.personal.github"
                    target="_blank"
                    class="rounded-full bg-gray-100 p-3 text-gray-600 transition hover:bg-gray-200"
                >
                    💻 GitHub
                </a>
            </div>
            <p class="text-gray-600">
                Creado con 💜 • {{ new Date().getFullYear() }}
            </p>
        </footer>
    </div>
</template>

<style scoped>
.transition {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-scale:hover {
    transform: scale(1.05);
}
</style>
