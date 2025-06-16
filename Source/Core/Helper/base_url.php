<?php

if (!function_exists('base_url')) {
    function base_url($path = '') {
        // Obtém a URL base dinamicamente
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];

        return $protocol . $host . '/' . ltrim($path, '/');
    }
}