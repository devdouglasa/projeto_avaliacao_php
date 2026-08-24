<?php

namespace App\Core;

/**
 * Roteador responsável por mapear a URL para o controller/método.
*/
class App
{
    private $routes = [
        'GET' => [
            ''                    => ['AuthController', 'index'],
            'login'               => ['AuthController', 'index'],
            'logout'              => ['AuthController', 'logout'],
            'usuario/cadastrar'   => ['UserController', 'create'],
            'dashboard'           => ['DashboardController', 'index'],
            'servico/cadastrar'   => ['ServiceController', 'create'],
        ],
        'POST' => [
            'login'               => ['AuthController', 'authenticate'],
            'usuario/cadastrar'   => ['UserController', 'store'],
            'servico/cadastrar'   => ['ServiceController', 'store'],
        ],
    ];

    /**
     * Rotas que recebem um identificador numérico no final.
     */
    private $paramRoutes = [
        'GET' => [
            'servico/editar' => ['ServiceController', 'edit'],
        ],
        'POST' => [
            'servico/editar'    => ['ServiceController', 'update'],
            'servico/excluir'   => ['ServiceController', 'delete'],
            'servico/finalizar' => ['ServiceController', 'finish'],
        ],
    ];

    public function run()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $url = $this->currentUrl();

        if (isset($this->routes[$method][$url])) {
            $this->dispatch($this->routes[$method][$url]);
            return;
        }

        $paramRoutes = isset($this->paramRoutes[$method]) ? $this->paramRoutes[$method] : array();
        foreach ($paramRoutes as $prefix => $handler) {
            if (preg_match('#^' . preg_quote($prefix, '#') . '/(\d+)$#', $url, $matches)) {
                $this->dispatch($handler, [(int) $matches[1]]);
                return;
            }
        }

        http_response_code(404);
        echo 'Página não encontrada.';
    }

    private function currentUrl()
    {
        if (!empty($_GET['url'])) {
            return trim($_GET['url'], '/');
        }

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $scriptDir = rtrim($scriptDir, '/');

        if ($scriptDir !== '' && $scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }

        $uri = trim($uri, '/');

        if ($uri === 'index.php' || $uri === '') {
            return '';
        }

        return $uri;
    }

    private function dispatch(array $handler, array $params = [])
    {
        $class = 'App\\Controllers\\' . $handler[0];
        $action = $handler[1];
        $controller = new $class();

        call_user_func_array([$controller, $action], $params);
    }
}
