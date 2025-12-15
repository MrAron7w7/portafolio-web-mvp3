<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Support\Facades\Storage;

class GeneralController extends Controller
{
    /**
     * Display the general settings page.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        
        return Inertia::render('settings/General', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'username' => $user->username,
                'avatar_url' => $user->avatar_url ?? $this->generateDefaultAvatar($user->full_name),
                'timezone' => $user->timezone ?? 'America/Lima',
                'language' => $user->language ?? 'es',
                'email_notifications' => $user->email_notifications ?? true,
                'marketing_emails' => $user->marketing_emails ?? false,
                'status' => $user->status ?? 'active',
                'created_at' => $user->created_at->locale('es')->isoFormat('D MMMM, YYYY'),
                'last_login_at' => $user->last_login_at ? $user->last_login_at->locale('es')->diffForHumans() : null,
            ],
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's general information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'timezone' => ['nullable', 'string', 'max:50'],
            'language' => ['nullable', 'string', 'max:5'],
            'email_notifications' => ['nullable', 'boolean'],
            'marketing_emails' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:8192'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists and is not from UI Avatars
            if ($user->avatar_url && !str_contains($user->avatar_url, 'ui-avatars.com')) {
                $oldPath = str_replace('/storage/', '', $user->avatar_url);
                Storage::disk('public')->delete($oldPath);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar_url'] = '/storage/' . $path;
        }

        // Remove avatar from validated if not uploaded to avoid overwriting
        if (!$request->hasFile('avatar')) {
            unset($validated['avatar']);
        }

        $user->fill($validated);
        $user->save();

        return back()->with('status', 'general-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'account-deleted');
    }

    /**
     * Remove the user's avatar.
     */
    public function removeAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Delete avatar file if exists and is not from UI Avatars
        if ($user->avatar_url && !str_contains($user->avatar_url, 'ui-avatars.com')) {
            $oldPath = str_replace('/storage/', '', $user->avatar_url);
            Storage::disk('public')->delete($oldPath);
        }

        // Reset to default avatar
        $user->avatar_url = null;
        $user->save();

        return back()->with('status', 'avatar-removed');
    }

    /**
     * Generate a default avatar URL using UI Avatars.
     */
    private function generateDefaultAvatar(string $name): string
    {
        $initials = collect(explode(' ', $name))
            ->map(fn($word) => mb_substr($word, 0, 1))
            ->take(2)
            ->join('+');

        return "https://ui-avatars.com/api/?name={$initials}&background=4F46E5&color=fff&size=256";
    }
}