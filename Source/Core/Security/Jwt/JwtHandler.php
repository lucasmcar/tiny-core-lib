<?php
namespace Source\Core\Security\Jwt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHandler
{
    private static $secretKey; 
    private static $algorithm = 'HS256';

    public static function init()
    {
        self::$secretKey = $_ENV['JWT_TOKEN'] ?: 'default_secret_key'; // Fallback para teste
    }

    public static function generateToken($payload)
    {
        self::init();
        return JWT::encode($payload, self::$secretKey, self::$algorithm);
    }

    public static function validateToken($token)
    {
        self::init();
        try {
            $decoded = JWT::decode($token, new Key(self::$secretKey, self::$algorithm));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}