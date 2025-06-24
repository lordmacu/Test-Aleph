<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Categorías</h2>
    <button id="import-btn" class="btn btn-success">
        <i class="fas fa-sync-alt"></i> Importar Categorías desde API
    </button>
</div>

<!-- Container for AJAX messages -->
<div id="ajax-message" class="mb-3"></div>

<?php if (!empty($categories)): ?>

    <!-- The table is now only rendered if there are categories to display -->
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ver Registros</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= esc($cat['id']) ?></td>
                    <td><?= esc($cat['nombre']) ?></td>
                    <td>
                        <a href="<?= base_url('cmdb/' . $cat['id']) ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Ver CMDB
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php else: ?>

    <div class="alert alert-info text-center" role="alert">
        <h4 class="alert-heading">¡No hay categorías!</h4>
        <p>Actualmente no tienes ninguna categoría en tu base de datos local.</p>
        <hr>
        <p class="mb-0">Puedes importarlas desde la API usando el botón de arriba.</p>
    </div>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const importUrl = '<?= site_url('categorias/importar-ajax') ?>';
    const data = {
        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
    }
</script>
<script src="<?= base_url('js/categories.js') ?>"></script>

<?= $this->endSection() ?>