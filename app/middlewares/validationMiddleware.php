<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseClass;

class ValidationMiddleware {
    private $rules;

    public function __construct(array $rules) {
        $this->rules = $rules;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response {
        $json = $request->getBody()->getContents();
        $data = json_decode($json, true);
        $errors = [];

        foreach ($this->rules as $field => $required) {
            if ($required && empty($data[$field])) {
                $errors[$field] = 'Este campo es requerido';
            }
        }

        if (!empty($errors)) {
            $response = new ResponseClass();
            $response->getBody()->write(json_encode(['errors' => $errors]));
            return $response->withStatus(400);
        }

        return $handler->handle($request);
    }
}
