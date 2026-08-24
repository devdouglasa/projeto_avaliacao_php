<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

/**
 * Cadastro de usuário (tela do wireframe "Cadastrar Novo Usuário").
*/
class UserController extends Controller
{
    private $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function create()
    {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }

        $this->view('auth/register', [], 'auth');
    }

    public function store()
    {
        $this->validateCsrf('usuario/cadastrar');

        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if ($name === '' || $email === '' || $password === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Preencha nome, e-mail válido e senha.');
            $this->redirect('usuario/cadastrar');
        }

        if ($this->users->emailExists($email)) {
            Session::flash('error', 'Este e-mail já está cadastrado.');
            $this->redirect('usuario/cadastrar');
        }

        try {
            $id = $this->users->create($name, $email, hash_password($password));
        } catch (\PDOException $e) {
            $id = false;
        }

        if (!$id) {
            Session::flash('error', 'Não foi possível cadastrar o usuário.');
            $this->redirect('usuario/cadastrar');
        }

        Session::flash('success', 'Usuário cadastrado com sucesso. Faça o login.');
        $this->redirect('login');
    }
}
