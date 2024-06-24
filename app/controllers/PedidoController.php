<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\PedidoService;

class PedidoController {
    private $pedidoService;

    public function __construct(){
        $this->pedidoService = new PedidoService();
    }

    public function listarPedidos(Request $request, Response $response){
        try {
            $pedidos = $this->pedidoService->obtenerListaPedidos();
            $pedidosArray = array_map(function($pedido) {
                return [
                    'id' => $pedido->getId(),
                    'nombre_cliente' => $pedido->getNombreCliente(),
                    'id_mesa' => $pedido->getIdMesa(),
                    'estado' => $pedido->getEstado(),
                    'tiempo_estimado' => $pedido->getTiempoEstimado(),
                    'nombre_productos' => $pedido->getNombreProductos(),
                ];
            }, $pedidos);
            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Envio de lista de pedidos exitosa', 'data' => $pedidosArray]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);
        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(500);
        }
    }

    public function agregarPedido(Request $request, Response $response) {
        try {
            $data = $request->getParsedBody();
            $nombre_cliente = $data['nombre_cliente'] ?? null;
            $id_mesa = $data['id_mesa'] ?? null;
            $estado = $data['estado'] ?? null;
            $tiempo_estimado = $data['tiempo_estimado'] ?? null;
            $nombre_productos = $data['nombre_productos'] ?? null;
            
            if ($nombre_cliente !== null && $id_mesa !== null && $estado !== null && $tiempo_estimado !== null && $nombre_productos !== null) {
                $pedido = $this->pedidoService->crearPedido($nombre_cliente, $id_mesa, $estado, $tiempo_estimado, $nombre_productos);
                $payload = json_encode([
                    'success' => true,
                    'message' => 'Pedido creado exitosamente',
                    'data' => [
                        'id' => $pedido->getId(),
                        'nombre_cliente' => $pedido->getNombreCliente(),
                        'id_mesa' => $pedido->getIdMesa(),
                        'estado' => $pedido->getEstado(),
                        'tiempo_estimado' => $pedido->getTiempoEstimado(),
                        'nombre_productos' => $pedido->getNombreProductos(),
                    ],
                ]);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')
                                ->withStatus(201);
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

    public function modificarPedido(Request $request, Response $response) {
        try {
            $data = $request->getParsedBody();
            $id = $data['id'] ?? null;

            if ($id !== null) {
                $pedidoExistente = $this->pedidoService->obtenerPedidoPorId($id);

                if ($pedidoExistente) {
                    $nombre_cliente = $data['nombre_cliente'] ?? $pedidoExistente->getNombreCliente();
                    $id_mesa = $data['id_mesa'] ?? $pedidoExistente->getIdMesa();
                    $estado = $data['estado'] ?? $pedidoExistente->getEstado();
                    $tiempo_estimado = $data['tiempo_estimado'] ?? $pedidoExistente->getTiempoEstimado();
                    $nombre_productos = $data['nombre_productos'] ?? $pedidoExistente->getNombreProductos();

                    $pedidoModificado = $this->pedidoService->modificarPedido(
                        $pedidoExistente->getId(),
                        $nombre_cliente,
                        $id_mesa,
                        $estado,
                        $tiempo_estimado,
                        $nombre_productos
                    );
                    $payload = json_encode([
                        'success' => true,
                        'message' => 'Pedido modificado exitosamente',
                        'data' => [
                            'id' => $pedidoModificado->getId(),
                            'nombre_cliente' => $pedidoModificado->getNombreCliente(),
                            'id_mesa' => $pedidoModificado->getIdMesa(),
                            'estado' => $pedidoModificado->getEstado(),
                            'tiempo_estimado' => $pedidoModificado->getTiempoEstimado(),
                            'nombre_productos' => $pedidoModificado->getNombreProductos(),
                        ],
                    ]);
                } else {
                    $payload = json_encode(["message" => "El pedido no existe"]);
                }

                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $payload = json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function eliminarPedido(Request $request, Response $response) {
        try {
            $data = $request->getParsedBody();
            $id = $data['id'] ?? null;

            if ($id !== null) {
                $pedidoExistente = $this->pedidoService->obtenerPedidoPorId($id);

                if ($pedidoExistente) {
                    $this->pedidoService->eliminarPedido($id);
                    $payload = json_encode(['success' => true, 'message' => 'Pedido eliminado exitosamente']);
                } else {
                    $payload = json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
                }

                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $payload = json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function actualizarEstado(Request $request, Response $response) {
        $data = json_decode($request->getBody()->getContents(), true);
        $estado = $data['estado'] ?? null;
        $pedidoId = $data['pedidoId'] ?? null;

        if (!$estado || !$pedidoId) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Datos incompletos']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $resultado = $this->pedidoService->actualizarEstado($pedidoId, $estado);
        if ($resultado) {
            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Estado del pedido actualizado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Error al actualizar estado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}