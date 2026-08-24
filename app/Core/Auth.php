<?php

namespace App\Core;

/**
 * Autenticação baseada em sessão.
*/
class Auth
{
    public static function check()
    {
        return isset($_SESSION['user']);
    }

    public static function user()
    {
        return isset($_SESSION['user']) ? $_SESSION['user'] : null;
    }

    public static function id()
    {
        return isset($_SESSION['user']['id_user']) ? (int) $_SESSION['user']['id_user'] : null;
    }

    public static function login(array $user)
    {
        unset($user['password']);
        $_SESSION['user'] = $user;
    }

    public static function logout()
    {
        unset($_SESSION['user']);
    }

    public static function requireLogin()
    {
        if (!self::check()) {
            redirect('login');
        }
    }
}
