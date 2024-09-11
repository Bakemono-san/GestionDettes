<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FormatResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('swagger') || $request->is('swagger/*') || $request->is('telescope') || $request->is('telescope/*')) {
            return $next($request);
        }
        $response = $next($request);

        if ($response instanceof Response) {
            $statusCode = $response->getStatusCode();
            $content = $response->getContent();

            // Decode JSON content to an array, handle non-JSON responses
            $originalData = json_decode($content, true) ?? [];



            $formattedData = [
                'status' => $statusCode,
                'data' => $originalData ?? null,
                'message' => $originalData['message'] ?? $this->getDefaultMessage($statusCode),
                'success' => $response->isSuccessful(),
            ];

            return response()->json($formattedData, $statusCode);
        }

        // If the response is not an instance of Response, return it as is
        return $response;
    }

    /**
     * Get the default message based on the status code.
     *
     * @param int $statusCode
     * @return string
     */
    protected function getDefaultMessage(int $statusCode): string
    {
        return match ($statusCode) {
            200 => 'La requête a été effectuée avec succès.',
            201 => 'La ressource a été créée avec succès.',
            400 => 'Mauvaise requête.',
            401 => 'Non autorisé.',
            403 => 'Interdit.',
            404 => 'Ressource non trouvée.',
            500 => 'Erreur interne du serveur.',
            default => 'Une erreur inattendue s\'est produite.',
        };
    }
}
