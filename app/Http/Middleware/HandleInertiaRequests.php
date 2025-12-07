<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'first_name' => $request->user()->first_name,
                    'last_name' => $request->user()->last_name,
                    'full_name' => $request->user()->full_name ?? ($request->user()->first_name . ' ' . $request->user()->last_name),
                    'email' => $request->user()->email,
                    'avatar_url' => $this->getUserAvatar($request->user()),
                ] : null,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    private function getUserAvatar($user): string
    {
        // Si no tiene avatar, generar uno por defecto
        if (!$user->avatar_url) {
            $name = urlencode(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            return "https://ui-avatars.com/api/?name={$name}&background=4F46E5&color=fff&size=256";
        }

        // Si ya es una URL completa, devolverla
        if (str_starts_with($user->avatar_url, 'http')) {
            return $user->avatar_url;
        }

        // Si es una ruta relativa, convertirla a URL completa
        return asset($user->avatar_url);
    }
}