<?php

namespace App\Helpers;

/**
 * Envio de e-mail com a função nativa mail() (sem Composer).
 * Também grava uma cópia em logs/emails.log para conferência em ambiente local.
 */
class Mailer
{
    /**
     * Notifica o usuário dono do serviço de que ele foi finalizado.
     *
     * @param array $user    Dados do usuário (name, email)
     * @param array $service Dados do serviço
     * @return bool
     */
    public static function notifyServiceFinished(array $user, array $service)
    {
        $config = require ROOT_PATH . '/config/app.php';

        $subject = 'Serviço finalizado - JM Informática';
        $body = self::buildBody($user, $service);

        $fromName = $config['mail_from_name'];
        $from = $config['mail_from'];
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/plain; charset=UTF-8',
            'From: ' . $fromName . ' <' . $from . '>',
            'Reply-To: ' . $from,
            'X-Mailer: PHP/' . PHP_VERSION,
        ];

        $sent = @mail($user['email'], '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));

        self::log($user['email'], $subject, $body, $sent);

        return $sent;
    }

    private static function buildBody(array $user, array $service)
    {
        $linhas = [
            'Olá, ' . $user['name'] . '.',
            '',
            'O serviço abaixo foi marcado como finalizado.',
            '',
            'ID: ' . $service['id_service'],
            'Descrição: ' . $service['description'],
            'Valor: ' . money_br($service['price']),
            'Comissão: ' . money_br($service['commission_user']),
            'Data de finalização: ' . date_br($service['finished_at']),
            '',
            'Atenciosamente,',
            'JM Informática',
        ];

        return implode("\r\n", $linhas);
    }

    private static function log($to, $subject, $body, $sent)
    {
        $dir = ROOT_PATH . '/logs';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $status = $sent ? 'ENVIADO' : 'FALHA (registrado localmente)';
        $entry = sprintf(
            "[%s] %s | Para: %s | Assunto: %s%s%s%s%s",
            date('Y-m-d H:i:s'),
            $status,
            $to,
            $subject,
            PHP_EOL,
            $body,
            PHP_EOL,
            str_repeat('-', 60) . PHP_EOL
        );

        file_put_contents($dir . '/emails.log', $entry, FILE_APPEND);
    }
}
