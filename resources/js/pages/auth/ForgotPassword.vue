<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import password from '@/routes/password';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(password.email.url(), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <AuthLayout
        title="Forgot password"
        description="Enter your email to receive a password reset link"
    >
        <Head title="Forgot password" />

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600 dark:text-green-400"
        >
            {{ status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email" class="dark:text-slate-300">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        v-model="form.email"
                        autocomplete="off"
                        autofocus
                        placeholder="email@example.com"
                        class="dark:bg-slate-950 dark:border-slate-800 dark:placeholder:text-slate-600"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button
                        class="w-full bg-slate-900 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200"
                        :disabled="form.processing"
                        data-test="email-password-reset-link-button"
                    >
                        <Spinner v-if="form.processing" />
                        Email password reset link
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-muted-foreground dark:text-slate-400">
                <span>Or, return to</span>
                <TextLink :href="login()" class="dark:text-white dark:hover:text-indigo-400">log in</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
