<?php

namespace App\Models;

use App\Core\Database;

/**
 * Acesso aos dados da tabela user.
*/
class User
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->pdo();
    }

    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `user` WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch();

        return $user ? $user : null;
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `user` WHERE id_user = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        $user = $stmt->fetch();

        return $user ? $user : null;
    }

    public function emailExists($email)
    {
        $stmt = $this->pdo->prepare('SELECT id_user FROM `user` WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetch();
    }

    /**
     * Cadastra um novo usuário ativo.
     *
     * @return int|false
     */
    public function create($name, $email, $passwordHash)
    {
        $sql = 'INSERT INTO `user` (`name`, `email`, `password`, `created_at`, `update_at`, `ativo`)
                VALUES (:name, :email, :password, NOW(), NOW(), 1)';

        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute([
            'name'     => $name,
            'email'    => $email,
            'password' => $passwordHash,
        ]);

        return $ok ? (int) $this->pdo->lastInsertId() : false;
    }
}
