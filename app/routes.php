<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\UsuarioController;
use App\Controllers\ProductoController;
use App\Controllers\MesaController;
use App\Middlewares\AuthMiddleware;

$app->group('/usuarios', function (RouteCollectorProxy $group){
    $group->get('', UsuarioController::class . ':listarUsuarios')->add(new AuthMiddleware("socio"));
    $group->post('', UsuarioController::class . ':agregarUsuario')->add(new AuthMiddleware("socio"))
    ->add(new ValidationMiddleware([
        'nombreUsuario' => true,
        'clave' => true,
        'nombreEmpleado' => true,
        'rol' => true,
        'estadoEmpleado' => true
    ]));
});

$app->group('/productos', function (RouteCollectorProxy $group){
    $group->get('', ProductoController::class . ':listarProductos');
    $group->post('', ProductoController::class . ':agregarProducto');
});

$app->group('/mesas', function (RouteCollectorProxy $group){
    $group->get('', MesaController::class . ':listarMesas');
    $group->post('', MesaController::class . ':agregarMesa');
});

/*
$app->group('/login', function (RouteCollectorProxy $group){
    $group->post('', AuthMiddleware::class . ':verificarRol');
})->add(new AuthMiddleware('socio'));*/
