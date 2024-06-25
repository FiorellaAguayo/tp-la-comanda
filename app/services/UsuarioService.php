<?php

namespace App\Services;
use App\Data\Database;
use App\Models\Usuario;
use App\Models\Empleado;
use App\Models\Socio;
use \PDOException;
use \PDO;

class UsuarioService {
    private $db;

    public function __construct(){
        $this->db = Database::getInstance();
    }

    public function loginUsuario($email, $clave)
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && $clave === $usuario['clave']) {
            return $usuario;
        } else {
            return null;
        }
    }

    public function crearUsuario($nombre, $email, $clave, $rol, $tiempo_estimado, $fecha_ingreso, $estado) {
        $sql = "INSERT INTO usuarios (nombre, email, clave, rol, tiempo_estimado, fecha_ingreso, estado) VALUES (:nombre, :email, :clave, :rol, :tiempo_estimado, :fecha_ingreso, :estado)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':clave', $clave);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':tiempo_estimado', $tiempo_estimado);
        $stmt->bindParam(':fecha_ingreso', $fecha_ingreso);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();
        $id_usuario = $this->db->lastInsertId();

        return new Usuario($id_usuario, $nombre, $email, $clave, $rol, $tiempo_estimado, $fecha_ingreso, $estado);
    }

    public function obtenerUsuarioPorEmail($email) {
        try {
            $sql = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario) {
                return new Usuario($usuario['id'], $usuario['nombre'], $usuario['email'], $usuario['clave'], $usuario['rol'], $usuario['tiempo_estimado'], $usuario['fecha_ingreso'], $usuario['estado']);
            }
            return null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function obtenerListaUsuarios(){
        try {
            $sql = "SELECT * FROM usuarios";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $listaRetorno = [];
            foreach ($usuarios as $usuario) {
                $usuarioInstance = new Usuario($usuario['id'], $usuario['nombre'], $usuario['email'], $usuario['clave'], $usuario['rol'], $usuario['tiempo_estimado'], $usuario['fecha_ingreso'], $usuario['estado']);
                array_push($listaRetorno, $usuarioInstance);
            }
            return $listaRetorno;
        } catch(PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function modificarUsuario($id, $nombre, $email, $clave, $rol, $tiempo_estimado, $fecha_ingreso, $estado) {
        $sql = "UPDATE usuarios SET ";
        $fields = [];
        if (!is_null($nombre)) $fields[] = "nombre = :nombre";
        if (!is_null($email)) $fields[] = "email = :email";
        if (!is_null($clave)) $fields[] = "clave = :clave";
        if (!is_null($rol)) $fields[] = "rol = :rol";
        if (!is_null($tiempo_estimado)) $fields[] = "tiempo_estimado = :tiempo_estimado";
        if (!is_null($fecha_ingreso)) $fields[] = "fecha_ingreso = :fecha_ingreso";
        if (!is_null($estado)) $fields[] = "estado = :estado";
        $sql .= implode(", ", $fields);
        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        if (!is_null($nombre)) $stmt->bindParam(':nombre', $nombre);
        if (!is_null($email)) $stmt->bindParam(':email', $email);
        if (!is_null($clave)) $stmt->bindParam(':clave', $clave);
        if (!is_null($rol)) $stmt->bindParam(':rol', $rol);
        if (!is_null($tiempo_estimado)) $stmt->bindParam(':tiempo_estimado', $tiempo_estimado);
        if (!is_null($fecha_ingreso)) $stmt->bindParam(':fecha_ingreso', $fecha_ingreso);
        if (!is_null($estado)) $stmt->bindParam(':estado', $estado);
        $stmt->execute();

        return new Usuario($id, $nombre, $email, $clave, $rol, $tiempo_estimado, $fecha_ingreso, $estado);
    }

    public function eliminarUsuario($id) {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}