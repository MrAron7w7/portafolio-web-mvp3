<script setup lang="ts">
import { Lock, ArrowLeft, Mail } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    reason: string;
    portfolioTitle: string;
}>();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-6">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
                <!-- Icono -->
                <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                    <Lock class="w-8 h-8 text-red-500" />
                </div>

                <!-- Título -->
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    Acceso Restringido
                </h1>

                <!-- Nombre del portafolio -->
                <p class="text-gray-600 mb-4">
                    <span class="font-medium">{{ portfolioTitle }}</span>
                </p>

                <!-- Razón -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                    <p class="text-amber-800 text-sm">
                        {{ reason }}
                    </p>
                </div>

                <!-- Acciones -->
                <div class="space-y-3">
                    <Link
                        href="/login"
                        class="w-full inline-flex items-center justify-center gap-2 bg-[#005aeb] text-white font-medium py-3 px-4 rounded-lg hover:bg-[#0048c4] transition-colors"
                    >
                        <Mail class="w-4 h-4" />
                        Iniciar sesión
                    </Link>

                    <Link
                        href="/"
                        class="w-full inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-medium py-3 px-4 rounded-lg hover:bg-gray-200 transition-colors"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Volver al inicio
                    </Link>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-center text-gray-500 text-sm mt-6">
                ¿Crees que deberías tener acceso? Contacta al propietario del portafolio.
            </p>
        </div>
    </div>
</template>
