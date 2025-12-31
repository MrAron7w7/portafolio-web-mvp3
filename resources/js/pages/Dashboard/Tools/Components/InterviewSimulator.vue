<script setup lang="ts">
import { ref, nextTick, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Loader2, Brain, Check, MessageSquare, Send, User, Bot, RefreshCw } from 'lucide-vue-next';

const props = defineProps<{
    portfolios: Array<{ id: number; title: string }>;
}>();

const page = usePage();
const user = (page.props.auth as any).user;

const selectedPortfolioId = ref<number | null>(props.portfolios[0]?.id || null);
const jobDescription = ref('');
const isGenerating = ref(false);
const isSending = ref(false);

const chatHistory = ref<Array<{ role: 'user' | 'assistant', content: string }>>([]);
const userMessage = ref('');
const messagesContainer = ref<HTMLElement | null>(null);

const scrollToBottom = async () => {
    await nextTick();
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

const startInterview = async () => {
    if (!selectedPortfolioId.value) return;
    
    isGenerating.value = true;
    chatHistory.value = [];

    try {
        const response = await fetch('/dashboard/herramientas/interview-interaction', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({ 
                portfolio_id: selectedPortfolioId.value,
                job_description: jobDescription.value,
                chat_history: []
            }),
        });

        if (!response.ok) {
            throw new Error(`Error del servidor: ${response.status}`);
        }

        const data = await response.json();
        if (data.error) throw new Error(data.error);

        if (data.reply) {
            chatHistory.value.push({ role: 'assistant', content: data.reply });
            scrollToBottom();
        }
    } catch (error: any) {
        console.error('Error starting interview:', error);
        if (error.message && error.message.includes('message channel closed')) {
           alert('Tu navegador o una extensión (como adblock/traductor) interrumpió la conexión. Por favor intenta de nuevo o revisa tus extensiones.');
        } else {
           alert(error.message || 'Ocurrió un error al iniciar la entrevista.');
        }
    } finally {
        isGenerating.value = false;
    }
};

const sendMessage = async () => {
    if (!userMessage.value.trim() || isSending.value) return;

    const message = userMessage.value;
    userMessage.value = '';
    
    // Add user message immediately
    chatHistory.value.push({ role: 'user', content: message });
    scrollToBottom();
    
    isSending.value = true;

    try {
        const response = await fetch('/dashboard/herramientas/interview-interaction', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({ 
                portfolio_id: selectedPortfolioId.value,
                job_description: jobDescription.value,
                chat_history: chatHistory.value.slice(0, -1),
                message: message 
            }),
        });

        if (!response.ok) {
            throw new Error(`Error del servidor: ${response.status}`);
        }

        const data = await response.json();
        if (data.error) throw new Error(data.error);

        if (data.reply) {
            chatHistory.value.push({ role: 'assistant', content: data.reply });
            scrollToBottom();
        }
    } catch (error: any) {
        console.error('Error sending message:', error);
        if (error.message && error.message.includes('message channel closed')) {
           alert('Tu navegador o una extensión interrumpió la conexión. Intenta de nuevo.');
        } else {
           alert(error.message || 'Error al enviar mensaje.');
        }
    } finally {
        isSending.value = false;
    }
};

const resetInterview = () => {
    chatHistory.value = [];
    userMessage.value = '';
    isGenerating.value = false;
    isSending.value = false;
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
                        :disabled="chatHistory.length > 0"
                        class="w-full rounded-xl border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 px-4 py-3 text-gray-900 dark:text-white focus:border-violet-500 focus:ring-violet-500 appearance-none transition-colors hover:border-violet-300 dark:hover:border-slate-600 disabled:opacity-70 disabled:cursor-not-allowed"
                    >
                        <option v-for="p in portfolios" :key="p.id" :value="p.id" class="bg-white dark:bg-slate-800 text-gray-900 dark:text-white">{{ p.title }}</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 dark:text-slate-400">
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-slate-500">Las preguntas se basarán en tu experiencia y esta vacante.</p>
            </div>

            <!-- Descripción de la Vacante -->
            <div class="md:col-span-2 space-y-3">
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Descripción de la Vacante</label>
                <textarea 
                    v-model="jobDescription"
                    :disabled="chatHistory.length > 0"
                    rows="4"
                    spellcheck="false"
                    data-gramm="false"
                    data-lpignore="true"
                    autocomplete="off"
                    class="w-full rounded-xl border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-600 focus:border-violet-500 focus:outline-none focus:ring-4 focus:ring-violet-500/10 transition-all disabled:opacity-70 disabled:cursor-not-allowed"
                    placeholder="Ej: Buscamos un desarrollador Frontend con experiencia en Vue.js, Tailwind CSS y consumo de APIs REST..."
                ></textarea>
            </div>
        </div>

        <!-- Start Button -->
        <button
            v-if="chatHistory.length === 0"
            @click="startInterview"
            :disabled="isGenerating || !selectedPortfolioId"
            class="flex w-full items-center justify-center gap-3 rounded-xl bg-linear-to-r from-violet-600 to-purple-600 px-6 py-4 font-bold text-white shadow-lg shadow-violet-500/30 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl hover:shadow-violet-500/40 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100 border border-violet-400/20"
        >
            <Loader2 v-if="isGenerating" class="h-5 w-5 animate-spin" />
            <Brain v-else class="h-5 w-5" />
            <span>{{ isGenerating ? 'Conectando con Entrevista...' : 'Iniciar Simulador de Entrevista' }}</span>
        </button>

        <!-- Chat Interface -->
        <div v-else class="rounded-3xl bg-white dark:bg-slate-900/40 border border-gray-200 dark:border-slate-800 overflow-hidden shadow-2xl shadow-indigo-500/10 animate-in fade-in slide-in-from-bottom-4 duration-500 flex flex-col h-[600px]">
            <!-- Chat Header -->
            <div class="bg-linear-to-r from-violet-600 to-indigo-600 p-4 px-6 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-2.5 h-2.5 rounded-full bg-green-400 animate-pulse border-2 border-violet-500"></div>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-sm tracking-wide">Entrevista en Curso</h3>
                        <p class="text-violet-200 text-xs">Simulación IA en Tiempo Real</p>
                    </div>
                </div>
                <button 
                    @click="resetInterview" 
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/10 text-xs font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur-sm border border-white/10"
                >
                    <RefreshCw class="w-3.5 h-3.5" />
                    Reiniciar
                </button>
            </div>

            <!-- Messages Area -->
            <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-6 bg-slate-50 dark:bg-slate-950/50 scroll-smooth">
                <div v-for="(msg, idx) in chatHistory" :key="idx" class="flex gap-4 group" :class="msg.role === 'user' ? 'flex-row-reverse' : ''">
                    <!-- Avatar -->
                    <div class="shrink-0">
                        <template v-if="msg.role === 'user'">
                            <img 
                                v-if="user?.avatar_url" 
                                :src="user.avatar_url" 
                                class="w-10 h-10 rounded-full object-cover border-2 border-white dark:border-slate-800 shadow-md"
                                alt="User"
                            />
                            <div v-else class="w-10 h-10 rounded-full bg-linear-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white shadow-md border-2 border-white dark:border-slate-800">
                                <User class="w-5 h-5" />
                            </div>
                        </template>
                        <template v-else>
                            <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-violet-600 border border-gray-200 dark:border-slate-700 shadow-sm">
                                <Bot class="w-6 h-6" />
                            </div>
                        </template>
                    </div>

                    <!-- Bubble -->
                    <div class="max-w-[80%] rounded-2xl p-5 text-sm leading-relaxed shadow-sm transition-all duration-300 hover:shadow-md"
                         :class="msg.role === 'user' 
                            ? 'bg-linear-to-br from-indigo-600 to-violet-600 text-white rounded-tr-none' 
                            : 'bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-200 border border-gray-200 dark:border-slate-700 rounded-tl-none'">
                        {{ msg.content }}
                    </div>
                </div>

                <!-- Typing Indicator -->
                <div v-if="isSending" class="flex gap-4">
                    <div class="shrink-0 w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 flex items-center justify-center shadow-sm">
                        <Loader2 class="w-5 h-5 animate-spin text-violet-500" />
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-2xl rounded-tl-none p-4 flex items-center gap-1.5 shadow-sm">
                        <span class="text-xs text-gray-400 font-medium mr-2">Escribiendo</span>
                        <div class="w-1.5 h-1.5 rounded-full bg-violet-400 animate-bounce"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-violet-400 animate-bounce delay-100"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-violet-400 animate-bounce delay-200"></div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800 shrink-0">
                <div class="relative flex items-end gap-2">
                    <textarea 
                        v-model="userMessage"
                        @keydown.enter.prevent="sendMessage"
                        rows="1"
                        spellcheck="false"
                        data-gramm="false"
                        data-lpignore="true"
                        autocomplete="off"
                        class="w-full rounded-2xl border-0 bg-gray-100 dark:bg-slate-950 px-5 py-4 pr-14 text-sm focus:ring-2 focus:ring-violet-500/50 resize-none max-h-32 min-h-[56px] text-gray-900 dark:text-white placeholder-gray-500"
                        placeholder="Escribe tu respuesta..."
                        style="field-sizing: content;" 
                    ></textarea>
                    
                    <button 
                        @click="sendMessage"
                        :disabled="!userMessage.trim() || isSending"
                        class="absolute right-2 bottom-2 p-2.5 rounded-xl bg-violet-600 text-white hover:bg-violet-700 disabled:opacity-0 disabled:scale-95 transition-all duration-200 shadow-md shadow-violet-500/20 hover:shadow-lg hover:-translate-y-px"
                    >
                        <Send class="w-4 h-4" />
                    </button>
                </div>
                <div class="text-center mt-2">
                    <p class="text-[10px] text-gray-400 dark:text-slate-600">
                        Presiona Enter para enviar. La IA analizará tu respuesta y continuará la entrevista.
                    </p>
                </div>
            </div>
        </div>

        <!-- Empty State (Only if not chatting) -->
        <div v-if="chatHistory.length === 0 && !isGenerating" class="text-center py-16 border-2 border-dashed border-gray-300 dark:border-slate-800 rounded-3xl bg-gray-50 dark:bg-slate-900/20">
            <div class="w-20 h-20 bg-white dark:bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm dark:shadow-xl dark:shadow-indigo-500/5">
                <MessageSquare class="w-10 h-10 text-gray-400 dark:text-slate-500" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Simulador de Entrevista IA</h3>
            <p class="text-gray-500 dark:text-slate-400 max-w-md mx-auto">Selecciona un portafolio y practica con un reclutador experto que se adaptará a tus respuestas en tiempo real.</p>
        </div>
    </div>
</template>
