<?php
namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMw;
use App\Services\TokenService;

class AuthMiddleware
{
    private $rolesPermitidos;

    // Modificado para aceptar un array de roles
    public function __construct($rolesPermitidos) {
        $this->rolesPermitidos = (array) $rolesPermitidos;  // Asegura que siempre sea un array
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = new ResponseMw();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(str_replace('Bearer ', '', $header));

        if (empty($token)) {
            return $this->errorResponse($response, 'Token no proporcionado', 401);
        }

        try {
            $userData = TokenService::obtenerData($token);
            $rolUsuario = $userData->rol ?? null;
            $emailUsuario = $userData->email ?? null;

            // Agregar logs para verificar los datos del token
            error_log("Rol Usuario: " . $rolUsuario);
            error_log("Email Usuario: " . $emailUsuario);

            if (in_array($rolUsuario, $this->rolesPermitidos)) {
                $request = $request->withAttribute('emailUsuario', $emailUsuario);
                $request = $request->withAttribute('rolUsuario', $rolUsuario);
                return $handler->handle($request);
            } else {
                return $this->errorResponse($response, "Acceso denegado: no tiene permisos de '" . implode("' o '", $this->rolesPermitidos) . "'", 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($response, "Error de autenticación: " . $e->getMessage(), 401);
        }
    }

    private function errorResponse($response, $message, $statusCode) {
        $response->getBody()->write(json_encode(["error" => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }
}