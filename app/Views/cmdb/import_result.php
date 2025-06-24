<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h2>Importación finalizada</h2>
<p>Se insertaron <?= $insertados ?> registros.</p>

<?php if (!empty($errores)): ?>
    <ul>
        <?php foreach ($errores as $e): ?>
            <li><?= esc($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<a href="<?= base_url('cmdb/' . $categoria_id) ?>" class="btn btn-primary">Volver</a>

<?= $this->endSection() ?>
