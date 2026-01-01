<script setup lang="ts">
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    HelpCircle, 
    Book, 
    MessageCircle, 
    Mail, 
    ExternalLink, 
    FileText, 
    PlayCircle
} from 'lucide-vue-next';

// FAQ Data
const faqs = [
    {
        question: "¿Cómo creo mi primer portafolio?",
        answer: "Ve a la sección 'Mis proyectos' y haz clic en 'Nuevo Proyecto'. Sigue los pasos del asistente para elegir una plantilla y personalizar tu contenido."
    },
    {
        question: "¿Puedo usar mi propio dominio?",
        answer: "Sí, puedes configurar un dominio personalizado desde la sección de 'Configuración' -> 'Dominio' una vez que hayas actualizado tu plan."
    },
    {
        question: "¿Cómo exporto mi portafolio a PDF?",
        answer: "Dentro del editor de tu portafolio, busca el botón de 'Exportar' en la barra superior y selecciona la opción PDF. Esto generará un archivo descargable de alta calidad."
    },
    {
        question: "¿Es posible cambiar la plantilla después de crear el portafolio?",
        answer: "Sí, puedes cambiar la plantilla en cualquier momento desde el editor. Ten en cuenta que algunos contenidos podrían necesitar ajustes si el diseño cambia drásticamente."
    },
    {
        question: "¿Cómo comparto mi portafolio con otros?",
        answer: "Una vez publicado, obtendrás un enlace único (URL) que puedes compartir en redes sociales, LinkedIn o tu currículum. También puedes generar un código QR para un acceso rápido."
    },
    {
        question: "¿Mi portafolio se ve bien en dispositivos móviles?",
        answer: "Absolutamente. Todas nuestras plantillas son 100% responsivas y están optimizadas para lucir increíbles en celulares, tablets y computadoras de escritorio."
    },
    {
        question: "¿Puedo colaborar con otros usuarios en un mismo portafolio?",
        answer: "Actualmente, la edición es personal. Sin embargo, estamos trabajando en funciones de equipo para futuras actualizaciones."
    },
    {
        question: "¿Cómo gestiono mi suscripción?",
        answer: "Puedes ver y administrar tu plan actual, métodos de pago y facturación desde 'Configuración' -> 'Suscripción'. Puedes cancelar o mejorar tu plan en cualquier momento."
    },
    {
        question: "¿Qué hago si olvido mi contraseña?",
        answer: "En la pantalla de inicio de sesión, haz clic en '¿Olvidaste tu contraseña?'. Te enviaremos un enlace a tu correo electrónico para restablecerla de forma segura."
    },
    {
        question: "¿Ofrecen reembolsos?",
        answer: "Sí, ofrecemos una garantía de satisfacción de 14 días. Si no estás contento con el servicio, contáctanos y procesaremos tu reembolso sin preguntas."
    }
];

const resources = [
    {
        title: "Foro de Comunidad",
        description: "Pregunta y comparte con otros creadores.",
        icon: MessageCircle,
        link: "/dashboard/comunidad",
        color: "bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400"
    }
];
</script>

<template>
    <Head title="Ayuda y Soporte" />

    <DashboardLayout>
        <div class="p-6 max-w-5xl mx-auto space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                            <HelpCircle class="w-8 h-8" />
                        </div>
                        Ayuda y Soporte
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 max-w-2xl text-lg">
                        Encuentra respuestas, guías y recursos para sacar el máximo provecho a tu portafolio.
                    </p>
                </div>
                
                 <a href="mailto:soporte@portafolioai.com" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold rounded-xl hover:opacity-90 transition shadow-lg">
                    <Mail class="w-5 h-5" />
                    <span>Contactar Soporte</span>
                </a>
            </div>

            <!-- Main Content Area -->
            <div class="space-y-12">
                
                <!-- Frequently Asked Questions -->
                <section class="max-w-4xl mx-auto bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-100 dark:border-slate-800 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                        <Book class="w-5 h-5 text-indigo-500" />
                        Preguntas Frecuentes
                    </h2>
                    
                    <div class="space-y-4">
                        <div v-for="(faq, index) in faqs" :key="index" class="group">
                            <details class="group [&_summary::-webkit-details-marker]:hidden">
                                <summary class="flex cursor-pointer items-center justify-between gap-1.5 rounded-lg bg-slate-50 dark:bg-slate-800 p-4 text-slate-900 dark:text-white transition hover:bg-slate-100 dark:hover:bg-slate-700">
                                    <h3 class="font-medium text-lg">
                                        {{ faq.question }}
                                    </h3>
                                    <div class="white-space-nowrap">
                                        <svg
                                            class="h-5 w-5 shrink-0 transition duration-300 group-open:-rotate-180"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </summary>
                                <div class="mt-4 px-4 leading-relaxed text-slate-700 dark:text-slate-300">
                                    {{ faq.answer }}
                                </div>
                            </details>
                        </div>
                    </div>
                </section>

                <!-- Help Actions Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
                    
                    <!-- Community Banner -->
                    <a 
                        href="/dashboard/comunidad"
                        class="relative overflow-hidden rounded-3xl bg-slate-900 dark:bg-slate-800 p-8 text-white shadow-xl group transition-transform hover:scale-[1.01]"
                    >
                        <div class="relative z-10">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-indigo-400 mb-6 group-hover:scale-110 transition-transform">
                                <MessageCircle class="w-6 h-6" />
                            </div>
                            <h3 class="text-2xl font-bold mb-2">Foro de Comunidad</h3>
                            <p class="text-slate-400 mb-6">Únete a la conversación. Pregunta, comparte ideas y conecta con otros creadores.</p>
                            
                            <div class="inline-flex items-center gap-2 font-bold text-indigo-400 group-hover:text-indigo-300 transition-colors">
                                Ir a la comunidad <ExternalLink class="w-4 h-4" />
                            </div>
                        </div>
                        
                        <!-- Decoration -->
                        <div class="absolute -top-12 -right-12 h-40 w-40 rounded-full bg-indigo-500/10 blur-3xl group-hover:bg-indigo-500/20 transition-all"></div>
                    </a>

                    <!-- Contact Banner -->
                    <a 
                        href="mailto:soporte@portafolioai.com"
                        class="relative overflow-hidden rounded-3xl bg-linear-to-br from-indigo-600 to-violet-700 p-8 text-white shadow-xl group transition-transform hover:scale-[1.01]"
                    >
                        <div class="relative z-10">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                                <Mail class="w-6 h-6" />
                            </div>
                            <h3 class="text-2xl font-bold mb-2">Soporte Técnico</h3>
                            <p class="text-indigo-100 mb-6">¿No encuentras lo que buscas? Nuestro equipo está listo para ayudarte con cualquier problema.</p>
                            
                            <div class="inline-flex items-center gap-2 font-bold text-white group-hover:opacity-90 transition-opacity">
                                Contactar soporte
                            </div>
                        </div>
                        
                        <!-- Decoration -->
                        <div class="absolute -bottom-12 -left-12 h-40 w-40 rounded-full bg-white/10 blur-3xl group-hover:bg-white/20 transition-all"></div>
                    </a>

                </div>

            </div>

        </div>
    </DashboardLayout>
</template>
