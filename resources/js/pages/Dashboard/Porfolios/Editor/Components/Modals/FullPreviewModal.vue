<!-- ============================================
   Components/Modals/FullPreviewModal.vue
   ============================================ -->
   <script setup lang="ts">
    import { X } from 'lucide-vue-next';
 
 
    
    interface Props {
        isOpen: boolean;
        formData: any;
        currentTemplate: any;
        portfolio: any;
    }
    
    withDefaults(defineProps<Props>(), {});
    
    defineEmits<{
        close: [];
    }>();
    </script>
    
    <template>
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0">
            <div v-if="isOpen"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
                @click="$emit('close')">
                <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 scale-75"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95">
                    <div v-if="isOpen"
                        class="max-h-[90vh] w-full max-w-9xl transform rounded-2xl bg-white dark:bg-slate-900 shadow-2xl overflow-hidden"
                        @click.stop>
                        <!-- Header del modal -->
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50 p-6">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Vista Previa: {{ portfolio.template_type }}
                                </h3>
                                <p class="mt-1 text-gray-600 dark:text-slate-400">
                                    Vista previa completa de tu portafolio
                                </p>
                            </div>
                            <button @click="$emit('close')"
                                class="rounded-lg p-2 transition-colors duration-200 hover:bg-gray-200 dark:hover:bg-slate-800 text-gray-500 dark:text-slate-400">
                                <X class="h-5 w-5" />
                            </button>
                        </div>
    
                        <!-- Contenido del modal -->
                        <div class="max-h-[80vh] overflow-auto">
                            <component :is="currentTemplate" :data="{
                                personal: {
                                    name: `${formData.personal.firstName} ${formData.personal.lastName}`.trim(),
                                    title: formData.personal.title,
                                    email: formData.personal.email,
                                    phone: formData.personal.phone,
                                    photo: formData.personal.photo,
                                    location: `${formData.personal.city}, ${formData.personal.country}`
                                        .trim()
                                        .replace(/^,\s*|\s*,$/g, ''),
                                    website: formData.social.website || formData.personal.website,
                                    linkedin: formData.social.linkedin || formData.personal.linkedin,
                                    github: formData.social.github || formData.personal.github,
                                    summary: formData.personal.summary || formData.personal.summary,
                                    description: formData.personal.description,
                                },
                                experience: formData.experience.map((exp: any) => ({
                                    company: exp.company,
                                    position: exp.position,
                                    startDate: exp.startDate,
                                    endDate: exp.endDate,
                                    current: exp.current,
                                    description: exp.description,
                                })),
                                skills: {
                                    technical: formData.skills.technical.map((skill: any) => ({
                                        name: skill.name,
                                        level: skill.level,
                                    })),
                                    soft: formData.skills.soft.map((skill: any) => ({
                                        name: skill.name,
                                        level: skill.level,
                                    })),
                                },
                                projects: formData.projects,
                                education: formData.education,
                                languages: formData.languages,
                                certifications: formData.certifications,
                            }" />
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </template>
    
    <style scoped>
    /* Transiciones */
    </style>