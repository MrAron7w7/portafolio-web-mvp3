<script setup lang="ts">
import { ref } from 'vue';
import { Loader2, Brain, Check, MessageSquare } from 'lucide-vue-next';

const props = defineProps<{
    portfolios: Array<{ id: number; title: string }>;
}>();

const selectedPortfolioId = ref<number | null>(props.portfolios[0]?.id || null);
const isGenerating = ref(false);
const questions = ref<any[]>([]);
const practicedQuestions = ref<Set<number>>(new Set());

const generateQuestions = async () => {
    if (!selectedPortfolioId.value) return;
    
    isGenerating.value = true;
    questions.value = [];

    try {
        const response = await fetch('/dashboard/herramientas/interview-questions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({ portfolio_id: selectedPortfolioId.value }),
        });

        const data = await response.json();
        questions.value = data.questions || [];
    } catch (error) {
        console.error('Error generating questions:', error);
    } finally {
        isGenerating.value = false;
    }
};

const togglePracticed = (index: number) => {
    if (practicedQuestions.value.has(index)) {
        practicedQuestions.value.delete(index);
    } else {
        practicedQuestions.value.add(index);
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
                        class="w-full rounded-xl border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 py-3 pl-4 pr-10 text-gray-900 dark:text-white focus:border-violet-500 focus:ring-violet-500 appearance-none transition-colors"
                    >
                        <option v-for="p in portfolios" :key="p.id" :value="p.id" class="bg-white dark:bg-slate-800 text-gray-900 dark:text-white">{{ p.title }}</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 dark:text-slate-400">
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
                    </div>
                </div>
            </div>
            <button
                @click="generateQuestions"
                :disabled="isGenerating || !selectedPortfolioId"
                class="px-8 py-3.5 bg-violet-600 text-white rounded-xl font-bold hover:bg-violet-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transition-all shadow-lg shadow-violet-500/20 hover:shadow-violet-500/40 hover:-translate-y-0.5"
            >
                <Loader2 v-if="isGenerating" class="w-5 h-5 animate-spin" />
                <Brain v-else class="w-5 h-5" />
                <span>{{ isGenerating ? 'Generando...' : 'Generar Preguntas' }}</span>
            </button>
        </div>

        <!-- Questions List -->
        <div v-if="questions.length > 0" class="grid gap-4 animate-in fade-in slide-in-from-bottom-4 duration-500">
            <div 
                v-for="(q, index) in questions" 
                :key="index"
                class="group bg-white dark:bg-slate-800/40 p-6 rounded-2xl border-2 transition-all duration-300 hover:shadow-xl hover:bg-gray-50 dark:hover:bg-slate-800/80"
                :class="practicedQuestions.has(index) ? 'border-green-500/30' : 'border-gray-200 dark:border-slate-700/50 hover:border-violet-400 dark:hover:border-violet-500/50'"
            >
                <div class="flex items-start gap-5">
                    <div class="shrink-0 mt-1">
                        <button 
                            @click="togglePracticed(index)"
                            class="w-8 h-8 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all duration-300"
                            :class="practicedQuestions.has(index) ? 'bg-green-500 border-green-500 scale-110' : 'border-gray-300 dark:border-slate-600 hover:border-violet-500 hover:text-violet-400 text-transparent'"
                        >
                            <Check class="w-4 h-4 text-white" />
                        </button>
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-3 mb-3">
                            <span 
                                class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider border"
                                :class="{
                                    'bg-blue-500/10 text-blue-400 border-blue-500/20': q.category === 'técnica',
                                    'bg-purple-500/10 text-purple-400 border-purple-500/20': q.category === 'comportamental',
                                    'bg-amber-500/10 text-amber-400 border-amber-500/20': q.category === 'situacional'
                                }"
                            >
                                {{ q.category }}
                            </span>
                            <span class="text-xs font-medium text-gray-500 dark:text-slate-500">• {{ q.difficulty }}</span>
                        </div>
                        <p class="text-gray-900 dark:text-white font-medium text-lg leading-relaxed">{{ q.question }}</p>
                    </div>
                </div>
            </div>
            
            <div class="text-center pt-4">
                <p class="text-gray-500 dark:text-slate-500 text-sm">
                    Has practicado <span class="font-bold text-violet-600 dark:text-violet-400">{{ practicedQuestions.size }}</span> de <span class="text-gray-900 dark:text-white">{{ questions.length }}</span> preguntas
                </p>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="!isGenerating" class="text-center py-16 border-2 border-dashed border-gray-300 dark:border-slate-800 rounded-3xl bg-gray-50 dark:bg-slate-900/20">
            <div class="w-20 h-20 bg-white dark:bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm dark:shadow-xl dark:shadow-indigo-500/5">
                <MessageSquare class="w-10 h-10 text-gray-400 dark:text-slate-500" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Listo para practicar</h3>
            <p class="text-gray-500 dark:text-slate-400 max-w-md mx-auto">Selecciona un portafolio y genera preguntas personalizadas con IA para simular tu próxima entrevista técnica.</p>
        </div>
    </div>
</template>
