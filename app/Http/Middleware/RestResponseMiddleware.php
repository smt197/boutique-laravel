<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\StatusResponseEnum;

class RestResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Vérifier si la réponse est déjà formatée ou est une instance de JsonResponse
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $original = $response->getData(true);  // Utiliser true pour obtenir le tableau associatif

            // Si les données originales contiennent déjà 'data', 'status', et 'message', ne rien faire
            if (isset($original['data']) && isset($original['status']) && isset($original['message'])) {
                return $response;
            }

            // Sinon, formater la réponse
            return response()->json([
                'data' => $original ?? null,  // Utiliser les données originales si disponibles
                'status' => StatusResponseEnum::SUCCESS->value,
                'message' => 'Opération réussie',
            ], $response->status());
        }

        return $response;
    }
}
