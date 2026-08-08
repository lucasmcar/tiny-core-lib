<?php

namespace Core\View;

use Core\Security\Csrf;
use Core\View\Registers\Register;

use function Core\view_base_path;

class View
{
    private $vars = [];
    private $layout;
    private $styles = [];
    private $scripts = [];
    private $meta = [];
    private $view;

    public function __construct(
        string $view,
        array $vars = [],
        array $styles = [],
        array $scripts = [],
        $layout = 'layout',
        bool $autoRender = true
    ) {
        $this->view = $view;
        $this->setLayout($layout);
        $this->vars['csrf_token'] = Csrf::generateToken();
        foreach ($vars as $name => $value) {
            $this->assignArray($name, $value);
        }

        $this->styles = $styles;
        $this->scripts = $scripts;

        if ($autoRender) {
            $this->render();
        }
    }

    public function withMetaTags(array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    private function assignArray($name, $value)
    {
        $this->vars[$name] = $value;
    }

    public function render(): void
    {
        $template = $this->view;

        if (defined('PROJECT_VIEW_PATH')) {
            $templatePath = realpath(view_base_path() . '/' . $template . '.tpl');
        } else {
            $templatePath = realpath(view_base_path() . '/resources/views/' . $template . '.tpl');
        }

        if (!file_exists($templatePath)) {
            throw new \Exception("Template $templatePath not found!");
        }

        extract($this->vars);

        ob_start();
        include $templatePath;
        $content = ob_get_clean();

        if ($this->layout) {
            if (defined('PROJECT_VIEW_PATH')) {
                $layoutPath = realpath(view_base_path() . "/layouts/" . $this->layout . '.tpl');
            } else {
                $layoutPath = realpath(view_base_path() . '/resources/views/layouts/' . $this->layout . '.tpl');
            }

            if (!file_exists($layoutPath)) {
                throw new \Exception("Layout $layoutPath not found!");
            }

            $meta = $this->generateMeta();
            $styles = $this->generateStyles();
            $scripts = $this->generateScripts();

            $layoutContent = file_get_contents($layoutPath);
            $layoutContent = str_replace('{{! $meta }}', $meta, $layoutContent);
            $layoutContent = str_replace('{{! $styles }}', $styles, $layoutContent);
            $layoutContent = str_replace('{{! $scripts }}', $scripts, $layoutContent);
            $layoutContent = str_replace('{{ $content }}', $content, $layoutContent);
            $content = $layoutContent;
        }

        $parsedContent = $this->parse($content);
        echo $this->renderContent($parsedContent, $this->vars);
    }

    private function parse($content)
    {
        $patterns = Register::getPatterns();
        $replacements = Register::getReplacements();
        return preg_replace($patterns, $replacements, $content);
    }

    private function renderContent($content, $vars)
    {
        $vars['styles'] = $this->generateStyles();
        $vars['scripts'] = $this->generateScripts();
        $vars['meta'] = $this->generateMeta();

        extract($vars);

        $tempFile = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($tempFile, $content);

        ob_start();
        try {
            include $tempFile;
            return ob_get_clean();
        } finally {
            @unlink($tempFile);
        }
    }

    private function generateMeta(): string
    {
        $defaults = [
            'title'           => 'Nome do Site',
            'description'     => '',
            'keywords'        => '',
            'canonical'       => '',
            'robots'          => 'index, follow',
            'og:title'        => null,
            'og:description'  => null,
            'og:image'        => null,
            'og:type'         => 'website',
        ];

        $meta = array_merge($defaults, $this->meta);

        $output = "<title>" . htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8') . "</title>\n";

        if (!empty($meta['description'])) {
            $output .= '<meta name="description" content="' . htmlspecialchars($meta['description'], ENT_QUOTES, 'UTF-8') . "\">\n";
        }
        if (!empty($meta['keywords'])) {
            $output .= '<meta name="keywords" content="' . htmlspecialchars($meta['keywords'], ENT_QUOTES, 'UTF-8') . "\">\n";
        }
        if (!empty($meta['robots'])) {
            $output .= '<meta name="robots" content="' . htmlspecialchars($meta['robots'], ENT_QUOTES, 'UTF-8') . "\">\n";
        }
        if (!empty($meta['canonical'])) {
            $output .= '<link rel="canonical" href="' . htmlspecialchars($meta['canonical'], ENT_QUOTES, 'UTF-8') . "\">\n";
        }

        $ogTitle = $meta['og:title'] ?? $meta['title'];
        $ogDesc  = $meta['og:description'] ?? $meta['description'];

        $output .= '<meta property="og:title" content="' . htmlspecialchars($ogTitle, ENT_QUOTES, 'UTF-8') . "\">\n";
        if (!empty($ogDesc)) {
            $output .= '<meta property="og:description" content="' . htmlspecialchars($ogDesc, ENT_QUOTES, 'UTF-8') . "\">\n";
        }
        if (!empty($meta['og:image'])) {
            $output .= '<meta property="og:image" content="' . htmlspecialchars($meta['og:image'], ENT_QUOTES, 'UTF-8') . "\">\n";
        }
        $output .= '<meta property="og:type" content="' . htmlspecialchars($meta['og:type'], ENT_QUOTES, 'UTF-8') . "\">\n";

        return $output;
    }

    private function generateStyles()
    {
        $output = "";
        foreach ($this->styles as $style) {
            $output .= "<link rel='stylesheet' href='" . htmlspecialchars(\base_url($style), ENT_QUOTES, 'UTF-8') . "'>\n";
        }
        return $output;
    }

    private function generateScripts()
    {
        $output = "";
        foreach ($this->scripts as $script) {
            if (is_array($script)) {
                $src = \base_url($script['src']);
                $type = isset($script['type']) ? $script['type'] : 'text/javascript';
            } else {
                $src = \base_url($script);
                $type = 'text/javascript';
            }

            $output .= "<script type='" . htmlspecialchars($type, ENT_QUOTES, 'UTF-8') . "' src='" . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . "'></script>\n";
        }
        return $output;
    }
}