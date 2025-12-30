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
                @click="generateQuestions"
                :disabled="isGenerating || !selectedPortfolioId"
                class="mt-6 px-6 py-2 bg-violet-600 text-white rounded-lg font-medium hover:bg-violet-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transition-colors"
            >
                <Loader2 v-if="isGenerating" class="w-4 h-4 animate-spin" />
                <span v-else>Generar Preguntas</span>
            </button>
        </div>

        <!-- Questions List -->
        <div v-if="questions.length > 0" class="grid gap-4 animate-in fade-in slide-in-from-bottom-4 duration-500">
            <div 
                v-for="(q, index) in questions" 
                :key="index"
                class="bg-white p-6 rounded-xl border-2 transition-all duration-200 hover:shadow-md"
                :class="practicedQuestions.has(index) ? 'border-green-200 bg-green-50/30' : 'border-slate-100'"
            >
                <div class="flex items-start gap-4">
                    <div class="shrink-0 mt-1">
                        <div 
                            @click="togglePracticed(index)"
                            class="w-6 h-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-colors"
                            :class="practicedQuestions.has(index) ? 'bg-green-500 border-green-500' : 'border-slate-300 hover:border-violet-500'"
                        >
                            <Check v-if="practicedQuestions.has(index)" class="w-4 h-4 text-white" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span 
                                class="px-2 py-0.5 rounded text-xs font-medium uppercase tracking-wider"
                                :class="{
                                    'bg-blue-100 text-blue-700': q.category === 'técnica',
                                    'bg-purple-100 text-purple-700': q.category === 'comportamental',
                                    'bg-amber-100 text-amber-700': q.category === 'situacional'
                                }"
                            >
                                {{ q.category }}
                            </span>
                            <span class="text-xs text-slate-400">• {{ q.difficulty }}</span>
                        </div>
                        <p class="text-slate-800 font-medium text-lg">{{ q.question }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="!isGenerating" class="text-center py-12">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <MessageSquare class="w-8 h-8 text-slate-400" />
            </div>
            <h3 class="text-lg font-medium text-slate-900">Listo para practicar</h3>
            <p class="text-slate-500 max-w-sm mx-auto mt-1">Selecciona un portafolio y genera preguntas personalizadas para tu próxima entrevista.</p>
        </div>
    </div>
</template>
