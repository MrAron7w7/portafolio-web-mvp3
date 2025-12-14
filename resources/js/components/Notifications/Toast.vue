<script setup>
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'info',
        validator: (value) => ['info', 'success', 'warning', 'error'].includes(value)
    },
    title: { type: String, required: true },
    message: { type: String, required: true },
    duration: { type: Number, default: 5000 },
    position: {
        type: String,
        default: 'top-right',
        // Agregamos 'top-full' a las opciones válidas
        validator: (value) => ['top-right', 'top-left', 'bottom-right', 'bottom-left', 'top-center', 'top-full'].includes(value)
    }
});

const emit = defineEmits(['close']);
const isVisible = ref(false);
const progress = ref(100);

// Computed para saber si es modo barra completa
const isFullWidth = computed(() => props.position === 'top-full');

// Colores (Igual que antes)
const typeStyles = computed(() => {
    const styles = {
        info: { bg: 'bg-blue-50', border: 'border-blue-500', icon: '💡', iconBg: 'bg-blue-100', text: 'text-blue-900', progressBg: 'bg-blue-500' },
        success: { bg: 'bg-green-50', border: 'border-green-500', icon: '✅', iconBg: 'bg-green-100', text: 'text-green-900', progressBg: 'bg-green-500' },
        warning: { bg: 'bg-yellow-50', border: 'border-yellow-500', icon: '⚠️', iconBg: 'bg-yellow-100', text: 'text-yellow-900', progressBg: 'bg-yellow-500' },
        error: { bg: 'bg-red-50', border: 'border-red-500', icon: '❌', iconBg: 'bg-red-100', text: 'text-red-900', progressBg: 'bg-red-500' }
    };
    return styles[props.type] || styles.info;
});

// Clases de Posición
const positionClasses = computed(() => {
    const positions = {
        'top-right': 'top-4 right-4 max-w-sm', // Agregamos max-w-sm aquí
        'top-left': 'top-4 left-4 max-w-sm',
        'bottom-right': 'bottom-4 right-4 max-w-sm',
        'bottom-left': 'bottom-4 left-4 max-w-sm',
        'top-center': 'top-4 left-1/2 -translate-x-1/2 max-w-sm',
        'top-full': 'top-0 left-0 w-full' // Sin max-w, pegado arriba
    };
    return positions[props.position] || positions['top-right'];
});

// Configuración dinámica de la Transición
const transitionConfig = computed(() => {
    if (isFullWidth.value) {
        return {
            enterFrom: '-translate-y-full opacity-0',
            enterTo: 'translate-y-0 opacity-100',
            leaveFrom: 'translate-y-0 opacity-100',
            leaveTo: '-translate-y-full opacity-0'
        };
    }
    // Configuración normal para los toasts flotantes
    return {
        enterFrom: 'translate-y-2 opacity-0 scale-95',
        enterTo: 'translate-y-0 opacity-100 scale-100',
        leaveFrom: 'translate-y-0 opacity-100 scale-100',
        leaveTo: 'translate-y-2 opacity-0 scale-95'
    };
});

// ... Lógica de timers (close, onMounted) igual que tu código original ...
let progressInterval;
let closeTimeout;

const close = () => {
    isVisible.value = false;
    clearInterval(progressInterval);
    clearTimeout(closeTimeout);
    setTimeout(() => emit('close'), 300);
};

onMounted(() => {
    setTimeout(() => { isVisible.value = true; }, 10);
    const step = 100 / (props.duration / 50);
    progressInterval = setInterval(() => {
        progress.value -= step;
        if (progress.value <= 0) clearInterval(progressInterval);
    }, 50);
    closeTimeout = setTimeout(() => close(), props.duration);
});
</script>

<template>
    <Transition
        enter-active-class="transition duration-500 ease-out"
        :enter-from-class="transitionConfig.enterFrom"
        :enter-to-class="transitionConfig.enterTo"
        leave-active-class="transition duration-300 ease-in"
        :leave-from-class="transitionConfig.leaveFrom"
        :leave-to-class="transitionConfig.leaveTo"
    >
        <div
            v-if="isVisible"
            :class="[
                'fixed z-50 pointer-events-auto flex justify-center', // Flex justify-center ayuda a centrar contenido en barra
                positionClasses
            ]"
        >
            <div
                :class="[
                    'relative overflow-hidden shadow-xl transition-all',
                    typeStyles.bg,
                    // Lógica condicional de estilos:
                    isFullWidth ? 'w-full border-b-2' : 'w-full rounded-xl border-l-4',
                    isFullWidth ? typeStyles.border.replace('border-', 'border-b-') : typeStyles.border
                ]"
            >
                <div :class="['p-4', isFullWidth ? 'container mx-auto flex items-center justify-between' : '']">
                    
                    <div class="flex items-center gap-3 flex-1">
                        <div :class="['flex-shrink-0 flex items-center justify-center text-xl', isFullWidth ? 'w-8 h-8' : 'w-10 h-10 rounded-lg', typeStyles.iconBg]">
                            {{ typeStyles.icon }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <div :class="isFullWidth ? 'md:flex md:items-center md:gap-2' : ''">
                                <h3 :class="['text-sm font-semibold', typeStyles.text, isFullWidth ? 'mb-0' : 'mb-1']">
                                    {{ title }}
                                </h3>
                                <p :class="['text-sm', typeStyles.text, 'opacity-80', isFullWidth ? 'hidden md:block' : '']">
                                    <span v-if="isFullWidth" class="hidden md:inline mx-1">-</span>
                                    {{ message }}
                                </p>
                            </div>
                            <p v-if="isFullWidth" :class="['text-sm md:hidden', typeStyles.text, 'opacity-80']">
                                {{ message }}
                            </p>
                        </div>
                    </div>

                    <button @click="close" :class="['flex-shrink-0 ml-4 text-gray-500 hover:text-gray-800 transition-colors']">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="h-1 bg-black/5 absolute bottom-0 left-0 w-full">
                    <div
                        :class="['h-full transition-all duration-50', typeStyles.progressBg]"
                        :style="{ width: `${progress}%` }"
                    ></div>
                </div>
            </div>
        </div>
    </Transition>
</template>