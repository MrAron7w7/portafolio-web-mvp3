// Simple route helper for named routes
const routes: Record<string, string> = {
    'community': '/dashboard/comunidad',
    'community.show': '/dashboard/comunidad/:id',
    'community.store': '/dashboard/comunidad',
    'community.destroy': '/dashboard/comunidad/:id',
    'community.rate': '/dashboard/comunidad/:id/calificar',
    'community.comment.store': '/dashboard/comunidad/:postId/comentar',
    'community.comment.update': '/dashboard/comunidad/comentarios/:comment',
    'community.comment.destroy': '/dashboard/comunidad/comentarios/:comment',
};

export function route(name: string, params?: any): string {
    let path = routes[name];

    if (!path) {
        console.warn(`Route "${name}" not found`);
        return '/';
    }

    if (params) {
        // Replace URL parameters
        if (typeof params === 'object') {
            Object.keys(params).forEach(key => {
                path = path.replace(`:${key}`, String(params[key]));
            });
        } else {
            // Single parameter, replace first placeholder
            path = path.replace(/:[^/]+/, String(params));
        }
    }

    return path;
}

// Make globally available
if (typeof window !== 'undefined') {
    (window as any).route = route;
}
