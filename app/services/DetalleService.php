<?php

namespace App\Services;
use App\Data\Database;
use App\Models\Detalle;
use \PDOException;
use \PDO;

class DetalleService {
    private $db;

    public function __construct(){
        $this->db = Database::getInstance();
    }

    public function crearDetalle($id_mesa, $id_pedido, $tiempo_demora) {
        try {
            $sql = "INSERT INTO detalles (id_mesa, id_pedido, tiempo_demora) VALUES (:id_mesa, :id_pedido, :tiempo_demora)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id_mesa", $id_mesa);
            $stmt->bindParam(":id_pedido", $id_pedido);
            $stmt->bindParam(":tiempo_demora", $tiempo_demora);
            $stmt->execute();
    
            return new Detalle($id_mesa, $id_pedido, $tiempo_demora);
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public function obtenerDetallePorMesa($id_mesa) {
        try {
            $sql = "SELECT * FROM detalles WHERE id_mesa = :id_mesa";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id_mesa", $id_mesa);
            $stmt->execute();
            $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($detalle) {
                return new Detalle($detalle['id_mesa'], $detalle['id_pedido'], $detalle['tiempo_demora']);
            }
            return null;
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public function actualizarDetalle($id_mesa, $tiempo_demora) {
        try {
            $sql = "UPDATE detalles SET tiempo_demora = :tiempo_demora WHERE id_mesa = :id_mesa";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":tiempo_demora", $tiempo_demora);
            $stmt->bindParam(":id_mesa", $id_mesa);
            $stmt->execute();
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public function obtenerListaDetalles() {
        try {
            $sql = "SELECT * FROM detalles";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $listaRetorno = [];
            foreach ($detalles as $detalle) {
                $detalleInstance = new Detalle($detalle['id_mesa'], $detalle['id_pedido'], $detalle['tiempo_demora']);
                array_push($listaRetorno, $detalleInstance);
            }

            return $listaRetorno;
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public function eliminarDetalle($id_mesa, $id_pedido) {
        try {
            $sql = "DELETE FROM detalles WHERE id_mesa = :id_mesa AND id_pedido = :id_pedido";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_mesa', $id_mesa);
            $stmt->bindParam(':id_pedido', $id_pedido);
            $stmt->execute();
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }
}