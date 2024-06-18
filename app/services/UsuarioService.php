<?php

namespace App\Services;
use App\Data\Database;
use App\Models\Usuario;
use App\Models\Empleado;
use \PDOException;
use \PDO;

class UsuarioService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function crearUsuario($nombreUsuario, $clave, $nombreEmpleado, $rol, $estadoEmpleado){
        try {
            $sqlEmpleado = "INSERT INTO empleados (nombre_empleado, rol, estado) VALUES (:nombreEmpleado, :rol, :estado)";
            $stmtEmpleado = $this->db->prepare($sqlEmpleado);
            $stmtEmpleado->bindParam(":nombreEmpleado", $nombreEmpleado);
            $stmtEmpleado->bindParam(":rol", $rol);
            $stmtEmpleado->bindParam(":estado", $estadoEmpleado);
            $stmtEmpleado->execute();
            $empleadoId = $this->db->lastInsertId();

            $empleado = new Empleado($empleadoId, $nombreEmpleado, $rol, $estadoEmpleado);

            $sqlUsuario = "INSERT INTO usuarios (nombre_usuario, clave, empleado_id) VALUES (:nombreUsuario, :clave, :empleadoId)";
            $stmtUsuario = $this->db->prepare($sqlUsuario);
            $stmtUsuario->bindParam(":nombreUsuario", $nombreUsuario);
            $stmtUsuario->bindParam(":clave", $clave);
            $stmtUsuario->bindParam(":empleadoId", $empleadoId);
            $stmtUsuario->execute();
            $usuarioId = $this->db->lastInsertId();

            return new Usuario($usuarioId, $nombreUsuario, $clave, $empleado);
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public function obtenerUsuarios(){
        try {
            $sql = "SELECT * FROM usuarios";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [];
            foreach ($usuarios as $usuario) {
                $empleado = $this->obtenerEmpleadoPorId($usuario['empleado_id']);
                $resultado[] = new Usuario($usuario['id'], $usuario['nombre_usuario'], $usuario['clave'], $empleado);
            }

            return $resultado;
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    private function obtenerEmpleadoPorId($empleadoId) {
        try {
            $sql = "SELECT * FROM empleados WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $empleadoId);
            $stmt->execute();
            $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($empleado) {
                return new Empleado($empleado['id'], $empleado['nombre_empleado'], $empleado['rol'], $empleado['estado']);
            }
            return null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
}
