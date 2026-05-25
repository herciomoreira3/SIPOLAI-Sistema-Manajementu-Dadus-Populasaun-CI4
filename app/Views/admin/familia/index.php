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
    .card-premium .nav-pills .nav-link {
        background-color: #f4f4f5;
        color: #71717a;
        transition: all 0.2s ease !important;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        padding: 8px 18px;
    }
    .card-premium .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #09090b, #0f172a) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(9, 9, 11, 0.15) !important;
    }
    .card-premium .nav-pills .nav-link.active i {
        color: #ffffff !important;
    }
    .card-premium .nav-pills .nav-link.active:hover {
        background: linear-gradient(135deg, #1e293b, #0f172a) !important;
        box-shadow: 0 6px 16px rgba(9, 9, 11, 0.25) !important;
        transform: translateY(-1px) !important;
        color: #ffffff !important;
    }
    .card-premium .nav-pills .nav-link:not(.active):hover {
        background-color: #e4e4e7;
        color: #09090b !important;
        transform: translateY(-1px) !important;
    }
    .card-premium .nav-pills .nav-link:active {
        transform: translateY(0) !important;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-secondary mb-0"><i class="fas fa-id-card mr-2"></i> Jestaun Fixa Familia</h3>
                <a href="<?= base_url('admin/familia/new') ?>" class="btn btn-primary btn-rounded shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Rejistu Fixa Familia Foun
                </a>
            </div>
            <div class="card-body px-4 pb-4">
                <?php if (session()->getFlashdata('message')) : ?>
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
                        <i class="fas fa-check-circle mr-2"></i><?= session()->getFlashdata('message') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Custom Tabs -->
                <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active mr-2 px-4 py-2 border-0 shadow-sm" id="pills-new-tab" data-toggle="pill" data-target="#pills-new" type="button" role="tab" aria-controls="pills-new" aria-selected="true">
                            <i class="fas fa-file-signature mr-2 text-primary"></i> Fixa Familia Foun / Seidauk iha Membru
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-2 border-0 shadow-sm" id="pills-existing-tab" data-toggle="pill" data-target="#pills-existing" type="button" role="tab" aria-controls="pills-existing" aria-selected="false">
                            <i class="fas fa-users mr-2 text-success"></i> Fixa Familia ho Membru
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <!-- Tab 1: Empty Families (Fixa Familia Foun) -->
                    <div class="tab-pane fade show active" id="pills-new" role="tabpanel" aria-labelledby="pills-new-tab">
                        <div class="table-responsive">
                            <table class="table table-hover va-middle" id="table-familia-foun" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 8%">#</th>
                                        <th>Numeru Fixa (KK)</th>
                                        <th>Aldeia</th>
                                        <th class="text-center" style="width: 25%">Aksaun</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Existing Families (Fixa Familia ho Membru) -->
                    <div class="tab-pane fade" id="pills-existing" role="tabpanel" aria-labelledby="pills-existing-tab">
                        <div class="table-responsive">
                            <table class="table table-hover va-middle" id="table-familia-existente" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 8%">#</th>
                                        <th>Numeru Fixa (KK)</th>
                                        <th>Aldeia</th>
                                        <th>Xefe Familia</th>
                                        <th>Total Membru</th>
                                        <th class="text-center" style="width: 25%">Aksaun</th>
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
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        // Base route url for actions
        var baseUrl = '<?= base_url('admin/familia') ?>';

        // 1. Table Familia Foun (Empty Families)
        var tableFoun = $('#table-familia-foun').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [[1, 'asc']],
            ajax : {
                url: '<?= route_to('familia-data') ?>?type=foun',
                method: 'GET'
            },
            columnDefs: [{
                orderable: false,
                targets: [0, 3]
            }],
            columns : [
                { 'data': null },
                { 
                    'data': 'numeru_kk',
                    'render': function(data, type, row) {
                        return `<a href="${baseUrl}/${row.id_familia}" class="font-weight-bold text-primary">${data}</a>`;
                    }
                },
                { 
                    'data': 'naran_aldeia',
                    'render': function(data) {
                        return `<span class="badge badge-light badge-premium border">${data}</span>`;
                    }
                },
                {
                    "data": function(row) {
                        return `<div class="d-flex justify-content-center">
                                    <a href="${baseUrl}/${row.id_familia}" class="btn btn-sm btn-warning btn-rounded text-white mr-2 shadow-sm" title="Rejistu Xefe Familia">
                                        <i class="fas fa-user-plus mr-1"></i> Rejistu Xefe Familia
                                    </a>
                                    <a href="${baseUrl}/${row.id_familia}/edit" class="btn btn-sm btn-info rounded-circle mr-2 shadow-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger rounded-circle btn-delete-familia shadow-sm" data-id="${row.id_familia}" data-number="${row.numeru_kk}" title="Hasai">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>`;
                    }
                }
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
            }
        });

        tableFoun.on('draw.dt', function () {
            var PageInfo = $('#table-familia-foun').DataTable().page.info();
            tableFoun.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });
        });

        // 2. Table Familia Existente (Families with members)
        var tableExistente = $('#table-familia-existente').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [[1, 'asc']],
            ajax : {
                url: '<?= route_to('familia-data') ?>?type=existente',
                method: 'GET'
            },
            columnDefs: [{
                orderable: false,
                targets: [0, 4, 5]
            }],
            columns : [
                { 'data': null },
                { 
                    'data': 'numeru_kk',
                    'render': function(data, type, row) {
                        return `<a href="${baseUrl}/${row.id_familia}" class="font-weight-bold text-primary">${data}</a>`;
                    }
                },
                { 
                    'data': 'naran_aldeia',
                    'render': function(data) {
                        return `<span class="badge badge-light badge-premium border">${data}</span>`;
                    }
                },
                { 
                    'data': 'xefe_familia',
                    'render': function(data) {
                        return `<span class="text-secondary font-weight-bold">${data || '-'}</span>`;
                    }
                },
                { 
                    'data': 'total_membros',
                    'render': function(data) {
                        return `<span class="badge badge-info badge-premium px-3 py-1"><i class="fas fa-users mr-1"></i> ${data} Ema</span>`;
                    }
                },
                {
                    "data": function(row) {
                        return `<div class="d-flex justify-content-center">
                                    <a href="${baseUrl}/${row.id_familia}" class="btn btn-sm btn-light rounded-circle mr-2 shadow-sm" title="Haree Detaillu">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="${baseUrl}/${row.id_familia}?print=true" class="btn btn-sm btn-success rounded-circle mr-2 text-white shadow-sm" title="Imprime Fixa Familia" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="${baseUrl}/${row.id_familia}/edit" class="btn btn-sm btn-info rounded-circle mr-2 shadow-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger rounded-circle btn-delete-familia shadow-sm" data-id="${row.id_familia}" data-number="${row.numeru_kk}" title="Hasai">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>`;
                    }
                }
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
            }
        });

        tableExistente.on('draw.dt', function () {
            var PageInfo = $('#table-familia-existente').DataTable().page.info();
            tableExistente.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });
        });

        // 3. SweetAlert Delete Confirmation (Delegated Click)
        $(document).on('click', '.btn-delete-familia', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var number = $(this).data('number');

            Swal.fire({
                title: 'Ita boot sira certeza?',
                text: `Dadus Fixa Familia ho Númeru KK '${number}' sei hasai permanentemente!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff5858',
                cancelButtonColor: '#71717a',
                confirmButtonText: 'Sim, Hasai!',
                cancelButtonText: 'Kansela'
            }).then((result) => {
                if (result.value) {
                    // Submit virtual DELETE form
                    var form = $('<form>', {
                        action: `${baseUrl}/${id}`,
                        method: 'POST'
                    }).append($('<input>', {
                        type: 'hidden',
                        name: '_method',
                        value: 'DELETE'
                    })).append($('<input>', {
                        type: 'hidden',
                        name: '<?= csrf_token() ?>',
                        value: '<?= csrf_hash() ?>'
                    }));
                    $('body').append(form);
                    form.submit();
                }
            });
        });

        // Redraw tables when shifting tabs to ensure perfect header alignment
        $('button[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            tableFoun.columns.adjust().draw();
            tableExistente.columns.adjust().draw();
        });
    });
</script>
<?= $this->endSection() ?>
