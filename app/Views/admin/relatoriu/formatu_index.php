<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<style>
    .card-premium {
        border-radius: 16px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.02), 0 2px 8px -1px rgba(0, 0, 0, 0.01) !important;
        background: #ffffff !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-premium:hover {
        box-shadow: 0 12px 28px -2px rgba(0, 0, 0, 0.05), 0 8px 16px -1px rgba(0, 0, 0, 0.03) !important;
    }
    .btn-rounded {
        border-radius: 10px !important;
        padding: 8px 20px !important;
        font-weight: 600 !important;
        letter-spacing: 0.2px;
        transition: all 0.2s ease !important;
    }
    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        border: none !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.18) !important;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af) !important;
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25) !important;
        transform: translateY(-1px) !important;
    }
    .btn-outline-secondary {
        border: 1px solid #cbd5e1 !important;
        color: #475569 !important;
        background: transparent !important;
    }
    .btn-outline-secondary:hover {
        background: #f8fafc !important;
        color: #1e293b !important;
    }
    .table th {
        border-top: none !important;
        border-bottom: 2px solid #f1f5f9 !important;
        text-transform: uppercase;
        font-size: 11px !important;
        letter-spacing: 0.5px !important;
        color: #64748b !important;
        padding: 14px 12px !important;
        font-weight: 700 !important;
    }
    .table td {
        vertical-align: middle !important;
        padding: 14px 12px !important;
        font-size: 13.5px !important;
        color: #334155 !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .badge-premium {
        font-size: 11px;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }
    @media print { .no-print { display: none !important; } }
</style>

<div class="row">
    <div class="col-12">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-secondary mb-0"><i class="fas fa-heading mr-2"></i> Konfigurasaun Formatu Relatoriu (COP)</h3>
                <a href="<?= route_to('relatoriu') ?>" class="btn btn-outline-secondary btn-rounded">Fila</a>
            </div>
            <div class="card-body px-4 pb-4">
                <?php if (session()->getFlashdata('message')) : ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
                        <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('message') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
                        <i class="fas fa-exclamation-circle mr-2"></i> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="width:100%;">
                        <thead>
                            <tr>
                                <th style="width: 8%">#</th>
                                <th>Naran Formatu Relatoriu</th>
                                <th>Status Template COP</th>
                                <th class="text-center" style="width: 25%">Aksaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($formats as $f) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="font-weight-bold text-secondary"><?= esc($f['naran_relatoriu']) ?></td>
                                    <td>
                                        <?php if (!empty($f['template_cop'])) : ?>
                                            <span class="badge badge-success badge-premium"><i class="fas fa-check mr-1"></i> Prepara Tiha Ona</span>
                                        <?php else : ?>
                                            <span class="badge badge-warning badge-premium text-white"><i class="fas fa-exclamation mr-1"></i> Seidauk Iha Template</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/formatu-relatoriu/' . $f['id_formatu_relatoriu'] . '/edit') ?>" class="btn btn-primary btn-rounded btn-sm" style="padding: 6px 16px !important; font-size: 12.5px;">
                                            <i class="fas fa-cog mr-1"></i> Konfigura COP
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
