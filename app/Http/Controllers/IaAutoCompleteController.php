<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IaAutoCompleteController extends Controller
{
    public function generateSummary(Request $request)
    {
        $description = $request->input('in-0');

        if (!$description || strlen($description) < 1) {
            return response()->json(
                ['message' => 'Descripción vacía o muy corta para autocompletar'],
                422
            );
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.stack_ai.key'),
                'Content-Type'  => 'application/json',
            ])->withoutVerifying()->post(
                'https://api.stack-ai.com/inference/v0/run/' . 
                config('services.stack_ai.flow_id') . '/' . 
                config('services.stack_ai.node_id'),
                [
                    'in-0'   => $description,
                    'user_id'=> ''
                ]
            );

            if ($response->failed()) {
                return response()->json(
                    ['message' => 'Error en la IA de autocompletado'],
                    500
                );
            }

            $data = $response->json();

            // Extrae el summary de outputs['out-0']
            $summary = data_get($data, 'outputs.out-0', '');

            return response()->json([
                'summary' => $summary,
                'raw'     => $data, // opcional para depuración
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                ['message' => 'Error interno: ' . $e->getMessage()],
                500
            );
        }
    }
}
