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

    public function crearPedido($id, $nombre_cliente, $id_mesa, $estado, $tiempo_estimado, $producto, $sector) {
        $sql = "INSERT INTO pedidos (id, nombre_cliente, id_mesa, estado, tiempo_estimado, producto, sector) VALUES (:id, :nombre_cliente, :id_mesa, :estado, :tiempo_estimado, :producto, :sector)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre_cliente', $nombre_cliente);
        $stmt->bindParam(':id_mesa', $id_mesa);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':tiempo_estimado', $tiempo_estimado);
        $stmt->bindParam(':producto', $producto);
        $stmt->bindParam(':sector', $sector);
        $stmt->execute();

        return new Pedido($id, $nombre_cliente, $id_mesa, $estado, $tiempo_estimado, $producto, $sector);
    }

    public function obtenerPedidoPorIdYProducto($id, $producto) {
        try {
            $sql = "SELECT * FROM pedidos WHERE id = :id AND producto = :producto";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':producto', $producto);
            $stmt->execute();
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($pedido) {
                return new Pedido($pedido['id'], $pedido['nombre_cliente'], $pedido['id_mesa'], $pedido['estado'], $pedido['tiempo_estimado'], $pedido['producto'], $pedido['sector']);
            }
            return null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function obtenerListaPedidosPorRol($rol) {
        try {
            $sql = "SELECT * FROM pedidos";
            if ($rol == 'bartender') {
                $sql .= " WHERE producto = 'bebida'";
            } elseif ($rol == 'cocinero') {
                $sql .= " WHERE producto = 'comida'";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $listaRetorno = [];
            foreach ($pedidos as $pedido) {
                $pedidoInstance = new Pedido($pedido['id'], $pedido['nombre_cliente'], $pedido['id_mesa'], $pedido['estado'], $pedido['tiempo_estimado'], $pedido['producto'], $pedido['sector']);
                array_push($listaRetorno, $pedidoInstance);
            }
            return $listaRetorno;
        } catch(PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function modificarPedido($id, $producto, $estado, $tiempo_estimado) {
        try {
            $sql = "UPDATE pedidos SET estado = :estado, tiempo_estimado = :tiempo_estimado WHERE id = :id AND producto = :producto LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':tiempo_estimado', $tiempo_estimado);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':producto', $producto);
            $stmt->execute();

            $sql = "SELECT * FROM pedidos WHERE id = :id AND producto = :producto LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':producto', $producto);
            $stmt->execute();
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($pedido) {
                return new Pedido($pedido['id'], $pedido['nombre_cliente'], $pedido['id_mesa'], $pedido['estado'], $pedido['tiempo_estimado'], $pedido['producto'], $pedido['sector']);
            }

            return null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function eliminarPedido($id) {
        $sql = "DELETE FROM pedidos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

  
    public function obtenerListaPedidosPorSector($sector) {
        try {
            $sql = "SELECT p.* FROM pedidos p
                    WHERE p.sector = :sector";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':sector', $sector);
            $stmt->execute();
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $listaRetorno = [];
            foreach ($pedidos as $pedido) {
                $pedidoInstance = new Pedido($pedido['id'], $pedido['nombre_cliente'], $pedido['id_mesa'], $pedido['estado'], $pedido['tiempo_estimado'], $pedido['producto'], $pedido['sector']);
                array_push($listaRetorno, $pedidoInstance);
            }

            return $listaRetorno;
        } catch(PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

}