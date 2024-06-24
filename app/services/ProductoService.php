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

    public function crearProducto($nombre, $categoria, $sector, $precio) {
        $sql = "INSERT INTO productos (nombre, categoria, sector, precio) VALUES (:nombre, :categoria, :sector, :precio)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":categoria", $categoria);
        $stmt->bindParam(":sector", $sector);
        $stmt->bindParam(":precio", $precio);
        $stmt->execute();

        return new Producto($nombre, $categoria, $sector, $precio);
    }

    public function obtenerProductoPorNombre($nombre) {
        try {
            $sql = "SELECT * FROM productos WHERE nombre = :nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->execute();
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($producto) {
                return new Producto($producto['nombre'], $producto['categoria'], $producto['sector'], $producto['precio']);
            }
            return null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function obtenerListaProductos() {
        try {
            $sql = "SELECT * FROM productos";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $listaRetorno = [];
            foreach ($productos as $producto) {
                $productoInstance = new Producto($producto['nombre'], $producto['categoria'], $producto['sector'], $producto['precio']);
                array_push($listaRetorno, $productoInstance);
            }
            return $listaRetorno;
        } catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public function modificarProducto($nombre, $categoria, $sector, $precio) {
        $sql = "UPDATE productos SET ";
        $fields = [];
        if (!is_null($categoria)) $fields[] = "categoria = :categoria";
        if (!is_null($sector)) $fields[] = "sector = :sector";
        if (!is_null($precio)) $fields[] = "precio = :precio";
        $sql .= implode(", ", $fields);
        $sql .= " WHERE nombre = :nombre";
    
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        if (!is_null($categoria)) $stmt->bindParam(':categoria', $categoria);
        if (!is_null($sector)) $stmt->bindParam(':sector', $sector);
        if (!is_null($precio)) $stmt->bindParam(':precio', $precio);
        $stmt->execute();
        return new Producto($nombre, $categoria, $sector, $precio);
    }

    public function eliminarProducto($nombre) {
        $sql = "DELETE FROM productos WHERE nombre = :nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->execute();
    }    
}
