<?= $this->include('Boilerplate\Views\load\datatables') ?>
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
    .table th {
        border-top: none;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        color: #748a9e;
        padding: 12px 8px;
    }
    .table td {
        vertical-align: middle !important;
        padding: 12px 8px;
        font-size: 13px;
    }
    .badge-premium {
        font-size: 11px;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }
    .modal-premium {
        border-radius: 16px !important;
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .modal-header-premium {
        border-top-left-radius: 16px !important;
        border-top-right-radius: 16px !important;
        background: linear-gradient(135deg, #09090b, #1e293b);
        color: #ffffff;
        border-bottom: none;
        padding: 20px;
    }
    .modal-footer-premium {
        border-bottom-left-radius: 16px !important;
        border-bottom-right-radius: 16px !important;
        border-top: none;
        padding: 20px;
    }
    .form-control-premium {
        border-radius: 8px;
        border: 1px solid #e4e4e7;
        padding: 12px;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    .form-control-premium:focus {
        border-color: #09090b;
        box-shadow: 0 0 0 2px rgba(9, 9, 11, 0.05);
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-secondary mb-0">
                    <i class="fas fa-id-card text-primary mr-2"></i> <?= $title ?>
                </h3>
            </div>
            <div class="card-body px-4 pb-4">
                <!-- Filters Bar -->
                <div class="row mb-4 align-items-end">
                    <div class="col-md-4">
                        <label for="filter-aldeia" class="font-weight-bold text-muted small text-uppercase">Filtru bazeia ba Aldeia</label>
                        <select id="filter-aldeia" class="form-control select2 shadow-sm" style="border-radius: 8px;">
                            <option value="">-- Haree Aldeia Hotu --</option>
                            <?php foreach ($aldeias as $aldeia) : ?>
                                <option value="<?= esc($aldeia['naran_aldeia']) ?>"><?= esc($aldeia['naran_aldeia']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="table-eleitores" class="table table-hover va-middle" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 4%;">#</th>
                                <th>NIP/NIK</th>
                                <th>Naran Kompletu</th>
                                <th>Sexu</th>
                                <th>Aldeia</th>
                                <th>Data Deklarasaun</th>
                                <th>Kartaun Eleitoral</th>
                                <th>Estatutu</th>
                                <?php if (!in_groups('xefe-aldeia')) : ?>
                                    <th class="text-center" style="width: 12%;">Aksaun</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($eleitores as $eleitor) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><span class="font-weight-bold text-primary"><?= esc($eleitor['nik']) ?></span></td>
                                    <td><span class="font-weight-bold text-secondary"><?= esc($eleitor['naran_kompletu']) ?></span></td>
                                    <td>
                                        <span class="badge <?= $eleitor['jeneru'] === 'Mane' ? 'badge-primary' : 'badge-danger' ?> badge-premium"><?= esc($eleitor['jeneru']) ?></span>
                                    </td>
                                    <td><span class="badge badge-light badge-premium border"><?= esc($eleitor['naran_aldeia'] ?? '-') ?></span></td>
                                    <td><?= !empty($eleitor['data_aprovada']) ? date('d/m/Y', strtotime($eleitor['data_aprovada'])) : '-' ?></td>
                                    <td>
                                        <?php if (!empty($eleitor['no_eleitoral'])) : ?>
                                            <span class="badge badge-success badge-premium"><i class="fas fa-check-circle mr-1"></i> <?= esc($eleitor['no_eleitoral']) ?></span>
                                        <?php else : ?>
                                            <span class="badge badge-warning badge-premium text-dark"><i class="fas fa-exclamation-triangle mr-1"></i> Seidauk Iha Kartaun</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($eleitor['no_eleitoral'])) : ?>
                                            <span class="badge badge-success badge-premium"><i class="fas fa-check mr-1"></i> Ativu</span>
                                        <?php else : ?>
                                            <span class="badge badge-warning badge-premium text-dark"><i class="fas fa-sync-alt fa-spin mr-1"></i> Prosesa Hela</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if (!in_groups('xefe-aldeia')) : ?>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-sm <?= !empty($eleitor['no_eleitoral']) ? 'btn-info' : 'btn-primary' ?> btn-rounded btn-edit-eleitor shadow-sm" 
                                                        data-id="<?= $eleitor['id_populasaun'] ?>" 
                                                        data-naran="<?= esc($eleitor['naran_kompletu']) ?>" 
                                                        data-eleitoral="<?= esc($eleitor['no_eleitoral'] ?? '') ?>" 
                                                        title="<?= !empty($eleitor['no_eleitoral']) ? 'Hadia Kartaun' : 'Preenxe Kartaun' ?> Eleitoral">
                                                    <i class="<?= !empty($eleitor['no_eleitoral']) ? 'fas fa-edit' : 'fas fa-plus' ?> mr-1"></i> <?= !empty($eleitor['no_eleitoral']) ? 'Hadia Kartaun' : 'Preenxe Kartaun' ?>
                                                </button>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Kartaun Eleitoral -->
<div class="modal fade" id="modal-eleitor" tabindex="-1" role="dialog" aria-labelledby="modalEleitorTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-premium">
            <div class="modal-header modal-header-premium">
                <h5 class="modal-title font-weight-bold" id="modalEleitorTitle">
                    <i class="fas fa-id-card mr-2"></i> Kartaun Eleitoral
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-update-eleitor">
                <div class="modal-body p-4">
                    <input type="hidden" id="populasaun-id" name="id_populasaun">
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-secondary mb-1">Naran Kompletu</label>
                        <input type="text" id="populasaun-naran" class="form-control form-control-premium bg-light" readonly>
                    </div>
                    
                    <div class="form-group mb-0">
                        <label for="no-eleitoral" class="font-weight-bold text-secondary mb-1">Númeru Kartaun Eleitoral</label>
                        <input type="text" id="no-eleitoral" name="no_eleitoral" class="form-control form-control-premium" placeholder="Ex: 12345678" required autocomplete="off">
                        <small class="text-muted mt-1 d-block">Preenxe númeru kartaun eleitoral foun ne'ebé sidadaun ne'e halo ona.</small>
                    </div>
                </div>
                <div class="modal-footer modal-footer-premium d-flex justify-content-end">
                    <button type="button" class="btn btn-light btn-rounded mr-2" data-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-primary btn-rounded shadow-sm">
                        <i class="fas fa-save mr-1"></i> Salva Dadus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        var tableEleitores = $('#table-eleitores').DataTable({
            autoWidth: false,
            order: [[2, 'asc']],
            columnDefs: [{
                orderable: false,
                targets: <?= !in_groups('xefe-aldeia') ? '[0, 8]' : '[0]' ?>
            }],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
            }
        });

        tableEleitores.on('draw.dt', function () {
            var PageInfo = $('#table-eleitores').DataTable().page.info();
            tableEleitores.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });
        });

        // Filter by Aldeia column (column index 4)
        $('#filter-aldeia').on('change', function() {
            var val = $(this).val();
            if (!val) {
                tableEleitores.column(4).search('').draw();
            } else {
                tableEleitores.column(4).search('^' + $.fn.dataTable.util.escapeRegex(val) + '$', true, false).draw();
            }
        });

        // Open Modal to preenxe kartaun
        $(document).on('click', '.btn-edit-eleitor', function() {
            var id = $(this).data('id');
            var naran = $(this).data('naran');
            var eleitoral = $(this).data('eleitoral');

            $('#populasaun-id').val(id);
            $('#populasaun-naran').val(naran);
            $('#no-eleitoral').val(eleitoral);
            
            $('#modal-eleitor').modal('show');
        });

        // Submit update form via AJAX
        $('#form-update-eleitor').on('submit', function(e) {
            e.preventDefault();
            var id = $('#populasaun-id').val();
            var noEleitoral = $('#no-eleitoral').val();

            $.ajax({
                url: `<?= route_to('eleitores-index') ?>/${id}/update`,
                method: 'POST',
                data: {
                    no_eleitoral: noEleitoral,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                }
            }).done((data) => {
                $('#modal-eleitor').modal('hide');
                Toast.fire({
                    icon: 'success',
                    title: data.message
                });
                setTimeout(function() {
                    location.reload();
                }, 800);
            }).fail((xhr) => {
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.message : "Falla atualiza dadus eleitor!";
                Toast.fire({
                    icon: 'error',
                    title: errorMsg
                });
            });
        });
    });
</script>
<?= $this->endSection() ?>
