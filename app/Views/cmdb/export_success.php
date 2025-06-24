<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h2 class="mb-4">Exportación Exitosa</h2>

<div class="alert alert-success">
    El archivo se ha generado correctamente:
    <a href="<?= esc($file_url) ?>" target="_blank" class="fw-bold">Descargar <?= esc($file_name) ?></a>
</div>

<a href="<?= base_url('cmdb/' . $categoria_id) ?>" class="btn btn-primary">← Volver a la CMDB</a>

<?= $this->endSection() ?>
