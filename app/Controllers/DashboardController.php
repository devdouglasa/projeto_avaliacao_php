<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Service;

/**
 * Tela inicial: dados do usuário, data atual, resumos e tabela de serviços.
*/
class DashboardController extends Controller
{
    private $services;

    public function __construct()
    {
        $this->services = new Service();
    }

    public function index()
    {
        $this->requireLogin();

        $userId = Auth::id();
        $filters = [
            'description' => isset($_GET['description']) ? trim($_GET['description']) : '',
            'user_name'   => isset($_GET['user_name']) ? trim($_GET['user_name']) : '',
            'status'      => isset($_GET['status']) ? trim($_GET['status']) : '',
            'date_start'  => isset($_GET['date_start']) ? trim($_GET['date_start']) : '',
            'date_end'    => isset($_GET['date_end']) ? trim($_GET['date_end']) : '',
        ];

        $this->view('dashboard/index', [
            'services'      => $this->services->listWithUser($filters),
            'filters'       => $filters,
            'totalUser'     => $this->services->totalByUser($userId),
            'lastServices'  => $this->services->lastByUser($userId, 3),
            'pendingList'   => $this->services->lastPendingByUser($userId, 5),
            'currentDate'   => current_date_br(),
        ]);
    }
}
