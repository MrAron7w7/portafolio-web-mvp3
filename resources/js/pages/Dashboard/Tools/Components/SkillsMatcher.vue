<script setup lang="ts">
import { ref } from 'vue';
import { Loader2, TrendingUp, CheckCircle2, AlertCircle } from 'lucide-vue-next';

const props = defineProps<{
    portfolios: Array<{ id: number; title: string }>;
}>();

const selectedPortfolioId = ref<number | null>(props.portfolios[0]?.id || null);
const jobDescription = ref('');
const isAnalyzing = ref(false);
const result = ref<any>(null);

const analyzeMatch = () => {
    if (!jobDescription.value.trim() || !selectedPortfolioId.value) return;

    isAnalyzing.value = true;
    result.value = null;

    fetch('/dashboard/herramientas/analyze-skills', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ 
            job_description: jobDescription.value,
            portfolio_id: selectedPortfolioId.value 
        }),
    })
        .then((res) => res.json())
        .then((data) => {
            result.value = data;
            isAnalyzing.value = false;
        })
        .catch((err) => {
            console.error(err);
            isAnalyzing.value = false;
        });
};
</script>

<template>
    <div class="space-y-8">
        <!-- Input Area -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Selector de Portafolio -->
            <div class="md:col-span-1 space-y-3">
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Tu Portafolio</label>
                <div class="relative">
                    <select 
                        v-model="selectedPortfolioId"
                        class="w-full rounded-xl border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 px-4 py-3 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 appearance-none transition-colors hover:border-gray-300 dark:hover:border-slate-600"
                    >
                        <option v-for="p in portfolios" :key="p.id" :value="p.id" class="bg-white dark:bg-slate-800 text-gray-900 dark:text-white">{{ p.title }}</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 dark:text-slate-400">
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-slate-500">Tus skills se compararán contra la vacante.</p>
            </div>

            <!-- Descripción del Trabajo -->
            <div class="md:col-span-2 space-y-3">
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Descripción de la Vacante</label>
                <textarea
                    v-model="jobDescription"
                    rows="4"
                    class="w-full rounded-xl border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-600 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all"
                    placeholder="Pega aquí los requisitos, responsabilidades y skills de la oferta de trabajo..."
                ></textarea>
            </div>
        </div>

        <!-- Analyze Button -->
        <button
            @click="analyzeMatch"
            :disabled="!jobDescription.trim() || !selectedPortfolioId || isAnalyzing"
            class="flex w-full items-center justify-center gap-3 rounded-xl bg-linear-to-r from-indigo-600 to-violet-600 px-6 py-4 font-bold text-white shadow-lg shadow-indigo-500/30 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl hover:shadow-indigo-500/40 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100 border border-indigo-400/20"
        >
            <Loader2 v-if="isAnalyzing" class="h-5 w-5 animate-spin" />
            <TrendingUp v-else class="h-5 w-5" />
            <span>{{ isAnalyzing ? 'Analizando Coincidencias...' : 'Comparar mis Skills' }}</span>
        </button>

        <!-- Results -->
        <div v-if="result" class="space-y-8 rounded-3xl bg-white dark:bg-slate-800/40 border border-gray-200 dark:border-slate-700/50 p-8 animate-in fade-in slide-in-from-bottom-4 duration-500 backdrop-blur-sm">
            <!-- Match Percentage -->
            <div class="flex items-center justify-center py-4">
                <div class="relative h-48 w-48">
                    <svg class="h-full w-full -rotate-90 transform filter drop-shadow-[0_0_15px_rgba(79,70,229,0.3)]">
                        <circle cx="96" cy="96" r="88" stroke="#cbd5e1" class="dark:stroke-slate-800" stroke-width="12" fill="none" />
                        <circle
                            cx="96" cy="96" r="88"
                            :stroke="result.match_percentage >= 70 ? '#10b981' : result.match_percentage >= 50 ? '#f59e0b' : '#ef4444'"
                            stroke-width="12" fill="none"
                            :stroke-dasharray="`${(result.match_percentage / 100) * 552} 552`"
                            stroke-linecap="round"
                            class="transition-all duration-1000 ease-out"
                        />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-5xl font-black text-gray-900 dark:text-white tracking-tight">{{ result.match_percentage }}%</span>
                        <span class="text-sm text-gray-500 dark:text-slate-400 uppercase tracking-widest font-semibold mt-1">Match</span>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Matching Skills -->
                <div v-if="result.matching_skills?.length" class="bg-green-50 dark:bg-green-500/5 border border-green-200 dark:border-green-500/20 rounded-2xl p-6">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-green-100 dark:bg-green-500/20 flex items-center justify-center">
                            <CheckCircle2 class="h-5 w-5 text-green-600 dark:text-green-400" />
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-lg">Tus Fortalezas</h4>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="skill in result.matching_skills" :key="skill" class="rounded-full bg-white dark:bg-green-500/10 px-3 py-1.5 text-sm font-medium text-green-700 dark:text-green-400 border border-green-200 dark:border-green-500/20 shadow-sm dark:shadow-none">
                            {{ skill }}
                        </span>
                    </div>
                </div>

                <!-- Missing Skills -->
                <div v-if="result.missing_skills?.length" class="bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/20 rounded-2xl p-6">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                            <AlertCircle class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-lg">Habilidades Sugeridas</h4>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="skill in result.missing_skills" :key="skill" class="rounded-full bg-white dark:bg-amber-500/10 px-3 py-1.5 text-sm font-medium text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 shadow-sm dark:shadow-none">
                            {{ skill }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Recommendations -->
            <div v-if="result.recommendations">
                <h4 class="mb-4 font-semibold text-gray-800 dark:text-slate-200 flex items-center gap-2">
                    <span class="w-1 h-6 bg-indigo-500 rounded-full"></span>
                    Análisis Detallado de IA
                </h4>
                <div class="rounded-2xl bg-gray-50 dark:bg-slate-900/60 p-6 text-sm leading-relaxed text-gray-700 dark:text-slate-300 shadow-inner border border-gray-200 dark:border-slate-800">
                    {{ result.recommendations }}
                </div>
            </div>
        </div>
    </div>
</template>
