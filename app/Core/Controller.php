<?php

namespace App\Core;

/**
 * Controller base: renderização de views, sessão e proteção de rotas.
*/
class Controller
{
    /**
     * Renderiza uma view dentro de um layout.
     *
     * @param string $view   Caminho relativo em app/Views (ex.: auth/login)
     * @param array  $data   Variáveis disponíveis na view
     * @param string $layout Layout em app/Views/layouts
     */
    protected function view($view, array $data = [], $layout = 'app')
    {
        $data['flash'] = Session::pullFlash();
        $data['authUser'] = Auth::user();
        extract($data, EXTR_SKIP);

        $viewFile = ROOT_PATH . '/app/Views/' . $view . '.php';

        if ($layout) {
            require ROOT_PATH . '/app/Views/layouts/' . $layout . '.php';
            return;
        }

        require $viewFile;
    }

    protected function redirect($path)
    {
        redirect($path);
    }

    protected function requireLogin()
    {
        Auth::requireLogin();
    }

    /**
     * Valida o token CSRF enviado no POST.
     */
    protected function validateCsrf($redirectTo = 'dashboard')
    {
        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

        if (!csrf_verify($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect($redirectTo);
        }
    }
}
