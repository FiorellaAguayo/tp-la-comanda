<?php

namespace App\Models;
use App\Models\Usuario;

class Usuario {
    private $id_usuario;
    private $nombre;
    private $email;
    private $clave;
    private $rol; // bartender, cervecero, cocinero, mozo, socio
    private $sector;
    private $tiempo_estimado;
    private $fecha_ingreso;
    private $estado;

    // Constructor
    public function __construct($id_usuario, $nombre, $email, $clave, $rol, $sector, $tiempo_estimado, $fecha_ingreso, $estado) {
        $this->id_usuario = $id_usuario;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->clave = $clave;
        $this->rol = $rol;
        $this->sector = $sector;
        $this->tiempo_estimado = $tiempo_estimado;
        $this->fecha_ingreso = $fecha_ingreso;
        $this->estado = $estado;
    }

    // Métodos para obtener y establecer atributos
    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getClave() {
        return $this->clave;
    }

    public function getRol() {
        return $this->rol;
    }

    public function getSector() {
        return $this->sector;
    }

    public function getTiempoEstimado() {
        return $this->tiempo_estimado;
    }

    public function getFechaIngreso() {
        return $this->fecha_ingreso;
    }

    public function getEstado() {
        return $this->estado;
    }
}