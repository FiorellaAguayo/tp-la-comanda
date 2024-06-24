<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\MesaService;

class MesaController {
    private $mesaService;

    public function __construct(){
        $this->mesaService = new MesaService();
    }

    public function listarMesas(Request $request, Response $response){
        try {
            $mesas = $this->mesaService->obtenerListaMesas();
            $mesasArray = array_map(function($mesa) {
                return [
                    'id' => $mesa->getId(),
                    'estado' => $mesa->getEstado(),
                    'total_facturado' => $mesa->getTotalFacturado(),
                    'importe_mayor' => $mesa->getImporteMayor(),
                    'importe_menor' => $mesa->getImporteMenor(),
                    'cantidad_usada' => $mesa->getCantidadUsada(),
                ];
            }, $mesas);
            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Envio de lista de mesas exitosa', 'data' => $mesasArray]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function agregarMesa(Request $request, Response $response){
        try {
            $data = $request->getParsedBody();
            $id = $data['id'] ?? null;
            $estado = $data['estado'] ?? null;
            $total_facturado = $data['total_facturado'] ?? null;
            $importe_mayor = $data['importe_mayor'] ?? null;
            $importe_menor = $data['importe_menor'] ?? null;
            $cantidad_usada = $data['cantidad_usada'] ?? null;

            if($id !== null && $estado !== null) {
                $mesaExistente = $this->mesaService->obtenerMesaPorId($id);
                if($mesaExistente) {
                    $payload = json_encode(array("message" => "La mesa ya existe"));
                } else {
                    $mesa = $this->mesaService->crearMesa($id, $estado, $total_facturado, $importe_mayor, $importe_menor, $cantidad_usada);
                    $payload = json_encode([
                        'success' => true,
                        'message' => 'Mesa creado exitosamente',
                        'data' => [
                            'id' => $mesa->getId(),
                            'estado' => $mesa->getEstado(),
                            'total_facturado' => $mesa->getTotalFacturado(),
                            'importe_mayor' => $mesa->getImporteMayor(),
                            'importe_menor' => $mesa->getImporteMenor(),
                            'cantidad_usada' => $mesa->getCantidadUsada(),
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

    public function modificarMesa(Request $request, Response $response) {
        try {
            $data = $request->getParsedBody();
            $id = $data['id'] ?? null;

            if ($id !== null) {
                $mesaExistente = $this->mesaService->obtenerMesaPorId($id);

                if ($mesaExistente) {
                    $id = $data['id'] ?? $mesaExistente->getId();
                    $estado = $data['estado'] ?? $mesaExistente->getEstado();
                    $total_facturado = $data['total_facturado'] ?? $mesaExistente->getTotalFacturado();
                    $importe_mayor = $data['importe_mayor'] ?? $mesaExistente->getImporteMayor();
                    $importe_menor = $data['importe_menor'] ?? $mesaExistente->getImporteMenor();
                    $cantidad_usada = $data['cantidad_usada'] ?? $mesaExistente->getCantidadUsada();

                    $mesaModificado = $this->mesaService->modificarMesa($id, $estado, $total_facturado, $importe_mayor, $importe_menor, $cantidad_usada);
                    
                    $payload = json_encode([
                        'success' => true,
                        'message' => 'Mesa modificada exitosamente',
                        'data' => [
                            'id' => $mesaModificado->getId(),
                            'estado' => $mesaModificado->getEstado(),
                            'total_facturado' => $mesaModificado->getTotalFacturado(),
                            'importe_mayor' => $mesaModificado->getImporteMayor(),
                            'importe_menor' => $mesaModificado->getImporteMenor(),
                            'cantidad_usada' => $mesaModificado->getCantidadUsada(),
                        ],
                    ]);
                } else {
                    $payload = json_encode(["message" => "La mesa no existe"]);
                }

                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $payload = json_encode(['success' => false, 'message' => 'Id no proporcionado']);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function eliminarMesa(Request $request, Response $response) {
        try {
            $data = $request->getParsedBody();
            $id = $data['id'] ?? null;

            if ($id !== null) {
                $mesaExistente = $this->mesaService->obtenerMesaPorId($id);

                if ($mesaExistente) {
                    $this->mesaService->eliminarMesa($id);
                    $payload = json_encode(['success' => true, 'message' => 'Mesa eliminada exitosamente']);
                } else {
                    $payload = json_encode(['success' => false, 'message' => 'Mesa no encontrada']);
                }

                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');

            } else {
                $payload = json_encode(['success' => false, 'message' => 'Id no proporcionado']);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}