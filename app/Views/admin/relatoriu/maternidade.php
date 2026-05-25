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
    /* Print CSS */
    @media print {
        /* Hide all interface chrome and unwanted elements */
        .main-header, 
        .main-sidebar, 
        .main-footer, 
        .no-print, 
        .content-header, 
        .breadcrumb,
        footer {
            display: none !important;
        }
        
        /* Reset and maximize all layout containers */
        html, body {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: #ffffff !important;
        }
        
        .wrapper, 
        .content-wrapper, 
        .content, 
        .container-fluid, 
        .row, 
        .col-12 {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            position: static !important;
            display: block !important;
        }
        
        .print-section {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            position: static !important;
            display: block !important;
        }
        
        .card-header {
            padding: 0 !important;
            margin: 0 0 15px 0 !important;
            width: 100% !important;
            border: none !important;
            background: transparent !important;
            display: block !important;
        }
        
        .card-body {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            background: transparent !important;
            display: block !important;
        }
    }
    @media print { .no-print { display: none !important; } }
</style>

<div class="row">
    <!-- Action Panel (no-print) -->
    <div class="col-12 mb-4 no-print d-flex justify-content-between align-items-center">
        <a href="<?= route_to('relatoriu') ?>" class="btn btn-outline-secondary btn-rounded">Fila</a>
        <button onclick="window.print()" class="btn btn-success btn-rounded">
            <i class="fas fa-print mr-1"></i> Imprime Relatoriu
        </button>
    </div>

    <!-- Maternal Report Table (print-section) -->
    <div class="col-12">
        <div class="card card-premium print-section card-outline card-danger">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <?php if (!empty($cop_temp)) : ?>
                    <?= $cop_temp ?>
                <?php else : ?>
                    <div class="text-center">
                        <h2 class="font-weight-bold text-secondary mb-1">SIPOLAI</h2>
                        <h4 class="text-secondary font-weight-bold mb-3">Relatoriu Numeru Maternidade (Idade 15-49 Anos)</h4>
                    </div>
                <?php endif; ?>
                <hr class="my-4 no-print">
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NIP</th>
                                <th>Naran Kompletu</th>
                                <th>Data Moris</th>
                                <th>Idade</th>
                                <th>Aldeia</th>
                                <th>Estadu Sivil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($membros)) : ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Laiha dadus feto ho idade reprodutivu (15-49 anos).</td>
                                </tr>
                            <?php else : ?>
                                <?php $no = 1; foreach ($membros as $m) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td class="font-weight-bold"><?= esc($m['nik']) ?></td>
                                        <td class="font-weight-bold text-secondary"><?= esc($m['naran_kompletu']) ?></td>
                                        <td><?= date('d-m-Y', strtotime($m['data_moris'])) ?></td>
                                        <td class="font-weight-bold text-danger"><?= $m['age'] ?> Anos</td>
                                        <td><?= esc($m['naran_aldeia']) ?></td>
                                        <td><?= esc($m['status_kaza']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
