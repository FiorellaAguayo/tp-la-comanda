<?php

namespace App\Middlewares;
use Slim\Psr7\Response as ResponseClass;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthProductos
{
    public function validarCampos(Request $request, RequestHandler $requestHandler)
    {
        $params = $request->getParsedBody();

        if ((isset($params['nombre'], $params['categoria'], $params['sector'], $params['precio'])) &&
            !empty($params['nombre']) && !empty($params['categoria']) && !empty($params['sector']) && !empty($params['precio'])) {
                $response = $requestHandler->handle($request);
        } else {
            $response = new ResponseClass();
            $response->getBody()->write(json_encode(array("error" => "Parametros de productos incorrectos")));
            return $response->withHeader('Content-Type', 'application/json');
        }
        return $response;
    }
}