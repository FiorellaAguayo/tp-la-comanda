<?php
namespace App\Models;

class Pedido{
    public $id;
    public $nombre_cliente;
    public $id_mesa;
    public $estado;
    public $tiempo_estimado;
    public $nombre_productos; // Array de productos en el pedido

    public function __construct($id, $nombre_cliente, $id_mesa, $estado, $tiempo_estimado, $nombre_productos) {
        $this->id = $id;
        $this->nombre_cliente = $nombre_cliente;
        $this->id_mesa = $id_mesa;
        $this->estado = $estado;
        $this->tiempo_estimado = $tiempo_estimado;
        $this->nombre_productos = $nombre_productos;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombreCliente()
    {
        return $this->nombre_cliente;
    }

    public function getIdMesa()
    {
        return $this->id_mesa;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getTiempoEstimado()
    {
        return $this->tiempo_estimado;
    }

    public function getNombreProductos()
    {
        return $this->nombre_productos;
    }
}