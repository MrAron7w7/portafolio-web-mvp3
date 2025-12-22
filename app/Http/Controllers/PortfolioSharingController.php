<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

/**
 * Controlador para el sistema de enlaces compartidos de portafolios
 * 
 * Maneja:
 * - Generación y desactivación de enlaces
 * - Cambio de permisos (view / view_edit)
 * - Cambio de tipo de audiencia (any_with_link / emails_only)
 * - Gestión de correos autorizados
 * - Acceso público vía token
 */
class PortfolioSharingController extends Controller
{
    /**
     * Generar enlace compartido para un portafolio
     * 
     * POST /dashboard/portfolio/{portfolio}/share/generate
     */
    /**
     * Generar enlace compartido para un portafolio
     * 
     * POST /dashboard/portfolio/{portfolio}/share/generate
     */
    public function generateLink(Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        $shareUrl = $portfolio->generateShareLink();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Enlace generado correctamente',
                'share_url' => $shareUrl,
                'portfolio' => $portfolio->refresh(),
                'share_token' => $portfolio->share_token, 
            ]);
        }

        return back()->with([
            'success' => 'Enlace generado correctamente',
            'share_url' => $shareUrl,
        ]);
    }

    /**
     * Desactivar enlace y volver a modo privado
     * 
     * POST /dashboard/portfolio/{portfolio}/share/disable
     */
    public function disableLink(Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        $portfolio->disableShareLink();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Enlace desactivado. Tu portafolio ahora es privado.',
                'portfolio' => $portfolio->refresh(),
            ]);
        }

        return back()->with('success', 'Enlace desactivado. Tu portafolio ahora es privado.');
    }

    /**
     * Obtener estado actual de compartir
     * 
     * GET /dashboard/portfolio/{portfolio}/share/status
     */
    public function getStatus(Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        return response()->json([
            'access_mode' => $portfolio->access_mode,
            'link_permission' => $portfolio->link_permission,
            'link_audience_type' => $portfolio->link_audience_type,
            'share_token' => $portfolio->share_token,
            'share_url' => $portfolio->getShareUrl(),
            'has_active_link' => $portfolio->hasActiveLink(),
            'link_created_at' => $portfolio->link_created_at?->toISOString(),
            'authorized_emails' => $portfolio->authorizedEmails()->get(['id', 'email', 'invited_at', 'accessed_at']),
        ]);
    }

    /**
     * Cambiar permiso del enlace (view ↔ view_edit)
     * 
     * PATCH /dashboard/portfolio/{portfolio}/share/permission
     */
    public function updatePermission(Request $request, Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        $validator = Validator::make($request->all(), [
            'permission' => 'required|in:view,view_edit',
        ]);

        if ($validator->fails()) {
            if (request()->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator);
        }

        $portfolio->setLinkPermission($request->permission);
        $label = $request->permission === 'view' ? 'Ver' : 'Ver y Editar';

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Permiso cambiado a: {$label}",
                'link_permission' => $portfolio->link_permission,
            ]);
        }

        return back()->with('success', "Permiso cambiado a: {$label}");
    }

    /**
     * Cambiar tipo de audiencia (any_with_link ↔ emails_only)
     * 
     * PATCH /dashboard/portfolio/{portfolio}/share/audience
     */
    public function updateAudienceType(Request $request, Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        $validator = Validator::make($request->all(), [
            'audience_type' => 'required|in:any_with_link,emails_only',
        ]);

        if ($validator->fails()) {
             if (request()->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator);
        }

        $portfolio->setLinkAudienceType($request->audience_type);

        $label = $request->audience_type === 'any_with_link' 
            ? 'Cualquier persona con el enlace' 
            : 'Solo personas autorizadas por correo';

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Audiencia cambiada a: {$label}",
                'link_audience_type' => $portfolio->link_audience_type,
            ]);
        }

        return back()->with('success', "Audiencia cambiada a: {$label}");
    }

    /**
     * Agregar correo autorizado
     * 
     * POST /dashboard/portfolio/{portfolio}/share/emails
     */
    public function addAuthorizedEmail(Request $request, Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
             if (request()->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator);
        }

        $authorizedEmail = $portfolio->addAuthorizedEmail(
            $request->email,
            Auth::user()->first_name . ' ' . Auth::user()->last_name
        );

        if (request()->wantsJson()) {
             return response()->json([
                'success' => true,
                'message' => "Correo {$request->email} agregado correctamente",
                'authorized_email' => $authorizedEmail,
                'authorized_emails' => $portfolio->authorizedEmails()->get(), // Devuelve lista actualizada
            ]);
        }

        return back()->with('success', "Correo {$request->email} agregado correctamente");
    }

    /**
     * Eliminar correo autorizado
     * 
     * DELETE /dashboard/portfolio/{portfolio}/share/emails/{email}
     */
    public function removeAuthorizedEmail(Portfolio $portfolio, string $email)
    {
        $this->authorizeOwner($portfolio);

        $removed = $portfolio->removeAuthorizedEmail(urldecode($email));

        if (request()->wantsJson()) {
             if ($removed) {
                return response()->json([
                    'success' => true,
                    'message' => 'Correo eliminado correctamente',
                    'authorized_emails' => $portfolio->authorizedEmails()->get(),
                ]);
             }
             return response()->json(['error' => 'El correo no estaba en la lista'], 404);
        }

        if ($removed) {
            return back()->with('success', 'Correo eliminado correctamente');
        }

        return back()->with('error', 'El correo no estaba en la lista');
    }

    /**
     * Actualizar configuración completa de compartir (para uso desde el frontend)
     * 
     * PUT /dashboard/portfolio/{portfolio}/share
     */
    public function updateSharingConfig(Request $request, Portfolio $portfolio)
    {
        $this->authorizeOwner($portfolio);

        $validator = Validator::make($request->all(), [
            'access_mode' => 'required|in:owner_only,link',
            'link_permission' => 'required_if:access_mode,link|in:view,view_edit',
            'link_audience_type' => 'required_if:access_mode,link|in:any_with_link,emails_only',
        ]);

        if ($validator->fails()) {
             if (request()->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator);
        }

        // Si cambia a owner_only, desactivar todo
        if ($request->access_mode === 'owner_only') {
            $portfolio->disableShareLink();
            
            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Tu portafolio ahora es privado']);
            }
            return back()->with('success', 'Tu portafolio ahora es privado');
        }

        // Si cambia a link y no tiene token, generar uno
        if ($request->access_mode === 'link' && !$portfolio->share_token) {
            $portfolio->generateShareLink();
        }

        // Actualizar configuración
        $portfolio->update([
            'access_mode' => $request->access_mode,
            'link_permission' => $request->link_permission ?? 'view',
            'link_audience_type' => $request->link_audience_type ?? 'any_with_link',
            'is_public' => true,
        ]);

        if (request()->wantsJson()) {
             return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada',
                'share_url' => $portfolio->getShareUrl(),
                'portfolio' => $portfolio->refresh(),
            ]);
        }

        return back()->with([
            'success' => 'Configuración actualizada',
            'share_url' => $portfolio->getShareUrl(),
        ]);
    }

    // ========================================
    // ACCESO PÚBLICO VÍA TOKEN
    // ========================================

    /**
     * Vista pública del portafolio vía token compartido
     * 
     * GET /share/{token}
     */
    public function viewShared(string $token)
    {
        $portfolio = Portfolio::where('share_token', $token)->firstOrFail();

        // Verificar acceso
        $userEmail = Auth::check() ? Auth::user()->email : null;
        $access = $portfolio->canAccessViaLink($userEmail);

        if (!$access['allowed']) {
            // Si requiere login, redirigir
            if ($portfolio->requiresAuthorizedEmail() && !Auth::check()) {
                return redirect()->route('login', [
                    'redirect' => url("/share/{$token}"),
                ])->with('warning', 'Debes iniciar sesión para acceder a este portafolio');
            }

            // Mostrar página de acceso denegado
            return Inertia::render('Shared/AccessDenied', [
                'reason' => $access['reason'],
                'portfolioTitle' => $portfolio->title,
            ]);
        }

        // Acceso permitido - determinar si puede editar
        $canEdit = $access['permission'] === 'view_edit';
        $isOwner = Auth::check() && $portfolio->user_id === Auth::id();

        return Inertia::render('Shared/ViewPortfolio', [
            'portfolio' => $portfolio,
            'canEdit' => $canEdit,
            'isOwner' => $isOwner,
            'permission' => $access['permission'],
        ]);
    }

    /**
     * Actualizar portafolio vía enlace compartido (solo si tiene permiso view_edit)
     * 
     * PUT /share/{token}
     */
    public function updateShared(Request $request, string $token)
    {
        $portfolio = Portfolio::where('share_token', $token)->firstOrFail();

        // Verificar acceso
        $userEmail = Auth::check() ? Auth::user()->email : null;
        $access = $portfolio->canAccessViaLink($userEmail);

        if (!$access['allowed']) {
            return response()->json(['error' => $access['reason']], 403);
        }

        // Verificar permiso de edición
        if ($access['permission'] !== 'view_edit') {
            return response()->json([
                'error' => 'No tienes permiso para editar este portafolio',
            ], 403);
        }

        // Validar datos de actualización
        $validator = Validator::make($request->all(), [
            'template_data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Actualizar (solo template_data, no configuración sensible)
        $portfolio->update([
            'template_data' => $request->template_data,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Portafolio actualizado correctamente',
        ]);
    }

    // ========================================
    // HELPERS
    // ========================================

    /**
     * Verificar que el usuario autenticado es el propietario
     */
    private function authorizeOwner(Portfolio $portfolio): void
    {
        if ($portfolio->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para modificar este portafolio');
        }
    }
}
