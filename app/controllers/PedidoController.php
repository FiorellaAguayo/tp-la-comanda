<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerTreasuryAssistant as Request;
use App\Services\PedidoService;

class PedidoController {
    private $pedidoService;

    public function __construct() {
        $this->pedidoService = new PedidoService();
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