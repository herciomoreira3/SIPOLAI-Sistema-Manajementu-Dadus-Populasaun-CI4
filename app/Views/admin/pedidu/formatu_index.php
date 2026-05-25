<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<style>
    .card-premium {
        border-radius: 16px;
        border: none;
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.04);
        background: #fff;
    }
    .btn-rounded {
        border-radius: 30px;
        padding: 6px 20px;
        font-weight: 600;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-secondary mb-0"><i class="fas fa-file-signature mr-2"></i> Konfigurasaun Formatu Deklarasaun</h3>
                <a href="<?= route_to('relatoriu') ?>" class="btn btn-outline-secondary btn-rounded">Fila</a>
            </div>
            <div class="card-body px-4 pb-4">
                <?php if (session()->getFlashdata('message')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('message') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Naran Tipu Deklarasaun</th>
                                <th>Status Template</th>
                                <th class="text-center">Aksaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($tipus as $t) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="font-weight-bold text-secondary"><?= esc($t['naran_tipu_pedidu']) ?></td>
                                    <td>
                                        <?php if (!empty($t['template_formatu'])) : ?>
                                            <span class="badge badge-success px-3 py-1">Prepara Tiha Ona</span>
                                        <?php else : ?>
                                            <span class="badge badge-warning px-3 py-1">Seidauk Iha Template</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/formatu-deklarasaun/' . $t['id_tipu_pedidu'] . '/edit') ?>" class="btn btn-primary btn-rounded btn-sm">
                                            <i class="fas fa-cog mr-1"></i> Konfigura Template
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
