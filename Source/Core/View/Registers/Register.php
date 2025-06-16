<?php

namespace Core\View\Registers;

class Register
{
    protected static $patterns = [
        // Primeiro: conteúdo não escapado (consciente)
        '/\{\{\!\s*([$\w\[\]\->\'"]+)\s*\}\}/',

        // Segundo: conteúdo escapado por padrão
        '/\{\{\s*([$\w\[\]\->\'"]+)\s*\}\}/',

        '/\{\%\s*if\s+(.+?)\s*\%\}/',
        '/\{\%\s*elseif\s+(.+?)\s*\%\}/',
        '/\{\%\s*else\s*\%\}/',
        '/\{\%\s*endif;\s*\%\}/',
        '/\{\%\s*foreach\s+([$]\w+)\s*\%\}/',
        '/\{\%\s*foreach\s*(.+?)\s+as\s+(.+?)\s*=>\s*(.+?)\s*\%\}/',
        '/\{\%\s*endforeach;\s*\%\}/',
        '/\{\%\s*while\s+(.+?)\s*\%\}/',
        '/\{\%\s*endwhile;\s*\%\}/',
        '/\{\%\s*include\s+\'(.+?)\'\s*\%\}/',
        '/\{\%\s*debugger\s*\(\s*(.+?)\s*\)\s*\%\}/',
        '/@css\(\s*(.+?)\s*\)/',
        '/@js\(\s*["\'](.+?)["\']\s*\)/',
        '/@csrf/',
        '/\{\%\s*year\s*\%\}/',
        '/@for\(\s*(.+?),\s*(.+?),\s*(.+?)\s*\)/',
        '/@endfor\(\)/',
        '/\{\s*var\s*(.+?)\s*\}/'
    ];

    protected static $replacements = [
        '<?php echo $1; ?>', // {{! ... }} - sem escape
        '<?php echo htmlspecialchars($1, ENT_QUOTES, "UTF-8"); ?>', // {{ ... }} - com escape

        '<?php if ($1): ?>',
        '<?php elseif ($1): ?>',
        '<?php else: ?>',
        '<?php endif; ?>',
        '<?php foreach ($1 as $item): ?>',
        '<?php foreach ($1 as $2 => $3): ?>',
        '<?php endforeach; ?>',
        '<?php while ($1): ?>',
        '<?php endwhile; ?>',

        // include seguro
        '<?php $path = realpath(__DIR__ . "/../../views/$1.tpl"); if ($path && strpos($path, realpath(__DIR__ . "/../../views")) === 0 && file_exists($path)) include $path; ?>',

        '<?php print_r($1); ?>',
        '<link rel="stylesheet" href="$1">',
        '<script src="$1"></script>',
        '<input type="hidden" name="_csrf_token" id="_csrf_token" value="<?php echo htmlspecialchars($this->vars[\'csrf_token\'], ENT_QUOTES, \'UTF-8\'); ?>">',
        '<?php echo date("Y"); ?>',
        '<?php for($i=$1; $i<=$2; $3++): ?>',
        '<?php endfor; ?>',
        '<?php $1 ?>'
    ];

    public static function getPatterns(): array
    {
        return self::$patterns;
    }

    public static function getReplacements(): array
    {
        return self::$replacements;
    }
}
