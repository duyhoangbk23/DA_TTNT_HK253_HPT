<?php

declare(strict_types=1);

namespace App;

final class View
{
    public static function render(string $template, array $data = []): string
    {
        $templatesDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates';
        $templateFile = $templatesDir . DIRECTORY_SEPARATOR . $template . '.php';
        $layoutFile = $templatesDir . DIRECTORY_SEPARATOR . 'layout.php';

        if (!is_file($templateFile)) {
            throw new \RuntimeException(sprintf('Template not found: %s', $template));
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $templateFile;
        $content = ob_get_clean();

        ob_start();
        include $layoutFile;

        return (string) ob_get_clean();
    }
}
