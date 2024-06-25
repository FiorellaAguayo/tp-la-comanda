<?php

namespace App\Middlewares;
use Slim\Psr7\Response as ResponseClass;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthPedidos
{
    public function validarCampos(Request $request, RequestHandler $requestHandler)
    {
        $params = $request->getParsedBody();

        if ((isset($params['nombre_cliente'], $params['id_mesa'], $params['estado'], $params['tiempo_estimado'], $params['nombre_productos'])) &&
        !empty($params['nombre_cliente']) && !empty($params['id_mesa']) && !empty($params['estado']) && !empty($params['tiempo_estimado']) && !empty($params['nombre_productos'])) {
            $response = $requestHandler->handle($request);
        } else {
            $response = new ResponseClass();
            $response->getBody()->write(json_encode(array("error" => "Parametros de pedidos incorrectos")));
            return $response->withHeader('Content-Type', 'application/json');
        }
    
        return $response;
    }
}