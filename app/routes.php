<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\UsuarioController;
use App\Controllers\ProductoController;
use App\Controllers\MesaController;

$app->group('/usuarios', function (RouteCollectorProxy $group){
    $group->get('', UsuarioController::class . ':listarUsuarios');
    $group->post('', UsuarioController::class . ':agregarUsuario');
});

$app->group('/productos', function (RouteCollectorProxy $group){
    $group->get('', ProductoController::class . ':listarProductos');
    $group->post('', ProductoController::class . ':agregarProducto');
});

$app->group('/mesas', function (RouteCollectorProxy $group){
    $group->get('', MesaController::class . ':listarMesas');
    $group->post('', MesaController::class . ':agregarMesa');
});