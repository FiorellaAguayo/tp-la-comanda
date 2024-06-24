<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\ProductoService;
use App\Models\Producto;

class ProductoController {
    private $productoService;

    public function __construct(){
        $this->productoService = new ProductoService();
    }

    public function listarProductos(Request $request, Response $response){
        try {
            $productos = $this->productoService->obtenerListaProductos();
            $productosArray = array_map(function($producto) {
                return [
                    'nombre' => $producto->getNombre(),
                    'categoria' => $producto->getCategoria(),
                    'sector' => $producto->getSector(),
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
            $data = $request->getParsedBody();
            $nombre = $data['nombre'] ?? null;
            $categoria = $data['categoria'] ?? null;
            $sector = $data['sector'] ?? null;
            $precio = $data['precio'] ?? null;

            if($nombre !== null && $categoria !== null && $sector !== null && $precio !== null) {
                $productoExistente = $this->productoService->obtenerProductoPorNombre($nombre);
                if ($productoExistente) {
                    $payload = json_encode(array("message" => "El producto ya existe"));
                } else {
                    $producto = $this->productoService->crearProducto($nombre, $categoria, $sector, $precio);
                    $payload = json_encode([
                        'success' => true,
                        'message' => 'Producto creado exitosamente',
                        'data' => [
                            'nombre' => $producto->getNombre(),
                            'categoria' => $producto->getCategoria(),
                            'sector' => $producto->getSector(),
                            'precio' => $producto->getPrecio(),
                        ],
                    ]);
                }
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $payload = json_encode(['success' => false, 'message' => 'Datos incompletos']);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function modificarProducto(Request $request, Response $response) {
        try {
            $data = $request->getParsedBody();
            $nombre = $data['nombre'] ?? null;

            if ($nombre !== null) {
                $productoExistente = $this->productoService->obtenerProductoPorNombre($nombre);

                if ($productoExistente) {
                    $nombre = $data['nombre'] ?? $productoExistente->getNombre();
                    $categoria = $data['categoria'] ?? $productoExistente->getCategoria();
                    $sector = $data['sector'] ?? $productoExistente->getSector();
                    $precio = $data['precio'] ?? $productoExistente->getPrecio();

                    $productoModificado = $this->productoService->modificarProducto($nombre, $categoria, $sector, $precio);
                    
                    $payload = json_encode([
                        'success' => true,
                        'message' => 'Producto modificado exitosamente',
                        'data' => [
                            'nombre' => $productoModificado->getNombre(),
                            'categoria' => $productoModificado->getCategoria(),
                            'sector' => $productoModificado->getSector(),
                            'precio' => $productoModificado->getPrecio(),
                        ],
                    ]);
                } else {
                    $payload = json_encode(["message" => "El producto no existe"]);
                }

                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $payload = json_encode(['success' => false, 'message' => 'Nombre no proporcionado']);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function eliminarProducto(Request $request, Response $response) {
        try {
            $data = $request->getParsedBody();
            $nombre = $data['nombre'] ?? null;

            if ($nombre !== null) {
                $productoExistente = $this->productoService->obtenerProductoPorNombre($nombre);

                if ($productoExistente) {
                    $this->productoService->eliminarProducto($nombre);
                    $payload = json_encode(['success' => true, 'message' => 'Producto eliminado exitosamente']);
                } else {
                    $payload = json_encode(['success' => false, 'message' => 'Producto no encontrado']);
                }

                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');

            } else {
                $payload = json_encode(['success' => false, 'message' => 'Nombre no proporcionado']);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}