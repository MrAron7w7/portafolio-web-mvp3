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
                'roles' => $request->user() ? $request->user()->getRoleNames()->toArray() : [],
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'brand_name' => fn () => \App\Models\LandingPageSection::where('key', 'header')->first()?->content['brand_name'] ?? 'PortafolioAI',
            'logo_url' => fn () => \App\Models\LandingPageSection::where('key', 'header')->first()?->images['logo'] ?? null,
            'auth_bg' => fn () => \App\Models\LandingPageSection::where('key', 'auth')->first()?->images['background'] ?? null,
            'auth_content' => fn () => \App\Models\LandingPageSection::where('key', 'auth')->first()?->content ?? [],
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