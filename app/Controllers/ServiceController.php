<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Helpers\Commission;
use App\Helpers\Mailer;
use App\Models\Service;
use App\Models\User;

/**
 * Cadastro, alteração, exclusão e finalização de serviços.
*/
class ServiceController extends Controller
{
    private $services;
    private $users;

    public function __construct()
    {
        $this->services = new Service();
        $this->users = new User();
    }

    public function create()
    {
        $this->requireLogin();
        $this->view('service/form', [
            'title'        => 'Cadastrar Novo Serviço',
            'service'      => null,
            'action'       => base_url('servico/cadastrar'),
            'buttonLabel'  => 'Cadastrar',
        ], 'auth');
    }

    public function store()
    {
        $this->requireLogin();
        $this->validateCsrf();

        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $price = $this->parsePrice(isset($_POST['price']) ? $_POST['price'] : '');

        if (!$this->isValidServiceInput($description, $price)) {
            Session::flash('error', 'Falha ao cadastrar o serviço. Informe descrição e valor.');
            $this->redirect('dashboard');
        }

        try {
            $id = $this->services->create($description, $price, Auth::id());
        } catch (\PDOException $e) {
            $id = false;
        }

        if (!$id) {
            Session::flash('error', 'Falha ao cadastrar o serviço.');
            $this->redirect('dashboard');
        }

        Session::flash('success', 'Serviço cadastrado com sucesso.');
        $this->redirect('dashboard');
    }

    public function edit($id)
    {
        $this->requireLogin();

        $service = $this->services->findById($id);

        if (!$service) {
            Session::flash('error', 'Serviço não encontrado.');
            $this->redirect('dashboard');
        }

        $this->view('service/form', [
            'title'        => 'Alterar Serviço',
            'service'      => $service,
            'action'       => base_url('servico/editar/' . $id),
            'buttonLabel'  => 'Salvar',
        ], 'auth');
    }

    public function update($id)
    {
        $this->requireLogin();
        $this->validateCsrf();

        $service = $this->services->findById($id);

        if (!$service) {
            Session::flash('error', 'Serviço não encontrado.');
            $this->redirect('dashboard');
        }

        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $price = $this->parsePrice(isset($_POST['price']) ? $_POST['price'] : '');

        if (!$this->isValidServiceInput($description, $price)) {
            Session::flash('error', 'Falha ao alterar o serviço. Informe descrição e valor.');
            $this->redirect('dashboard');
        }

        try {
            $ok = $this->services->update($id, $description, $price);
        } catch (\PDOException $e) {
            $ok = false;
        }

        if (!$ok) {
            Session::flash('error', 'Falha ao alterar o serviço.');
            $this->redirect('dashboard');
        }

        Session::flash('success', 'Serviço alterado com sucesso.');
        $this->redirect('dashboard');
    }

    public function delete($id)
    {
        $this->requireLogin();
        $this->validateCsrf();

        $service = $this->services->findById($id);

        if (!$service) {
            Session::flash('error', 'Serviço não encontrado.');
            $this->redirect('dashboard');
        }

        $this->services->delete($id);
        Session::flash('success', 'Serviço excluído com sucesso.');
        $this->redirect('dashboard');
    }

    /**
     * Finaliza o serviço: grava finished_at, calcula comissão e envia e-mail.
     */
    public function finish($id)
    {
        $this->requireLogin();
        $this->validateCsrf();

        $service = $this->services->findById($id);

        if (!$service) {
            Session::flash('error', 'Serviço não encontrado.');
            $this->redirect('dashboard');
        }

        if (!empty($service['finished_at'])) {
            Session::flash('error', 'Este serviço já está finalizado.');
            $this->redirect('dashboard');
        }

        $commission = Commission::calculate($service['price']);
        $ok = $this->services->finish($id, $commission);

        if (!$ok) {
            Session::flash('error', 'Não foi possível finalizar o serviço.');
            $this->redirect('dashboard');
        }

        $updated = $this->services->findById($id);
        $owner = $this->users->findById($updated['user_id_user']);

        if ($owner) {
            Mailer::notifyServiceFinished($owner, $updated);
        }

        Session::flash('success', 'Serviço finalizado. Comissão: ' . money_br($commission) . '.');
        $this->redirect('dashboard');
    }

    private function isValidServiceInput($description, $price)
    {
        $length = function_exists('mb_strlen')
            ? mb_strlen($description, 'UTF-8')
            : strlen($description);

        return $description !== '' && $length <= 45 && $price !== null && $price > 0;
    }

    /**
     * Converte valor informado no formulário (1.250,50 ou 1250.50) para float.
     *
     * @return float|null
     */
    private function parsePrice($raw)
    {
        $raw = trim((string) $raw);

        if ($raw === '') {
            return null;
        }

        $raw = str_replace(['R$', ' '], '', $raw);

        if (strpos($raw, ',') !== false) {
            $raw = str_replace('.', '', $raw);
            $raw = str_replace(',', '.', $raw);
        }

        if (!is_numeric($raw)) {
            return null;
        }

        return round((float) $raw, 3);
    }
}
