<?php

namespace Core\View;


use Core\Security\Csrf;
use Core\View\Registers\Register;


class View 
{
    
    private $vars = [];
    private $layout;
    private $styles = [];
    private $scripts = [];

    public function __construct(string $view, array $vars,  array $styles = [], array $scripts = [], $layout = 'layout')
    {
        $this->setLayout($layout);
        $this->vars['csrf_token'] = Csrf::generateToken();
        foreach($vars as $name => $value){
           $this->assignArray($name, $value);
            
        }

        // Adiciona estilos e scripts específicos da página
        $this->styles = $styles;
        $this->scripts = $scripts;

        $this->render($view);
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

    public function render($template)
    {
        $templatePath = realpath('../resources/views/' . $template . '.tpl');

        if (!file_exists($templatePath)) {
            throw new \Exception("Template $templatePath not found!");
        }
        // Carregar arquivos de estilos e scripts específicos da página
        

        extract($this->vars);

        ob_start();
        include $templatePath;
        $content = ob_get_clean();


        if ($this->layout) {
            $layoutPath =  realpath('../resources/views/layouts/' . $this->layout . '.tpl');
    
            if (!file_exists($layoutPath)) {
                throw new \Exception("Layout $layoutPath not found!");
            }

            $styles = $this->generateStyles();
            $scripts = $this->generateScripts();
    
            // Include the content in the layout
             // Replace the {{ $content }} placeholder with the actual content
            $layoutContent = file_get_contents($layoutPath);
            $layoutContent = str_replace('{{! $styles }}', $styles, $layoutContent);
            $layoutContent = str_replace('{{! $scripts }}', $scripts, $layoutContent);
            $layoutContent = str_replace('{{ $content }}', $content, $layoutContent);
            $content = $layoutContent;
        
        }
        $parsedContent = $this->parse($content);
        // Render the parsed content within a safe context
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

        extract($vars);

        $tempFile = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($tempFile, $content);

        ob_start();
        include $tempFile;
        //unlink($tempFile);
        return ob_get_clean();
    }

    private function generateStyles()
    {
        $output = "";
        foreach ($this->styles as $style) {
            $output .= "<link rel='stylesheet' href='" . \base_url($style) . "'>\n";
        }
        return $output;
    }

    private function generateScripts()
    {
        $output = "";
        foreach ($this->scripts as $script) {
            $output .= "<script src='" . \base_url($script) . "'></script>\n";
        }
        return $output;
    }
}