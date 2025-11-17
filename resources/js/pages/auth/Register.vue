<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import google from '@/routes/google';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Register" />

    <div class="bg-background-light dark:bg-background-dark flex min-h-screen">
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
                        <svg
                            class="h-8 w-auto text-primary"
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
                            class="text-text-primary-light dark:text-text-primary-dark text-xl font-bold"
                            >Portafolio IA</span
                        >
                    </div>

                    <div class="flex flex-wrap justify-between gap-3">
                        <div class="flex w-full flex-col gap-3">
                            <p
                                class="text-text-primary-light dark:text-text-primary-dark text-3xl leading-tight font-black tracking-[-0.033em]"
                            >
                                Crea tu cuenta
                            </p>
                            <p
                                class="text-text-secondary-light dark:text-text-secondary-dark text-base leading-normal font-normal"
                            >
                                Regístrate para crear tu portafolio profesional
                                con IA.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Formulario -->
                <div class="mt-10">
                    <Form
                        v-bind="store.form()"
                        :reset-on-success="[
                            'password',
                            'password_confirmation',
                        ]"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <!-- Name -->
                        <div class="space-y-2">
                            <Label
                                for="name"
                                class="text-text-primary-light dark:text-text-primary-dark text-sm leading-normal font-medium"
                            >
                                Nombre completo
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="name"
                                name="name"
                                placeholder="Tu nombre completo"
                                class="text-text-primary-light dark:text-text-primary-dark border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark placeholder:text-text-secondary-light dark:placeholder:text-text-secondary-dark flex h-12 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg border px-4 text-base leading-normal font-normal focus:ring-2 focus:ring-primary/50 focus:outline-0"
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <Label
                                for="email"
                                class="text-text-primary-light dark:text-text-primary-dark text-sm leading-normal font-medium"
                            >
                                Email
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                required
                                :tabindex="2"
                                autocomplete="email"
                                name="email"
                                placeholder="tu@email.com"
                                class="text-text-primary-light dark:text-text-primary-dark border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark placeholder:text-text-secondary-light dark:placeholder:text-text-secondary-dark flex h-12 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg border px-4 text-base leading-normal font-normal focus:ring-2 focus:ring-primary/50 focus:outline-0"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <Label
                                for="password"
                                class="text-text-primary-light dark:text-text-primary-dark text-sm leading-normal font-medium"
                            >
                                Contraseña
                            </Label>
                            <div
                                class="flex w-full flex-1 items-stretch rounded-lg"
                            >
                                <Input
                                    id="password"
                                    type="password"
                                    required
                                    :tabindex="3"
                                    autocomplete="new-password"
                                    name="password"
                                    placeholder="Crea una contraseña segura"
                                    class="text-text-primary-light dark:text-text-primary-dark border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark placeholder:text-text-secondary-light dark:placeholder:text-text-secondary-dark flex h-12 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg rounded-r-none border border-r-0 px-4 pr-2 text-base leading-normal font-normal focus:ring-2 focus:ring-primary/50 focus:outline-0"
                                />
                                <button
                                    type="button"
                                    aria-label="Toggle password visibility"
                                    class="text-text-secondary-light dark:text-text-secondary-dark border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark flex items-center justify-center rounded-r-lg border border-l-0 pr-4 focus:ring-2 focus:ring-primary/50 focus:outline-none"
                                >
                                    <span
                                        class="material-symbols-outlined"
                                        data-icon="Eye"
                                        data-size="24px"
                                        data-weight="regular"
                                    >
                                        visibility_off
                                    </span>
                                </button>
                            </div>
                            <InputError :message="errors.password" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-2">
                            <Label
                                for="password_confirmation"
                                class="text-text-primary-light dark:text-text-primary-dark text-sm leading-normal font-medium"
                            >
                                Confirmar contraseña
                            </Label>
                            <div
                                class="flex w-full flex-1 items-stretch rounded-lg"
                            >
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    required
                                    :tabindex="4"
                                    autocomplete="new-password"
                                    name="password_confirmation"
                                    placeholder="Repite tu contraseña"
                                    class="text-text-primary-light dark:text-text-primary-dark border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark placeholder:text-text-secondary-light dark:placeholder:text-text-secondary-dark flex h-12 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg rounded-r-none border border-r-0 px-4 pr-2 text-base leading-normal font-normal focus:ring-2 focus:ring-primary/50 focus:outline-0"
                                />
                                <button
                                    type="button"
                                    aria-label="Toggle password visibility confirmation"
                                    class="text-text-secondary-light dark:text-text-secondary-dark border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark flex items-center justify-center rounded-r-lg border border-l-0 pr-4 focus:ring-2 focus:ring-primary/50 focus:outline-none"
                                >
                                    <span
                                        class="material-symbols-outlined"
                                        data-icon="Eye"
                                        data-size="24px"
                                        data-weight="regular"
                                    >
                                        visibility_off
                                    </span>
                                </button>
                            </div>
                            <InputError
                                :message="errors.password_confirmation"
                            />
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <Button
                                type="submit"
                                class="focus:ring-offset-background-light dark:focus:ring-offset-background-dark flex h-12 w-full min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg px-5 text-base leading-normal font-bold tracking-[0.015em] text-white transition-colors duration-200 focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none"
                                tabindex="5"
                                :disabled="processing"
                                data-test="register-user-button"
                            >
                                <Spinner v-if="processing" class="mr-2" />
                                <span class="truncate">
                                    {{
                                        processing
                                            ? 'Creando cuenta...'
                                            : 'Crear cuenta'
                                    }}
                                </span>
                            </Button>
                        </div>

                        <!-- Social Register -->
                        <div class="mt-10">
                            <div class="relative">
                                <div
                                    aria-hidden="true"
                                    class="absolute inset-0 flex items-center"
                                >
                                    <div
                                        class="border-border-light dark:border-border-dark w-full border-t"
                                    ></div>
                                </div>
                                <div
                                    class="relative flex justify-center text-sm leading-6 font-medium"
                                >
                                    <span
                                        class="bg-background-light dark:bg-background-dark text-text-secondary-light dark:text-text-secondary-dark px-6"
                                    >
                                        O regístrate con
                                    </span>
                                </div>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <a
                                    class="border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-primary-light dark:text-text-primary-dark focus:ring-offset-background-light dark:focus:ring-offset-background-dark flex w-full items-center justify-center gap-3 rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-colors duration-200 hover:bg-gray-50 focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none dark:hover:bg-white/5"
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
                                    class="border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-primary-light dark:text-text-primary-dark focus:ring-offset-background-light dark:focus:ring-offset-background-dark flex w-full items-center justify-center gap-3 rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-colors duration-200 hover:bg-gray-50 focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none dark:hover:bg-white/5"
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

                        <!-- Login Link -->
                        <div
                            class="text-text-secondary-light dark:text-text-secondary-dark mt-10 text-center text-sm"
                        >
                            ¿Ya tienes una cuenta?
                            <TextLink
                                :href="login()"
                                class="leading-6 font-semibold text-primary transition-colors duration-200 hover:text-accent dark:hover:text-accent"
                                :tabindex="6"
                            >
                                Iniciar sesión
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
                data-alt="Abstract gradient illustration with blue and purple geometric shapes on a light gray background, suggesting technology and creativity."
                style="
                    background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAX5XnN3gbka49V_brfHg0VNidQ6wlDhG1AvtQAYz23Kj9isy6OLlgBqVOgy_EhJ1zInBv-JgPCbrxb8vHG6fr0gyY4A3tjCeRq6-bUUhjNZFpL1Aq3ah5kbOSHHky6Yk_a97tpzi3E4qbwB8tcHSjvRCeWDWLGC6JWL74lpgiUEC63aCsuodJP73NcbJ6zcpl0tEocEn3gJIVT_1nx5A0QecpiM1CP52J3Zt52KhvPtSyaVvi--bPEwSJk8f4PQwRySLzORZV_XiQ');
                "
            >
                <div
                    class="to-background-light/10 dark:to-background-dark/10 absolute inset-0 h-full w-full bg-gradient-to-br from-primary/30 via-accent/30 dark:from-primary/50 dark:via-accent/50"
                ></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Asegurar que los estilos de los iconos de materiales se carguen */
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined');

/* Variables CSS para los colores personalizados */
:root {
    --primary: #005aeb;
    --background-light: #f5f6f8;
    --background-dark: #0f1723;
    --accent: #7b2ff7;
    --text-primary-light: #111827;
    --text-primary-dark: #f5f6f8;
    --text-secondary-light: #6b7280;
    --text-secondary-dark: #94a3b8;
    --border-light: #e5e7eb;
    --border-dark: #334155;
}
</style>
