<?php

namespace App\Core;

/**
 * Autoload simples no padrão PSR-4 para o namespace App\,
*/
class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $prefix = 'App\\';
            $baseDir = ROOT_PATH . '/app/';

            if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
                return;
            }

            $relative = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

            if (is_file($file)) {
                require_once $file;
            }
        });
    }
}
