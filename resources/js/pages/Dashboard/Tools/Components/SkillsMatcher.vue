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
    <div class="space-y-6">
        <!-- Input Area -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Selector de Portafolio -->
            <div class="md:col-span-1 space-y-2">
                <label class="block text-sm font-semibold text-slate-700">Tu Portafolio</label>
                <select 
                    v-model="selectedPortfolioId"
                    class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-slate-900 focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option v-for="p in portfolios" :key="p.id" :value="p.id">{{ p.title }}</option>
                </select>
                <p class="text-xs text-slate-500">Tus skills se compararán contra la vacante.</p>
            </div>

            <!-- Descripción del Trabajo -->
            <div class="md:col-span-2 space-y-2">
                <label class="block text-sm font-semibold text-slate-700">Descripción de la Vacante</label>
                <textarea
                    v-model="jobDescription"
                    rows="4"
                    class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10"
                    placeholder="Pega aquí los requisitos, responsabilidades y skills de la oferta de trabajo..."
                ></textarea>
            </div>
        </div>

        <!-- Analyze Button -->
        <button
            @click="analyzeMatch"
            :disabled="!jobDescription.trim() || !selectedPortfolioId || isAnalyzing"
            class="flex w-full items-center justify-center gap-3 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-4 font-bold text-white shadow-lg shadow-indigo-500/30 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl hover:shadow-indigo-500/40 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100"
        >
            <Loader2 v-if="isAnalyzing" class="h-5 w-5 animate-spin" />
            <TrendingUp v-else class="h-5 w-5" />
            <span>{{ isAnalyzing ? 'Analizando...' : 'Comparar mis Skills' }}</span>
        </button>

        <!-- Results -->
        <div v-if="result" class="space-y-6 rounded-2xl bg-gradient-to-br from-slate-50 to-indigo-50/30 p-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
            <!-- Match Percentage -->
            <div class="flex items-center justify-center">
                <div class="relative h-40 w-40">
                    <svg class="h-full w-full -rotate-90 transform">
                        <circle cx="80" cy="80" r="70" stroke="#e2e8f0" stroke-width="12" fill="none" />
                        <circle
                            cx="80" cy="80" r="70"
                            :stroke="result.match_percentage >= 70 ? '#10b981' : result.match_percentage >= 50 ? '#f59e0b' : '#ef4444'"
                            stroke-width="12" fill="none"
                            :stroke-dasharray="`${(result.match_percentage / 100) * 440} 440`"
                            stroke-linecap="round"
                            class="transition-all duration-1000 ease-out"
                        />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-bold text-slate-900">{{ result.match_percentage }}%</span>
                        <span class="text-xs text-slate-600 uppercase tracking-wider">Match</span>
                    </div>
                </div>
            </div>

            <!-- Matching Skills -->
            <div v-if="result.matching_skills?.length">
                <div class="mb-3 flex items-center gap-2">
                    <CheckCircle2 class="h-5 w-5 text-green-600" />
                    <h4 class="font-semibold text-slate-900">Tus Fortalezas</h4>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span v-for="skill in result.matching_skills" :key="skill" class="rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800 border border-green-200">
                        {{ skill }}
                    </span>
                </div>
            </div>

            <!-- Missing Skills -->
            <div v-if="result.missing_skills?.length">
                <div class="mb-3 flex items-center gap-2">
                    <AlertCircle class="h-5 w-5 text-amber-600" />
                    <h4 class="font-semibold text-slate-900">Te Falta (Sugerido)</h4>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span v-for="skill in result.missing_skills" :key="skill" class="rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-800 border border-amber-200">
                        {{ skill }}
                    </span>
                </div>
            </div>

            <!-- Recommendations -->
            <div v-if="result.recommendations">
                <h4 class="mb-3 font-semibold text-slate-900">Análisis detallado</h4>
                <div class="rounded-xl bg-white p-5 text-sm leading-relaxed text-slate-700 shadow-sm border border-slate-100">
                    {{ result.recommendations }}
                </div>
            </div>
        </div>
    </div>
</template>
