<?php

namespace App\Models;

class Cliente {
    private $nombre;
    private $id_pedido;
    private $id_mesa;

    public function __construct($nombre, $id_pedido, $id_mesa) {
        $this->nombre = $nombre;
        $this->id_pedido = $id_pedido;
        $this->id_mesa = $id_mesa;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getIdPedido()
    {
        return $this->id_pedido;
    }

    public function getIdMesa()
    {
        return $this->id_mesa;
    }
}