<?php

namespace Core\Http;

class Request
{
    private $query;
    private $request;
    private $cookies;
    private $files;
    private $server;
    private $headers;
    private $rawInput;
    private $routeParams = []; // Novo atributo para parâmetros da rota

    public function __construct()
    {
        $this->query = $_GET;
        $this->request = $_POST;
        $this->cookies = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->headers = $this->getHeaders();
        $this->rawInput = file_get_contents("php://input");
    }

    // Método para injetar parâmetros da rota (chamado pelo Router)
    public function setRouteParams(array $params)
    {
        $this->routeParams = $params;
    }

    // Acessar todos os parâmetros da rota
    public function routeParameters()
    {
        return $this->routeParams;
    }

    // Acessar um parâmetro específico da rota (similar ao Laravel)
    public function route($key = null, $default = null)
    {
        if ($key === null) {
            return $this->routeParameters();
        }
        return $this->routeParams[$key] ?? $default;
    }

    // Obter valor de GET
    public function query($key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    // Obter valor de POST
    public function post($key = null, $default = null)
    {
        return $this->getValue($this->request, $key, $default);
    }

    // Obter valor de GET ou POST
    public function input($key = null, $default = null)
    {
        return $this->getValue(array_merge($this->query, $this->request), $key, $default);
    }

    // Obter valor dos cookies
    public function cookie($key = null, $default = null)
    {
        return $this->getValue($this->cookies, $key, $default);
    }

    // Obter arquivo enviado
    public function file($key = null)
    {
        return $key === null ? $this->files : ($this->files[$key] ?? null);
    }

    // Obter valor do servidor
    public function server($key = null, $default = null)
    {
        return $this->getValue($this->server, $key, $default);
    }

    // Obter valor dos headers
    public function header($key = null, $default = null)
    {
        return $this->getValue($this->headers, $key, $default);
    }

    // Obter método da requisição
    public function method()
    {
        return strtoupper($this->server('REQUEST_METHOD', 'GET'));
    }

    public function isPost()
    {
        return $this->method() === 'POST';
    }

    public function isGet()
    {
        return $this->method() === 'GET';
    }

    public function isPut()
    {
        return $this->method() === 'PUT';
    }

    public function isDelete()
    {
        return $this->method() === 'DELETE';
    }

    // Obter todos os parâmetros (GET, POST, JSON, Route Params)
    public function all()
    {
        return array_merge($this->query, $this->request, $this->json(), $this->routeParams);
    }

    // Verificar se um campo existe
    public function has($key)
    {
        return isset($this->query[$key]) || isset($this->request[$key]) || isset($this->routeParams[$key]);
    }

    // Obter dados JSON
    public function json($key = null, $default = null)
    {
        $data = json_decode($this->rawInput, true) ?? [];
        return $this->getValue($data, $key, $default);
    }

    // Obter Bearer Token
    public function bearerToken()
    {
        $authHeader = $this->header('Authorization');
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }
        return null;
    }

    // Sanitizar entradas
    public function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    // Obter headers
    private function getHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    // Método auxiliar
    private function getValue(array $source, $key, $default)
    {
        if ($key === null) {
            return $source;
        }
        return $source[$key] ?? $default;
    }
}