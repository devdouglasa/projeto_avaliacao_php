<h1>Sistema de Controle de Serviços</h1>

<form method="post" action="<?php echo e(base_url('login')); ?>" class="form-stack js-validate-login">
    <?php echo csrf_field(); ?>
    <input type="email" name="email" placeholder="email@email.com" required>
    <input type="password" name="password" placeholder="****************" required>
    <div class="form-row">
        <button type="submit" class="btn-dark">Entrar</button>
        <a class="link-register" href="<?php echo e(base_url('usuario/cadastrar')); ?>">Cadastrar usuário</a>
    </div>
</form>
