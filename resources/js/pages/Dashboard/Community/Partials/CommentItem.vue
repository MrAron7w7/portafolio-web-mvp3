<script setup lang="ts">
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { MessageSquare, Reply, User as UserIcon, Pencil, Trash2, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea/index';
import { route } from '@/utils/route';

interface User {
    id: number;
    first_name: string;
    last_name: string;
    profile_photo_path?: string;
    avatar_url?: string; // Add this
    community_post_ratings?: { rating: number }[];
}

interface Comment {
    id: number;
    user_id: number;
    content: string;
    created_at: string;
    user: User;
    replies?: Comment[];
}

const props = defineProps<{
    comment: Comment;
    postId: number;
    depth?: number;
}>();

const page = usePage();
const isOwner = computed(() => page.props?.auth?.user?.id === props.comment.user_id);
const userRating = computed(() => {
    const ratings = props.comment.user.community_post_ratings;
    return ratings && ratings.length > 0 ? ratings[0].rating : null;
});

const depthLevel = props.depth || 0;
const isReplying = ref(false);
const isEditing = ref(false);

const form = useForm({
    content: '',
    parent_id: props.comment.id
});

const editForm = useForm({
    content: props.comment.content
});

const deleteForm = useForm({});

const submitReply = () => {
    form.post(route('community.comment.store', props.postId), {
        onSuccess: () => {
            isReplying.value = false;
            form.reset();
        },
        preserveScroll: true
    });
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const startEditing = () => {
    editForm.content = props.comment.content;
    isEditing.value = true;
};

const saveEdit = () => {
    editForm.put(route('community.comment.update', props.comment.id), {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        }
    });
};

const deleteComment = () => {
    if (!confirm('¿Eliminar este comentario?')) return;

    deleteForm.delete(route('community.comment.destroy', props.comment.id), {
        preserveScroll: true
    });
};
</script>

<template>
    <div :class="['relative', depthLevel > 0 ? 'mt-4' : '']">
        <!-- Connecting line for threads -->
        <div v-if="depthLevel > 0" class="absolute -left-4 top-0 bottom-0 w-px bg-gray-200 dark:bg-slate-800"></div>
        
        <div class="flex gap-4 group">
            <div class="shrink-0">
                <div v-if="comment.user.avatar_url" class="h-10 w-10 rounded-full overflow-hidden border border-gray-200 dark:border-slate-700">
                    <img :src="comment.user.avatar_url" :alt="comment.user.first_name" class="h-full w-full object-cover">
                </div>
                <div v-else class="h-10 w-10 rounded-full bg-linear-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white font-bold shadow-md">
                    {{ comment.user.first_name.charAt(0) }}
                </div>
            </div>

            <div class="flex-1 space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-bold text-gray-900 dark:text-white text-sm">{{ comment.user.first_name }} {{ comment.user.last_name }}</span>
                        
                        <div v-if="userRating" class="flex items-center gap-0.5 bg-yellow-50 dark:bg-yellow-900/10 px-1.5 py-0.5 rounded border border-yellow-100 dark:border-yellow-900/20" title="Calificación del usuario">
                            <span class="text-xs font-bold text-yellow-700 dark:text-yellow-500">{{ userRating.toFixed(1) }}</span>
                            <svg class="h-3 w-3 text-yellow-500 fill-current" viewBox="0 0 24 24"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        </div>

                        <span class="text-xs text-gray-500 dark:text-slate-500">• {{ formatDate(comment.created_at) }}</span>
                    </div>
                    <div v-if="isOwner" class="flex items-center gap-1">
                        <template v-if="!isEditing">
                            <button 
                                @click="startEditing" 
                                class="p-1.5 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 rounded-lg transition-all"
                                title="Editar comentario"
                            >
                                <Pencil class="h-3.5 w-3.5" />
                            </button>
                            <button 
                                @click="deleteComment" 
                                :disabled="deleteForm.processing"
                                class="p-1.5 text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/10 hover:bg-red-100 dark:hover:bg-red-900/20 rounded-lg transition-all disabled:opacity-50"
                                title="Eliminar comentario"
                            >
                                <Trash2 class="h-3.5 w-3.5" />
                            </button>
                        </template>
                        <button 
                            v-else 
                            @click="isEditing = false" 
                            class="p-1.5 text-gray-600 dark:text-slate-400 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg transition-all"
                            title="Cancelar edición"
                        >
                            <X class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>

                <div v-if="isEditing" class="space-y-2">
                    <Textarea 
                        v-model="editForm.content"
                        rows="3"
                        class="bg-white dark:bg-slate-900 dark:border-slate-700 dark:text-white"
                    />
                    <div class="flex items-center gap-2 justify-end">
                        <Button 
                            size="sm" 
                            variant="ghost" 
                            type="button" 
                            @click="isEditing = false"
                            class="text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                        >
                            Cancelar
                        </Button>
                        <Button 
                            size="sm" 
                            type="button"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white"
                            :disabled="editForm.processing"
                            @click="saveEdit"
                        >
                            Guardar
                        </Button>
                    </div>
                </div>
                <div v-else class="prose prose-sm text-gray-700 dark:text-slate-300 max-w-none">
                     <p class="whitespace-pre-wrap">{{ comment.content }}</p>
                </div>

                <div class="flex items-center gap-4 pt-1">
                    <button 
                        @click="isReplying = !isReplying"
                        class="text-xs font-medium text-gray-500 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1 transition-colors"
                    >
                        <Reply class="h-3 w-3" /> Responder
                    </button>
                </div>

                <!-- Reply Form -->
                <div v-if="isReplying" class="mt-4 bg-gray-50 dark:bg-slate-800/50 p-4 rounded-xl border border-gray-100 dark:border-slate-800 animate-in fade-in slide-in-from-top-2">
                    <form @submit.prevent="submitReply" class="space-y-3">
                        <Textarea 
                            v-model="form.content" 
                            placeholder="Escribe tu respuesta..." 
                            rows="2"
                            class="bg-white dark:bg-slate-900 dark:border-slate-700 dark:text-white"
                            auto-focus
                        />
                        <div class="flex justify-end gap-2">
                            <Button type="button" variant="ghost" size="sm" @click="isReplying = false" class="text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Cancelar</Button>
                            <Button 
                                type="submit" 
                                size="sm" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white"
                                :disabled="form.processing"
                            >
                                Responder
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recursive Replies -->
        <div v-if="comment.replies && comment.replies.length > 0" class="ml-4 pl-4 border-l-2 border-gray-100 dark:border-slate-800 space-y-4 mt-4">
            <CommentItem 
                v-for="reply in comment.replies" 
                :key="reply.id" 
                :comment="reply" 
                :postId="postId"
                :depth="depthLevel + 1"
            />
        </div>
    </div>
</template>
