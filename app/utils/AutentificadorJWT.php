<?php

namespace App\Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;  // Asegúrate de añadir esta línea
use Exception;

class AutentificadorJWT
{
    private static $claveSecreta = 'T3sT$JWT';
    private static $tipoEncriptacion = 'HS256';  // Asegúrate de que esto no sea un array

    public function CrearToken($datos)
    {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            'exp' => $ahora + 60000,  // Duración del token
            'aud' => self::Aud(),
            'data' => $datos,
            'app' => "LaComanda"
        );
        return JWT::encode($payload, self::$claveSecreta, self::$tipoEncriptacion);
    }

    public static function verificarToken($token) {
        if (empty($token)) {
            throw new Exception("El token está vacío");
        }
    
        try {
            // Creando un nuevo objeto Key
            $key = new Key(self::$claveSecreta, self::$tipoEncriptacion);
            $decodificado = JWT::decode($token, $key);
        } catch (Exception $e) {
            throw new Exception("Token inválido: " . $e->getMessage());
        }
    
        if ($decodificado->aud !== self::Aud()) {
            throw new Exception("No es el usuario válido");
        }
    }
    
    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token está vacío");
        }
        $key = new Key(self::$claveSecreta, self::$tipoEncriptacion);
        return JWT::decode($token, $key);
    }

    public static function ObtenerData($token)
    {
        $key = new Key(self::$claveSecreta, self::$tipoEncriptacion);
        return JWT::decode($token, $key)->data;
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}
