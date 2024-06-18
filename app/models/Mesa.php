<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;

class Mesa{
    private $id;
    private $codigo;
    private $estado;
    private $idCliente;
    private $idMozo;

    public function __construct($id, $codigo, $estado, $idCliente, $idMozo) {
        $this->id = $id;
        $this->codigo = $codigo;
        $this->estado = $estado;
        $this->idCliente = $idCliente;
        $this->idMozo = $idMozo;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getIdCliente()
    {
        return $this->idCliente;
    }

    public function getIdMozo()
    {
        return $this->idMozo;
    }
}