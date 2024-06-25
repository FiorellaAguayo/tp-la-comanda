<?php

namespace App\Middlewares;
use Slim\Psr7\Response as ResponseClass;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMesas
{
    public function validarCampos(Request $request, RequestHandler $requestHandler)
    {
        $params = $request->getParsedBody();

        if ((isset($params['id'], $params['estado'], $params['total_facturado'], $params['importe_mayor'], $params['importe_menor'], $params['cantidad_usada'])) &&
            !empty($params['id']) && !empty($params['estado']) && !empty($params['total_facturado']) && !empty($params['importe_mayor']) && !empty($params['importe_menor']) && !empty($params['cantidad_usada'])) {
                $response = $requestHandler->handle($request);
        } else {
            $response = new ResponseClass();
            $response->getBody()->write(json_encode(array("error" => "Parametros de mesas incorrectos")));
            return $response->withHeader('Content-Type', 'application/json');
        }
        return $response;
    }
}