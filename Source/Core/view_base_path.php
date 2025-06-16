<?php

namespace Core;

if (!function_exists('view_base_path')) {
    function view_base_path(): string
    {
        // Caminho base da lib (mesmo dentro do vendor ou em dev)
        $libBase = dirname(__DIR__, 2);

        // Se estiver sendo usada dentro de um projeto via composer (ou seja, instalada no vendor)
        // então usamos o caminho do projeto principal
        $projectBase = getcwd();

        // Se o caminho atual inclui "vendor", então estamos numa instalação de dependência
        if (strpos($libBase, 'vendor') !== false && file_exists($projectBase . '/resources/views')) {
            return $projectBase . '/resources/views';
        }

        // Caso contrário, estamos rodando a lib de forma standalone (dev ou teste)
        return $libBase;
    }
}