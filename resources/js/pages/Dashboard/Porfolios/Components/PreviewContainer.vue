<script setup lang="ts">
    import { Monitor, Smartphone } from 'lucide-vue-next';
    import { ref, computed, watch, nextTick } from 'vue';
    
    const device = ref<'desktop' | 'mobile'>('mobile');
    const containerRef = ref<HTMLElement>();
    const scrollAreaRef = ref<HTMLElement>();
    const viewportWrapperRef = ref<HTMLElement>();
    const isTransitioning = ref(false);
    const showContent = ref(true);
    
    const setDevice = (d: 'desktop' | 'mobile') => {
        if (device.value === d) return;
        
        // Fase 1: Desvanecer
        showContent.value = false;
        isTransitioning.value = true;
        
        setTimeout(() => {
            // Fase 2: Cambiar aspecto
            device.value = d;
        }, 150);
        
        setTimeout(async () => {
            // Fase 3: Preparar contenido
            await nextTick();
            if (scrollAreaRef.value) {
                scrollAreaRef.value.scrollTop = 0;
            }
            
            // Fase 4: Mostrar
            showContent.value = true;
            isTransitioning.value = false;
        }, 180);
    };
    
    const scaleTransform = computed(() => {
        if (!containerRef.value) return 1;
        const baseWidth = device.value === 'desktop' ? 1200 : 375;
        const availableWidth = containerRef.value.clientWidth - 32;
        return Math.min(1, (availableWidth / baseWidth));
    });
    
    const scrollAreaKey = computed(() => `scroll-area-${device.value}`);
</script>

<template>
    <div class="preview-container dark:bg-slate-950!" ref="containerRef">
        <!-- Toolbar -->
        <div class="toolbar bg-gray-50 border-b border-gray-200 dark:bg-slate-950! dark:border-slate-800!">
            <div class="flex space-x-1.5">
                <div class="h-3 w-3 rounded-full bg-red-400"></div>
                <div class="h-3 w-3 rounded-full bg-yellow-400"></div>
                <div class="h-3 w-3 rounded-full bg-green-400"></div>
            </div>

            <div class="device-toggle dark:bg-slate-900!">
                <button @click="setDevice('mobile')" :class="[
                    'device-toggle-btn transition-all duration-300',
                    device === 'mobile' 
                        ? 'bg-white dark:bg-slate-800! text-gray-900 dark:text-white! shadow-sm' 
                        : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800/50'
                ]">
                    <Smartphone class="h-4 w-4 shrink-0" />
                    <span class="btn-label">Disp. Móvil</span>
                </button>
                <button @click="setDevice('desktop')" :class="[
                    'device-toggle-btn transition-all duration-300',
                    device === 'desktop' 
                        ? 'bg-white dark:bg-slate-800! text-gray-900 dark:text-white! shadow-sm' 
                        : 'text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800/50'
                ]">
                    <Monitor class="h-4 w-4 shrink-0" />
                    <span class="btn-label">Escritorio</span>
                </button>
            </div>

            <div class="w-12"></div>
        </div>

        <!-- Viewport -->
        <div class="viewport-wrapper bg-linear-to-br from-gray-50 to-gray-200 dark:from-slate-950! dark:to-slate-900!" ref="viewportWrapperRef" :data-device="device">
            <div :style="{
                transform: `scale(${scaleTransform})`,
                transformOrigin: 'top center',
                opacity: showContent ? 1 : 0
            }" :class="[
                'preview-device dark:bg-slate-900! dark:border-slate-800!',
                device === 'mobile' ? 'mobile' : 'desktop'
            ]">
                <!-- Frame Desktop -->
                <div v-if="device === 'desktop'" class="desktop-frame">
                    <div class="frame-header dark:bg-slate-900 dark:border-slate-800">
                        <div class="frame-dots">
                            <div class="dot red"></div>
                            <div class="dot yellow"></div>
                            <div class="dot green"></div>
                        </div>
                        <div class="frame-title dark:text-slate-400">Preview</div>
                        <div class="frame-controls"></div>
                    </div>
                    <div :key="scrollAreaKey" ref="scrollAreaRef" class="scroll-area">
                        <slot />
                    </div>
                </div>

                <!-- Frame Mobile -->
                <div v-else class="mobile-frame">
                    <div class="notch"></div>
                    <div :key="scrollAreaKey" ref="scrollAreaRef" class="scroll-area">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.preview-container {
    display: flex;
    flex-direction: column;
    height: 100%;
    width: 100%;
    overflow: hidden;
}

.toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    height: 60px;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

:global(.dark) .toolbar {
    background: #020617 !important;
    border-color: #1e293b !important;
}

.device-toggle {
    display: flex;
    gap: 0.5rem;
    background: #e5e7eb;
    padding: 0.25rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

:global(.dark) .device-toggle {
    background: #0f172a !important;
}

.device-toggle-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.75rem;
    border: none;
    background: transparent;
    border-radius: 0.375rem;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.btn-label {
    display: inline-block;
    max-width: 0;
    overflow: hidden;
    white-space: nowrap;
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    transform: translateX(-10px);
}

.bg-white .btn-label, .dark\:bg-slate-800\! .btn-label {
    max-width: 100px;
    opacity: 1;
    transform: translateX(0);
    margin-left: 0.25rem;
}

.viewport-wrapper {
    flex: 1;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    width: 100%;
    overflow: hidden;
    transition: all 0.3s ease;
}

:global(.dark) .viewport-wrapper {
    background: linear-gradient(135deg, #020617 0%, #0f172a 100%) !important;
}

.preview-device {
    position: relative;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    flex-shrink: 0;
    transition: opacity 0.15s ease-in-out, background 0.3s ease;
}

:global(.dark) .preview-device {
    background: #0f172a !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
}

/* DESKTOP DEVICE */
.preview-device.desktop {
    width: 1200px;
    height: auto;
    border-radius: 0.75rem;
    border: 1px solid #e5e7eb;
}

:global(.dark) .preview-device.desktop {
    border-color: #334155 !important;
}

.desktop-frame {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100%;
}

.frame-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    flex-shrink: 0;
}

:global(.dark) .frame-header {
    background: linear-gradient(to bottom, #1e293b, #0f172a) !important;
    border-color: #334155 !important;
}

.frame-dots {
    display: flex;
    gap: 0.5rem;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.dot.red {
    background: #ef4444;
}

.dot.yellow {
    background: #eab308;
}

.dot.green {
    background: #22c55e;
}

.frame-title {
    flex: 1;
    text-align: center;
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    letter-spacing: 0.05em;
}

.frame-controls {
    width: 40px;
}

/* MOBILE DEVICE */
.preview-device.mobile {
    width: 375px;
    height: 667px;
    border-radius: 3rem;
    border: 8px solid #1f2937;
}

:global(.dark) .preview-device.mobile {
    border-color: #020617 !important;
}

.mobile-frame {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.notch {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 8rem;
    height: 1.5rem;
    background: #1f2937;
    border-radius: 0 0 0.75rem 0.75rem;
    z-index: 10;
}

.scroll-area {
    width: 100%;
    overflow-y: auto;
    overflow-x: hidden;
    background: transparent !important;
}

/* DESKTOP SCROLL */
.desktop-frame .scroll-area {
    height: auto;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f1f1;
    flex: 1;
}

.desktop-frame .scroll-area::-webkit-scrollbar {
    width: 8px;
}

.desktop-frame .scroll-area::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.desktop-frame .scroll-area::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.desktop-frame .scroll-area::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* MOBILE SCROLL */
.mobile-frame .scroll-area {
    flex: 1;
    scrollbar-width: none;
    -ms-overflow-style: none;

}

.mobile-frame .scroll-area::-webkit-scrollbar {
    display: none;
}

/* Overrides para mobile - solo lo esencial */
.scroll-area :deep(.lg\:grid-cols-3),
.scroll-area :deep(.lg\:grid-cols-2),
.scroll-area :deep(.md\:grid-cols-2),
.scroll-area :deep(.md\:grid-cols-3) {
    grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
}

.scroll-area :deep(.lg\:w-1\/3),
.scroll-area :deep(.lg\:w-1\/2),
.scroll-area :deep(.md\:w-1\/2) {
    width: 100% !important;
}

.scroll-area :deep(.lg\:p-8),
.scroll-area :deep(.lg\:px-8),
.scroll-area :deep(.lg\:py-8),
.scroll-area :deep(.md\:p-6),
.scroll-area :deep(.md\:px-6) {
    padding: 1rem !important;
}

.scroll-area :deep(.lg\:py-8),
.scroll-area :deep(.md\:py-6) {
    padding-top: 1rem !important;
    padding-bottom: 1rem !important;
}

.scroll-area :deep(.gap-8),
.scroll-area :deep(.lg\:gap-8) {
    gap: 1rem !important;
}

.scroll-area :deep(.gap-6),
.scroll-area :deep(.md\:gap-6) {
    gap: 0.75rem !important;
}

.scroll-area :deep(.text-2xl),
.scroll-area :deep(.lg\:text-3xl) {
    font-size: 1.25rem !important;
}

.scroll-area :deep(.text-xl),
.scroll-area :deep(.md\:text-2xl) {
    font-size: 1rem !important;
}

.scroll-area :deep(.text-lg) {
    font-size: 0.875rem !important;
}
</style>
