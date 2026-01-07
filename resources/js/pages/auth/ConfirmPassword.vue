<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import confirm from '@/routes/password/confirm';
import { Head, useForm } from '@inertiajs/vue3';
</script>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  setup() {
    const form = useForm({
      password: "",
    });

    const submit = () => {
      form.post(confirm.store.url(), {
        onFinish: () => form.reset(),
      });
    };

    return { form, submit };
  },
});
</script>

<template>
    <AuthLayout
        title="Confirm your password"
        description="This is a secure area of the application. Please confirm your password before continuing."
    >
        <Head title="Confirm password" />

        <form @submit.prevent="submit">
            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label htmlFor="password" class="dark:text-slate-300">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        v-model="form.password"
                        class="mt-1 block w-full dark:bg-slate-950 dark:border-slate-800"
                        required
                        autocomplete="current-password"
                        autofocus
                    />

                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center">
                    <Button
                        class="w-full bg-slate-900 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200"
                        :disabled="form.processing"
                        data-test="confirm-password-button"
                    >
                        <Spinner v-if="form.processing" />
                        Confirm Password
                    </Button>
                </div>
            </div>
        </form>
    </AuthLayout>
</template>
