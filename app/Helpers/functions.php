<?php

/**
 * Funções auxiliares globais da aplicação.
 */

function base_url($path = '')
{
    $script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/index.php';
    $base = rtrim(str_replace('\\', '/', dirname($script)), '/');

    if ($base === '/' || $base === '\\' || $base === '.') {
        $base = '';
    }

    $path = ltrim($path, '/');

    return $base . '/' . $path;
}

function asset($path)
{
    return base_url('public/' . ltrim($path, '/'));
}

function redirect($path)
{
    header('Location: ' . base_url($path));
    exit;
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money_br($value)
{
    return 'R$ ' . number_format((float) $value, 2, ',', '.');
}

function date_br($value)
{
    if (empty($value)) {
        return '';
    }

    $time = strtotime($value);

    return $time ? date('d/m/Y', $time) : '';
}

function current_date_br()
{
    $meses = [
        1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
        5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
        9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro',
    ];

    $dia = date('d');
    $mes = $meses[(int) date('n')];
    $ano = date('Y');

    return $dia . ' de ' . $mes . ' de ' . $ano;
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify($token)
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function service_status($finishedAt)
{
    return empty($finishedAt) ? 'Pendente' : 'Finalizado';
}

function hash_password($plain)
{
    return sha1($plain);
}
