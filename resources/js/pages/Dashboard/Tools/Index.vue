<script setup lang="ts">
import { ref } from 'vue';
import { Target, MessageSquare, FileSearch, Sparkles } from 'lucide-vue-next';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import SkillsMatcher from './Components/SkillsMatcher.vue';
import InterviewSimulator from './Components/InterviewSimulator.vue';
import ATSScanner from './Components/ATSScanner.vue';

const props = defineProps<{
    portfolios: Array<{
        id: number;
        title: string;
        template_type: string;
        data: any;
    }>;
}>();

const activeTool = ref<string | null>(null);

const tools = [
    {
        id: 'skills',
        name: 'Analizador de Perfil',
        description: 'Analiza la fortaleza de tu perfil y sugiere roles ideales',
        icon: Target,
        color: 'from-blue-600 to-cyan-600',
    },
    {
        id: 'interview',
        name: 'Simulador de Entrevista',
        description: 'Genera preguntas personalizadas según tu experiencia',
        icon: MessageSquare,
        color: 'from-violet-600 to-purple-600',
    },
    {
        id: 'ats',
        name: 'Escáner ATS',
        description: 'Verifica si tu portafolio pasará los filtros automáticos',
        icon: FileSearch,
        color: 'from-indigo-600 to-blue-600',
    },
];
</script>

<template>
    <DashboardLayout v-slot="{ toggleSidebar }">
        <!-- Main Content -->
        <div class="min-h-screen bg-slate-50/50">
            <!-- Header -->
            <header class="sticky top-0 z-30 border-b border-slate-200/60 bg-white/80 backdrop-blur-sm">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between py-6">
                        <div class="flex items-center gap-4">
                            <button
                                @click="toggleSidebar"
                                class="rounded-lg p-2 transition-colors duration-200 hover:bg-slate-100 lg:hidden"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div>
                                <h1 class="text-3xl font-bold text-slate-900">
                                    Herramientas de Análisis
                                </h1>
                                <p class="mt-1 text-slate-600">
                                    Potencia tu búsqueda laboral con IA
                                </p>
                            </div>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 shadow-lg shadow-indigo-500/25">
                            <Sparkles class="h-6 w-6 text-white" />
                        </div>
                    </div>
                </div>
            </header>

            <main class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-6xl space-y-6">
                    <!-- Tool Cards (Accordion) -->
                    <div
                        v-for="tool in tools"
                        :key="tool.id"
                        class="group overflow-hidden rounded-3xl border-2 transition-all duration-300"
                        :class="activeTool === tool.id ? 'border-indigo-500 bg-white shadow-2xl shadow-indigo-500/20' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-xl'"
                    >
                        <button
                            @click="activeTool = activeTool === tool.id ? null : tool.id"
                            class="flex w-full items-center gap-6 p-6 text-left"
                        >
                            <div
                                class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br transition-transform duration-300 group-hover:scale-110 shadow-lg"
                                :class="tool.color"
                            >
                                <component :is="tool.icon" class="h-8 w-8 text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                                    {{ tool.name }}
                                </h3>
                                <p class="text-slate-500">{{ tool.description }}</p>
                            </div>
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 transition-all duration-300"
                                :class="activeTool === tool.id ? 'border-indigo-500 bg-indigo-50 rotate-90' : 'border-slate-200 bg-slate-50 group-hover:border-slate-300'"
                            >
                                <svg
                                    class="h-5 w-5 transition-colors"
                                    :class="activeTool === tool.id ? 'text-indigo-600' : 'text-slate-400'"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </button>
                        
                        <Transition
                            enter-active-class="transition duration-300 ease-out"
                            enter-from-class="transform scale-y-95 opacity-0"
                            enter-to-class="transform scale-y-100 opacity-100"
                            leave-active-class="transition duration-200 ease-in"
                            leave-from-class="transform scale-y-100 opacity-100"
                            leave-to-class="transform scale-y-95 opacity-0"
                        >
                            <div v-if="activeTool === tool.id" class="border-t border-slate-100 bg-slate-50/50 overflow-hidden">
                                <div class="p-6">
                                    <SkillsMatcher v-if="tool.id === 'skills'" :portfolios="portfolios" />
                                    <InterviewSimulator v-if="tool.id === 'interview'" :portfolios="portfolios" />
                                    <ATSScanner v-if="tool.id === 'ats'" :portfolios="portfolios" />
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>

                <!-- Help Text -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-slate-400">
                        Estas herramientas utilizan IA avanzada para analizar tu portafolio.
                        <br />
                        Los resultados son sugerencias para mejorar tu perfil profesional.
                    </p>
                </div>
            </main>
        </div>
    </DashboardLayout>
</template>
