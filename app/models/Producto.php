<?php

namespace App\Models;
//use Illuminate\Database\Eloquent\Model;

class Producto {
    public $id;
    public $nombre;
    public $categoria;
    public $precio;

    public function __construct($id, $nombre, $categoria, $precio) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->categoria = $categoria;
        $this->precio = $precio;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function getPrecio()
    {
        return $this->precio;
    }
}