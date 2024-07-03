<?php

namespace App\Middlewares;
use Slim\Psr7\Response as ResponseClass;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthUsuarios
{
    public function validarCampos(Request $request, RequestHandler $requestHandler)
    {
        $params = $request->getParsedBody();

        if ((isset($params['nombre'], $params['email'], $params['clave'], $params['rol'], $params['tiempo_estimado'], $params['fecha_ingreso'], $params['estado'], $params['sector'])) &&
            !empty($params['nombre']) && !empty($params['email']) && !empty($params['clave']) && !empty($params['rol']) && !empty($params['tiempo_estimado']) && !empty($params['fecha_ingreso']) && !empty($params['estado']) && !empty($params['sector'])) {
            $response = $requestHandler->handle($request);
        } else {
            $response = new ResponseClass();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
            return $response->withHeader('Content-Type', 'application/json');
        }
        return $response;
    }
}