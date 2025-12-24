    <script setup lang="ts">
    import {
        MoreVertical,
        Eye,
        Save,
        Check,
        Loader2,
        Globe,
        Lock,
        X,
        Edit3,
        ChevronDown,
        Link2,
        Mail,
        UserPlus,
        Trash2,
        Copy,
    } from 'lucide-vue-next';
    import { router } from '@inertiajs/vue3';
    import { ref, computed, watch, onMounted } from 'vue';
    
    interface Props {
        portfolio: any;
        progress: number;
        hasUnsavedChanges: boolean;
        isSaving: boolean;
        isSaved: boolean;
        isPortfolioPublic: boolean;
        formData: any;
        isPublicEdit?: boolean;
        returnUrl?: string;
    }
    
    const props = withDefaults(defineProps<Props>(), {
        isPublicEdit: false,
        returnUrl: ''
    }); 
    
    const emits = defineEmits<{
        save: [];
        openPreview: [];
        openPublicModal: [];
        finish: [];
    }>();

    // ========================================
    // ESTADO DEL SISTEMA DE COMPARTIR
    // ========================================
    const showShareMenu = ref(false);
    const isUpdating = ref(false);
    
    // Sincronización local con props para evitar lag
    const accessMode = ref(props.portfolio.access_mode || 'owner_only');
    const linkPermission = ref(props.portfolio.link_permission || 'view');
    const linkAudienceType = ref(props.portfolio.link_audience_type || 'any_with_link');
    const shareToken = ref(props.portfolio.share_token);
    const authorizedEmails = ref<any[]>(props.portfolio.authorized_emails || []);
    
    const newEmail = ref('');
    const emailError = ref('');
    const copySuccess = ref(false);

    // Watcher para sincronizar si cambian las props externamente
    watch(() => props.portfolio, (newVal) => {
        if (newVal) {
            accessMode.value = newVal.access_mode || 'owner_only';
            linkPermission.value = newVal.link_permission || 'view';
            linkAudienceType.value = newVal.link_audience_type || 'any_with_link';
            shareToken.value = newVal.share_token;
            authorizedEmails.value = newVal.authorized_emails || [];
        }
    }, { deep: true });

    const isPrivate = computed(() => accessMode.value === 'owner_only');
    const isLinkSharing = computed(() => accessMode.value === 'link');
    const hasActiveLink = computed(() => !!shareToken.value);
    
    // URL pública usando el slug del portafolio (más limpia y profesional)
    const publicUrl = computed(() => {
        if (!props.portfolio?.slug) return '';
        return `${window.location.origin}/p/${props.portfolio.slug}`;
    });

    /**
     * Cerrar el menú de compartir
     */
    const closeShareMenu = () => {
        showShareMenu.value = false;
        emailError.value = '';
        newEmail.value = '';
    };

    /**
     * Cambiar a modo privado (Solo tú tienes acceso)
     */
    const setPrivate = () => {
        if (isPrivate.value && !hasActiveLink.value) return;
        
        isUpdating.value = true;
        
        router.post(`/dashboard/portfolio/${props.portfolio.id}/share/disable`, {}, {
            preserveScroll: true,
            onSuccess: () => console.log('✅ Cambiado a privado'),
            onFinish: () => isUpdating.value = false,
        });
    };

    /**
     * Cambiar a modo enlace compartido
     */
    const setLinkSharing = () => {
        if (isLinkSharing.value && hasActiveLink.value) return;
        
        isUpdating.value = true;
        
        router.post(`/dashboard/portfolio/${props.portfolio.id}/share/generate`, {}, {
            preserveScroll: true,
            onSuccess: () => console.log('✅ Enlace generado'),
            onFinish: () => isUpdating.value = false,
        });
    };

    /**
     * Actualiza el permiso del enlace
     */
    const updatePermission = (permission: 'view' | 'view_edit') => {
        if (props.portfolio.link_permission === permission) return;
        
        isUpdating.value = true;
        
        router.patch(`/dashboard/portfolio/${props.portfolio.id}/share/permission`, { permission }, {
            preserveScroll: true,
            onSuccess: () => console.log('✅ Permiso actualizado'),
            onFinish: () => isUpdating.value = false,
        });
    };

    /**
     * Actualiza el tipo de audiencia
     */
    const updateAudience = (audience: 'any_with_link' | 'emails_only') => {
        if (props.portfolio.link_audience_type === audience) return;
        
        isUpdating.value = true;
        
        router.patch(`/dashboard/portfolio/${props.portfolio.id}/share/audience`, { audience_type: audience }, {
            preserveScroll: true,
            onSuccess: () => console.log('✅ Audiencia actualizada'),
            onFinish: () => isUpdating.value = false,
        });
    };

    /**
     * Agregar correo
     */
    const addEmail = () => {
        if (!newEmail.value.trim()) return;
        
        isUpdating.value = true;
        // Limpiamos error previo
        emailError.value = '';

        router.post(`/dashboard/portfolio/${props.portfolio.id}/share/emails`, { email: newEmail.value }, {
            preserveScroll: true,
            onSuccess: () => {
                newEmail.value = '';
                console.log('✅ Correo agregado');
            },
            onError: (errors) => {
                emailError.value = errors.email || 'Error al agregar correo';
            },
            onFinish: () => isUpdating.value = false,
        });
    };

    /**
     * Eliminar correo
     */
    const removeEmail = (email: string) => {
        isUpdating.value = true;
        
        router.delete(`/dashboard/portfolio/${props.portfolio.id}/share/emails/${encodeURIComponent(email)}`, {
            preserveScroll: true,
            onSuccess: () => console.log('✅ Correo eliminado'),
            onFinish: () => isUpdating.value = false,
        });
    };

    const copyToClipboard = () => {
        navigator.clipboard.writeText(publicUrl.value);
        copySuccess.value = true;
        setTimeout(() => copySuccess.value = false, 2000);
    };

    const handleViewPublic = () => {
        router.visit(`/dashboard/portfolio/${props.portfolio.id}`);
    };
    </script>
    
    <template>
        <header class="border-b border-gray-200/60 bg-white sticky top-0 z-30">
            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3 shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#005aeb]">
                        <span class="text-sm font-bold text-white">P</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">PortafolioAI</span>
                </div>
    
                    <!-- Centro: Progreso -->
                    <div class="hidden items-center space-x-4 md:flex">
                        <div class="flex items-center space-x-2">
                            <div :class="[
                                'h-2.5 w-2.5 rounded-full transition-colors duration-300',
                                hasUnsavedChanges ? 'bg-amber-500 animate-pulse' : 'bg-green-500'
                            ]"></div>
                            <span class="text-xs font-medium text-gray-600">
                                {{ hasUnsavedChanges ? 'Sin guardar' : 'Guardado' }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-[#005aeb]">{{ progress }}%</span>
                            completado
                        </div>
                        <div class="h-2 w-32 rounded-full bg-gray-200">
                            <div class="h-2 rounded-full bg-[#005aeb] transition-all duration-500"
                                :style="{ width: progress + '%' }"></div>
                        </div>
                    </div>
    
                    <!-- Título del Portafolio (Centro-Derecha) -->
                    <div class="hidden flex-1 max-w-[280px] px-6 lg:block">
                        <div class="relative group">
                            <input
                                type="text"
                                v-model="formData.config.title"
                                class="w-full bg-gray-50/50 border border-gray-200 rounded-lg px-4 py-2 text-sm font-semibold !text-gray-700 transition-all duration-200 focus:bg-white focus:border-[#005aeb] focus:ring-4 focus:ring-blue-50 focus:outline-none placeholder:text-gray-400 group-hover:border-gray-300"
                                placeholder="Nombre de tu proyecto..."
                            />
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <Edit3 class="w-3.5 h-3.5 text-gray-400" />
                            </div>
                        </div>
                    </div>
    
                    <!-- Derecha: Acciones -->
                    <div class="flex items-center space-x-3">
                        
                        <!-- Dropdown de Compartir -->
                        <div class="relative">
                            <button
                                @click="showShareMenu = !showShareMenu"
                                :class="[
                                    'flex items-center space-x-2 rounded-lg px-4 py-2 font-medium transition-all duration-200 border',
                                    isPrivate 
                                        ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                        : 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100'
                                ]">
                                <Loader2 v-if="isUpdating" class="h-4 w-4 animate-spin" />
                                <Lock v-else-if="isPrivate" class="h-4 w-4" />
                                <Globe v-else class="h-4 w-4" />
                                <span class="text-sm hidden sm:inline">
                                    {{ isPrivate ? 'Solo tú' : 'Con enlace' }}
                                </span>
                                <ChevronDown class="h-4 w-4 opacity-50" />
                            </button>

                            <!-- Menú Dropdown -->
                            <Transition
                                enter-active-class="transition duration-200 ease-out"
                                enter-from-class="translate-y-1 opacity-0"
                                enter-to-class="translate-y-0 opacity-100"
                                leave-active-class="transition duration-150 ease-in"
                                leave-from-class="translate-y-0 opacity-100"
                                leave-to-class="translate-y-1 opacity-0">
                                <div v-if="showShareMenu" 
                                    @click.stop
                                    class="absolute right-0 mt-3 w-[380px] max-w-[90vw] rounded-2xl border border-gray-100 bg-white shadow-xl shadow-gray-200/50 z-50 overflow-hidden ring-1 ring-black/5">
                                    
                                    <!-- Header Minimalista -->
                                    <div class="px-5 py-4 flex items-center justify-between border-b border-gray-50">
                                        <div>
                                            <h3 class="font-bold text-gray-900 text-sm">Compartir portafolio</h3>
                                            <p class="text-[11px] text-gray-500 mt-0.5">Configura la privacidad y el acceso</p>
                                        </div>
                                        <button @click="showShareMenu = false" class="rounded-full p-1.5 bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                                            <X class="h-4 w-4" />
                                        </button>
                                    </div>

                                    <div class="p-5 space-y-5">
                                        <!-- Opciones de Modo (Tarjetas) - Solo para dueño -->
                                        <div v-if="!isPublicEdit" class="grid gap-3">
                                            <!-- Modo Privado -->
                                            <button 
                                                type="button"
                                                @click="setPrivate"
                                                class="group relative flex items-start w-full text-left gap-3 p-3.5 rounded-xl border transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                                :class="isPrivate ? 'border-blue-200 bg-blue-50/30' : 'border-gray-100 bg-white hover:border-gray-200 hover:bg-gray-50/50'">
                                                
                                                <div class="mt-0.5 p-2 rounded-lg transition-colors"
                                                    :class="isPrivate ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400 group-hover:bg-white group-hover:text-gray-500'">
                                                    <Lock class="w-4 h-4" />
                                                </div>
                                                
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-sm font-semibold text-gray-900">Solo tú</span>
                                                        <div v-if="isPrivate" class="h-2 w-2 rounded-full bg-blue-500"></div>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-0.5">Nadie más puede ver este portafolio.</p>
                                                </div>
                                            </button>

                                            <!-- Modo Enlace -->
                                            <button 
                                                type="button"
                                                @click="setLinkSharing"
                                                class="group relative flex items-start w-full text-left gap-3 p-3.5 rounded-xl border transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                                :class="isLinkSharing ? 'border-blue-200 bg-blue-50/30' : 'border-gray-100 bg-white hover:border-gray-200 hover:bg-gray-50/50'">
                                                
                                                <div class="mt-0.5 p-2 rounded-lg transition-colors"
                                                    :class="isLinkSharing ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400 group-hover:bg-white group-hover:text-gray-500'">
                                                    <Globe class="w-4 h-4" />
                                                </div>
                                                
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-sm font-semibold text-gray-900">Público</span>
                                                        <div v-if="isLinkSharing" class="h-2 w-2 rounded-full bg-blue-500"></div>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-0.5">Cualquier persona con el enlace puede acceder.</p>
                                                </div>
                                            </button>
                                        </div>

                                        <!-- Configuración del Enlace -->
                                        <Transition
                                            enter-active-class="transition duration-300 ease-out"
                                            enter-from-class="-translate-y-2 opacity-0"
                                            enter-to-class="translate-y-0 opacity-100"
                                            leave-active-class="transition duration-150 ease-in"
                                            leave-from-class="translate-y-0 opacity-100"
                                            leave-to-class="-translate-y-2 opacity-0">
                                            
                                            <div v-if="isLinkSharing" class="space-y-4 pt-4 border-t border-gray-50">
                                                <!-- Configuración Grid -->
                                                <div v-if="!isPublicEdit" class="grid grid-cols-2 gap-3">
                                                    <div class="space-y-1.5">
                                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1">Permiso</label>
                                                        <div class="relative">
                                                            <select 
                                                                v-model="linkPermission"
                                                                @change="updatePermission(($event.target as HTMLSelectElement).value as 'view' | 'view_edit')"
                                                                class="appearance-none w-full text-xs font-medium bg-gray-50 border border-transparent rounded-lg py-2.5 pl-3 pr-8 hover:bg-gray-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all cursor-pointer">
                                                                <option value="view">Solo lectura</option>
                                                                <option value="view_edit">Lectura y Edición</option>
                                                            </select>
                                                            <ChevronDown class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" />
                                                        </div>
                                                    </div>

                                                    <div class="space-y-1.5">
                                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1">Audiencia</label>
                                                        <div class="relative">
                                                            <select 
                                                                v-model="linkAudienceType"
                                                                @change="updateAudience(($event.target as HTMLSelectElement).value as 'any_with_link' | 'emails_only')"
                                                                class="appearance-none w-full text-xs font-medium bg-gray-50 border border-transparent rounded-lg py-2.5 pl-3 pr-8 hover:bg-gray-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all cursor-pointer">
                                                                <option value="any_with_link">Todo el mundo</option>
                                                                <option value="emails_only">Restringido</option>
                                                            </select>
                                                            <ChevronDown class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Link Box Minimalista -->
                                                <div class="relative">
                                                    <div class="flex items-center gap-2 p-1.5 pl-3 rounded-lg border border-gray-200 bg-white hover:border-blue-400 transition-colors group">
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-[10px] text-gray-400 font-medium mb-0.5">Enlace para compartir</p>
                                                            <p class="text-xs text-gray-700 font-mono truncate select-all">{{ publicUrl }}</p>
                                                        </div>
                                                        <button 
                                                            @click="copyToClipboard"
                                                            class="p-2 rounded-md hover:bg-gray-50 text-gray-400 hover:text-blue-600 transition-all relative">
                                                            <Check v-if="copySuccess" class="w-4 h-4 text-green-500" />
                                                            <Copy v-else class="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                    <p v-if="copySuccess" class="absolute -bottom-5 right-0 text-[10px] text-green-600 font-medium animate-fade-in-up">
                                                        ¡Copiado!
                                                    </p>
                                                </div>

                                                <!-- Gestión de Correos (Solo restringido) -->
                                                <div v-if="linkAudienceType === 'emails_only' && !isPublicEdit" class="mt-4 p-4 rounded-xl bg-gradient-to-b from-amber-50/50 to-orange-50/30 border border-amber-100/80">
                                                    <!-- Header de la sección -->
                                                    <div class="flex items-start gap-3 mb-4">
                                                        <div class="p-2 rounded-lg bg-amber-100 text-amber-600">
                                                            <Mail class="w-4 h-4" />
                                                        </div>
                                                        <div class="flex-1">
                                                            <h4 class="text-sm font-semibold text-gray-900">Acceso restringido</h4>
                                                            <p class="text-[11px] text-gray-500 mt-0.5 leading-relaxed">
                                                                Solo las personas con estos correos podrán ver tu portafolio. Deben iniciar sesión para acceder.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <!-- Input para agregar correo -->
                                                    <div class="flex gap-2 mb-3">
                                                        <input 
                                                            type="email" 
                                                            v-model="newEmail"
                                                            placeholder="correo@ejemplo.com"
                                                            @keydown.enter="addEmail"
                                                            class="flex-1 text-xs bg-white border border-amber-200 rounded-lg px-3 py-2.5 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 outline-none transition-all placeholder:text-gray-300">
                                                        <button 
                                                            @click="addEmail"
                                                            :disabled="!newEmail"
                                                            class="px-3.5 py-2.5 bg-amber-500 text-white rounded-lg hover:bg-amber-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium text-xs">
                                                            Agregar
                                                        </button>
                                                    </div>

                                                    <!-- Lista de correos autorizados -->
                                                    <div v-if="authorizedEmails.length > 0" class="space-y-2 max-h-[140px] overflow-y-auto custom-scrollbar">
                                                        <div v-for="email in authorizedEmails" :key="email.id" 
                                                            class="flex items-center justify-between p-2.5 rounded-lg bg-white border border-gray-100 group hover:border-red-200 transition-colors">
                                                            <div class="flex items-center gap-2.5 overflow-hidden">
                                                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-[11px] font-bold text-white uppercase shadow-sm">
                                                                    {{ email.email[0] }}
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <span class="text-xs text-gray-800 font-medium truncate block">{{ email.email }}</span>
                                                                    <span class="text-[10px] text-gray-400">Puede ver el portafolio</span>
                                                                </div>
                                                            </div>
                                                            <button 
                                                                @click="removeEmail(email.email)"
                                                                class="p-1.5 rounded-md opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all"
                                                                title="Quitar acceso">
                                                                <Trash2 class="w-3.5 h-3.5" />
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Estado vacío -->
                                                    <div v-else class="text-center py-4 px-2">
                                                        <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-amber-100/50 flex items-center justify-center">
                                                            <UserPlus class="w-5 h-5 text-amber-400" />
                                                        </div>
                                                        <p class="text-xs text-gray-500 font-medium">Sin personas autorizadas</p>
                                                        <p class="text-[10px] text-gray-400 mt-0.5">Agrega correos para dar acceso</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </Transition>
                                    </div>
                                </div>
                            </Transition>
                        </div>
    
                        <!-- Botón Vista Previa -->
                        <button @click="emits('openPreview')"
                            class="hidden items-center space-x-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-50 sm:flex">
                            <Eye class="h-4 w-4" />
                            <span>Vista previa</span>
                        </button>
    
                        <!-- Botón Guardar -->
                        <button
                            @click="emits('save')"
                            :disabled="isSaving || isSaved || !hasUnsavedChanges || isUpdating"
                            :class="[
                                'flex items-center space-x-2 rounded-lg font-medium px-4 py-2 transition-all duration-300',
                                !isSaving && !isSaved && hasUnsavedChanges && !isUpdating && 'bg-[#005aeb] text-white hover:bg-[#0048c4] shadow-md hover:shadow-lg',
                                (isSaving || isUpdating) && 'bg-[#005aeb] text-white opacity-75 cursor-not-allowed',
                                isSaved && 'bg-green-500 text-white shadow-lg shadow-green-500/30',
                                !hasUnsavedChanges && !isSaving && !isSaved && !isUpdating && 'bg-gray-100 text-gray-500 cursor-not-allowed'
                            ]">
                            <Loader2 v-if="isSaving" class="w-5 h-5 animate-spin" />
                            <Check v-else-if="isSaved" class="w-5 h-5 animate-bounce" />
                            <Save v-else class="h-4 w-4" />
                            <span class="text-sm">
                                {{ isSaving ? 'Guardando...' : isSaved ? '¡Guardado!' : 'Guardar' }}
                            </span>
                        </button>
    
                        <!-- Dropdown Opciones Adicionales -->
                        <div class="relative group">
                            <button
                                class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200 text-gray-600 hover:text-gray-900">
                                <MoreVertical class="h-5 w-5" />
                            </button>
                            <div
                                class="absolute right-0 mt-2 w-48 rounded-lg border border-gray-200 bg-white shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-20">
                                <button
                                    @click="handleViewPublic"
                                    :disabled="!isLinkSharing"
                                    class="flex items-center gap-3 w-full px-4 py-2.5 text-left text-sm transition-colors first:rounded-t-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-white">
                                    <Eye class="w-5 h-5" />
                                    Ver portafolio público
                                </button>
                                <button
                                    @click="emits('finish')"
                                    class="flex items-center gap-3 w-full px-4 py-2.5 text-left text-sm text-red-600 hover:bg-red-50 transition-colors last:rounded-b-lg border-t border-gray-100">
                                    <X class="w-4 h-4 flex-shrink-0" />
                                    Salir del editor
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    </template>
    
    <style scoped>
    .fade-slide-enter-active, .fade-slide-leave-active {
        transition: all 0.3s ease;
    }
    .fade-slide-enter-from, .fade-slide-leave-to {
        opacity: 0;
        transform: translateY(-10px);
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
    </style>