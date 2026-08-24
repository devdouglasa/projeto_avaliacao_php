<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($title) ? $title : 'DASHBOARD'); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>
<body class="page-app">
    <aside class="sidebar">
        <p class="sidebar-logged">Logado como:</p>
        <p class="sidebar-user"><?php echo e($authUser['name']); ?></p>
        <p class="sidebar-date"><?php echo e(current_date_br()); ?></p>
        <a class="sidebar-link" href="<?php echo e(base_url('servico/cadastrar')); ?>">Cadastrar Serviço</a>
        <a class="sidebar-link sidebar-link-out" href="<?php echo e(base_url('logout')); ?>">Sair</a>
    </aside>

    <div class="content">
        <?php if (!empty($flash)): ?>
            <div class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['message']); ?></div>
        <?php endif; ?>

        <?php require $viewFile; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>
</body>
</html>
