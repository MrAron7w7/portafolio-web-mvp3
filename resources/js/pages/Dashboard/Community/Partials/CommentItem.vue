<script setup lang="ts">
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { MessageSquare, Reply, User as UserIcon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea/index';
import { route } from '@/utils/route';

interface User {
    id: number;
    first_name: string;
    last_name: string;
    profile_photo_path?: string;
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
        <div v-if="depthLevel > 0" class="absolute -left-4 top-0 bottom-0 w-px bg-gray-200"></div>
        
        <div class="flex gap-4 group">
             <div class="flex-shrink-0">
                <div v-if="comment.user.profile_photo_path" class="h-10 w-10 rounded-full overflow-hidden border border-gray-200">
                    <img :src="comment.user.profile_photo_path" :alt="comment.user.first_name" class="h-full w-full object-cover">
                </div>
                <div v-else class="h-10 w-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-500 font-bold shadow-inner">
                    {{ comment.user.first_name.charAt(0) }}
                </div>
            </div>

            <div class="flex-1 space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-bold text-gray-900 text-sm mr-2">{{ comment.user.first_name }} {{ comment.user.last_name }}</span>
                        <span class="text-xs text-gray-500">{{ formatDate(comment.created_at) }}</span>
                    </div>
                    <div v-if="isOwner" class="flex items-center gap-2 text-xs">
                        <button 
                            v-if="!isEditing" 
                            @click="startEditing" 
                            class="text-gray-500 hover:text-indigo-600 transition-colors"
                        >
                            Editar
                        </button>
                        <button 
                            v-else 
                            @click="isEditing = false" 
                            class="text-gray-500 hover:text-gray-700 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button 
                            @click="deleteComment" 
                            :disabled="deleteForm.processing"
                            class="text-red-500 hover:text-red-600 transition-colors disabled:opacity-60"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>

                <div v-if="isEditing" class="space-y-2">
                    <Textarea 
                        v-model="editForm.content"
                        rows="3"
                        class="bg-white"
                    />
                    <div class="flex items-center gap-2 justify-end">
                        <Button 
                            size="sm" 
                            variant="ghost" 
                            type="button" 
                            @click="isEditing = false"
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
                <div v-else class="prose prose-sm text-gray-700 max-w-none">
                     <p class="whitespace-pre-wrap">{{ comment.content }}</p>
                </div>

                <div class="flex items-center gap-4 pt-1">
                    <button 
                        @click="isReplying = !isReplying"
                        class="text-xs font-medium text-gray-500 hover:text-indigo-600 flex items-center gap-1 transition-colors"
                    >
                        <Reply class="h-3 w-3" /> Responder
                    </button>
                </div>

                <!-- Reply Form -->
                <div v-if="isReplying" class="mt-4 bg-gray-50 p-4 rounded-xl border border-gray-100 animate-in fade-in slide-in-from-top-2">
                    <form @submit.prevent="submitReply" class="space-y-3">
                        <Textarea 
                            v-model="form.content" 
                            placeholder="Escribe tu respuesta..." 
                            rows="2"
                            class="bg-white"
                            auto-focus
                        />
                        <div class="flex justify-end gap-2">
                            <Button type="button" variant="ghost" size="sm" @click="isReplying = false">Cancelar</Button>
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
        <div v-if="comment.replies && comment.replies.length > 0" class="ml-4 pl-4 border-l-2 border-gray-100 space-y-4 mt-4">
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
