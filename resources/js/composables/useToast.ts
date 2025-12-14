// resources/js/Composables/useToast.ts
import { ref, Ref } from 'vue';

export type ToastType = 'success' | 'error' | 'warning' | 'info';
export type ToastPosition = 
    | 'top-right'
    | 'top-left'
    | 'bottom-right'
    | 'bottom-left'
    | 'top-center'
    | 'bottom-center';

export interface ToastOptions {
    type?: ToastType;
    title?: string;
    message?: string;
    duration?: number;
    position?: ToastPosition;
}

export interface Toast extends Required<ToastOptions> {
    id: number;
}

const toasts: Ref<Toast[]> = ref([]);

export function useToast() {
    const show = (options: ToastOptions): number => {
        const id = Date.now() + Math.random();

        const toast: Toast = {
            id,
            type: options.type ?? 'info',
            title: options.title ?? 'Notificación',
            message: options.message ?? '',
            duration: options.duration ?? 5000,
            position: options.position ?? 'top-right',
        };

        toasts.value.push(toast);

        return id;
    };

    const remove = (id: number): void => {
        const index = toasts.value.findIndex(t => t.id === id);
        if (index !== -1) {
            toasts.value.splice(index, 1);
        }
    };

    const success = (title: string, message = '', duration?: number) =>
        show({ type: 'success', title, message, duration });

    const error = (title: string, message = '', duration?: number) =>
        show({ type: 'error', title, message, duration });

    const warning = (title: string, message = '', duration?: number) =>
        show({ type: 'warning', title, message, duration });

    const info = (title: string, message = '', duration?: number) =>
        show({ type: 'info', title, message, duration });

    return {
        toasts,
        show,
        remove,
        success,
        error,
        warning,
        info,
    };
}
