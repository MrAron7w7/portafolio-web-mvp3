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
        <div class="min-h-screen bg-gray-50 dark:bg-slate-950 relative overflow-x-hidden font-sans selection:bg-indigo-500/30 transition-colors duration-300">
            <!-- Futuristic Background (Dark Mode Only) -->
            <div class="fixed inset-0 z-0 pointer-events-none hidden dark:block">
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#4f4f4f2e_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f2e_1px,transparent_1px)] bg-size-[4rem_4rem] mask-[radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
                <div class="absolute top-0 -left-1/4 w-1/2 h-1/2 bg-indigo-500/10 blur-[120px] rounded-full"></div>
                <div class="absolute bottom-0 -right-1/4 w-1/2 h-1/2 bg-purple-500/10 blur-[120px] rounded-full"></div>
            </div>

            <!-- Header -->
            <header class="sticky top-0 z-30 border-b border-gray-200 dark:border-indigo-500/10 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl transition-colors duration-300">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between py-6">
                        <div class="flex items-center gap-4">
                            <button
                                @click="toggleSidebar"
                                class="rounded-lg p-2 text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200 lg:hidden"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                                    <span class="bg-linear-to-r from-indigo-600 to-cyan-600 dark:from-indigo-400 dark:to-cyan-400 bg-clip-text text-transparent">AI Analysis</span>
                                    <span class="text-gray-500 dark:text-slate-500 text-lg font-normal">Hub</span>
                                </h1>
                                <p class="mt-1 text-gray-500 dark:text-slate-400 text-sm">
                                    Centro de inteligencia artificial para tu carrera
                                </p>
                            </div>
                        </div>
                        <div class="hidden sm:flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 shadow-sm dark:shadow-[0_0_15px_rgba(99,102,241,0.2)]">
                            <Sparkles class="h-6 w-6 text-indigo-600 dark:text-indigo-400 animate-pulse" />
                        </div>
                    </div>
                </div>
            </header>

            <main class="container mx-auto px-4 py-12 sm:px-6 lg:px-8 relative z-10">
                <div class="mx-auto max-w-7xl space-y-12">
                    
                    <!-- Tool Selection Cards (3D) -->
                    <div class="grid md:grid-cols-3 gap-8 perspective-container">
                        <button
                            v-for="tool in tools"
                            :key="tool.id"
                            @click="activeTool = tool.id"
                            class="group relative h-48 w-full text-left perspective-card preserve-3d transition-all duration-500 ease-out focus:outline-none"
                            :class="activeTool === tool.id ? 'scale-[1.02] z-20' : 'hover:scale-105 opacity-90 hover:opacity-100'"
                        >
                            <!-- Card Body -->
                            <div 
                                class="absolute inset-0 rounded-3xl border p-6 flex flex-col justify-between backdrop-blur-xl transition-all duration-300"
                                :class="activeTool === tool.id 
                                    ? 'bg-white border-indigo-500 shadow-xl dark:bg-slate-800/80 dark:shadow-[0_0_30px_rgba(99,102,241,0.2)]' 
                                    : 'bg-white border-gray-200 hover:border-indigo-300 dark:bg-slate-900/40 dark:border-slate-700 dark:hover:border-slate-500'"
                            >
                                <!-- Icon & Glow -->
                                <div class="flex items-start justify-between">
                                    <div 
                                        class="h-12 w-12 rounded-xl flex items-center justify-center transition-colors duration-300 shadow-sm"
                                        :class="activeTool === tool.id 
                                            ? 'bg-indigo-600 text-white dark:bg-indigo-500' 
                                            : 'bg-indigo-50 text-indigo-600 dark:bg-slate-800 dark:text-slate-400 group-hover:bg-indigo-100 dark:group-hover:bg-slate-700 dark:group-hover:text-white'"
                                    >
                                        <component :is="tool.icon" class="h-6 w-6" />
                                    </div>
                                    <div v-if="activeTool === tool.id" class="h-2 w-2 rounded-full bg-green-500 dark:bg-green-400 shadow-[0_0_10px_rgba(74,222,128,0.8)] animate-pulse"></div>
                                </div>

                                <div>
                                    <h3 class="text-lg font-bold mb-1 transition-colors" 
                                        :class="activeTool === tool.id ? 'text-gray-900 dark:text-white' : 'text-gray-800 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-300'">
                                        {{ tool.name }}
                                    </h3>
                                    <p class="text-xs line-clamp-2 leading-relaxed" 
                                        :class="activeTool === tool.id ? 'text-gray-500 dark:text-slate-400' : 'text-gray-500 dark:text-slate-400'">
                                        {{ tool.description }}
                                    </p>
                                </div>
                                
                                <!-- Active Selection Indicator (Bottom Bar) -->
                                <div 
                                    class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-indigo-500 via-purple-500 to-indigo-500 transition-all duration-500"
                                    :class="activeTool === tool.id ? 'opacity-100' : 'opacity-0'"
                                ></div>
                            </div>
                        </button>
                    </div>

                    <!-- Active Tool Workspace -->
                    <Transition
                        enter-active-class="transition duration-500 ease-out"
                        enter-from-class="opacity-0 translate-y-8 scale-95"
                        enter-to-class="opacity-100 translate-y-0 scale-100"
                        leave-active-class="transition duration-300 ease-in"
                        leave-from-class="opacity-100 translate-y-0 scale-100"
                        leave-to-class="opacity-0 translate-y-8 scale-95"
                        mode="out-in"
                    >
                        <div v-if="activeTool" :key="activeTool" class="relative">
                            <!-- Glowing Border (Dark Only) -->
                            <div class="absolute -inset-0.5 bg-linear-to-r from-indigo-500 via-purple-500 to-cyan-500 rounded-4xl opacity-0 dark:opacity-30 blur-lg animate-pulse"></div>
                            
                            <!-- Main workspace container -->
                            <div class="relative bg-white dark:bg-slate-900/60 rounded-4xl shadow-xl dark:shadow-2xl border border-gray-200 dark:border-slate-800 dark:backdrop-blur-xl transition-colors duration-300">
                                <!-- Top Bar -->
                                <div class="bg-gray-50 dark:bg-slate-900/80 border-b border-gray-200 dark:border-slate-800 px-6 py-4 flex items-center justify-between backdrop-blur-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="flex gap-1.5">
                                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-slate-400 ml-2 border-l border-gray-200 dark:border-slate-700 pl-3">
                                            {{ tools.find(t => t.id === activeTool)?.name }}
                                        </span>
                                    </div>
                                    <div class="px-2 py-1 rounded bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-xs font-bold uppercase tracking-wider border border-indigo-100 dark:border-transparent">
                                        AI Engine Ready
                                    </div>
                                </div>

                                <div class="p-8 lg:p-10 min-h-[500px]">
                                    <SkillsMatcher v-if="activeTool === 'skills'" :portfolios="portfolios" />
                                    <InterviewSimulator v-if="activeTool === 'interview'" :portfolios="portfolios" />
                                    <ATSScanner v-if="activeTool === 'ats'" :portfolios="portfolios" />
                                </div>
                            </div>
                        </div>
                        
                        <!-- Empty State -->
                        <div v-else class="min-h-[400px] flex flex-col items-center justify-center text-center p-8 border-2 border-dashed border-gray-300 dark:border-slate-800 rounded-4xl bg-gray-50/50 dark:bg-slate-900/30">
                            <div class="h-24 w-24 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center mb-6 shadow-sm dark:shadow-none animate-pulse">
                                <Sparkles class="h-10 w-10 text-gray-400 dark:text-slate-600" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Selecciona una herramienta</h3>
                            <p class="text-gray-500 dark:text-slate-400 max-w-sm">
                                Elige uno de los módulos superiores para iniciar el análisis inteligente de tu perfil.
                            </p>
                        </div>
                    </Transition>

                </div>
            </main>
        </div>
    </DashboardLayout>
</template>

<style scoped>
.perspective-container {
    perspective: 1000px;
}

.preserve-3d {
    transform-style: preserve-3d;
}

.perspective-card:hover {
    transform: translateY(-5px) rotateX(2deg);
}
</style>
