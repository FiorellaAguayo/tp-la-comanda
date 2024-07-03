<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\UsuarioService;
use App\Models\Usuario;

class UsuarioController {
    private $usuarioService;

    public function __construct(){
        $this->usuarioService = new UsuarioService();
    }

    public function listarUsuarios(Request $request, Response $response){
        try {
            $usuarios = $this->usuarioService->obtenerListaUsuarios();
            $usuariosArray = array_map(function($usuario) {
                return [
                    'id' => $usuario->getIdUsuario(),
                    'nombre' => $usuario->getNombre(),
                    'email' => $usuario->getEmail(),
                    'clave' => $usuario->getClave(),
                    'rol' => $usuario->getRol(),
                    'sector' => $usuario->getSector(),
                    'tiempo_estimado' => $usuario->getTiempoEstimado(),
                    'fecha_ingreso' => $usuario->getFechaIngreso(),
                    'estado' => $usuario->getEstado(),
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

    public function agregarUsuario(Request $request, Response $response) {
        try {
            $data = $request->getParsedBody();
            $nombre = $data['nombre'] ?? null;
            $email = $data['email'] ?? null;
            $clave = $data['clave'] ?? null;
            $rol = $data['rol'] ?? null;
            $sector = $data['sector'] ?? null;
            $tiempo_estimado = $data['tiempo_estimado'] ?? null;
            $fecha_ingreso = $data['fecha_ingreso'] ?? null;
            $estado = $data['estado'] ?? null;
            
            if ($nombre !== null && $email !== null && $clave !== null && $rol !== null && $sector !== null && $tiempo_estimado !== null && $fecha_ingreso !== null && $estado !== null) {
                $usuarioExistente = $this->usuarioService->obtenerUsuarioPorEmail($email);

                if ($usuarioExistente) {
                    $payload = json_encode(array("message" => "El usuario ya existe"));

                } else {
                    $usuario = $this->usuarioService->crearUsuario($nombre, $email, $clave, $rol, $sector, $tiempo_estimado, $fecha_ingreso, $estado);
                    $payload = json_encode([
                        'success' => true,
                        'message' => 'Usuario creado exitosamente',
                        'data' => [
                            'id' => $usuario->getIdUsuario(),
                            'nombre' => $usuario->getNombre(),
                            'email' => $usuario->getEmail(),
                            'clave' => $usuario->getClave(),
                            'rol' => $usuario->getRol(),
                            'sector' => $usuario->getSector(),
                            'tiempo_estimado' => $usuario->getTiempoEstimado(),
                            'fecha_ingreso' => $usuario->getFechaIngreso(),
                            'estado' => $usuario->getEstado(),
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

    public function modificarUsuario(Request $request, Response $response) {
        try {
            $data = $request->getParsedBody();
            $email = $data['email'] ?? null;

            if ($email !== null) {
                $usuarioExistente = $this->usuarioService->obtenerUsuarioPorEmail($email);

                if ($usuarioExistente) {
                    $nombre = $data['nombre'] ?? $usuarioExistente->getNombre();
                    $clave = $data['clave'] ?? $usuarioExistente->getClave();
                    $rol = $data['rol'] ?? $usuarioExistente->getRol();
                    $sector = $data['sector'] ?? $usuarioExistente->getSector();
                    $tiempo_estimado = $data['tiempo_estimado'] ?? $usuarioExistente->getTiempoEstimado();
                    $fecha_ingreso = $data['fecha_ingreso'] ?? $usuarioExistente->getFechaIngreso();
                    $estado = $data['estado'] ?? $usuarioExistente->getEstado();

                    $usuarioModificado = $this->usuarioService->modificarUsuario(
                        $usuarioExistente->getIdUsuario(),
                        $nombre,
                        $email,
                        $clave,
                        $rol,
                        $sector,
                        $tiempo_estimado,
                        $fecha_ingreso,
                        $estado
                    );
                    $payload = json_encode([
                        'success' => true,
                        'message' => 'Usuario modificado exitosamente',
                        'data' => [
                            'id' => $usuarioModificado->getIdUsuario(),
                            'nombre' => $usuarioModificado->getNombre(),
                            'email' => $usuarioModificado->getEmail(),
                            'clave' => $usuarioModificado->getClave(),
                            'rol' => $usuarioModificado->getRol(),
                            'sector' => $usuarioModificado->getSector(),
                            'tiempo_estimado' => $usuarioModificado->getTiempoEstimado(),
                            'fecha_ingreso' => $usuarioModificado->getFechaIngreso(),
                            'estado' => $usuarioModificado->getEstado(),
                        ],
                    ]);
                } else {
                    $payload = json_encode(["message" => "El usuario no existe"]);
                }

                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $payload = json_encode(['success' => false, 'message' => 'Email no proporcionado']);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function eliminarUsuario(Request $request, Response $response) {
        try {
            $data = $request->getParsedBody();
            $email = $data['email'] ?? null;

            if ($email !== null) {
                $usuarioExistente = $this->usuarioService->obtenerUsuarioPorEmail($email);

                if ($usuarioExistente) {
                    $this->usuarioService->eliminarUsuario($usuarioExistente->getIdUsuario());
                    $payload = json_encode(['success' => true, 'message' => 'Usuario eliminado exitosamente']);
                } else {
                    $payload = json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                }

                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');

            } else {
                $payload = json_encode(['success' => false, 'message' => 'Email no proporcionado']);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

        } catch(\Exception $e){
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}