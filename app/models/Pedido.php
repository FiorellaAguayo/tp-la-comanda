<?php

namespace App\Models;
//use Illuminate\Database\Eloquent\Model;

class Pedido{
    public $id;
    public $codigoUnico;
    public $idCliente;
    public $idMesa;
    public $estado;
    public $tiempoEstimado;
    public $productos; // Array de productos en el pedido

    public function __construct($id, $codigoUnico, $idCliente, $idMesa, $estado, $tiempoEstimado, $productos) {
        $this->id = $id;
        $this->codigoUnico = $codigoUnico;
        $this->idCliente = $idCliente;
        $this->idMesa = $idMesa;
        $this->estado = $estado;
        $this->tiempoEstimado = $tiempoEstimado;
        $this->productos = $productos;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCodigoUnico()
    {
        return $this->codigoUnico;
    }

    public function getIdCliente()
    {
        return $this->idCliente;
    }

    public function getIdMesa()
    {
        return $this->idMesa;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getTiempoEstimado()
    {
        return $this->tiempoEstimado;
    }

    public function getProductos()
    {
        return $this->productos;
    }
}