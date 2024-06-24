<?php

namespace App\Models;

class Mesa {
    private $id;
    private $estado;
    private $total_facturado;
    private $importe_mayor;
    private $importe_menor;
    private $cantidad_usada;

    public function __construct($id, $estado, $total_facturado, $importe_mayor, $importe_menor, $cantidad_usada) {
        $this->id = $id;
        $this->estado = $estado;
        $this->total_facturado = $total_facturado;
        $this->importe_mayor = $importe_mayor;
        $this->importe_menor = $importe_menor;
        $this->cantidad_usada = $cantidad_usada;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getTotalFacturado()
    {
        return $this->total_facturado;
    }

    public function getImporteMayor()
    {
        return $this->importe_mayor;
    }

    public function getImporteMenor()
    {
        return $this->importe_menor;
    }

    public function getCantidadUsada()
    {
        return $this->cantidad_usada;
    }
}