<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class AdminSettingController extends Controller
{
    /**
     * Mostrar formulario de configuración
     */
    public function index()
    {
        $settings = SystemSetting::all()->groupBy('group');
        
        return Inertia::render('Admin/Settings', [
            'settings' => $settings,
            'grouped_settings' => $this->groupSettings($settings)
        ]);
    }

    /**
     * Actualizar configuraciones
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'logo' => 'nullable|image|max:2048', // 2MB max
        ]);

        try {
            // Procesar logo si existe
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
                
                SystemSetting::updateOrCreate(
                    ['key' => 'app_logo'],
                    [
                        'value' => $logoPath,
                        'type' => 'image',
                        'group' => 'general'
                    ]
                );
            }

            // Procesar resto de configuraciones
            foreach ($request->input('settings', []) as $key => $value) {
                // Validar que el key existe en la base de datos
                $setting = SystemSetting::where('key', $key)->first();
                
                if ($setting) {
                    $setting->update(['value' => $value]);
                }
            }

            // Limpiar caché si lo estás usando
            Cache::forget('system_settings');

            return redirect()->back()->with('success', 'Configuración actualizada correctamente');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Agrupar settings para el frontend
     */
    private function groupSettings($settings)
    {
        $grouped = [];
        
        foreach ($settings as $group => $items) {
            $grouped[$group] = $items->mapWithKeys(function ($item) {
                return [$item->key => [
                    'value' => $item->value,
                    'type' => $item->type,
                ]];
            });
        }
        
        return $grouped;
    }

    /**
     * Resetear a valores por defecto
     */
    public function reset()
    {
        try {
            // Ejecutar el seeder nuevamente
            \Artisan::call('db:seed', ['--class' => 'SystemSettingSeeder', '--force' => true]);
            
            Cache::forget('system_settings');
            
            return redirect()->back()->with('success', 'Configuración restaurada a valores por defecto');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al resetear: ' . $e->getMessage());
        }
    }
}