<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\TokenService;
use Exception;

class TokenController {
    private $tokenService;

    public function __construct(){
        $this->tokenService = new TokenService();
    }

    public function crearToken(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $email = $data['email'];
        $rol = $data['rol'];
        $clave = $data['clave'];
        $arrayDatos = ['email' => $email, 'rol' => $rol, 'clave' => $clave];

        $token = $this->tokenService->crearToken($arrayDatos);

        if ($token) {
            $response->getBody()->write(json_encode(['token' => $token]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['error' => 'No se pudo crear el token']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }

    public function verificarToken(Request $request, Response $response) {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(str_replace('Bearer', '', $header));

        try {
            $this->tokenService->verificarToken($token);
            $payload = json_encode(['valid' => true]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) {
            $payload = json_encode(['error' => $e->getMessage()]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }

    public function devolverPayload(Request $request, Response $response) {
        $header = $request->getHeaderLine('Authorization');

        if($header) {
            $token = trim(explode("Bearer", $header)[1]);
        } else {
            $token = '';
        }

        try {
            $payload = json_enconde(array('payload' => AutentificadorJWT::obtenerPayLoad($token)));
        } catch(Exception $e) {
            $payload = json_encode(array("error" => $e->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function devolverData(Request $request, Response $response) {
        $header = $request->getHeaderLine('Authorization');

        if($header) {
            $token = trim(explode("Bearer", $header)[1]);
        } else {
            $token = '';
        }

        try {
            $payload = json_enconde(array('payload' => AutentificadorJWT::obtenerData($token)));
        } catch(Exception $e) {
            $response = new ResponseClass();
            $payload = json_encode(array("error" => "Hubo un error con el token"));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}