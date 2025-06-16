<?php 

namespace Core\View\Template;

class Template
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function render(array $vars = []): string
    {
        extract($vars);

        ob_start();
        include $this->path;
        return ob_get_clean();
    }
}