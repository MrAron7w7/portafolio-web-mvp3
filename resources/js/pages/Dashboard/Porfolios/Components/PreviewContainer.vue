<script setup lang="ts">
import { Monitor, Smartphone } from 'lucide-vue-next';
import { ref, computed, watch, nextTick } from 'vue';

const device = ref<'desktop' | 'mobile'>('mobile');
const containerRef = ref<HTMLElement>();
const scrollAreaRef = ref<HTMLElement>();
const viewportWrapperRef = ref<HTMLElement>(); // ← Agregar referencia

const setDevice = (d: 'desktop' | 'mobile') => {
    device.value = d;
};

const scaleTransform = computed(() => {
    if (!containerRef.value) return 1;
    const baseWidth = device.value === 'desktop' ? 1200 : 375;
    const availableWidth = containerRef.value.clientWidth - 32;
    return Math.min(1, (availableWidth / baseWidth));
});

const scrollAreaKey = computed(() => `scroll-area-${device.value}`);

watch(device, async (newDevice, oldDevice) => {
    if (newDevice !== oldDevice) {
        await nextTick();
        if (scrollAreaRef.value) {
            scrollAreaRef.value.scrollTop = 0;
        }
        if (containerRef.value) {
            const _ = containerRef.value.offsetHeight;
        }
    }
});
</script>

<template>
    <div class="preview-container" ref="containerRef">
        <!-- Toolbar -->
        <div class="toolbar">
            <div class="flex space-x-1.5">
                <div class="h-3 w-3 rounded-full bg-red-400"></div>
                <div class="h-3 w-3 rounded-full bg-yellow-400"></div>
                <div class="h-3 w-3 rounded-full bg-green-400"></div>
            </div>

            <div class="device-toggle">
                <button @click="setDevice('mobile')" :class="[
                    'device-toggle-btn',
                    device === 'mobile' && 'active'
                ]">
                    <Smartphone class="h-4 w-4 flex-shrink-0" />
                    <span class="btn-label">Disp. Móvil</span>
                </button>
                <button @click="setDevice('desktop')" :class="[
                    'device-toggle-btn',
                    device === 'desktop' && 'active'
                ]">
                    <Monitor class="h-4 w-4 flex-shrink-0" />
                    <span class="btn-label">Escritorio</span>
                </button>
            </div>

            <div class="w-12"></div>
        </div>

        <!-- Viewport con transform scale -->
        <div class="viewport-wrapper" ref="viewportWrapperRef" :data-device="device">
            <div :style="{
                transform: `scale(${scaleTransform})`,
                transformOrigin: 'top center',
                transition: 'transform 0.3s ease'
            }" :class="[
                    'preview-device',
                    device === 'mobile' ? 'mobile' : 'desktop'
                ]">
                <div v-if="device === 'mobile'" class="notch"></div>
                <div :key="scrollAreaKey" ref="scrollAreaRef" :data-device="device" :class="[
                    'scroll-area',
                    device === 'mobile' && 'scroll-area--hidden'
                ]">
                    <slot />
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
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    padding: 0.75rem 1rem;
    flex-shrink: 0;
    height: 60px;
}

.device-toggle {
    display: flex;
    gap: 0.5rem;
    background: #e5e7eb;
    padding: 0.25rem;
    border-radius: 0.5rem;
}

.device-toggle button {
    padding: 0.375rem 0.75rem;
    border: none;
    background: transparent;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s;
    color: #6b7280;
}

.device-toggle button.active {
    background: white;
    color: #111827;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.viewport-wrapper {
    flex: 1;
    background: #f3f4f6;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    width: 100%;
    overflow-y: hidden;
    overflow-x: hidden;
}

.viewport-wrapper[data-device="mobile"] {
    overflow: hidden;
}

.preview-device {
    background: white;
    box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
    position: relative;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    flex-shrink: 0;
}

.preview-device.desktop {
    width: 1200px;
    height: auto;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
}

.preview-device.mobile {
    width: 375px;
    height: 667px;
    border-radius: 3rem;
    border: 8px solid #1f2937;
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
}

/* DESKTOP: Sin flex, solo contenido natural */
.preview-device.desktop .scroll-area {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f1f1;
    height: auto;
    flex: 0;
}

.preview-device.desktop .scroll-area::-webkit-scrollbar {
    width: 8px;
}

.preview-device.desktop .scroll-area::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.preview-device.desktop .scroll-area::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.preview-device.desktop .scroll-area::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* MOBILE: Flex + ocultar scrollbar */
.preview-device.mobile .scroll-area {
    flex: 1;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.preview-device.mobile .scroll-area::-webkit-scrollbar {
    display: none;
}

.scroll-area--hidden::-webkit-scrollbar {
    display: none;
}

.scroll-area[data-device="mobile"] :deep(.lg\:grid-cols-3) {
    grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
}

.scroll-area[data-device="mobile"] :deep(.lg\:grid-cols-2) {
    grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
}

.scroll-area[data-device="mobile"] :deep(.md\:grid-cols-2) {
    grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
}

.scroll-area[data-device="mobile"] :deep(.md\:grid-cols-3) {
    grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
}

.scroll-area[data-device="mobile"] :deep(.lg\:w-1\/3) {
    width: 100% !important;
}

.scroll-area[data-device="mobile"] :deep(.lg\:w-1\/2) {
    width: 100% !important;
}

.scroll-area[data-device="mobile"] :deep(.md\:w-1\/2) {
    width: 100% !important;
}

.scroll-area[data-device="mobile"] :deep(.lg\:p-8) {
    padding: 1rem !important;
}

.scroll-area[data-device="mobile"] :deep(.lg\:px-8) {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
}

.scroll-area[data-device="mobile"] :deep(.lg\:py-8) {
    padding-top: 1rem !important;
    padding-bottom: 1rem !important;
}

.scroll-area[data-device="mobile"] :deep(.md\:p-6) {
    padding: 1rem !important;
}

.scroll-area[data-device="mobile"] :deep(.md\:px-6) {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
}

.scroll-area[data-device="mobile"] :deep(.gap-8) {
    gap: 1rem !important;
}

.scroll-area[data-device="mobile"] :deep(.gap-6) {
    gap: 0.75rem !important;
}

.scroll-area[data-device="mobile"] :deep(.lg\:gap-8) {
    gap: 1rem !important;
}

.scroll-area[data-device="mobile"] :deep(.md\:gap-6) {
    gap: 0.75rem !important;
}

.scroll-area[data-device="mobile"] :deep(.text-2xl) {
    font-size: 1.25rem !important;
}

.scroll-area[data-device="mobile"] :deep(.text-xl) {
    font-size: 1rem !important;
}

.scroll-area[data-device="mobile"] :deep(.text-lg) {
    font-size: 0.875rem !important;
}

.scroll-area[data-device="mobile"] :deep(.lg\:text-3xl) {
    font-size: 1.25rem !important;
}

.scroll-area[data-device="mobile"] :deep(.md\:text-2xl) {
    font-size: 1.125rem !important;
}

.scroll-area[data-device="mobile"] :deep(.lg\:block) {
    display: block !important;
}

.scroll-area[data-device="mobile"] :deep(.md\:flex) {
    display: flex !important;
    flex-direction: column;
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
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative;
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

.device-toggle-btn.active .btn-label {
    max-width: 100px;
    opacity: 1;
    transform: translateX(0);
    margin-left: 0.25rem;
}

.device-toggle-btn.active {
    background: white;
    color: #111827;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    padding: 0.375rem 1rem;
}
</style>

