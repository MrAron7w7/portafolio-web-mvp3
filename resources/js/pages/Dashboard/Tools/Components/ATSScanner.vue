<script setup lang="ts">
import { ref } from 'vue';
import { Loader2, Shield, CheckCircle2, AlertTriangle, XCircle, FileSearch } from 'lucide-vue-next';

const props = defineProps<{
    portfolios: Array<{ id: number; title: string }>;
}>();

const selectedPortfolioId = ref<number | null>(props.portfolios[0]?.id || null);
const isScanning = ref(false);
const result = ref<any>(null);

const scanPortfolio = async () => {
    if (!selectedPortfolioId.value) return;
    
    isScanning.value = true;
    result.value = null;

    try {
        const response = await fetch('/dashboard/herramientas/ats-scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({ portfolio_id: selectedPortfolioId.value }),
        });

        const data = await response.json();
        result.value = data;
    } catch (error) {
        console.error('Error scanning portfolio:', error);
    } finally {
        isScanning.value = false;
    }
};
</script>

<template>
    <div class="space-y-8">
        <!-- Controls -->
        <div class="flex flex-wrap items-end gap-6 p-6 bg-white dark:bg-slate-800/40 rounded-3xl border border-gray-200 dark:border-slate-700/50 backdrop-blur-sm">
            <div class="flex-1 min-w-[250px]">
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Selecciona tu Portafolio</label>
                <div class="relative">
                    <select 
                        v-model="selectedPortfolioId"
                        class="w-full rounded-xl border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 py-3 pl-4 pr-10 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 appearance-none transition-colors"
                    >
                        <option v-for="p in portfolios" :key="p.id" :value="p.id" class="bg-white dark:bg-slate-800 text-gray-900 dark:text-white">{{ p.title }}</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 dark:text-slate-400">
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
                    </div>
                </div>
            </div>
            <button
                @click="scanPortfolio"
                :disabled="isScanning || !selectedPortfolioId"
                class="px-8 py-3.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transition-all shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 hover:-translate-y-0.5"
            >
                <Loader2 v-if="isScanning" class="w-5 h-5 animate-spin" />
                <Shield v-else class="w-5 h-5" />
                <span>{{ isScanning ? 'Escaneando...' : 'Escanear ATS' }}</span>
            </button>
        </div>

        <!-- Results -->
        <div v-if="result" class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
            <!-- Score Card -->
            <div class="bg-linear-to-br from-indigo-600 to-blue-700 p-8 rounded-3xl text-white shadow-2xl shadow-indigo-900/50 relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.1)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.1)_1px,transparent_1px)] bg-size-[20px_20px] opacity-20"></div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-8 relative z-10">
                    <div class="text-center md:text-left">
                        <h3 class="text-3xl font-bold mb-2">ATS Score</h3>
                        <p class="text-indigo-100 max-w-sm text-lg leading-relaxed opacity-90">
                            Tu portafolio tiene una alta probabilidad de pasar los filtros automáticos.
                        </p>
                    </div>
                    <div class="relative flex items-center justify-center h-40 w-40 shrink-0">
                        <svg class="h-full w-full transform -rotate-90 drop-shadow-xl">
                            <circle cx="80" cy="80" r="72" stroke="currentColor" stroke-width="10" class="text-white/20" fill="none" />
                            <circle cx="80" cy="80" r="72" stroke="currentColor" stroke-width="10" class="text-white transition-all duration-1000 ease-out" 
                                fill="none" :stroke-dasharray="452.39" :stroke-dashoffset="452.39 - (452.39 * Number(result.ats_score)) / 100" stroke-linecap="round" />
                        </svg>
                        <span class="absolute text-5xl font-black tracking-tight">{{ result.ats_score }}</span>
                    </div>
                </div>
            </div>

            <!-- Issues List -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Good Points -->
                <div class="bg-white dark:bg-slate-800/50 p-6 rounded-2xl border border-gray-200 dark:border-slate-700/50 text-gray-600 dark:text-slate-300">
                    <div class="flex items-center gap-3 mb-5 border-b border-gray-100 dark:border-slate-700/50 pb-4">
                        <CheckCircle2 class="w-6 h-6 text-green-500 dark:text-green-400" />
                        <h4 class="font-bold text-gray-900 dark:text-white text-lg">Puntos Fuertes</h4>
                    </div>
                    <ul class="space-y-3">
                        <li v-for="(item, index) in result.keywords_found" :key="index" class="flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-green-500 mt-2 shrink-0 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                            <span class="text-gray-700 dark:text-slate-300 font-medium">{{ item }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Missing Keywords/Issues -->
                <div class="bg-white dark:bg-slate-800/50 p-6 rounded-2xl border border-gray-200 dark:border-slate-700/50 text-gray-600 dark:text-slate-300">
                    <div class="flex items-center gap-3 mb-5 border-b border-gray-100 dark:border-slate-700/50 pb-4">
                        <AlertTriangle class="w-6 h-6 text-amber-500 dark:text-amber-400" />
                        <h4 class="font-bold text-gray-900 dark:text-white text-lg">Palabras Clave Faltantes</h4>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="(keyword, index) in result.missing_keywords" :key="index" class="px-3 py-1.5 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 text-sm font-medium rounded-lg border border-amber-200 dark:border-amber-500/20">
                            {{ keyword }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Suggestions -->
            <div class="bg-indigo-50 dark:bg-indigo-900/10 p-6 rounded-2xl border border-indigo-100 dark:border-indigo-500/20">
                <h4 class="font-bold text-indigo-700 dark:text-indigo-300 mb-4 flex items-center gap-2">
                    <Shield class="w-5 h-5" />
                    Sugerencias de Optimización
                </h4>
                <div class="space-y-4">
                    <div v-for="(suggestion, index) in result.suggestions" :key="index" class="flex gap-4">
                        <div class="w-7 h-7 rounded-lg bg-indigo-500/10 dark:bg-indigo-500/20 flex items-center justify-center shrink-0 text-sm font-bold text-indigo-600 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30">
                            {{ index + 1 }}
                        </div>
                        <p class="text-gray-700 dark:text-slate-300 leading-relaxed">{{ suggestion }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="!isScanning" class="text-center py-16 border-2 border-dashed border-gray-300 dark:border-slate-800 rounded-3xl bg-gray-50 dark:bg-slate-900/20">
            <div class="w-20 h-20 bg-white dark:bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm dark:shadow-xl dark:shadow-indigo-500/5">
                <FileSearch class="w-10 h-10 text-gray-400 dark:text-slate-500" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Analiza tu compatibilidad ATS</h3>
            <p class="text-gray-500 dark:text-slate-400 max-w-md mx-auto">Descubre qué tan optimizado está tu portafolio para los sistemas de reclutamiento automático.</p>
        </div>
    </div>
</template>
