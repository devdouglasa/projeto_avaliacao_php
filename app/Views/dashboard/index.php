<section class="summary-row">
    <article class="card card-total">
        <h2>Valor total dos seus serviços</h2>
        <p class="total-value"><?php echo e(money_br($totalUser)); ?></p>
    </article>
</section>

<section class="lists-row">
    <article class="card">
        <h2>Ultimos Serviços</h2>
        <?php if (empty($lastServices)): ?>
            <p class="empty">Nenhum serviço cadastrado.</p>
        <?php else: ?>
            <ul class="plain-list">
                <?php foreach ($lastServices as $item): ?>
                    <li><?php echo e($item['id_service']); ?> - <?php echo e($item['description']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>

    <article class="card">
        <h2>Serviços Pendentes</h2>
        <?php if (empty($pendingList)): ?>
            <p class="empty">Nenhum serviço pendente.</p>
        <?php else: ?>
            <ul class="plain-list">
                <?php foreach ($pendingList as $item): ?>
                    <li><?php echo e($item['id_service']); ?> - <?php echo e($item['description']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>
</section>

<form method="get" action="<?php echo e(base_url('dashboard')); ?>" class="filters">
    <input type="text" name="description" placeholder="Nome do serviço" value="<?php echo e($filters['description']); ?>">
    <input type="text" name="user_name" placeholder="Nome do usuário" value="<?php echo e($filters['user_name']); ?>">
    <select name="status">
        <option value="">Status</option>
        <option value="Pendente" <?php echo $filters['status'] === 'Pendente' ? 'selected' : ''; ?>>Pendente</option>
        <option value="Finalizado" <?php echo $filters['status'] === 'Finalizado' ? 'selected' : ''; ?>>Finalizado</option>
    </select>
    <input type="date" name="date_start" value="<?php echo e($filters['date_start']); ?>">
    <input type="date" name="date_end" value="<?php echo e($filters['date_end']); ?>">
    <button type="submit" class="btn-dark">Filtrar</button>
    <a class="btn-clear" href="<?php echo e(base_url('dashboard')); ?>">Limpar</a>
</form>

<section class="table-box">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>DESCRIÇÃO</th>
                <th>STATUS</th>
                <th>VALOR</th>
                <th>USUÁRIO</th>
                <th>AÇÕES</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($services)): ?>
                <tr>
                    <td colspan="6" class="empty">Nenhum serviço encontrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                    <?php $status = service_status($service['finished_at']); ?>
                    <tr>
                        <td><?php echo e($service['id_service']); ?></td>
                        <td><?php echo e($service['description']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $status === 'Pendente' ? 'pending' : 'done'; ?>">
                                <?php echo e($status); ?>
                            </span>
                        </td>
                        <td><?php echo e(money_br($service['price'])); ?></td>
                        <td><?php echo e($service['user_name']); ?></td>
                        <td class="actions">
                            <a class="btn-small" href="<?php echo e(base_url('servico/editar/' . $service['id_service'])); ?>">Alterar</a>
                            <form method="post" action="<?php echo e(base_url('servico/excluir/' . $service['id_service'])); ?>" class="inline-form js-confirm-delete">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn-small btn-danger">Excluir</button>
                            </form>
                            <?php if ($status === 'Pendente'): ?>
                                <form method="post" action="<?php echo e(base_url('servico/finalizar/' . $service['id_service'])); ?>" class="inline-form js-confirm-finish">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn-small btn-finish">Finalizar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>
