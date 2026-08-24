<h1><?php echo e($title); ?></h1>

<form method="post" action="<?php echo e($action); ?>" class="form-stack form-service js-validate-service">
    <?php echo csrf_field(); ?>
    <input
        type="text"
        name="description"
        placeholder="descrição"
        maxlength="45"
        required
        value="<?php echo e($service ? $service['description'] : ''); ?>"
    >
    <input
        type="text"
        name="price"
        class="js-price"
        placeholder="preço"
        required
        value="<?php echo e($service ? number_format((float) $service['price'], 2, ',', '.') : ''); ?>"
    >
    <div class="form-row">
        <button type="submit" class="btn-dark"><?php echo e($buttonLabel); ?></button>
        <a class="link-register" href="<?php echo e(base_url('dashboard')); ?>">Voltar</a>
    </div>
</form>
