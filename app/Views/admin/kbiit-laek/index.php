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
                    <i class="fas fa-hand-holding-heart text-primary mr-2"></i> <?= $title ?>
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
                                <option value="<?= $aldeia['id_aldeia'] ?>"><?= esc($aldeia['naran_aldeia']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="table-kbiit-laek" class="table table-hover va-middle" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 4%;">#</th>
                                <th>NIP/NIK</th>
                                <th>Naran Kompletu</th>
                                <th>Sexu</th>
                                <th>Aldeia</th>
                                <th>Data Deklarasaun</th>
                                <th>Kartaun / Sertifikadu</th>
                                <?php if (!in_groups('xefe-aldeia')) : ?>
                                    <th class="text-center" style="width: 12%;">Aksaun</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Kartaun Kbiit Laek -->
<div class="modal fade" id="modal-kbiit-laek" tabindex="-1" role="dialog" aria-labelledby="modalKbiitLaekTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-premium">
            <div class="modal-header modal-header-premium">
                <h5 class="modal-title font-weight-bold" id="modalKbiitLaekTitle">
                    <i class="fas fa-hand-holding-heart mr-2"></i> Kartaun / Sertifikadu Kbiit Laek
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-update-kbiit-laek">
                <div class="modal-body p-4">
                    <input type="hidden" id="populasaun-id" name="id_populasaun">
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-secondary mb-1">Naran Kompletu</label>
                        <input type="text" id="populasaun-naran" class="form-control form-control-premium bg-light" readonly>
                    </div>
                    
                    <div class="form-group mb-0">
                        <label for="no-kbiit-laek" class="font-weight-bold text-secondary mb-1">Númeru Kartaun / Sertifikadu Kbiit Laek</label>
                        <input type="text" id="no-kbiit-laek" name="no_kbiit_laek" class="form-control form-control-premium" placeholder="Ex: KB-123456" required autocomplete="off">
                        <small class="text-muted mt-1 d-block">Preenxe númeru kartaun ka sertifikadu kbiit laek foun ne'ebé sidadaun ne'e hetan ona.</small>
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
        var tableKbiitLaek = $('#table-kbiit-laek').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [[2, 'asc']],
            ajax : {
                url: '<?= route_to('kbiit-laek-index') ?>',
                method: 'GET',
                data: function(d) {
                    d.id_aldeia = $('#filter-aldeia').val();
                }
            },
            columnDefs: [{
                orderable: false,
                targets: <?= !in_groups('xefe-aldeia') ? '[0, 7]' : '[0]' ?>
            }],
            columns : [
                { 'data': null },
                { 
                    'data': 'nik',
                    'render': function(data) {
                        return `<span class="font-weight-bold text-primary">${data}</span>`;
                    }
                },
                { 
                    'data': 'naran_kompletu',
                    'render': function(data) {
                        return `<span class="font-weight-bold text-secondary">${data}</span>`;
                    }
                },
                { 
                    'data': 'jeneru',
                    'render': function(data) {
                        let badgeClass = data === 'Mane' ? 'badge-primary' : 'badge-danger';
                        return `<span class="badge ${badgeClass} badge-premium">${data}</span>`;
                    }
                },
                { 
                    'data': 'naran_aldeia',
                    'render': function(data) {
                        return `<span class="badge badge-light badge-premium border">${data}</span>`;
                    }
                },
                { 
                    'data': 'data_aprovada',
                    'render': function(data) {
                        if (!data) return '-';
                        var d = new Date(data);
                        return d.toLocaleDateString('pt-PT');
                    }
                },
                { 
                    'data': 'no_kbiit_laek',
                    'render': function(data) {
                        if (data) {
                            return `<span class="badge badge-success badge-premium"><i class="fas fa-check-circle mr-1"></i> ${data}</span>`;
                        }
                        return `<span class="badge badge-warning badge-premium text-dark"><i class="fas fa-exclamation-triangle mr-1"></i> Seidauk Rejista Kartaun</span>`;
                    }
                }
                <?php if (!in_groups('xefe-aldeia')) : ?>,
                {
                    "data": function(data) {
                        let btnClass = data.no_kbiit_laek ? 'btn-info' : 'btn-primary';
                        let btnText = data.no_kbiit_laek ? 'Hadia Dadus' : 'Preenxe Kartaun';
                        let btnIcon = data.no_kbiit_laek ? 'fas fa-edit' : 'fas fa-plus';
                        return `<div class="d-flex justify-content-center">
                                    <button class="btn btn-sm ${btnClass} btn-rounded btn-edit-kbiit shadow-sm" 
                                            data-id="${data.id_populasaun}" 
                                            data-naran="${data.naran_kompletu}" 
                                            data-kbiit="${data.no_kbiit_laek || ''}" 
                                            title="${btnText} Kbiit Laek">
                                        <i class="${btnIcon} mr-1"></i> ${btnText}
                                    </button>
                                </div>`;
                    }
                }
                <?php endif; ?>
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
            }
        });

        tableKbiitLaek.on('draw.dt', function () {
            var PageInfo = $('#table-kbiit-laek').DataTable().page.info();
            tableKbiitLaek.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });
        });

        // Filter reload on change
        $('#filter-aldeia').on('change', function() {
            tableKbiitLaek.ajax.reload();
        });

        // Open Modal to preenxe kartaun
        $(document).on('click', '.btn-edit-kbiit', function() {
            var id = $(this).data('id');
            var naran = $(this).data('naran');
            var kbiit = $(this).data('kbiit');

            $('#populasaun-id').val(id);
            $('#populasaun-naran').val(naran);
            $('#no-kbiit-laek').val(kbiit);
            
            $('#modal-kbiit-laek').modal('show');
        });

        // Submit update form via AJAX
        $('#form-update-kbiit-laek').on('submit', function(e) {
            e.preventDefault();
            var id = $('#populasaun-id').val();
            var noKbiitLaek = $('#no-kbiit-laek').val();

            $.ajax({
                url: `<?= route_to('kbiit-laek-index') ?>/${id}/update`,
                method: 'POST',
                data: {
                    no_kbiit_laek: noKbiitLaek,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                }
            }).done((data) => {
                $('#modal-kbiit-laek').modal('hide');
                Toast.fire({
                    icon: 'success',
                    title: data.message
                });
                tableKbiitLaek.ajax.reload();
            }).fail((xhr) => {
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.message : "Falla atualiza dadus kbiit laek!";
                Toast.fire({
                    icon: 'error',
                    title: errorMsg
                });
            });
        });
    });
</script>
<?= $this->endSection() ?>
