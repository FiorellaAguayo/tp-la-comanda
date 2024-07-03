<?php

use Slim\Routing\RouteCollectorProxy;

// controllers
use App\Controllers\UsuarioController;
use App\Controllers\ProductoController;
use App\Controllers\MesaController;
use App\Controllers\PedidoController;
use App\Controllers\TokenController;
use App\Controllers\LoginController;

// models
use App\Models\AutentificadorJWT;

// middlewares
use App\Middlewares\ValidationMiddleware;
use App\Middlewares\TokenMiddleware;
use App\Middlewares\Logger;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\AuthUsuarios;
use App\Middlewares\AuthProductos;
use App\Middlewares\AuthPedidos;
use App\Middlewares\AuthMesas;
//use App\Middlewares\AuthMiddleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as ResponseClass;

// LOGIN DE USUARIOS (devuelve token)
$app->group('/auth', function (RouteCollectorProxy $group) {
    $group->post('/login', LoginController::class . ':login');
});

// MANEJO DE USUARIOS (agrega, modifica, elimina, lista)
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->post('/agregarUsuario', UsuarioController::class . ':agregarUsuario')->add(AuthUsuarios::class . ':validarCampos')->add(new AuthMiddleware('socio'));
    $group->post('/modificarUsuario', UsuarioController::class . ':modificarUsuario')->add(new AuthMiddleware('socio'));
    $group->post('/eliminarUsuario', UsuarioController::class . ':eliminarUsuario')->add(new AuthMiddleware('socio'));;
    $group->get('/listarUsuarios', UsuarioController::class . ':listarUsuarios')->add(new AuthMiddleware('socio'));;
})->add(new Logger());

// MANEJO DE PRODUCTOS (agrega, modifica, elimina, lista)
$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->post('/agregarProducto', ProductoController::class . ':agregarProducto')->add(AuthProductos::class . ':validarCampos')->add(new AuthMiddleware('socio'));
    $group->post('/modificarProducto', ProductoController::class . ':modificarProducto')->add(new AuthMiddleware('socio'));
    $group->post('/eliminarProducto', ProductoController::class . ':eliminarProducto')->add(new AuthMiddleware('socio'));
    $group->get('/listarProductos', ProductoController::class . ':listarProductos')->add(new AuthMiddleware('socio'));
})->add(new Logger());

// MANEJO DE MESAS (agrega, modifica, elimina, lista)
$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->post('/agregarMesa', MesaController::class . ':agregarMesa')->add(AuthMesas::class . ':validarCampos')->add(new AuthMiddleware('socio'));
    $group->post('/modificarMesa', MesaController::class . ':modificarMesa')->add(new AuthMiddleware('socio'));
    $group->post('/eliminarMesa', MesaController::class . ':eliminarMesa')->add(new AuthMiddleware('socio'));
    $group->get('/listarMesas', MesaController::class . ':listarMesas')->add(new AuthMiddleware('socio'));
});

// MANEJO DE PEDIDOS (agrega, modifica, elimina, lista)
$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->post('/agregarPedido', PedidoController::class . ':agregarPedido')->add(AuthPedidos::class . ':validarCampos')->add(new AuthMiddleware(['socio', 'mozo']));
    $group->post('/modificarPedido', PedidoController::class . ':modificarPedido')->add(new AuthMiddleware(['socio', 'bartender', 'cervecero', 'cocinero']));
    $group->post('/eliminarPedido', PedidoController::class . ':eliminarPedido')->add(new AuthMiddleware('socio'));
    $group->get('/listarPedidos', PedidoController::class . ':listarPedidos')->add(new AuthMiddleware(['socio', 'bartender', 'cervecero', 'cocinero']));
})->add(new Logger());

// LISTADO DE PENDIENTES
$app->group('/pendientes', function (RouteCollectorProxy $group) {
    //$group->post('/agregarPendientes', PedidoController::class . ':agregarPedido')->add(AuthPedidos::class . ':validarCampos')->add(new AuthMiddleware(['socio', 'mozo']));
    //$group->post('/modificarPendientes', PedidoController::class . ':modificarPedido')->add(new AuthMiddleware('socio'));
    //$group->post('/eliminarPendientes', PedidoController::class . ':eliminarPedido')->add(new AuthMiddleware('socio'));
    $group->get('/mostrarPendientes', PendienteController::class . ':listarPendientes');
});

/*
// TOKEN
$app->group('/jwt', function (RouteCollectorProxy $group) {
    $group->post('/login', \App\Controllers\TokenController::class . ':crearToken');
    $group->get('/verificar', \App\Controllers\TokenController::class . ':verificarToken');
});


/*
$app->group('/api', function (\Slim\Routing\RouteCollectorProxy $group) {
    $group->get('/listarUsuarios', \App\Controllers\UsuarioController::class . ':listarUsuarios');
    //$group->post('/pedidos', \App\Controllers\PedidoController::class . ':agregarPedido');
})->add(\App\Middlewares\TokenMiddleware::class);
*/

/*
$app->group('/login', function (RouteCollectorProxy $group){
    $group->post('', AuthMiddleware::class . ':verificarRol');
})->add(new AuthMiddleware('socio'));
*/

$app->group('/jwt', function (RouteCollectorProxy $group) {
    // DEVOLVER PAYLOAD
    $group->get('/devolverPayload', function (Request $request, Response $response) {
        $header = $request->getHeaderLine('Authorization');

        if($header) {
            $token = trim(explode("Bearer", $header)[1]);
        } else {
            $token = '';
        }

        try {
            $payload = json_enconde(array('payload' => TokenController::obtenerPayLoad($token)));
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
            $payload = json_enconde(array('payload' => TokenController::obtenerData($token)));
        } catch(Exception $e) {
            $response = new ResponseClass();
            $payload = json_encode(array("error" => "Hubo un error con el token"));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });
});