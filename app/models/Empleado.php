<?php

namespace App\Models;

class Empleado {
    public $id;
    public $nombreEmpleado;
    public $rol;
    public $estado;

    public function __construct($id, $nombreEmpleado, $rol, $estado) {
        $this->id = $id;
        $this->nombreEmpleado = $nombreEmpleado;
        $this->rol = $rol;
        $this->estado = $estado;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombreEmpleado()
    {
        return $this->nombreEmpleado;
    }

    public function getRol()
    {
        return $this->rol;
    }

    public function getEstado()
    {
        return $this->estado;
    }
}
