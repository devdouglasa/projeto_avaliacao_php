<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

/**
 * Autenticação: login e logout.
*/
class AuthController extends Controller
{
    private $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function index()
    {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }

        $this->view('auth/login', [], 'auth');
    }

    public function authenticate()
    {
        $this->validateCsrf('login');

        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        $user = $this->users->findByEmail($email);
        $valid = $user
            && (int) $user['ativo'] === 1
            && hash_equals($user['password'], hash_password($password));

        if (!$valid) {
            Session::flash('error', 'Ops, Email ou Senha inválido');
            $this->redirect('login');
        }

        Auth::login($user);
        $this->redirect('dashboard');
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('login');
    }
}
