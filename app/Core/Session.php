<?php

namespace App\Core;

/**
 * Mensagens flash (exibidas uma única vez após redirect).
*/
class Session
{
    public static function flash($type, $message)
    {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message,
        ];
    }

    public static function pullFlash()
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }

        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);

        return $flash;
    }
}
