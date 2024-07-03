<?php

namespace App\Models;

class Detalle {
    private $id_mesa;
    private $id_pedido;
    private $tiempo_demora;

    public function __construct($id_mesa, $id_pedido, $tiempo_demora) {
        $this->id_mesa = $id_mesa;
        $this->id_pedido = $id_pedido;
        $this->tiempo_demora = $tiempo_demora;
    }

    public function getIdMesa()
    {
        return $this->id_mesa;
    }

    public function getIdPedido()
    {
        return $this->id_pedido;
    }

    public function getTiempoDemora()
    {
        return $this->tiempo_demora;
    }
}
