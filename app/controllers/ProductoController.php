<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\ProductoService;

class ProductoController {
    private $productoService;

    public function __construct(){
        $this->productoService = new ProductoService();
    }

    public function listarProductos(Request $request, Response $response){
        try {
            $productos = $this->productoService->obtenerProductos();
            $productosArray = array_map(function($producto) {
                return [
                    'id' => $producto->getId(),
                    'nombre' => $producto->getNombre(),
                    'categoria' => $producto->getCategoria(),
                    'precio' => $producto->getPrecio(),
                ];
            }, $productos);
            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Envio de lista de productos exitosa', 'data' => $productosArray]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);
        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(500);
        }
    }

    public function agregarProducto(Request $request, Response $response){
        try {
            $json = $request->getBody()->getContents();
            $data = json_decode($json, true);

            $nombre = $data['nombre'] ?? null;
            $categoria = $data['categoria'] ?? null;
            $precio = $data['precio'] ?? null;

            if($nombre && $categoria && $precio){
                $producto = $this->productoService->crearProducto($nombre, $categoria, $precio);

                $response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Producto creado exitosamente',
                    'data' => [
                        'id' => $producto->getId(),
                        'nombre' => $producto->getNombre(),
                        'categoria' => $producto->getCategoria(),
                        'precio' => $producto->getPrecio(),
                    ],
                ]));
                return $response->withHeader('Content-Type', 'application/json')
                                ->withStatus(201);
            } else {
                $response->getBody()->write(json_encode(['success' => false, 'message' => 'Datos incompletos']));
                return $response->withHeader('Content-Type', 'application/json')
                                ->withStatus(400);
            }

        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}