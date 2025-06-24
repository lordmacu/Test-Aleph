<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="text-center mt-5">
    <h1>Bienvenido al Gestor CMDB Aleph</h1>
    <p class="lead">Desde aquí podrás visualizar y gestionar las categorías y registros de la CMDB usando la API de Aleph.</p>
    <a href="<?= base_url('categorias') ?>" class="btn btn-primary mt-3">
        Ver Categorías
    </a>
</div>

<?= $this->endSection() ?>
