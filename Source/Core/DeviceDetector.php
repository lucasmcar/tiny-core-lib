<?php

namespace Core;

class DeviceDetector
{
    private static ?DeviceDetector $instance = null;

    private string $userAgent;

    // Construtor privado impede instanciação direta
    private function __construct()
    {
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    // Método de acesso à instância única (Singleton)
    public static function getInstance(): DeviceDetector
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // Verifica se é um dispositivo mobile
    public function isMobile(): bool
    {
        $mobileAgents = [
            'iPhone', 'Android', 'webOS', 'BlackBerry', 'iPod',
            'Symbian', 'Windows Phone', 'Opera Mini', 'IEMobile'
        ];

        foreach ($mobileAgents as $agent) {
            if (stripos($this->userAgent, $agent) !== false) {
                return true;
            }
        }

        return false;
    }

    // Verifica se é um desktop
    public function isDesktop(): bool
    {
        return !$this->isMobile();
    }
}
