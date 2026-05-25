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
    /* Dynamic Print CSS */
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
    <div class="col-12 mb-4 no-print card card-premium p-3 border-0">
        <form method="GET" action="" class="row align-items-center">
            <div class="col-md-3">
                <a href="<?= route_to('relatoriu') ?>" class="btn btn-outline-secondary btn-rounded">
                    <i class="fas fa-arrow-left mr-1"></i> Fila
                </a>
            </div>
            <div class="col-md-6 d-flex align-items-center">
                <label for="id_aldeia" class="font-weight-bold text-muted mb-0 mr-2 small text-uppercase">Aldeia:</label>
                <select name="id_aldeia" id="id_aldeia" class="form-control select2 mr-2" style="border-radius: 8px;">
                    <option value="">-- Haree Aldeia Hotu --</option>
                    <?php foreach ($aldeias as $aldeia) : ?>
                        <option value="<?= $aldeia['id_aldeia'] ?>" <?= ($filter_aldeia == $aldeia['id_aldeia']) ? 'selected' : '' ?>>
                            <?= esc($aldeia['naran_aldeia']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary btn-rounded shadow-sm">Filtrar</button>
            </div>
            <div class="col-md-3 text-right">
                <button type="button" onclick="window.print()" class="btn btn-success btn-rounded">
                    <i class="fas fa-print mr-1"></i> Imprime
                </button>
            </div>
        </form>
    </div>

    <!-- Report Table (print-section) -->
    <div class="col-12">
        <div class="card card-premium print-section card-outline card-dark">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <?php if (!empty($cop_temp)) : ?>
                    <?= $cop_temp ?>
                <?php else : ?>
                    <div class="text-center">
                        <h2 class="font-weight-bold text-secondary mb-1">SIPOLAI</h2>
                        <h4 class="text-secondary font-weight-bold mb-3">Relatoriu Dadus Populasaun Suku Laisorolai de Baixo</h4>
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
                                <th>Naran</th>
                                <th>Sexu</th>
                                <th>Fatin Moris</th>
                                <th>Data Moris</th>
                                <th>Estadu Sivil</th>
                                <th>Profisaun</th>
                                <th>Relijiaun</th>
                                <th>Literatura</th>
                                <th>Aldeia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($populasaun)) : ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted">Laiha dadus hetan ho filtru ne'e.</td>
                                </tr>
                            <?php else : ?>
                                <?php $no = 1; foreach ($populasaun as $pop) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td class="font-weight-bold"><?= esc($pop['nik']) ?></td>
                                        <td class="font-weight-bold text-secondary"><?= esc($pop['naran_kompletu']) ?></td>
                                        <td><?= esc($pop['jeneru']) ?></td>
                                        <td><?= esc($pop['fatin_moris']) ?></td>
                                        <td><?= esc($pop['data_moris']) ?></td>
                                        <td><?= esc($pop['status_kaza']) ?></td>
                                        <td><?= esc($pop['naran_profisaun']) ?></td>
                                        <td><?= esc($pop['naran_relijiaun'] ?? '-') ?></td>
                                        <td><?= esc($pop['naran_literatura']) ?></td>
                                        <td><?= esc($pop['naran_aldeia']) ?></td>
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
