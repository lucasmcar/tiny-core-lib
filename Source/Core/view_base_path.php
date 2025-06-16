<?php

namespace Core;

if (!function_exists('view_base_path')) {
    function view_base_path(): string
    {
        // Se o projeto principal definir uma constante ou variável global, usa ela
        if (defined('PROJECT_VIEW_PATH')) {
            return PROJECT_VIEW_PATH;
        }

        // Caminho padrão: views dentro da lib
        $path = dirname(__DIR__, 2);
        return $path;
    }
}