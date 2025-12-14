<script setup>
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
// Asegúrate de importar la función para añadir (aquí asumo que se llama 'add')
import { useToast } from '@/Composables/useToast';
import Toast from '@/Components/Toast.vue';

const { toasts, remove, add } = useToast(); // <--- OJO AQUI con 'add'
const page = usePage();

watch(
    () => page.props.flash?.notification,
    (notification) => {
        if (notification) {
            add({ // Usamos la función del composable
                type: notification.type,
                title: notification.title,
                message: notification.message,
                // Si el backend manda "position: 'top-full'", la barra aparecerá
                position: notification.position || 'top-right', 
                duration: notification.duration || 5000
            });
        }
    },
    { immediate: true }
);
</script>