<?php
use Firebase\JWT\JWT;

class Autentificador
{
    private static $claveSecreta = 'T3sT$JWT';
    private static $tipoEncriptacion = ['HS256'];

    public static function crearToken($datos) {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            'exp' => $ahora + (60000),
            'aud' => self::aud(),
            'data' => $datos,
            'app' => "Test JWT"
        );
        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function verificarToken($token) {
        if(empty($token)) {
            throw new Exception("El token está vacío");
        }

        try {
            $decodificado = JWT::decode($token, self::$claveSecreta, self::$tipoEncriptacion);
        } catch(Exception $e) {
            throw $e;
        }

        if($decodificado->aud !== self::Aud()) {
            throw new Exception("No es el usuario válido");
        }
    }

    public static function obtenerPayLoad($token) {
        if(empty($token)) {
            throw new Exception("El token está vacío");
        }
        return JWT::decode($token, self::$claveSecreta, self::$tipoEncriptacion);
    }

    public static function obtenerData() {
        return JWT::decode($token, self::$claveSecreta, self::$tipoEncriptacion)->data; 
    }

    public static function aud() {
        $aud = '';

        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}