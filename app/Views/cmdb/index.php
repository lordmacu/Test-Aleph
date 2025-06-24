<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h2 class="mb-4">Registros de la CMDB - Categoría #<?= esc($categoria_id) ?></h2>

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="<?= base_url('categorias') ?>" class="btn btn-secondary">← Volver a categorías</a>
    <div>
        <button class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#modalImportar">
            Importar Registros
        </button>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalExportar">
            Exportar Registros
        </button>
    </div>
</div>

<?php if (!empty($registros)): ?>
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Identificador</th>
                <th>Fecha Creación</th>
                <th>Activado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($item['nombre'] ?? 'N/A') ?></td>
                    <td><?= esc($item['identificador'] ?? 'N/A') ?></td>
                    <td><?= esc($item['fecha_creacion'] ?? '-') ?></td>
                    <td>
                        <?= isset($item['activado']) && $item['activado'] == 1
                            ? '<span class="badge bg-success">Sí</span>'
                            : '<span class="badge bg-danger">No</span>' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-warning">
        No se encontraron registros para esta categoría.
    </div>
<?php endif; ?>

<!-- Modal Importar -->
<?= view('cmdb/import_form', ['categoria_id' => $categoria_id]) ?>

<!-- Modal Exportar -->
<div class="modal fade" id="modalExportar" tabindex="-1" aria-labelledby="modalExportarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="get" action="<?= base_url('cmdb/exportar/' . $categoria_id) ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExportarLabel">Confirmar Exportación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ¿Deseas generar un archivo con los registros actuales de esta categoría?
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Exportar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>