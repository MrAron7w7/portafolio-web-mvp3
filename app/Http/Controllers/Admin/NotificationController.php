<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Mostrar panel de notificaciones
     */
    public function index()
    {
        $notifications = Notification::with('user')
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Notifications', [
            'notifications' => $notifications
        ]);
    }

    /**
     * Crear notificación global
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:info,success,warning,error',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'is_global' => 'boolean',
        ]);

        Notification::create([
            'type' => $request->type,
            'title' => $request->title,
            'message' => $request->message,
            'is_global' => $request->boolean('is_global'),
            'user_id' => $request->is_global ? null : auth()->id(),
        ]);

        return redirect()->back()->with('notification', [
            'type' => $request->type,
            'title' => $request->title,
            'message' => $request->message,
        ]);

    }

    /**
     * Obtener notificaciones del usuario actual
     */
    public function getUserNotifications()
    {
        $notifications = Notification::forUser(auth()->id())
            ->latest()
            ->take(10)
            ->get();

        return response()->json($notifications);
    }

    /**
     * Marcar notificación como leída
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);

        // Verificar que el usuario tenga acceso
        if (!$notification->is_global && $notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Marcar todas como leídas
     */
    public function markAllAsRead()
    {
        Notification::forUser(auth()->id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Eliminar notificación
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', 'Notificación eliminada');
    }
}