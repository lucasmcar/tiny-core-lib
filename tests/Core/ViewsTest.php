<?php

namespace Core\Tests;

use PHPUnit\Framework\TestCase;
use Core\View\View;

class ViewsTest extends TestCase
{

    
    public function testRenderReturnsString()
    {
        ob_start();
    new \Core\View\View('template', ['nome' => 'Lucas']);
    $output = ob_get_clean();

    $this->assertStringContainsString('Olá Lucas', $output);
    }
}
