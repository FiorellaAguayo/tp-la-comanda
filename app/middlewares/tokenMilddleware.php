<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\TokenService;
use Exception;

class TokenMiddleware {

    public function __invoke(Request $request, Response $response, $next) {
        try {
            // Extraemos el token del header 'Authorization'
            $header = $request->getHeaderLine('Authorization');
            $token = trim(str_replace('Bearer ', '', $header));  // Asegúrate de incluir un espacio después de 'Bearer'

            // Verificamos el token utilizando TokenService
            $tokenService = new TokenService();
            $tokenService->verificarToken($token);  // Lanza una excepción si el token no es válido

            // Si el token es válido, procedemos con el siguiente middleware o controlador
            $response = $next($request, $response);

        } catch (Exception $e) {
            // Si hay un error (token no válido o no proporcionado), devolvemos un error
            $response->getBody()->write(json_encode(['error' => 'Token no válido o ausente: ' . $e->getMessage()]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);  // 401 Unauthorized
        }

        return $response;
    }
}
