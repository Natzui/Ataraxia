<?php
/**
 * View - renders PHP templates from app/views and wraps them in a layout.
 */
class View
{
    public static function render(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require self::path($view);
        $content = ob_get_clean();

        if ($layout !== '') {
            require self::path('layouts/' . $layout);
        } else {
            echo $content;
        }
    }

    public static function partial(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require self::path($view);
    }

    private static function path(string $view): string
    {
        $file = APP_PATH . '/views/' . $view . '.php';
        if (!is_file($file)) {
            throw new RuntimeException('View not found: ' . $view);
        }
        return $file;
    }
}
