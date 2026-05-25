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
    <!-- Left Column: Family Card details and Add Member -->
    <div class="col-md-4">
        <!-- Details Card -->
        <div class="card card-premium mb-4 card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-info-circle mr-2"></i> Detaillu Fixa Familia</h3>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="mb-3">
                    <label class="text-muted font-weight-bold d-block mb-1">Numeru Fixa Familia</label>
                    <span class="h4 font-weight-bold text-primary"><?= esc($familia['numeru_kk']) ?></span>
                </div>
                <div class="mb-3">
                    <label class="text-muted font-weight-bold d-block mb-1">Aldeia</label>
                    <span class="badge badge-light p-2 font-weight-bold text-secondary" style="font-size: 14px;">
                        <i class="fas fa-map-marked-alt mr-1"></i> <?= esc($familia['naran_aldeia']) ?>
                    </span>
                </div>
                <div class="mb-3">
                    <label class="text-muted font-weight-bold d-block mb-1">Total Membru</label>
                    <span class="badge badge-info px-3 py-1 font-weight-bold" style="font-size: 14px;">
                        <?= count($membros) ?> Ema
                    </span>
                </div>
                <hr>
                <?php
                $hasXefe = false;
                foreach ($membros as $m) {
                    if ($m['relasaun_familia'] == 'Xefe Familia') {
                        $hasXefe = true;
                        break;
                    }
                }
                ?>
                <div class="d-flex justify-content-between mt-3">
                    <a href="<?= base_url('admin/familia') ?>" class="btn btn-outline-secondary btn-rounded">Fila</a>
                    <div>
                        <?php if ($hasXefe) : ?>
                            <button class="btn btn-warning btn-rounded mr-1 text-white" data-toggle="modal" data-target="#uploadFotoModal" title="Upload Foto Xefe Familia">
                                <i class="fas fa-camera"></i>
                            </button>
                            <button onclick="window.print();" class="btn btn-success btn-rounded mr-1 text-white" title="Imprime Fixa Familia">
                                <i class="fas fa-print"></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?= base_url('admin/familia/' . $familia['id_familia'] . '/edit') ?>" class="btn btn-info btn-rounded">Hadia</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Member Card -->
        <div class="card card-premium card-outline card-warning">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-user-plus mr-2"></i> Aumenta Membru ba Fixa Familia</h3>
            </div>
            <div class="card-body px-4 pb-4">
                <?php if (empty($unassignedResidents)) : ?>
                    <div class="alert alert-warning text-center" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Laiha rezidente iha Aldeia <?= esc($familia['naran_aldeia']) ?> ne'ebé laiha Fixa Familia. Kria rezidente foun uluk!
                    </div>
                <?php else : ?>
                    <form action="<?= base_url('admin/familia/' . $familia['id_familia'] . '/add') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="form-group mb-3">
                            <label for="id_populasaun" class="font-weight-bold text-muted">Hili Rezidente</label>
                            <select name="id_populasaun" id="id_populasaun" class="form-select select2" style="width: 100%;" required>
                                <option value="">-- Hili rezidente --</option>
                                <?php foreach ($unassignedResidents as $res) : ?>
                                    <option value="<?= $res['id_populasaun'] ?>">
                                        <?= esc($res['naran_kompletu']) ?> (NIP: <?= esc($res['nik']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="relasaun_familia" class="font-weight-bold text-muted">Relasaun Familia</label>
                            <select name="relasaun_familia" id="relasaun_familia" class="form-select" required>
                                <option value="">-- Hili Relasaun --</option>
                                <?php if (!$hasXefe) : ?>
                                    <option value="Xefe Familia" <?= count($membros) == 0 ? 'selected' : '' ?>>Xefe Familia</option>
                                <?php endif; ?>
                                <?php if (count($membros) > 0) : ?>
                                    <option value="Fen">Kônjuge / Fen</option>
                                    <option value="Oan">Oan</option>
                                    <option value="Seluk">Seluk</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-warning btn-block btn-rounded text-white mt-3">
                            <i class="fas fa-plus mr-1"></i> Aumenta Membru
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Family Members List -->
    <div class="col-md-8">
        <div class="card card-premium card-outline card-success">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-users mr-2"></i> Membru Familia</h3>
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
                
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (empty($membros)) : ?>
                    <div class="text-center py-5">
                        <img src="https://cdn-icons-png.flaticon.com/512/3233/3233481.png" alt="Empty" style="width: 100px; opacity: 0.3;" class="mb-3">
                        <h5 class="text-muted font-weight-bold">Seidauk iha membru rejistadu</h5>
                        <p class="text-muted small">Uza form iha karuk atu aumenta membru ba fixa familia ne'e.</p>
                    </div>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>NIP</th>
                                    <th>Naran Kompletu</th>
                                    <th>Sexu</th>
                                    <th>Data Moris</th>
                                    <th>Relasaun</th>
                                    <th class="text-center">Aksaun</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($membros as $membru) : ?>
                                    <tr>
                                        <td class="font-weight-bold"><?= esc($membru['nik']) ?></td>
                                        <td class="text-secondary font-weight-bold"><?= esc($membru['naran_kompletu']) ?></td>
                                        <td><span class="badge badge-light p-2"><?= esc($membru['jeneru']) ?></span></td>
                                        <td><?= date('d-m-Y', strtotime($membru['data_moris'])) ?></td>
                                        <td>
                                            <?php if ($membru['relasaun_familia'] == 'Xefe Familia') : ?>
                                                <span class="badge badge-primary px-3 py-1"><?= esc($membru['relasaun_familia']) ?></span>
                                            <?php else : ?>
                                                <span class="badge badge-secondary px-3 py-1"><?= esc($membru['relasaun_familia']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('admin/populasaun/' . $membru['id_populasaun'] . '/edit') ?>" class="btn btn-sm btn-info rounded-circle mr-1" title="Hadia Rezidente">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('admin/familia/' . $familia['id_familia'] . '/remove/' . $membru['id_populasaun']) ?>" class="btn btn-sm btn-danger rounded-circle remove-member-btn" title="Hasai husi Fixa Familia">
                                                <i class="fas fa-user-minus"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<!-- PRINTABLE OFFICIAL FAMILY CARD (FIXA FAMILIA) -->
<style>
    /* Screen Styles for Printable Ficha */
    #printable-ficha {
        display: none;
    }

    /* Print Styles */
    @media print {
        /* Hide everything else */
        body * {
            visibility: hidden;
        }
        /* Show only printable ficha */
        #printable-ficha, #printable-ficha * {
            visibility: visible;
        }
        #printable-ficha {
            display: block !important;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background-color: #fff;
            padding: 10px 0;
        }
        /* Page setup */
        @page {
            size: landscape;
            margin: 0.8cm;
        }
        .no-print {
            display: none !important;
        }
    }

    /* Styling of official card elements */
    .ficha-title {
        font-family: 'Times New Roman', Times, serif;
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 25px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        text-decoration: underline;
    }
    .ficha-header-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        font-family: 'Times New Roman', Times, serif;
        font-size: 13px;
        line-height: 1.5;
    }
    .ficha-header-left {
        width: 35%;
    }
    .ficha-header-middle {
        width: 30%;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .ficha-header-right {
        width: 35%;
        text-align: left;
        padding-left: 20px;
    }
    .ficha-photo-box {
        border: 1.5px solid #000;
        width: 100px;
        height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        font-size: 11px;
        font-weight: bold;
        line-height: 1.3;
        margin-top: 10px;
        background: #fff;
    }
    .ficha-table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Times New Roman', Times, serif;
        font-size: 11px;
        margin-top: 15px;
    }
    .ficha-table th, .ficha-table td {
        border: 1px solid #000 !important;
        padding: 5px 3px;
        text-align: center;
        vertical-align: middle;
    }
    .ficha-table th {
        background-color: #e9ecef !important;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 10px;
        height: 28px;
    }
    .ficha-table .num-row td {
        background-color: #f1f3f5 !important;
        font-size: 9px;
        font-weight: bold;
        padding: 2px !important;
        height: auto;
    }
    .ficha-table tbody tr {
        height: 32px;
    }
</style>

<?php
// Extract Xefe Familia Name
$xefeName = '-';
foreach ($membros as $m) {
    if ($m['relasaun_familia'] == 'Xefe Familia') {
        $xefeName = $m['naran_kompletu'];
        break;
    }
}
?>

<div id="printable-ficha">
    <?php if (!empty($cop_temp)) : ?>
        <?= $cop_temp ?>
    <?php endif; ?>

    <div class="ficha-title">
        LIVRU REJISTU UMA KAIN ( FICHA FAMÍLIA )
    </div>

    <div class="ficha-header-container">
        <!-- Left Column -->
        <div class="ficha-header-left">
            <table style="width: 100%; border: none;">
                <tr style="border: none;">
                    <td style="width: 35%; border: none; padding: 2px 0;">Munisipiu</td>
                    <td style="width: 5%; border: none; padding: 2px 0;">:</td>
                    <td style="border: none; padding: 2px 0; border-bottom: 1px dotted #000;">Baucau</td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; padding: 2px 0;">Postu Administrativu</td>
                    <td style="border: none; padding: 2px 0;">:</td>
                    <td style="border: none; padding: 2px 0; border-bottom: 1px dotted #000;">Matebian</td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; padding: 2px 0;">Suku</td>
                    <td style="border: none; padding: 2px 0;">:</td>
                    <td style="border: none; padding: 2px 0; border-bottom: 1px dotted #000;">Laisorolai de Baixo</td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; padding: 2px 0;">Aldeia / Bairo</td>
                    <td style="border: none; padding: 2px 0;">:</td>
                    <td style="border: none; padding: 2px 0; border-bottom: 1px dotted #000; font-weight: bold;"><?= esc($familia['naran_aldeia']) ?></td>
                </tr>
            </table>
        </div>

        <!-- Middle Column -->
        <div class="ficha-header-middle">
            <table style="width: 100%; border: none;">
                <tr style="border: none;">
                    <td style="width: 35%; border: none; padding: 2px 0; text-align: right; padding-right: 10px;">No.Fixa</td>
                    <td style="width: 5%; border: none; padding: 2px 0;">:</td>
                    <td style="border: none; padding: 2px 0; border-bottom: 1px dotted #000; text-align: left; font-weight: bold; font-size: 14px;"><?= esc($familia['numeru_kk']) ?></td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; padding: 2px 0; text-align: right; padding-right: 10px;">Tinan</td>
                    <td style="border: none; padding: 2px 0;">:</td>
                    <td style="border: none; padding: 2px 0; border-bottom: 1px dotted #000; text-align: left;"><?= date('Y', strtotime($familia['created_at'] ?? 'now')) ?></td>
                </tr>
            </table>
            <div class="ficha-photo-box">
                <?php if (!empty($familia['foto']) && file_exists(FCPATH . 'uploads/familia/' . $familia['foto'])) : ?>
                    <img src="<?= base_url('uploads/familia/' . $familia['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="Foto Xefe Familia">
                <?php else : ?>
                    <div>Foto Xefe</div>
                    <div>Familia</div>
                    <div style="font-size: 9px; margin-top: 5px;">(2x3)</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column -->
        <div class="ficha-header-right">
            <table style="width: 100%; border: none;">
                <tr style="border: none;">
                    <td style="width: 35%; border: none; padding: 2px 0;">Fam</td>
                    <td style="width: 5%; border: none; padding: 2px 0;">:</td>
                    <td style="border: none; padding: 2px 0; border-bottom: 1px dotted #000;">ID-<?= esc($familia['id_familia']) ?></td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; padding: 2px 0; vertical-align: top;">Xefe Familia</td>
                    <td style="border: none; padding: 2px 0; vertical-align: top;">:</td>
                    <td style="border: none; padding: 2px 0; border-bottom: 1px dotted #000; font-weight: bold;"><?= esc($xefeName) ?></td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; padding: 2px 0;">&nbsp;</td>
                    <td style="border: none; padding: 2px 0;">&nbsp;</td>
                    <td style="border: none; padding: 2px 0; border-bottom: 1px dotted #000;">&nbsp;</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Official Table -->
    <table class="ficha-table">
        <thead>
            <tr>
                <th style="width: 4%;">Nu</th>
                <th style="width: 20%;">Naran</th>
                <th style="width: 7%;">Sexu</th>
                <th style="width: 12%;">Relasaun Familia</th>
                <th style="width: 10%;">Fatin Moris</th>
                <th style="width: 11%;">Data, Fulan no Tinan Moris</th>
                <th style="width: 9%;">Estado Sivil</th>
                <th style="width: 9%;">Profisaun</th>
                <th style="width: 9%;">Relijiaun</th>
                <th style="width: 9%;">Habilitasaun Literaria</th>
                <th style="width: 5%;">Obs</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $maxRows = 10;
            ?>
            <!-- Member Records -->
            <?php foreach ($membros as $membru) : ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td style="text-align: left; padding-left: 6px; font-weight: bold;"><?= esc($membru['naran_kompletu']) ?></td>
                    <td><?= esc($membru['jeneru']) ?></td>
                    <td><?= esc($membru['relasaun_familia']) ?></td>
                    <td><?= esc($membru['fatin_moris']) ?></td>
                    <td><?= date('d-m-Y', strtotime($membru['data_moris'])) ?></td>
                    <td><?= esc($membru['status_kaza']) ?></td>
                    <td><?= esc($membru['naran_profisaun']) ?: '-' ?></td>
                    <td><?= esc($membru['naran_relijiaun']) ?: '-' ?></td>
                    <td><?= esc($membru['naran_literatura']) ?: '-' ?></td>
                    <td>-</td>
                </tr>
            <?php endforeach; ?>

            <!-- Additional Blank Rows up to maxRows to match official layout -->
            <?php for ($i = count($membros) + 1; $i <= $maxRows; $i++) : ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>

<!-- UPLOAD FOTO MODAL -->
<div class="modal fade" id="uploadFotoModal" tabindex="-1" role="dialog" aria-labelledby="uploadFotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header bg-warning text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title font-weight-bold" id="uploadFotoModalLabel"><i class="fas fa-camera mr-2"></i> Upload Foto Xefe Familia</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/familia/' . $familia['id_familia'] . '/upload-foto') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <?php if (!empty($familia['foto']) && file_exists(FCPATH . 'uploads/familia/' . $familia['foto'])) : ?>
                            <img src="<?= base_url('uploads/familia/' . $familia['foto']) ?>" class="img-thumbnail" style="max-width: 150px; height: 180px; object-fit: cover; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" alt="Foto">
                            <p class="text-muted small mt-2">Foto atual ne'ebé uza daudaun</p>
                        <?php else : ?>
                            <div class="d-inline-flex align-items-center justify-content-center bg-light text-muted" style="width: 150px; height: 180px; border: 2px dashed #ccc; border-radius: 12px;">
                                <div class="text-center">
                                    <i class="fas fa-image fa-3x mb-2 text-muted"></i>
                                    <p class="small m-0 text-muted">Laiha foto</p>
                                </div>
                            </div>
                            <p class="text-muted small mt-2">Seidauk iha foto. Upload foto formatu 2x3.</p>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="foto" class="font-weight-bold text-muted">Hili Fail Imajen</label>
                        <div class="custom-file">
                            <input type="file" name="foto" class="custom-file-input" id="foto" accept="image/*" required>
                            <label class="custom-file-label" for="foto">Hili imajen...</label>
                        </div>
                        <small class="form-text text-muted">Format uza: JPEG, PNG. Rekomenda taman foto 2x3.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-warning btn-rounded text-white font-weight-bold"><i class="fas fa-cloud-upload-alt mr-1"></i> Upload Agora</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- Include Select2 CDN -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4'
        });

        // SweetAlert Remove Member Confirmation
        $('.remove-member-btn').on('click', function(e) {
            e.preventDefault();
            var url = e.currentTarget.href || $(this).attr('href');
            if (!url) {
                return;
            }
            Swal.fire({
                title: 'Hasai membru ne\'e?',
                text: "Membru ne'e sei hasai husi Fixa Familia maibe sei la hamoos husi dadus populasaun!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff5858',
                cancelButtonColor: '#a18cd1',
                confirmButtonText: 'Sim, Hasai!',
                cancelButtonText: 'Kansela'
            }).then((result) => {
                if (result.isConfirmed || result.value === true) {
                    window.location.href = url;
                }
            });
        });

        // Display selected file name in custom file input
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        <?php if (isset($_GET['print']) && $_GET['print'] === 'true') : ?>
            // Automatic Print Trigger
            setTimeout(function() {
                window.print();
            }, 600);
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>
