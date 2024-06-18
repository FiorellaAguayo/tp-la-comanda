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
            $mesas = $this->mesaService->obtenerMesas();
            $mesasArray = array_map(function($mesa) {
                return [
                    'id' => $mesa->getId(),
                    'codigo' => $mesa->getCodigo(),
                    'estado' => $mesa->getEstado(),
                ];
            }, $mesas);
            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Envio de lista de mesas exitosa', 'data' => $mesasArray]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);
        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(500);
        }
    }

    public function agregarMesa(Request $request, Response $response){
        try {
            $json = $request->getBody()->getContents();
            $data = json_decode($json, true);

            $codigo = $data['codigo'] ?? null;
            $estado = $data['estado'] ?? null;

            if($codigo && $estado){
                $mesa = $this->mesaService->crearMesa($codigo, $estado);

                $response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Mesa creada exitosamente',
                    'data' => [
                        'id' => $mesa->getId(),
                        'codigo' => $mesa->getCodigo(),
                        'estado' => $mesa->getEstado(),
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