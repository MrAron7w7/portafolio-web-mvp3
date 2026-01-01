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
    'admin.landing.update': '/admin/landing/:key',
    'settings': '/settings/general',
    'settings.general': '/settings/general',
    'profile.edit': '/settings/profile',
    'user-password.edit': '/settings/password',
    'settings.password.update': '/settings/password',
    'appearance.edit': '/settings/appearance',
    'two-factor.show': '/settings/two-factor',
    'two-factor.qr-code': '/user/two-factor-qr-code',
    'two-factor.secret-key': '/user/two-factor-secret-key',
    'two-factor.recovery-codes': '/user/two-factor-recovery-codes',
    'two-factor.enable': '/user/two-factor-authentication',
    'two-factor.disable': '/user/two-factor-authentication',
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
