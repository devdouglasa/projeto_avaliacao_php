<h1>Cadastrar Novo Usuário</h1>

<form method="post" action="<?php echo e(base_url('usuario/cadastrar')); ?>" class="form-stack js-validate-register">
    <?php echo csrf_field(); ?>
    <input type="text" name="name" placeholder="nome" maxlength="150" required>
    <input type="email" name="email" placeholder="email@email.com" required>
    <input type="password" name="password" placeholder="****************" required>
    <div class="form-row">
        <button type="submit" class="btn-dark">Cadastrar</button>
        <a class="link-register" href="<?php echo e(base_url('login')); ?>">Voltar ao login</a>
    </div>
</form>
