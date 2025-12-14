<!-- ============================================
   AIResultsModal.vue - VERSIÓN FINAL SIN @apply
   ============================================ -->

   <script setup lang="ts">
    import { ref, computed } from 'vue';
    import { 
        Sparkles, 
        ClipboardList, 
        Briefcase, 
        GraduationCap, 
        Globe, 
        Wrench, 
        Zap, 
        Loader2,
        ExternalLink,
        Code2,
        User
    } from 'lucide-vue-next';
    
    // Types - Interfaz correcta del mapa de IA
    interface AIAnalysisResult {
        personal: Array<{ summary: string }>;
        experience: Array<{ 
            company: string; 
            position: string; 
            startDate: string; 
            endDate: string; 
            current: boolean; 
            description: string 
        }>;
        skills: {
            technical: Array<{ name: string; level: number }>;
            soft: Array<{ name: string; level: number }>;
        };
        projects: Array<{ 
            id: number; 
            name: string; 
            description: string; 
            image: string | null; 
            link: string | null; 
            technologies: string[] 
        }>;
        education: Array<{ 
            id: number; 
            degree: string; 
            institution: string; 
            startDate: string; 
            endDate: string; 
            description: string | null; 
            current: boolean 
        }>;
        languages: Array<{ 
            id: number; 
            name: string; 
            level: string 
        }>;
    }
    
    // Props
    const props = defineProps<{
        isOpen: boolean;
        isLoading?: boolean;
        analysisResult: AIAnalysisResult | null;
    }>();
    
    // Emits
    const emit = defineEmits<{
        (e: 'close'): void;
        (e: 'auto-complete'): void;
    }>();
    
    // Estado local
    const selectedTab = ref<'overview' | 'experience' | 'education' | 'projects' | 'languages' | 'skills'>('overview');
    const isAutoCompleting = ref(false);
    
    // ============================================
    // ✅ SAFE COMPUTED PROPERTIES
    // ============================================
    
    // Personal summary
    const personalSummary = computed(() => props.analysisResult?.personal?.[0]?.summary || '');
    
    // Arrays seguros con defaults
    const experienceArray = computed(() => props.analysisResult?.experience || []);
    const projectsArray = computed(() => props.analysisResult?.projects || []);
    const educationArray = computed(() => props.analysisResult?.education || []);
    const languagesArray = computed(() => props.analysisResult?.languages || []);
    const technicalSkills = computed(() => props.analysisResult?.skills?.technical || []);
    const softSkills = computed(() => props.analysisResult?.skills?.soft || []);
    
    // Conteos seguros
    const experienceCount = computed(() => experienceArray.value.length);
    const projectsCount = computed(() => projectsArray.value.length);
    const educationCount = computed(() => educationArray.value.length);
    const languageCount = computed(() => languagesArray.value.length);
    const totalSkills = computed(() => technicalSkills.value.length + softSkills.value.length);
    
    // Verificación de datos
    const hasData = computed(() => props.analysisResult !== null);
    
    // ============================================
    // FUNCIONES AUXILIARES
    // ============================================
    
    const getSkillLevelColor = (level: number) => {
        if (level >= 80) return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
        if (level >= 60) return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
        if (level >= 40) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300';
        return 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300';
    };
    
    const getSkillLevelLabel = (level: number) => {
        if (level >= 80) return 'Experto';
        if (level >= 60) return 'Avanzado';
        if (level >= 40) return 'Intermedio';
        return 'Básico';
    };
    
    const formatDate = (dateString: string) => {
        if (!dateString) return 'Presente';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'long' });
        } catch {
            return dateString;
        }
    };
    
    const getLanguageColor = (level: string) => {
        switch (level?.toLowerCase()) {
            case 'nativo':
                return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
            case 'fluido':
                return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
            case 'intermedio':
                return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300';
            case 'básico':
                return 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300';
            default:
                return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
        }
    };
    
    const getProgressBar = (level: number) => {
        if (level >= 80) return 'bg-green-500';
        if (level >= 60) return 'bg-blue-500';
        if (level >= 40) return 'bg-yellow-500';
        return 'bg-orange-500';
    };
    
    // Métodos
    const handleAutoComplete = async () => {
        isAutoCompleting.value = true;
        await new Promise(resolve => setTimeout(resolve, 500));
        emit('auto-complete');
    };
    
    const handleClose = () => {
        if (!isAutoCompleting.value) {
            emit('close');
        }
    };
    
    const openProjectLink = (link: string | null) => {
        if (link) {
            window.open(link, '_blank');
        }
    };
    </script>
    
    <template>
        <!-- Modal Container -->
        <Transition name="slide-up">
            <div 
                v-if="isOpen" 
                class="fixed inset-0 flex items-center justify-center z-50 px-4"
            >
                <div 
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col"
                    @click.stop
                >
                    <!-- Header -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 px-8 py-8 text-white flex-shrink-0">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white rounded-full -mr-20 -mt-20 opacity-10" />
                        
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-1">
                                <Sparkles class="w-8 h-8 text-yellow-200 animate-pulse" />
                                <h1 class="text-3xl font-bold">Análisis Completado</h1>
                            </div>
                            <p class="text-indigo-100 text-lg ml-11">Tu perfil ha sido analizado con IA</p>
                        </div>
                    </div>
    
                    <!-- Stats Bar -->
                    <div v-if="hasData" class="grid grid-cols-5 gap-3 px-8 py-6 bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                        <div class="text-center group hover:bg-indigo-50 dark:hover:bg-slate-600 rounded-lg p-2 transition-colors">
                            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ experienceCount }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 flex justify-center items-center gap-1">
                                <Briefcase class="w-3 h-3" /> Exp
                            </div>
                        </div>
                        <div class="text-center group hover:bg-purple-50 dark:hover:bg-slate-600 rounded-lg p-2 transition-colors">
                            <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ educationCount }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 flex justify-center items-center gap-1">
                                <GraduationCap class="w-3 h-3" /> Edu
                            </div>
                        </div>
                        <div class="text-center group hover:bg-pink-50 dark:hover:bg-slate-600 rounded-lg p-2 transition-colors">
                            <div class="text-3xl font-bold text-pink-600 dark:text-pink-400">{{ projectsCount }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 flex justify-center items-center gap-1">
                                <Code2 class="w-3 h-3" /> Proj
                            </div>
                        </div>
                        <div class="text-center group hover:bg-cyan-50 dark:hover:bg-slate-600 rounded-lg p-2 transition-colors">
                            <div class="text-3xl font-bold text-cyan-600 dark:text-cyan-400">{{ languageCount }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 flex justify-center items-center gap-1">
                                <Globe class="w-3 h-3" /> Lang
                            </div>
                        </div>
                        <div class="text-center group hover:bg-blue-50 dark:hover:bg-slate-600 rounded-lg p-2 transition-colors">
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ totalSkills }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 flex justify-center items-center gap-1">
                                <Wrench class="w-3 h-3" /> Skills
                            </div>
                        </div>
                    </div>
    
                    <!-- Tabs -->
                    <div class="flex gap-2 px-8 py-4 border-b border-gray-200 dark:border-gray-700 overflow-x-auto flex-shrink-0">
                        <button
                            v-for="tab in ['overview', 'experience', 'education', 'projects', 'languages', 'skills']"
                            :key="tab"
                            @click="selectedTab = tab as any"
                            :class="[
                                'px-4 py-2 rounded-lg font-medium whitespace-nowrap transition-all flex items-center gap-2',
                                selectedTab === tab
                                    ? 'bg-indigo-500 text-white shadow-md'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                            ]"
                        >
                            <ClipboardList v-if="tab === 'overview'" class="w-4 h-4" />
                            <Briefcase v-else-if="tab === 'experience'" class="w-4 h-4" />
                            <GraduationCap v-else-if="tab === 'education'" class="w-4 h-4" />
                            <Code2 v-else-if="tab === 'projects'" class="w-4 h-4" />
                            <Globe v-else-if="tab === 'languages'" class="w-4 h-4" />
                            <Wrench v-else-if="tab === 'skills'" class="w-4 h-4" />
                            
                            <span>
                                {{ 
                                    tab === 'overview' ? 'Resumen' : 
                                    tab === 'experience' ? 'Experiencia' : 
                                    tab === 'education' ? 'Educación' :
                                    tab === 'projects' ? 'Proyectos' :
                                    tab === 'languages' ? 'Idiomas' : 'Habilidades' 
                                }}
                            </span>
                        </button>
                    </div>
    
                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                        <!-- Loading State -->
                        <div v-if="isLoading" class="flex flex-col items-center justify-center py-12">
                            <div class="mb-4">
                                <Loader2 class="w-12 h-12 text-indigo-500 animate-spin" />
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 font-medium">Analizando con IA...</p>
                        </div>
    
                        <!-- TRANSICIÓN PRINCIPAL DE TABS -->
                        <Transition name="tab-slide" mode="out-in">
                            <!-- Overview Tab -->
                            <div v-if="selectedTab === 'overview' && hasData" key="overview" class="space-y-6">
                                <div v-if="personalSummary" class="p-6 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl border border-indigo-200 dark:border-indigo-800">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                        <User class="w-5 h-5 text-indigo-500" /> Resumen del Perfil
                                    </h3>
                                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                        {{ personalSummary }}
                                    </p>
                                </div>
    
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 flex items-center gap-4">
                                        <div class="p-3 bg-blue-100 dark:bg-blue-800 rounded-full flex-shrink-0">
                                            <Briefcase class="w-6 h-6 text-blue-600 dark:text-blue-300" />
                                        </div>
                                        <div>
                                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ experienceCount }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Experiencias</p>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 flex items-center gap-4">
                                        <div class="p-3 bg-green-100 dark:bg-green-800 rounded-full flex-shrink-0">
                                            <Code2 class="w-6 h-6 text-green-600 dark:text-green-300" />
                                        </div>
                                        <div>
                                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ projectsCount }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Proyectos</p>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800 flex items-center gap-4">
                                        <div class="p-3 bg-purple-100 dark:bg-purple-800 rounded-full flex-shrink-0">
                                            <GraduationCap class="w-6 h-6 text-purple-600 dark:text-purple-300" />
                                        </div>
                                        <div>
                                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ educationCount }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Educación</p>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-pink-50 dark:bg-pink-900/20 rounded-lg border border-pink-200 dark:border-pink-800 flex items-center gap-4">
                                        <div class="p-3 bg-pink-100 dark:bg-pink-800 rounded-full flex-shrink-0">
                                            <Wrench class="w-6 h-6 text-pink-600 dark:text-pink-300" />
                                        </div>
                                        <div>
                                            <p class="text-2xl font-bold text-pink-600 dark:text-pink-400">{{ totalSkills }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Habilidades</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <!-- Experience Tab -->
                            <div v-else-if="selectedTab === 'experience' && hasData" key="experience" class="space-y-4">
                                <div v-if="experienceArray.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    No se encontraron experiencias profesionales
                                </div>
                                
                                <TransitionGroup name="slide-item-left" tag="div" class="space-y-4">
                                    <div 
                                        v-for="(exp, idx) in experienceArray" 
                                        :key="exp.company + exp.position"
                                        class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition-shadow relative overflow-hidden"
                                    >
                                        <div class="absolute top-0 right-0 p-4 opacity-5">
                                            <Briefcase class="w-16 h-16" />
                                        </div>
                                        <div class="flex justify-between items-start mb-2 relative z-10">
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ exp.position }}</h4>
                                                <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">{{ exp.company }}</p>
                                            </div>
                                            <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full text-xs font-medium whitespace-nowrap">
                                                {{ exp.current ? 'Actual' : formatDate(exp.endDate) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 relative z-10">
                                            {{ formatDate(exp.startDate) }} - {{ exp.current ? 'Presente' : formatDate(exp.endDate) }}
                                        </p>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm relative z-10">{{ exp.description }}</p>
                                    </div>
                                </TransitionGroup>
                            </div>
    
                            <!-- Education Tab -->
                            <div v-else-if="selectedTab === 'education' && hasData" key="education" class="space-y-4">
                                <div v-if="educationArray.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    No se encontró información de educación
                                </div>
                                
                                <TransitionGroup name="slide-item-right" tag="div" class="space-y-4">
                                    <div 
                                        v-for="(edu) in educationArray" 
                                        :key="edu.id"
                                        class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition-shadow flex items-start gap-4"
                                    >
                                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex-shrink-0">
                                            <GraduationCap class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ edu.degree }}</h4>
                                            <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">{{ edu.institution }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                {{ formatDate(edu.startDate) }} - {{ edu.current ? 'Presente' : formatDate(edu.endDate) }}
                                            </p>
                                            <p v-if="edu.description" class="text-gray-600 dark:text-gray-400 text-sm mt-2">{{ edu.description }}</p>
                                        </div>
                                    </div>
                                </TransitionGroup>
                            </div>
    
                            <!-- Projects Tab -->
                            <div v-else-if="selectedTab === 'projects' && hasData" key="projects" class="space-y-4">
                                <div v-if="projectsArray.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    No se encontraron proyectos
                                </div>
                                
                                <TransitionGroup name="slide-item-left" tag="div" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div 
                                        v-for="(project) in projectsArray" 
                                        :key="project.id"
                                        class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition-all hover:border-pink-300 dark:hover:border-pink-700 relative overflow-hidden group"
                                    >
                                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                            <Code2 class="w-16 h-16" />
                                        </div>
                                        
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 relative z-10">{{ project.name }}</h4>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 relative z-10">{{ project.description }}</p>
                                        
                                        <div v-if="project.technologies.length > 0" class="mb-4 flex flex-wrap gap-2 relative z-10">
                                            <span 
                                                v-for="(tech, idx) in project.technologies" 
                                                :key="idx"
                                                class="px-2 py-1 bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 rounded text-xs font-medium"
                                            >
                                                {{ tech }}
                                            </span>
                                        </div>
                                        
                                        <a 
    :href="project.link"
    target="_blank"
    rel="noopener noreferrer"
    class="inline-flex items-center gap-2 text-pink-500 hover:text-pink-600 ..."
>
    <ExternalLink class="w-4 h-4 flex-shrink-0" />
    <span class="underline">{{ project.link }}</span>
</a>

                                    </div>
                                </TransitionGroup>
                            </div>
    
                            <!-- Languages Tab -->
                            <div v-else-if="selectedTab === 'languages' && hasData" key="languages" class="space-y-3">
                                <div v-if="languagesArray.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    No se encontraron idiomas
                                </div>
                                
                                <TransitionGroup name="slide-item-left" tag="div" class="space-y-3">
                                    <div 
                                        v-for="(lang) in languagesArray" 
                                        :key="lang.id"
                                        class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                    >
                                        <div class="flex items-center gap-4">
                                            <Globe class="w-8 h-8 text-cyan-500" />
                                            <div>
                                                <p class="font-semibold text-gray-900 dark:text-white">{{ lang.name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ lang.level }}</p>
                                            </div>
                                        </div>
                                        <span :class="['px-3 py-1 rounded-full text-xs font-medium', getLanguageColor(lang.level)]">
                                            {{ lang.level }}
                                        </span>
                                    </div>
                                </TransitionGroup>
                            </div>
    
                            <!-- Skills Tab -->

<div v-else-if="selectedTab === 'skills' && hasData" key="skills" class="space-y-4">
    <!-- Technical Skills -->
    <div v-if="technicalSkills.length > 0">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
            <Wrench class="w-5 h-5 text-blue-500" /> Habilidades Técnicas
        </h3>
        <TransitionGroup name="slide-item-left" tag="div" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div 
                v-for="(skill) in technicalSkills" 
                :key="'tech-' + skill.name"
                class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition-all hover:border-blue-300 dark:hover:border-blue-700 relative overflow-hidden group"
            >
                <div class="absolute top-0 right-0 p-3 opacity-5 group-hover:opacity-10 transition-opacity">
                    <Wrench class="w-12 h-12" />
                </div>
                
                <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-1 relative z-10">{{ skill.name }}</h4>
                
                <div class="flex items-center justify-between relative z-10">
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ getSkillLevelLabel(skill.level) }}</p>
                    <span :class="['px-2 py-0.5 rounded-full text-xs font-bold', getSkillLevelColor(skill.level)]">
                        {{ skill.level }}%
                    </span>
                </div>
            </div>
        </TransitionGroup>
    </div>

    <!-- Soft Skills -->
    <div v-if="softSkills.length > 0">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
            <User class="w-5 h-5 text-purple-500" /> Habilidades Blandas
        </h3>
        <TransitionGroup name="slide-item-left" tag="div" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div 
                v-for="(skill) in softSkills" 
                :key="'soft-' + skill.name"
                class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition-all hover:border-purple-300 dark:hover:border-purple-700 relative overflow-hidden group"
            >
                <div class="absolute top-0 right-0 p-3 opacity-5 group-hover:opacity-10 transition-opacity">
                    <User class="w-12 h-12" />
                </div>
                
                <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-1 relative z-10">{{ skill.name }}</h4>
                
                <div class="flex items-center justify-between relative z-10">
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ getSkillLevelLabel(skill.level) }}</p>
                    <span :class="['px-2 py-0.5 rounded-full text-xs font-bold', getSkillLevelColor(skill.level)]">
                        {{ skill.level }}%
                    </span>
                </div>
            </div>
        </TransitionGroup>
    </div>
</div>
                        </Transition>
                    </div>
    
                    <!-- Footer -->
                    <div class="border-t border-gray-200 dark:border-gray-700 px-8 py-6 flex-shrink-0 bg-gray-50 dark:bg-slate-700 flex gap-3">
                        <button
                            @click="handleClose"
                            :disabled="isAutoCompleting"
                            class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="handleAutoComplete"
                            :disabled="!hasData || isAutoCompleting"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg font-semibold hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2"
                        >
                            <Loader2 v-if="isAutoCompleting" class="w-5 h-5 animate-spin" />
                            <Zap v-else class="w-5 h-5" />
                            <span>{{ isAutoCompleting ? 'Completando...' : 'Autocompletar Formulario' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </template>
    
    <style scoped>
    .slide-up-enter-active,
    .slide-up-leave-active {
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    
    .slide-up-enter-from {
        transform: translateY(20px);
        opacity: 0;
    }
    
    .slide-up-leave-to {
        transform: translateY(20px);
        opacity: 0;
    }
    
    /* ============================================
       TRANSICIÓN PRINCIPAL DE TABS CON SLIDE
       ============================================ */
    
    .tab-slide-enter-active {
        transition: all 0.4s ease-out;
    }
    
    .tab-slide-enter-from {
        opacity: 0;
        transform: translateX(30px);
    }
    
    .tab-slide-leave-active {
        transition: all 0.3s ease-in;
    }
    
    .tab-slide-leave-to {
        opacity: 0;
        transform: translateX(-30px);
    }
    
    /* ============================================
       TRANSICIONES SLIDE PARA ITEMS DENTRO DE TABS
       ============================================ */
    
    .slide-item-left-enter-active {
        transition: all 0.5s ease-out;
    }
    
    .slide-item-left-enter-from {
        opacity: 0;
        transform: translateX(-20px);
    }
    
    .slide-item-left-leave-active {
        transition: all 0.3s ease-in;
    }
    
    .slide-item-left-leave-to {
        opacity: 0;
        transform: translateX(20px);
    }
    
    .slide-item-right-enter-active {
        transition: all 0.5s ease-out;
    }
    
    .slide-item-right-enter-from {
        opacity: 0;
        transform: translateX(20px);
    }
    
    .slide-item-right-leave-active {
        transition: all 0.3s ease-in;
    }
    
    .slide-item-right-leave-to {
        opacity: 0;
        transform: translateX(-20px);
    }
    
    /* Stagger automático para múltiples items */
    .slide-item-left-enter-active:nth-child(1) { transition-delay: 0.05s; }
    .slide-item-left-enter-active:nth-child(2) { transition-delay: 0.1s; }
    .slide-item-left-enter-active:nth-child(3) { transition-delay: 0.15s; }
    .slide-item-left-enter-active:nth-child(4) { transition-delay: 0.2s; }
    .slide-item-left-enter-active:nth-child(5) { transition-delay: 0.25s; }
    .slide-item-left-enter-active:nth-child(6) { transition-delay: 0.3s; }
    
    .slide-item-right-enter-active:nth-child(1) { transition-delay: 0.05s; }
    .slide-item-right-enter-active:nth-child(2) { transition-delay: 0.1s; }
    .slide-item-right-enter-active:nth-child(3) { transition-delay: 0.15s; }
    .slide-item-right-enter-active:nth-child(4) { transition-delay: 0.2s; }
    .slide-item-right-enter-active:nth-child(5) { transition-delay: 0.25s; }
    .slide-item-right-enter-active:nth-child(6) { transition-delay: 0.3s; }
    
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #475569;
    }
    
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
    </style>