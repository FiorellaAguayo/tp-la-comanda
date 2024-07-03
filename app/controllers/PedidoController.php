<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\PedidoService;
use App\Services\UsuarioService;
use App\Services\DetalleService;


class PedidoController {
    private $pedidoService;
    private $usuarioService;
    private $detalleService;

    public function __construct(){
        $this->pedidoService = new PedidoService();
        $this->usuarioService = new UsuarioService();
        $this->detalleService = new DetalleService(); 
    }

    public function listarPedidos(Request $request, Response $response){
        try {
            $rolUsuario = $request->getAttribute('rolUsuario');
            $emailUsuario = $request->getAttribute('emailUsuario');
            $sectorUsuario = $this->usuarioService->obtenerSectorPorEmail($emailUsuario);

            $sector = '';
            if ($rolUsuario == 'cervecero') {
                $sector = 'barra de choperas';
            } elseif ($rolUsuario == 'bartender') {
                $sector = 'barra de tragos y vinos';
            } elseif ($rolUsuario == 'cocinero') {
                if ($sectorUsuario == 'cocina') {
                    $sector = 'cocina';
                } elseif ($sectorUsuario == 'candy bar') {
                    $sector = 'candy bar';
                }
            }

            $pedidos = $this->pedidoService->obtenerListaPedidosPorSector($sector);
            $pedidosArray = array_map(function($pedido) {
                return [
                    'id' => $pedido->getId(),
                    'nombre_cliente' => $pedido->getNombreCliente(),
                    'id_mesa' => $pedido->getIdMesa(),
                    'estado' => $pedido->getEstado(),
                    'tiempo_estimado' => $pedido->getTiempoEstimado(),
                    'producto' => $pedido->getProducto(),
                    'sector' => $pedido->getSector()
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
            $id = $data['id'] ?? null;
            $nombre_cliente = $data['nombre_cliente'] ?? null;
            $id_mesa = $data['id_mesa'] ?? null;
            $estado = $data['estado'] ?? null;
            $tiempo_estimado = $data['tiempo_estimado'] ?? null;
            $producto = $data['producto'] ?? null;
            $sector = $data['sector'] ?? null;

            if ($id !== null && $nombre_cliente !== null && $id_mesa !== null && $estado !== null && $tiempo_estimado !== null && $producto !== null && $sector !== null) {
                $pedido = $this->pedidoService->crearPedido($id, $nombre_cliente, $id_mesa, $estado, $tiempo_estimado, $producto, $sector);

                // Crear el detalle con tiempo_demora inicial en 0
                $this->detalleService->crearDetalle($id_mesa, $id, 0);

                $payload = json_encode([
                    'success' => true,
                    'message' => 'Pedido creado exitosamente',
                    'data' => [
                        'id' => $pedido->getId(),
                        'nombre_cliente' => $pedido->getNombreCliente(),
                        'id_mesa' => $pedido->getIdMesa(),
                        'estado' => $pedido->getEstado(),
                        'tiempo_estimado' => $pedido->getTiempoEstimado(),
                        'producto' => $pedido->getProducto(),
                        'sector' => $pedido->getSector(),
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
            $producto = $data['producto'] ?? null;
            $estado = $data['estado'] ?? null;
            $tiempo_estimado = $data['tiempo_estimado'] ?? null;

            if ($id !== null && $producto !== null) {
                $pedidoExistente = $this->pedidoService->obtenerPedidoPorIdYProducto($id, $producto);

                if ($pedidoExistente) {
                    $estado = $estado ?? $pedidoExistente->getEstado();
                    $tiempo_estimado = $tiempo_estimado ?? $pedidoExistente->getTiempoEstimado();

                    $pedidoModificado = $this->pedidoService->modificarPedido(
                        $pedidoExistente->getId(),
                        $producto,
                        $estado,
                        $tiempo_estimado
                    );

                    // Si el estado es "en preparacion", acumular el tiempo_estimado
                    if ($estado == 'en preparacion') {
                        $id_mesa = $pedidoExistente->getIdMesa();
                        $detalleExistente = $this->detalleService->obtenerDetallePorMesa($id_mesa);

                        if ($detalleExistente) {
                            $nuevoTiempoDemora = $detalleExistente->getTiempoDemora() + $tiempo_estimado;
                            $this->detalleService->actualizarDetalle($id_mesa, $nuevoTiempoDemora);
                        }
                    }

                    $payload = json_encode([
                        'success' => true,
                        'message' => 'Pedido modificado exitosamente',
                        'data' => [
                            'id' => $pedidoModificado->getId(),
                            'nombre_cliente' => $pedidoModificado->getNombreCliente(),
                            'id_mesa' => $pedidoModificado->getIdMesa(),
                            'estado' => $pedidoModificado->getEstado(),
                            'tiempo_estimado' => $pedidoModificado->getTiempoEstimado(),
                            'producto' => $pedidoModificado->getProducto(),
                            'sector' => $pedidoModificado->getSector()
                        ],
                    ]);
                } else {
                    $payload = json_encode(["message" => "El pedido no existe"]);
                }

                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $payload = json_encode(['success' => false, 'message' => 'ID o Producto no proporcionado']);
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
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $estado = $data['estado'] ?? null;
            $pedidoId = $data['pedidoId'] ?? null;
            $tiempo_estimado = $data['tiempo_estimado'] ?? null;

            if (!$estado || !$pedidoId || !$tiempo_estimado) {
                $response->getBody()->write(json_encode(['success' => false, 'message' => 'Datos incompletos']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $pedidoExistente = $this->pedidoService->obtenerPedidoPorId($pedidoId);
            if ($pedidoExistente) {
                $this->pedidoService->actualizarEstado($pedidoId, $estado);

                // Si el estado es "en preparacion", acumular el tiempo_estimado
                if ($estado == 'en preparacion') {
                    $id_mesa = $pedidoExistente->getIdMesa();
                    $detalleExistente = $this->detalleService->obtenerDetallePorMesa($id_mesa);

                    if ($detalleExistente) {
                        $nuevoTiempoDemora = $detalleExistente->getTiempoDemora() + $tiempo_estimado;
                        $this->detalleService->actualizarDetalle($id_mesa, $nuevoTiempoDemora);
                    }
                }

                $response->getBody()->write(json_encode(['success' => true, 'message' => 'Estado del pedido actualizado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['success' => false, 'message' => 'Pedido no encontrado']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    
}