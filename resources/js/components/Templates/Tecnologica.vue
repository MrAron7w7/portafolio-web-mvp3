<script setup lang="ts">
import { Cloud, Code, Cpu, Database, Server, Terminal } from 'lucide-vue-next';

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
        <header class="relative overflow-hidden px-8 py-16">
            <!-- Efectos de fondo tecnológico -->
            <div class="absolute inset-0 opacity-10">
                <div
                    class="absolute top-0 left-0 h-64 w-64 rounded-full bg-blue-500 blur-3xl"
                ></div>
                <div
                    class="absolute right-0 bottom-0 h-64 w-64 rounded-full bg-cyan-500 blur-3xl"
                ></div>
            </div>

            <!-- Patrón de grid -->
            <div class="bg-grid-white absolute inset-0 opacity-5"></div>

            <div class="relative mx-auto max-w-6xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h1
                            class="mb-3 bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-5xl font-bold text-transparent"
                        >
                            {{ data.personal.name || 'Tu Nombre' }}
                        </h1>
                        <p class="mb-6 text-2xl text-gray-300">
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
                                :key="tech"
                                class="rounded-full border border-blue-400/30 bg-blue-400/10 px-4 py-2 text-sm backdrop-blur-sm"
                            >
                                {{ tech }}
                            </span>
                        </div>
                    </div>

                    <!-- Contacto Tecnológico -->
                    <div class="text-right">
                        <div class="space-y-2 text-sm text-gray-300">
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
                            <div class="mt-3 flex justify-end space-x-3">
                                <a
                                    v-if="data.personal.github"
                                    :href="data.personal.github"
                                    target="_blank"
                                    class="rounded-lg bg-white/10 p-2 backdrop-blur-sm transition hover:bg-white/20"
                                >
                                    <Code class="h-5 w-5" />
                                </a>
                                <a
                                    v-if="data.personal.linkedin"
                                    :href="data.personal.linkedin"
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
        <main class="relative mx-auto max-w-6xl px-8 py-12">
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

            <div class="grid gap-8 lg:grid-cols-3">
                <!-- Columna Principal -->
                <div class="lg:col-span-2">
                    <!-- Experiencia Tecnológica -->
                    <section v-if="data.experience?.length" class="mb-8">
                        <h2 class="mb-6 flex items-center text-2xl font-bold">
                            <Server class="mr-3 h-6 w-6 text-cyan-400" />
                            Experiencia Técnica
                        </h2>

                        <div class="space-y-6">
                            <div
                                v-for="(exp, index) in data.experience"
                                :key="index"
                                class="rounded-2xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm"
                            >
                                <div
                                    class="mb-4 flex items-start justify-between"
                                >
                                    <div>
                                        <h3 class="text-xl font-bold">
                                            {{ exp.position }}
                                        </h3>
                                        <p class="text-cyan-400">
                                            {{ exp.company }}
                                        </p>
                                    </div>
                                    <span
                                        class="rounded-full bg-cyan-400/20 px-4 py-1 text-sm text-cyan-300"
                                    >
                                        {{ formatDate(exp.startDate) }} –
                                        {{
                                            exp.current
                                                ? 'Presente'
                                                : formatDate(exp.endDate || '')
                                        }}
                                    </span>
                                </div>

                                <p class="mb-4 leading-relaxed text-gray-300">
                                    {{ exp.description }}
                                </p>

                                <!-- Tecnologías usadas -->
                                <div
                                    v-if="exp.technologies?.length"
                                    class="flex flex-wrap gap-2"
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
                    </section>

                    <!-- Proyectos Técnicos -->
                    <section v-if="data.projects?.length" class="mb-8">
                        <h2 class="mb-6 flex items-center text-2xl font-bold">
                            <Code class="mr-3 h-6 w-6 text-cyan-400" />
                            Proyectos Técnicos
                        </h2>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div
                                v-for="(project, index) in data.projects"
                                :key="index"
                                class="rounded-xl border border-blue-400/20 bg-blue-400/5 p-6 backdrop-blur-sm transition hover:border-cyan-400/40"
                            >
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
                    </section>
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
                                    :key="skill"
                                    class="flex items-center justify-between"
                                >
                                    <div class="flex items-center">
                                        <component
                                            :is="techIcons[skill] || Code"
                                            class="mr-3 h-4 w-4 text-cyan-400"
                                        />
                                        <span class="text-sm">{{ skill }}</span>
                                    </div>
                                    <div
                                        class="h-1 w-16 rounded-full bg-gray-700"
                                    >
                                        <div
                                            class="h-full rounded-full bg-cyan-400"
                                            style="width: 85%"
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
</style>
