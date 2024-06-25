<?php

namespace App\Middlewares;

use App\Utils\AutentificadorJWT;
use App\Services\TokenService;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class Logger
{
    private $autentificadorJWT;
    private $tokenService;

    public function __construct() {
        $this->autentificadorJWT = new AutentificadorJWT();
        $this->tokenService = new TokenService();
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $token = null;

        if (strpos($header, 'Bearer ') === 0) {
            $token = trim(str_replace('Bearer ', '', $header));
        }

        try {
            if ($token) {
                $this->tokenService->verificarToken($token);
                $response = $handler->handle($request);
            } else {
                throw new \Exception("Token no proporcionado");
            }
        } catch (\Exception $e) {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN', 'error' => $e->getMessage()));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
