<?php
namespace App\Middlewares;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseClass;

class AuthMiddleware {
    private $roleRequired;

    public function __construct($roleRequired) {
        $this->roleRequired = $roleRequired;
    }

    public function __invoke(Request $request, RequestHandler $handler) {
        return $this->verificarRol($request, $handler);
    }

    private function verificarRol(Request $request, RequestHandler $handler) {
        $json = $request->getBody()->getContents();
        $data = json_decode($json, true);
        $rol = $data['rol'] ?? null;

        if ($rol === 'socio') {
            return $handler->handle($request);
        } else if ($rol === 'empleado') {
            $response = new ResponseClass();
            $response->getBody()->write(json_encode(["error" => "Acceso restringido, solo socios"]));
            return $response->withStatus(403);
        } else {
            $response = new ResponseClass();
            $response->getBody()->write(json_encode(["error" => "Rol no válido o no proporcionado"]));
            return $response->withStatus(401);
        }
    }
}
?>