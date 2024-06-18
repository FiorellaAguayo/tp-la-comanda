<?php

namespace App\Services;
use App\Data\Database;
use App\Models\Producto;
use \PDOException;
use \PDO;

class ProductoService {
    private $db;

    public function __construct(){
        $this->db = Database::getInstance();
    }

    public function crearProducto($nombre, $categoria, $precio){
        try {
            // Crear producto
            $sqlProducto = "INSERT INTO productos (nombre, categoria, precio) VALUES (:nombre, :categoria, :precio)";
            $stmtProducto = $this->db->prepare($sqlProducto);
            $stmtProducto->bindParam(":nombre", $nombre);
            $stmtProducto->bindParam(":categoria", $categoria);
            $stmtProducto->bindParam(":precio", $precio);
            $stmtProducto->execute();
            $productoId = $this->db->lastInsertId();

            return new Producto($productoId, $nombre, $categoria, $precio);
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public function obtenerProductos(){
        try {
            $sql = "SELECT * FROM productos";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [];
            foreach ($productos as $producto) {
                $resultado[] = new Producto($producto['id'], $producto['nombre'], $producto['categoria'], $producto['precio']);
            }

            return $resultado;
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    private function obtenerProductoPorId($productoId) {
        try {
            $sql = "SELECT * FROM empleados WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $productoId);
            $stmt->execute();
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($producto) {
                return new Producto($producto['id'], $producto['nombre'], $producto['categoria'], $producto['precio']);
            }
            return null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
}
