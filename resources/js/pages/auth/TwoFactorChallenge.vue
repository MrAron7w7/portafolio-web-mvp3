<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    PinInput,
    PinInputGroup,
    PinInputSlot,
} from '@/components/ui/pin-input';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { KeyRound, ShieldCheck, ArrowLeft } from 'lucide-vue-next';

const page = usePage();

interface AuthConfigContent {
    title: string;
    description: string;
    toggleText: string;
}

const authConfigContent = computed<AuthConfigContent>(() => {
    if (showRecoveryInput.value) {
        return {
            title: 'Código de Recuperación',
            description: 'Ingresa uno de tus códigos de recuperación de emergencia para acceder a tu cuenta.',
            toggleText: 'usar código de autenticación',
        };
    }

    return {
        title: 'Verificación en Dos Pasos',
        description: 'Te enviamos un código de 6 dígitos a tu correo electrónico. Ingrésalo para continuar.',
        toggleText: 'usar código de recuperación',
    };
});

const showRecoveryInput = ref<boolean>(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const toggleRecoveryMode = (): void => {
    showRecoveryInput.value = !showRecoveryInput.value;
    form.clearErrors();
    form.reset();
    code.value = [];
};

const code = ref<number[]>([]);

// Sync PIN input with form.code
watch(code, (newCode) => {
    form.code = newCode.join('');
});

const submit = () => {
    form.post('/two-factor-challenge');
};

</script>

<template>
    <Head title="Verificación 2FA" />

    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-slate-50 dark:bg-slate-950 p-6 md:p-10 transition-colors duration-300">
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <!-- Logo -->
                <div class="flex flex-col items-center gap-4">
                    <a href="/" class="flex flex-col items-center gap-2">
                        <img
                            v-if="(page.props as any).logo_url"
                            :src="(page.props as any).logo_url"
                            class="h-10 w-auto object-contain"
                            alt="Logo"
                        />
                        <svg
                            v-else
                            class="h-10 w-auto text-indigo-600 dark:text-indigo-500"
                            fill="none"
                            height="40"
                            viewBox="0 0 35 32"
                            width="43"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M34.9312 16C34.9312 24.8366 27.2764 32 17.6531 32C8.0298 32 0.375 24.8366 0.375 16C0.375 7.16344 8.0298 0 17.6531 0C27.2764 0 34.9312 7.16344 34.9312 16ZM10.0125 10.6667V21.3333L19.4687 16L10.0125 10.6667Z"
                                fill="currentColor"
                            />
                        </svg>
                    </a>
                    
                    <!-- Title & Description -->
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ authConfigContent.title }}</h1>
                        <p class="text-center text-sm text-slate-500 dark:text-slate-400">
                            {{ authConfigContent.description }}
                        </p>
                    </div>
                </div>

                <!-- Card -->
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 w-full">
                    <div class="space-y-6">
                        <!-- Authentication Code Form -->
                        <template v-if="!showRecoveryInput">
                            <form @submit.prevent="submit" class="space-y-6">
                                <!-- PIN Input -->
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="flex w-full items-center justify-center">
                                        <PinInput
                                            id="otp"
                                            placeholder="○"
                                            v-model="code"
                                            type="number"
                                            otp
                                            class="text-slate-900 dark:text-white"
                                        >
                                            <PinInputGroup class="gap-2">
                                                <PinInputSlot
                                                    v-for="(id, index) in 6"
                                                    :key="id"
                                                    :index="index"
                                                    :disabled="form.processing"
                                                    autofocus
                                                    class="w-11 h-12 text-lg font-bold bg-slate-50 dark:bg-slate-950 border-2 border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                                                />
                                            </PinInputGroup>
                                        </PinInput>
                                    </div>
                                    <InputError :message="form.errors.code" class="text-center" />
                                </div>

                                <!-- Info Box -->
                                <div class="p-3 bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 rounded-xl">
                                    <p class="text-xs text-indigo-700 dark:text-indigo-300 text-center">
                                        <ShieldCheck class="w-3.5 h-3.5 inline-block mr-1 -mt-0.5" />
                                        El código expira en <strong>10 minutos</strong>
                                    </p>
                                </div>

                                <!-- Submit Button -->
                                <Button 
                                    type="submit" 
                                    class="w-full h-11 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-300" 
                                    :disabled="form.processing || code.length !== 6"
                                >
                                    <span v-if="form.processing" class="flex items-center gap-2">
                                        <span class="animate-spin">⟳</span> Verificando...
                                    </span>
                                    <span v-else>Verificar y Continuar</span>
                                </Button>

                                <!-- Toggle to Recovery -->
                                <div class="text-center">
                                    <button
                                        type="button"
                                        class="text-sm text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                        @click="toggleRecoveryMode"
                                    >
                                        ¿Problemas? <span class="underline underline-offset-2">{{ authConfigContent.toggleText }}</span>
                                    </button>
                                </div>
                            </form>
                        </template>

                        <!-- Recovery Code Form -->
                        <template v-else>
                            <form @submit.prevent="submit" class="space-y-6">
                                <!-- Recovery Input -->
                                <div class="space-y-4">
                                    <Input
                                        v-model="form.recovery_code"
                                        type="text"
                                        placeholder="XXXX-XXXX-XXXX"
                                        :autofocus="showRecoveryInput"
                                        required
                                        class="h-12 text-center text-lg font-mono tracking-widest bg-slate-50 dark:bg-slate-950 border-2 border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder:text-slate-300 dark:placeholder:text-slate-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                    />
                                    <InputError :message="form.errors.recovery_code" class="text-center" />
                                </div>

                                <!-- Warning Box -->
                                <div class="p-3 bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 rounded-xl">
                                    <p class="text-xs text-amber-700 dark:text-amber-300 text-center">
                                        ⚠️ Los códigos de recuperación son de <strong>un solo uso</strong>
                                    </p>
                                </div>

                                <!-- Submit Button -->
                                <Button 
                                    type="submit" 
                                    class="w-full h-11 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-300" 
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing" class="flex items-center gap-2">
                                        <span class="animate-spin">⟳</span> Verificando...
                                    </span>
                                    <span v-else>Recuperar Acceso</span>
                                </Button>

                                <!-- Back to Code -->
                                <div class="text-center">
                                    <button
                                        type="button"
                                        class="text-sm text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors flex items-center justify-center gap-1 mx-auto"
                                        @click="toggleRecoveryMode"
                                    >
                                        <ArrowLeft class="w-4 h-4" />
                                        Volver a {{ authConfigContent.toggleText }}
                                    </button>
                                </div>
                            </form>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
