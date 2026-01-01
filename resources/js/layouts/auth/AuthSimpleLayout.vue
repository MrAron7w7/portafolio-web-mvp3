<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center justify-center gap-6 bg-slate-50 dark:bg-slate-950 p-6 md:p-10 transition-colors duration-300"
    >
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-2 font-medium"
                    >
                        <div
                            class="mb-1 flex h-9 w-9 items-center justify-center rounded-md"
                        >
                            <AppLogoIcon
                                class="size-9 fill-current text-indigo-600 dark:text-indigo-500"
                            />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ title }}</h1>
                        <p class="text-center text-sm text-slate-500 dark:text-slate-400">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <!-- Content wrapper with dark mode support for inner elements -->
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 w-full text-slate-900 dark:text-slate-200">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
