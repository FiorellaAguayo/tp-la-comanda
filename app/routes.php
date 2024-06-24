<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\UsuarioController;
use App\Controllers\ProductoController;
use App\Controllers\MesaController;
use App\Controllers\PedidoController;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\ValidationMiddleware;
use App\Models\AutentificadorJWT;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as ResponseClass;

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('/listarUsuarios', UsuarioController::class . ':listarUsuarios');
    $group->post('/agregarUsuario', UsuarioController::class . ':agregarUsuario');
    $group->post('/modificarUsuario', UsuarioController::class . ':modificarUsuario');
    $group->post('/eliminarUsuario', UsuarioController::class . ':eliminarUsuario');
});

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('/listarProductos', ProductoController::class . ':listarProductos');
    $group->post('/agregarProducto', ProductoController::class . ':agregarProducto');
    $group->post('/modificarProducto', ProductoController::class . ':modificarProducto');
    $group->post('/eliminarProducto', ProductoController::class . ':eliminarProducto');
});


$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('/listarMesas', MesaController::class . ':listarMesas');
    $group->post('/agregarMesa', MesaController::class . ':agregarMesa');
    $group->post('/modificarMesa', MesaController::class . ':modificarMesa');
    $group->post('/eliminarMesa', MesaController::class . ':eliminarMesa');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('/listarPedidos', PedidoController::class . ':listarPedidos');
    $group->post('/agregarPedido', PedidoController::class . ':agregarPedido');
    $group->post('/modificarPedido', PedidoController::class . ':modificarPedido');
    $group->post('/eliminarPedido', PedidoController::class . ':eliminarPedido');
});

/*
$app->group('/login', function (RouteCollectorProxy $group){
    $group->post('', AuthMiddleware::class . ':verificarRol');
})->add(new AuthMiddleware('socio'));

$app->group('/jwt', function (RouteCollectorProxy $group) {

    // CREAR TOKEN
    $group->post('/crearToken', function (Request $request, Response $response) {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $perfil = $parametros['perfil'];
        $alias = $parametros['alias'];

        $datos = array('usuario' => $usuario, 'perfil' => $perfil, 'alias' => $alias);

        $token = AutentificadorJWT::crearToken($datos);
        $payload = json_encode(array('jwt' => $token));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // VERIFICAR TOKEN
    $group->get('/verificarToken', function (Request $request, Response $response) {
        $header = $request->getHeaderLine('Authorization');
        $esValido = false;

        if($header) {
            $token = trim(explode("Bearer", $header)[1]);
        } else {
            $token = '';
        }

        try {
            AutentificadorJWT::verificarToken($token);
            $esValido = true;
        } catch(Exception $e) {
            $payload = json_encode(array("error" => $e->getMessage()));
        }

        if($esValido) {
            $payload = json_encode(array("valid" => $esValido));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // DEVOLVER PAYLOAD
    $group->get('/devolverPayload', function (Request $request, Response $response) {
        $header = $request->getHeaderLine('Authorization');

        if($header) {
            $token = trim(explode("Bearer", $header)[1]);
        } else {
            $token = '';
        }

        try {
            $payload = json_enconde(array('payload' => AutentificadorJWT::obtenerPayLoad($token)));
        } catch(Exception $e) {
            $payload = json_encode(array("error" => $e->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });

    // DEVOLVER DATA
    $group->get('/devolverData', function (Request $request, Response $response) {
        $header = $request->getHeaderLine('Authorization');

        if($header) {
            $token = trim(explode("Bearer", $header)[1]);
        } else {
            $token = '';
        }

        try {
            $payload = json_enconde(array('payload' => AutentificadorJWT::obtenerData($token)));
        } catch(Exception $e) {
            $response = new ResponseClass();
            $payload = json_encode(array("error" => "Hubo un error con el token"));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });
});*/