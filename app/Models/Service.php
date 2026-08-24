<?php

namespace App\Models;

use App\Core\Database;

/**
 * Acesso aos dados da tabela service.
*/
class Service
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->pdo();
    }

    /**
     * Lista serviços com o nome do funcionário, aplicando filtros opcionais.
     *
     * @param array $filters description, user_name, status, date_start, date_end
     * @return array
     */
    public function listWithUser(array $filters = [])
    {
        $sql = 'SELECT s.*, u.name AS user_name, u.email AS user_email
                FROM `service` s
                INNER JOIN `user` u ON u.id_user = s.user_id_user
                WHERE 1 = 1';
        $params = [];

        if (!empty($filters['description'])) {
            $sql .= ' AND s.description LIKE :description';
            $params['description'] = '%' . $filters['description'] . '%';
        }

        if (!empty($filters['user_name'])) {
            $sql .= ' AND u.name LIKE :user_name';
            $params['user_name'] = '%' . $filters['user_name'] . '%';
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'Pendente') {
                $sql .= ' AND s.finished_at IS NULL';
            } elseif ($filters['status'] === 'Finalizado') {
                $sql .= ' AND s.finished_at IS NOT NULL';
            }
        }

        if (!empty($filters['date_start'])) {
            $sql .= ' AND DATE(s.created_at) >= :date_start';
            $params['date_start'] = $filters['date_start'];
        }

        if (!empty($filters['date_end'])) {
            $sql .= ' AND DATE(s.created_at) <= :date_end';
            $params['date_end'] = $filters['date_end'];
        }

        $sql .= ' ORDER BY s.created_at DESC, s.id_service DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $sql = 'SELECT s.*, u.name AS user_name, u.email AS user_email
                FROM `service` s
                INNER JOIN `user` u ON u.id_user = s.user_id_user
                WHERE s.id_service = :id
                LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $row : null;
    }

    /**
     * Soma o valor dos serviços prestados por um usuário.
     */
    public function totalByUser($userId)
    {
        $stmt = $this->pdo->prepare(
            'SELECT COALESCE(SUM(price), 0) AS total FROM `service` WHERE user_id_user = :user_id'
        );
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return (float) $row['total'];
    }

    /**
     * Últimos serviços do usuário, independente do status.
     */
    public function lastByUser($userId, $limit = 3)
    {
        $sql = 'SELECT * FROM `service`
                WHERE user_id_user = :user_id
                ORDER BY created_at DESC, id_service DESC
                LIMIT ' . (int) $limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    /**
     * Últimos serviços pendentes do usuário (sem data de finalização).
     */
    public function lastPendingByUser($userId, $limit = 5)
    {
        $sql = 'SELECT * FROM `service`
                WHERE user_id_user = :user_id
                  AND finished_at IS NULL
                ORDER BY created_at DESC, id_service DESC
                LIMIT ' . (int) $limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function create($description, $price, $userId)
    {
        $sql = 'INSERT INTO `service`
                    (`description`, `price`, `created_at`, `update_at`, `finished_at`, `commission_user`, `user_id_user`)
                VALUES
                    (:description, :price, NOW(), NOW(), NULL, NULL, :user_id)';

        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute([
            'description' => $description,
            'price'       => $price,
            'user_id'     => $userId,
        ]);

        return $ok ? (int) $this->pdo->lastInsertId() : false;
    }

    public function update($id, $description, $price)
    {
        $sql = 'UPDATE `service`
                SET description = :description,
                    price = :price,
                    update_at = NOW()
                WHERE id_service = :id';

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'description' => $description,
            'price'       => $price,
            'id'          => $id,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `service` WHERE id_service = :id');

        return $stmt->execute(['id' => $id]);
    }

    /**
     * Grava data de finalização e comissão.
     */
    public function finish($id, $commission)
    {
        $sql = 'UPDATE `service`
                SET finished_at = NOW(),
                    commission_user = :commission,
                    update_at = NOW()
                WHERE id_service = :id
                  AND finished_at IS NULL';

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'commission' => $commission,
            'id'         => $id,
        ]);
    }
}
