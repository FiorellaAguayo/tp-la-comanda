<?php

namespace App\Controllers;
//require_once './Model/Empleado.php';
//require_once './Utilities/AutentificadorJWT.php';
use App\Services\UsuarioService;
use App\Services\TokenService;
use App\Models\Usuario;
use App\Utils\AutentificadorJWT;

class LoginController {
    private $usuarioService;
    private $autentificadorJWT;
    private $tokenService;

    public function __construct() {
        $this->usuarioService = new UsuarioService();
        $this->autentificadorJWT = new AutentificadorJWT();
        $this->tokenService = new TokenService();
    }

    public function login($request, $response, $args) {
        $parametros = $request->getParsedBody();
        $email = $parametros['email'];
        $clave = $parametros['clave'];

        $usuario = $this->usuarioService->loginUsuario($email, $clave);
        
        if ($usuario) {
            $datos = array('email' => $usuario['email'], 'rol' => $usuario['rol'], 'clave' => $usuario['clave']);
            $token = $this->tokenService->crearToken($datos);
            $payload = json_encode(array('jwt' => $token));
        } else {
            $payload = json_encode(array('error' => 'Usuario o contraseña incorrectos'));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}