<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\UsuarioService;

class UsuarioController {
    private $usuarioService;

    public function __construct(){
        $this->usuarioService = new UsuarioService();
    }

    public function listarUsuarios(Request $request, Response $response){
        try {
            $usuarios = $this->usuarioService->obtenerUsuarios();
            $usuariosArray = array_map(function($usuario) {
                return [
                    'id' => $usuario->getId(),
                    'nombreUsuario' => $usuario->getNombreUsuario(),
                    'empleado' => [
                        'id' => $usuario->getEmpleado()->getId(),
                        'nombreEmpleado' => $usuario->getEmpleado()->getNombreEmpleado(),
                        'rol' => $usuario->getEmpleado()->getRol(),
                        'estado' => $usuario->getEmpleado()->getEstado(),
                    ],
                ];
            }, $usuarios);
            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Envio de lista de usuarios exitosa', 'data' => $usuariosArray]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);
        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(500);
        }
    }

    public function agregarUsuario(Request $request, Response $response){
        try {
            $json = $request->getBody()->getContents();
            $data = json_decode($json, true);

            $nombreUsuario = $data['nombreUsuario'] ?? null;
            $clave = $data['clave'] ?? null;
            $nombreEmpleado = $data['nombreEmpleado'] ?? null;
            $rol = $data['rol'] ?? null;
            $estadoEmpleado = $data['estadoEmpleado'] ?? null;

            if ($nombreUsuario && $clave && $nombreEmpleado && $rol && $estadoEmpleado) {
                $usuario = $this->usuarioService->crearUsuario($nombreUsuario, $clave, $nombreEmpleado, $rol, $estadoEmpleado);

                $response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Usuario creado exitosamente',
                    'data' => [
                        'id' => $usuario->getId(),
                        'nombreUsuario' => $usuario->getNombreUsuario(),
                        'empleado' => [
                            'id' => $usuario->getEmpleado()->getId(),
                            'nombreEmpleado' => $usuario->getEmpleado()->getNombreEmpleado(),
                            'rol' => $usuario->getEmpleado()->getRol(),
                            'estado' => $usuario->getEmpleado()->getEstado(),
                        ],
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
