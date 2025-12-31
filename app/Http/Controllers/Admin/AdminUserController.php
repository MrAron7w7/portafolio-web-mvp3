<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    /**
     * Lista todos los usuarios
     */
    public function index()
    {
        $users = User::with('roles')
            ->latest()
            ->paginate(15)
            ->through(fn ($user) => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name ?? 'user',
                'created_at' => $user->created_at->format('d M Y'),
                'created_at_iso' => $user->created_at->toISOString(),
                // 'status' => $user->status ?? 'active', // Anterior: Estado de cuenta
                // Nuevo: Estado de actividad (Online si visto en últimos 5 min)
                'status' => ($user->last_seen_at && $user->last_seen_at->gt(now()->subMinutes(5))) ? 'active' : 'inactive',
                'last_seen' => $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Nunca',
                'avatar_url' => $user->avatar_url,
                'portfolios_count' => $user->portfolios()->count(),
            ]);

        $roles = Role::all(['id', 'name']);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Ver detalle de un usuario
     */
    public function show(User $user)
    {
        $user->load(['roles', 'portfolios']);

        return Inertia::render('Admin/Users/Show', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name ?? 'user',
                'created_at' => $user->created_at->format('d M Y'),
                'status' => $user->status ?? 'active',
            ],
            'portfolios' => $user->portfolios->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'template_type' => $p->template_type,
                'is_public' => $p->is_public,
                'created_at' => $p->created_at->format('d M Y'),
            ]),
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    /**
     * Editar usuario (vista)
     */
    public function edit(User $user)
    {
        return $this->show($user);
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:active,inactive',
            'role' => 'sometimes|exists:roles,name',
        ]);

        // No permitir cambiar el propio rol
        if (isset($validated['role']) && $user->id === Auth::id()) {
            return back()->with('error', 'No puedes cambiar tu propio rol.');
        }

        // Actualizar datos básicos
        $user->update([
            'first_name' => $validated['first_name'] ?? $user->first_name,
            'last_name' => $validated['last_name'] ?? $user->last_name,
            'status' => $validated['status'] ?? $user->status,
        ]);

        // Actualizar rol si se envió
        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        // No permitir eliminarse a sí mismo
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        // Eliminar portafolios del usuario
        $user->portfolios()->delete();
        
        // Eliminar usuario
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
