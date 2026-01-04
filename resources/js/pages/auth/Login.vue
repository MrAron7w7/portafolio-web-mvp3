<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import google from '@/routes/google';
import login from '@/routes/login';
import password from '@/routes/password';
import { Form, Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();




interface PageProps {
    auth: {
        user: any;
    };
    flash: {
        success: string | null;
        error: string | null;
    };
    name: string;
    quote: { author: string; message: string };
    sidebarOpen: boolean;
    logo_url?: string;
    brand_name?: string;
    auth_bg?: string;
    auth_content?: {
        welcome_title?: string;
        welcome_subtitle?: string;
    };
    [key: string]: any;
}

const page = usePage<PageProps>();
const showPassword = ref(false);
</script>

<template>
    <Head title="Iniciar sesión" />

    <div class="bg-slate-50 dark:bg-slate-950 flex min-h-screen transition-colors duration-300">
        <!-- Sección izquierda - Formulario -->
        <div
            class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24"
        >
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Header -->
                <div>
                    <div
                        class="mb-10 flex items-center justify-center gap-3 lg:justify-start"
                    >
                        <img
                            v-if="page.props.logo_url"
                            :src="page.props.logo_url"
                            class="h-8 w-auto object-contain"
                            alt="Logo"
                        />
                        <svg
                            v-else
                            class="h-8 w-auto text-indigo-600 dark:text-indigo-500"
                            fill="none"
                            height="32"
                            viewBox="0 0 35 32"
                            width="35"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M34.9312 16C34.9312 24.8366 27.2764 32 17.6531 32C8.0298 32 0.375 24.8366 0.375 16C0.375 7.16344 8.0298 0 17.6531 0C27.2764 0 34.9312 7.16344 34.9312 16ZM10.0125 10.6667V21.3333L19.4687 16L10.0125 10.6667Z"
                                fill="currentColor"
                            />
                        </svg>
                        <span
                            class="text-slate-900 dark:text-white text-xl font-bold"
                            >{{ page.props.brand_name || 'Portafolio IA' }}</span
                        >
                    </div>

                    <div class="flex flex-wrap justify-between gap-3">
                        <div class="flex w-full flex-col gap-3">
                            <p
                                class="text-slate-900 dark:text-white text-bold text-3xl leading-tight font-black tracking-[-0.033em]"
                            >
                                {{ page.props.auth_content?.welcome_title || 'Bienvenido a Portafolio Profesional IA' }}
                            </p>
                            <p
                                class="text-slate-500 dark:text-slate-400 text-base leading-normal font-normal"
                            >
                                {{ page.props.auth_content?.welcome_subtitle || 'Crea tu portafolio perfecto con inteligencia artificial.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Status Message -->
                <div
                    v-if="status"
                    class="mt-6 rounded-lg border border-green-200 bg-green-50 p-3 text-center text-sm font-medium text-green-600 dark:bg-green-900/30 dark:border-green-900/50 dark:text-green-400"
                >
                    {{ status }}
                </div>

                <!-- Formulario -->
                <div class="mt-10">
                    <Form
                        v-bind="login.store.form()"
                        :reset-on-success="['password']"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <!-- Email -->
                        <div class="space-y-2">
                            <Label
                                for="email"
                                class="text-slate-900 dark:text-white text-sm leading-normal font-medium"
                            >
                                Email
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="email"
                                placeholder="tu@email.com"
                                class="text-slate-900 dark:text-white border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-900 placeholder:text-slate-400 dark:placeholder:text-slate-500 flex h-12 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg border px-4 text-base leading-normal font-normal focus:ring-2 focus:ring-indigo-600/50 dark:focus:ring-indigo-500/50 focus:outline-0"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <Label
                                    for="password"
                                    class="text-slate-900 dark:text-white text-sm leading-normal font-medium"
                                >
                                    Contraseña
                                </Label>
                                <TextLink
                                    v-if="canResetPassword"
                                    :href="password.request()"
                                    class="text-sm text-indigo-600 dark:text-indigo-400 transition-colors duration-200 hover:text-indigo-700 dark:hover:text-indigo-300"
                                    :tabindex="5"
                                >
                                    <!-- ¿Olvidaste tu contraseña? -->
                                </TextLink>
                            </div>
                            <div class="relative">
                                <Input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    required
                                    :tabindex="2"
                                    autocomplete="current-password"
                                    placeholder="Ingresa tu contraseña"
                                    class="text-slate-900 dark:text-white border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-900 placeholder:text-slate-400 dark:placeholder:text-slate-500 flex h-12 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg px-4 pr-12 text-base leading-normal font-normal focus:ring-2 focus:ring-indigo-600/50 dark:focus:ring-indigo-500/50 focus:outline-0 transition-all"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    aria-label="Toggle password visibility"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors cursor-pointer z-10"
                                >
                                    <span
                                        class="material-symbols-outlined select-none"
                                        style="font-size: 20px;"
                                    >
                                        {{ showPassword ? 'visibility' : 'visibility_off' }}
                                    </span>
                                </button>
                            </div>
                            <InputError :message="errors.password" />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <Label
                                for="remember"
                                class="flex cursor-pointer items-center space-x-3"
                            >
                                <Checkbox
                                    id="remember"
                                    name="remember"
                                    :tabindex="3"
                                    class="data-[state=checked]:border-indigo-600 dark:data-[state=checked]:border-indigo-500 data-[state=checked]:bg-indigo-600 dark:data-[state=checked]:bg-indigo-500 border-slate-300 dark:border-slate-600"
                                />
                                <span
                                    class="text-slate-900 dark:text-white text-sm"
                                    >Recordarme</span
                                >
                            </Label>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <Button
                                type="submit"
                                class="focus:ring-offset-white dark:focus:ring-offset-slate-950 flex h-12 w-full min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg px-5 text-base leading-normal font-bold tracking-[0.015em] text-white transition-colors duration-200 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-500 focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:outline-none"
                                :tabindex="4"
                                :disabled="processing"
                                data-test="login-button"
                            >
                                <Spinner v-if="processing" class="mr-2" />
                                <span class="truncate">
                                    {{
                                        processing
                                            ? 'Iniciando sesión...'
                                            : 'Iniciar sesión'
                                    }}
                                </span>
                            </Button>
                        </div>

                        <!-- Social Login -->
                        <div class="mt-10">
                            <div class="relative">
                                <div
                                    aria-hidden="true"
                                    class="absolute inset-0 flex items-center"
                                >
                                    <div
                                        class="border-slate-400 dark:border-slate-600 w-full border-t"
                                    ></div>
                                </div>
                                <div
                                    class="relative flex justify-center text-sm leading-6 font-medium"
                                >
                                    <span
                                        class="bg-slate-50 dark:bg-slate-950 text-slate-500 dark:text-slate-400 px-6"
                                    >
                                        O continúa con
                                    </span>
                                </div>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <a
                                    class="border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-offset-white dark:focus:ring-offset-slate-950 flex w-full items-center justify-center gap-3 rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-colors duration-200 hover:bg-slate-50 focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:outline-none dark:hover:bg-slate-800"
                                    :href="google.login().url"
                                >
                                    <svg
                                        aria-hidden="true"
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                            fill="#4285F4"
                                        />
                                        <path
                                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                            fill="#34A853"
                                        />
                                        <path
                                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"
                                            fill="#FBBC05"
                                        />
                                        <path
                                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                            fill="#EA4335"
                                        />
                                        <path d="M1 1h22v22H1z" fill="none" />
                                    </svg>
                                    <span>Google</span>
                                </a>
                                <a
                                    class="border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-offset-white dark:focus:ring-offset-slate-950 flex w-full items-center justify-center gap-3 rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-colors duration-200 hover:bg-slate-50 focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:outline-none dark:hover:bg-slate-800"
                                    href="#"
                                >
                                    <svg
                                        aria-hidden="true"
                                        class="h-6 w-6"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.225 0z"
                                        />
                                    </svg>
                                    <span>LinkedIn</span>
                                </a>
                            </div>
                        </div>

                        <!-- Register Link -->
                        <div
                            class="text-slate-500 dark:text-slate-400 mt-10 text-center text-sm"
                            v-if="canRegister"
                        >
                            ¿No tienes cuenta?
                            <TextLink
                                :href="register()"
                                :tabindex="5"
                                class="leading-6 font-semibold text-indigo-600 dark:text-indigo-400 transition-colors duration-200 hover:text-indigo-700 dark:hover:text-indigo-300"
                            >
                                Crear cuenta
                            </TextLink>
                        </div>
                    </Form>
                </div>
            </div>
        </div>

        <!-- Sección derecha - Imagen -->
        <div class="relative hidden w-0 flex-1 lg:block">
            <div
                class="absolute inset-0 h-full w-full bg-cover bg-center bg-no-repeat"
                style="
                    background-image: url('');
                "
                :style="{ backgroundImage: `url(${page.props.auth_bg || 'https://lh3.googleusercontent.com/aida-public/AB6AXuAX5XnN3gbka49V_brfHg0VNidQ6wlDhG1AvtQAYz23Kj9isy6OLlgBqVOgy_EhJ1zInBv-JgPCbrxb8vHG6fr0gyY4A3tjCeRq6-bUUhjNZFpL1Aq3ah5kbOSHHky6Yk_a97tpzi3E4qbwB8tcHSjvRCeWDWLGC6JWL74lpgiUEC63aCsuodJP73NcbJ6zcpl0tEocEn3gJIVT_1nx5A0QecpiM1CP52J3Zt52KhvPtSyaVvi--bPEwSJk8f4PQwRySLzORZV_XiQ'})` }"
            >
                <div
                    class="to-slate-50/10 dark:to-slate-900/10 absolute inset-0 h-full w-full bg-linear-to-br from-indigo-600/30 via-violet-600/30 dark:from-indigo-600/50 dark:via-violet-600/50"
                ></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Asegurar que los estilos de los iconos de materiales se carguen */
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined');
</style>
