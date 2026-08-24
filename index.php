<?php
/**
 * Ponto de entrada da aplicação (Front Controller).
 *
 * Todas as requisições passam por este arquivo, que inicia a sessão,
 * carrega o autoload e despacha a rota correspondente.
 */

define('ROOT_PATH', __DIR__);

session_start();

require ROOT_PATH . '/app/Helpers/functions.php';
require ROOT_PATH . '/app/Core/Autoloader.php';

App\Core\Autoloader::register();

$app = new App\Core\App();
$app->run();
