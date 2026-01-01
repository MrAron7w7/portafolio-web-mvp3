<!-- ============================================
   Components/Layout/EditorPreview.vue
   ============================================ -->
   <script setup lang="ts">
    import PreviewContainer from '@/pages/Dashboard/Porfolios/Components/PreviewContainer.vue';
    
    interface Props {
        formData: any;
        currentTemplate: any;
    }
    
    withDefaults(defineProps<Props>(), {});
    </script>
    
    <template>
        <div class="lg:col-span-4">
            <div class="sticky top-8 h-[calc(100vh-8rem)] overflow-hidden rounded-2xl border border-gray-200/60 dark:border-slate-800 bg-white dark:bg-slate-950! shadow-xs transition-colors duration-300">
                <PreviewContainer>
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
                    }" class="w-full" />
                </PreviewContainer>
            </div>
        </div>
    </template>
    
    <style scoped>
    /* Estilos heredados */
    </style>
    
    