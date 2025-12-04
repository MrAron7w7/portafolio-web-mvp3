<script setup lang="ts">
import { Monitor, Smartphone } from 'lucide-vue-next';
import { ref } from 'vue';

const device = ref<'desktop' | 'mobile'>('desktop');

const setDevice = (d: 'desktop' | 'mobile') => {
    device.value = d;
};
</script>

<template>
    <div class="flex flex-col h-full">
        <!-- Toolbar -->
        <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-4 py-3">
            <div class="flex items-center space-x-2">
                <div class="flex space-x-1.5">
                    <div class="h-3 w-3 rounded-full bg-red-400"></div>
                    <div class="h-3 w-3 rounded-full bg-yellow-400"></div>
                    <div class="h-3 w-3 rounded-full bg-green-400"></div>
                </div>
            </div>
            
            <!-- Device Toggles -->
            <div class="flex items-center space-x-1 rounded-lg bg-gray-200 p-1">
                <button
                    @click="setDevice('desktop')"
                    :class="[
                        'rounded-md p-1.5 transition-all duration-200',
                        device === 'desktop'
                            ? 'bg-white text-gray-900 shadow-sm'
                            : 'text-gray-500 hover:text-gray-700'
                    ]"
                    title="Vista de escritorio"
                >
                    <Monitor class="h-4 w-4" />
                </button>
                <button
                    @click="setDevice('mobile')"
                    :class="[
                        'rounded-md p-1.5 transition-all duration-200',
                        device === 'mobile'
                            ? 'bg-white text-gray-900 shadow-sm'
                            : 'text-gray-500 hover:text-gray-700'
                    ]"
                    title="Vista móvil"
                >
                    <Smartphone class="h-4 w-4" />
                </button>
            </div>

            <div class="w-16"></div> <!-- Spacer for balance -->
        </div>

        <!-- Viewport Container -->
        <div class="flex-1 overflow-hidden bg-gray-100 p-4 flex items-center justify-center">
            <div
                :class="[
                    'bg-white transition-all duration-500 ease-in-out shadow-2xl overflow-hidden',
                    device === 'mobile' 
                        ? 'w-[375px] h-[667px] rounded-[3rem] border-[8px] border-gray-800' 
                        : 'w-full h-full rounded-lg border border-gray-200'
                ]"
            >
                <!-- Mobile Notch -->
                <div v-if="device === 'mobile'" class="absolute top-0 left-1/2 -translate-x-1/2 h-6 w-32 bg-gray-800 rounded-b-xl z-10"></div>

                <!-- Content Scroll Area -->
                <div class="h-full w-full overflow-y-auto scrollbar-hide">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
