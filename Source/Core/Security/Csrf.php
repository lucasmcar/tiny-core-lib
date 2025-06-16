<?php 

namespace Core\Security;

class Csrf
{
    public static function generateToken()
    {
        if (!session_id()) {
            session_start();
        }

        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    public static function getToken()
    {
        if (!session_id()) {
            session_start();
        }

        return $_SESSION['_csrf_token'] ?? null;
    }

    public static function verifyToken($token)
    {
        if (!session_id()) {
            session_start();
        }

        return hash_equals($_SESSION['_csrf_token'], $token);
    }
} 