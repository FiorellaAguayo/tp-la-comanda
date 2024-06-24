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

    function generarCodigoAlfanumerico($longitud) {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigo = '';
        for ($i = 0; $i < $longitud; $i++) {
            $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        return $codigo;
    }    

    public function crearMesa($id, $estado, $total_facturado, $importe_mayor, $importe_menor, $cantidad_usada) {
        try {
            $sql = "INSERT INTO mesas (id, estado, total_facturado, importe_mayor, importe_menor, cantidad_usada) VALUES (:id, :estado, :total_facturado, :importe_mayor, :importe_menor, :cantidad_usada)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":estado", $estado);
            $stmt->bindParam(":total_facturado", $total_facturado);
            $stmt->bindParam(":importe_mayor", $importe_mayor);
            $stmt->bindParam(":importe_menor", $importe_menor);
            $stmt->bindParam(":cantidad_usada", $cantidad_usada);
            $stmt->execute();
    
            return new Mesa($id, $estado, $total_facturado, $importe_mayor, $importe_menor, $cantidad_usada);
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }
    
    public function obtenerListaMesas() {
        try {
            $sql = "SELECT * FROM mesas";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $listaRetorno = [];
            foreach ($mesas as $mesa) {
                $mesaInstance = new Mesa($mesa['id'], $mesa['estado'], $mesa['total_facturado'], $mesa['importe_mayor'], $mesa['importe_menor'], $mesa['cantidad_usada']);
                array_push($listaRetorno, $mesaInstance);
            }

            return $listaRetorno;
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public function obtenerMesaPorId($mesaId) {
        try {
            $sql = "SELECT * FROM mesas WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $mesaId);
            $stmt->execute();
            $mesa = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($mesa) {
                return new Mesa($mesa['id'], $mesa['estado'], $mesa['total_facturado'], $mesa['importe_mayor'], $mesa['importe_menor'], $mesa['cantidad_usada']);
            }
            return null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function modificarMesa($id, $estado, $total_facturado, $importe_mayor, $importe_menor, $cantidad_usada) {
        $sql = "UPDATE mesas SET ";
        $fields = [];
        if (!is_null($estado)) $fields[] = "estado = :estado";
        if (!is_null($total_facturado)) $fields[] = "total_facturado = :total_facturado";
        if (!is_null($importe_mayor)) $fields[] = "importe_mayor = :importe_mayor";
        if (!is_null($importe_menor)) $fields[] = "importe_menor = :importe_menor";
        if (!is_null($cantidad_usada)) $fields[] = "cantidad_usada = :cantidad_usada";
        $sql .= implode(", ", $fields);
        $sql .= " WHERE id = :id";
    
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        if (!is_null($estado)) $stmt->bindParam(':estado', $estado);
        if (!is_null($total_facturado)) $stmt->bindParam(':total_facturado', $total_facturado);
        if (!is_null($importe_mayor)) $stmt->bindParam(':importe_mayor', $importe_mayor);
        if (!is_null($importe_menor)) $stmt->bindParam(':importe_menor', $importe_menor);
        if (!is_null($cantidad_usada)) $stmt->bindParam(':cantidad_usada', $cantidad_usada);
        $stmt->execute();
        return new Mesa($id, $estado, $total_facturado, $importe_mayor, $importe_menor, $cantidad_usada);
    }
    

    public function eliminarMesa($id) {
        $sql = "DELETE FROM mesas WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}