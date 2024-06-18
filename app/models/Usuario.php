<?php

namespace App\Models;

class Usuario {
    public $id;
    public $nombreUsuario;
    public $clave;
    public $empleado;

    public function __construct($id, $nombreUsuario, $clave, $empleado) {
        $this->id = $id;
        $this->nombreUsuario = $nombreUsuario;
        $this->clave = $clave;
        $this->empleado = $empleado;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    public function getClave()
    {
        return $this->clave;
    }

    public function getEmpleado()
    {
        return $this->empleado;
    }
}
