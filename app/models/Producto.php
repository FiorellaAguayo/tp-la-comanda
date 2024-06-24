<?php

namespace App\Models;
//use Illuminate\Database\Eloquent\Model;

class Producto {
    public $nombre;
    public $categoria;
    private $sector;
    public $precio;

    public function __construct($nombre, $categoria, $sector, $precio) {
        $this->nombre = $nombre;
        $this->categoria = $categoria;
        $this->sector = $sector;
        $this->precio = $precio;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function getSector()
    {
        return $this->sector;
    }

    public function getPrecio()
    {
        return $this->precio;
    }
}