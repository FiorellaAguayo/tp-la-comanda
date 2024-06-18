<?php

namespace App\Services;
use App\Data\Database;
use App\Models\Mesa;
use \PDOException;
use \PDO;

class MesaService {
    private $db;

    public function __construct(){
        $this->db = Database::getInstance();
    }

    public function crearMesa($codigo, $estado){
        try {
            $sqlMesa = "INSERT INTO mesas (codigo, estado) VALUES (:codigo, :estado)";
            $stmtMesa = $this->db->prepare($sqlMesa);
            $stmtMesa->bindParam(":codigo", $codigo);
            $stmtMesa->bindParam(":estado", $estado);
            $stmtMesa->execute();
            $mesaId = $this->db->lastInsertId();

            return new Mesa($mesaId, $codigo, $estado);
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public function obtenerMesas(){
        try {
            $sql = "SELECT * FROM mesas";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [];
            foreach ($mesas as $mesa) {
                $resultado[] = new Mesa($mesa['id'], $mesa['codigo'], $mesa['estado']);
            }

            return $resultado;
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    private function obtenerMesaPorId($mesaId) {
        try {
            $sql = "SELECT * FROM mesas WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $mesaId);
            $stmt->execute();
            $mesa = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($mesa) {
                return new Mesa($mesa['id'], $mesa['codigo'], $mesa['estado']);
            }
            return null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
}