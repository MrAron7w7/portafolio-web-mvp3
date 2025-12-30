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
    <div class="space-y-6">
        <!-- Controls -->
        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">Selecciona tu Portafolio</label>
                <select 
                    v-model="selectedPortfolioId"
                    class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option v-for="p in portfolios" :key="p.id" :value="p.id">{{ p.title }}</option>
                </select>
            </div>
            <button
                @click="scanPortfolio"
                :disabled="isScanning || !selectedPortfolioId"
                class="mt-6 px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transition-colors"
            >
                <Loader2 v-if="isScanning" class="w-4 h-4 animate-spin" />
                <span v-else>Escanear ATS</span>
            </button>
        </div>

        <!-- Results -->
        <div v-if="result" class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
            <!-- Score Card -->
            <div class="bg-gradient-to-br from-indigo-600 to-blue-600 p-8 rounded-2xl text-white shadow-xl shadow-indigo-500/20">
                <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="text-center md:text-left">
                        <h3 class="text-2xl font-bold mb-2">ATS Score</h3>
                        <p class="text-indigo-100 max-w-sm">
                            Este puntaje indica qué tan probable es que tu portafolio pase los filtros automáticos de reclutamiento.
                        </p>
                    </div>
                    <div class="relative flex items-center justify-center h-32 w-32 shrink-0">
                        <svg class="h-full w-full transform -rotate-90">
                            <circle cx="64" cy="64" r="60" stroke="currentColor" stroke-width="8" class="text-indigo-500/30" fill="none" />
                            <circle cx="64" cy="64" r="60" stroke="currentColor" stroke-width="8" class="text-white transition-all duration-1000 ease-out" 
                                fill="none" :stroke-dasharray="376.99" :stroke-dashoffset="376.99 - (376.99 * result.ats_score) / 100" />
                        </svg>
                        <span class="absolute text-4xl font-bold">{{ result.ats_score }}</span>
                    </div>
                </div>
            </div>

            <!-- Issues List -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Good Points -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200">
                    <div class="flex items-center gap-2 mb-4">
                        <CheckCircle2 class="w-5 h-5 text-green-500" />
                        <h4 class="font-semibold text-slate-900">Puntos Fuertes</h4>
                    </div>
                    <ul class="space-y-3">
                        <li v-for="(item, index) in result.keywords_found" :key="index" class="flex items-start gap-2 text-slate-600 text-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mt-1.5 shrink-0"></span>
                            {{ item }}
                        </li>
                    </ul>
                </div>

                <!-- Missing Keywords/Issues -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200">
                    <div class="flex items-center gap-2 mb-4">
                        <AlertTriangle class="w-5 h-5 text-amber-500" />
                        <h4 class="font-semibold text-slate-900">Palabras Clave Faltantes</h4>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="(keyword, index) in result.missing_keywords" :key="index" class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-medium rounded-full border border-amber-100">
                            {{ keyword }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Suggestions -->
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                <h4 class="font-semibold text-slate-900 mb-4">Sugerencias de Mejora</h4>
                <div class="space-y-3">
                    <div v-for="(suggestion, index) in result.suggestions" :key="index" class="flex gap-3">
                        <div class="w-6 h-6 rounded-full bg-white border border-slate-200 flex items-center justify-center shrink-0 text-xs font-bold text-slate-500 shadow-sm">
                            {{ index + 1 }}
                        </div>
                        <p class="text-slate-600 text-sm">{{ suggestion }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="!isScanning" class="text-center py-12">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <FileSearch class="w-8 h-8 text-slate-400" />
            </div>
            <h3 class="text-lg font-medium text-slate-900">Analiza tu compatibilidad ATS</h3>
            <p class="text-slate-500 max-w-sm mx-auto mt-1">Descubre qué tan optimizado está tu portafolio para los sistemas de reclutamiento automático.</p>
        </div>
    </div>
</template>
