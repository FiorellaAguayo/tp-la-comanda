<?php

namespace App\Services;
use App\Data\Database;
use App\Models\Pedido;
use \PDOException;
use \PDO;

class PedidoService {
    private $db;

    public function __construct(){
        $this->db = Database::getInstance();
    }

    private function generarIdAleatorio($longitud = 5) {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $id = '';
        for ($i = 0; $i < $longitud; $i++) {
            $id .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        return $id;
    }

    public function crearPedido($nombre_cliente, $id_mesa, $estado, $tiempo_estimado, $nombre_productos) {
        $id = $this->generarIdAleatorio();
        $sql = "INSERT INTO pedidos (id, nombre_cliente, id_mesa, estado, tiempo_estimado, nombre_productos) VALUES (:id, :nombre_cliente, :id_mesa, :estado, :tiempo_estimado, :nombre_productos)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre_cliente', $nombre_cliente);
        $stmt->bindParam(':id_mesa', $id_mesa);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':tiempo_estimado', $tiempo_estimado);
        $stmt->bindParam(':nombre_productos', $nombre_productos);
        $stmt->execute();

        return new Pedido($id, $nombre_cliente, $id_mesa, $estado, $tiempo_estimado, $nombre_productos);
    }

    public function obtenerPedidoPorId($id) {
        try {
            $sql = "SELECT * FROM pedidos WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($pedido) {
                return new Pedido($pedido['id'], $pedido['nombre_cliente'], $pedido['id_mesa'], $pedido['estado'], $pedido['tiempo_estimado'], $pedido['nombre_productos']);
            }
            return null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function obtenerListaPedidos() {
        try {
            $sql = "SELECT * FROM pedidos";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $listaRetorno = [];
            foreach ($pedidos as $pedido) {
                $pedidoInstance = new Pedido($pedido['id'], $pedido['nombre_cliente'], $pedido['id_mesa'], $pedido['estado'], $pedido['tiempo_estimado'], $pedido['nombre_productos']);
                array_push($listaRetorno, $pedidoInstance);
            }
            return $listaRetorno;
        } catch(PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function modificarPedido($id, $nombre_cliente, $id_mesa, $estado, $tiempo_estimado, $nombre_productos) {
        $sql = "UPDATE pedidos SET ";
        $fields = [];
        if (!is_null($nombre_cliente)) $fields[] = "nombre_cliente = :nombre_cliente";
        if (!is_null($id_mesa)) $fields[] = "id_mesa = :id_mesa";
        if (!is_null($estado)) $fields[] = "estado = :estado";
        if (!is_null($tiempo_estimado)) $fields[] = "tiempo_estimado = :tiempo_estimado";
        if (!is_null($nombre_productos)) $fields[] = "nombre_productos = :nombre_productos";
        $sql .= implode(", ", $fields);
        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        if (!is_null($nombre_cliente)) $stmt->bindParam(':nombre_cliente', $nombre_cliente);
        if (!is_null($id_mesa)) $stmt->bindParam(':id_mesa', $id_mesa);
        if (!is_null($estado)) $stmt->bindParam(':estado', $estado);
        if (!is_null($tiempo_estimado)) $stmt->bindParam(':tiempo_estimado', $tiempo_estimado);
        if (!is_null($nombre_productos)) $stmt->bindParam(':nombre_productos', $nombre_productos);
        $stmt->execute();

        return new Pedido($id, $nombre_cliente, $id_mesa, $estado, $tiempo_estimado, $nombre_productos);
    }

    public function eliminarPedido($id) {
        $sql = "DELETE FROM pedidos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}