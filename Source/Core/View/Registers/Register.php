<?php

namespace Core\View\Registers;

class Register
{
    protected static $patterns = [
        // Conteúdo não escapado (use com muita cautela no template)
        '/\{\{\!\s*([$\w\[\]\->\'"]+)\s*\}\}/',

        // Conteúdo escapado por padrão
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

        '/@css\(\s*["\'](.+?)["\']\s*\)/',
        '/@js\(\s*["\'](.+?)["\']\s*\)/',

        '/@csrf/',

        '/\{\%\s*year\s*\%\}/',

        // Controle de loop
        '/@for\(\s*(.+?),\s*(.+?),\s*(.+?)\s*\)/',
        '/@endfor\(\)/',
    ];

    protected static $replacements = [
        '<?php echo $1; ?>', // Conteúdo não escapado (programador assume o risco)
        '<?php echo htmlspecialchars($1, ENT_QUOTES, "UTF-8"); ?>', // Conteúdo escapado

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

        // debugger - uso interno somente
        '<?php print_r(htmlspecialchars(print_r($1, true), ENT_QUOTES, "UTF-8")); ?>',

        // CSS seguro (apenas arquivos locais permitidos)
        '<?php if (filter_var("$1", FILTER_VALIDATE_URL) || preg_match("/^[a-zA-Z0-9_\-\/\.]+\.css$/", "$1")): ?><link rel="stylesheet" href="<?php echo htmlspecialchars("$1", ENT_QUOTES, "UTF-8"); ?>"><?php endif; ?>',

        // JS seguro (apenas arquivos locais permitidos)
        '<?php if (preg_match("/^(https?:\/\/[a-zA-Z0-9\-\.\/:_?&=]+|[a-zA-Z0-9_\-\/\.]+\.js)$/", "$1")): ?><script src="<?php echo htmlspecialchars("$1", ENT_QUOTES, "UTF-8"); ?>"></script><?php endif; ?>',

        '<input type="hidden" name="_csrf_token" id="_csrf_token" value="<?php echo htmlspecialchars($this->vars[\'csrf_token\'], ENT_QUOTES, \'UTF-8\'); ?>">',

        '<?php echo date("Y"); ?>',

        // For seguro - apenas números inteiros
        '<?php if (is_numeric($1) && is_numeric($2) && is_numeric($3)): for($i=$1; $i<=$2; $i+=$3): ?>',
        '<?php endfor; endif; ?>',
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
