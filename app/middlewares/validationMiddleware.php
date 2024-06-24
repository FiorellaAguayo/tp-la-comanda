<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseClass;

class ValidationMiddleware {
    private $campos;

    public function __construct(array $campos) {
        $this->campos = $campos;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response {
        $json = $request->getBody()->getContents();
        $data = json_decode($json, true);
        $errores = [];

        foreach ($this->campos as $campo => $required) {
            if ($required && empty($data[$campo])) {
                $errors[$campo] = 'Este campo es requerido';
            }
        }

        if (!empty($errores)) {
            $response = new ResponseClass();
            $response->getBody()->write(json_encode(['errors' => $errores]));
            return $response->withStatus(400);
        }

        return $handler->handle($request);
    }
}
