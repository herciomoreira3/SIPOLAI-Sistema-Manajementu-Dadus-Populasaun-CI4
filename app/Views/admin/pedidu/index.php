<?= $this->include('Boilerplate\Views\load\datatables') ?>
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
    .btn-sm.rounded-circle {
        width: 32px !important;
        height: 32px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s ease !important;
        border: none !important;
    }
    .btn-success.rounded-circle {
        background-color: #f0fdf4 !important;
        color: #166534 !important;
    }
    .btn-success.rounded-circle:hover {
        background-color: #166534 !important;
        color: #ffffff !important;
        transform: scale(1.08) !important;
    }
    .btn-info.rounded-circle {
        background-color: #eff6ff !important;
        color: #2563eb !important;
    }
    .btn-info.rounded-circle:hover {
        background-color: #2563eb !important;
        color: #ffffff !important;
        transform: scale(1.08) !important;
    }
    .btn-danger.rounded-circle {
        background-color: #fef2f2 !important;
        color: #ef4444 !important;
    }
    .btn-danger.rounded-circle:hover {
        background-color: #ef4444 !important;
        color: #ffffff !important;
        transform: scale(1.08) !important;
    }
    .badge-premium {
        font-size: 11px;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <?php if (!empty($naran_pedidu)) : ?>
                    <a href="<?= route_to('inventoriu') ?>" class="btn btn-outline-secondary btn-rounded mr-3" style="border-radius: 10px !important; padding: 6px 15px !important; font-weight: 600 !important; transition: all 0.2s ease !important;">
                        <i class="fas fa-arrow-left mr-1"></i> Fila
                    </a>
                    <?php endif; ?>
                    <h3 class="card-title font-weight-bold text-secondary mb-0"><i class="fas fa-file-signature mr-2"></i> <?= esc($title) ?></h3>
                </div>
                <?php if (empty($naran_pedidu)) : ?>
                <a href="<?= route_to('admin/pedidu/new') ?>" class="btn btn-primary btn-rounded shadow-sm">
                    <i class="fa fa-plus mr-1"></i> Kria Pedidu Foun
                </a>
                <?php else : ?>
                <div class="d-flex align-items-center">
                    <label for="status-filter" class="mr-2 mb-0 font-weight-bold text-secondary" style="font-size: 13px;">Filtru Status:</label>
                    <select id="status-filter" class="form-control form-control-sm shadow-sm" style="border-radius: 8px; width: 180px; height: 36px; padding: 0 10px; font-weight: 600; color: #475569;">
                        <option value="">Hatudu Hotu</option>
                        <option value="Aprovadu">Aprovadu (Sim)</option>
                        <option value="Rezeitadu">Rezeitadu (Lae)</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table id="table-pedidu" class="table table-hover va-middle" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th>Tipu Deklarasaun</th>
                                <th>Naran</th>
                                <th>Aldeia</th>
                                <th>Data Pedidu</th>
                                <th>Status</th>
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
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>    
    var isApprover = <?= in_groups(['admin', 'xefe-suku']) ? 'true' : 'false' ?>;

    var tablePedidu = $('#table-pedidu').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        order: [[4, 'desc']],
        ajax : {
            url: '<?= route_to('admin/pedidu') ?>',
            method: 'GET',
            data: function(d) {
                d.naran_pedidu = '<?= esc($naran_pedidu ?? '') ?>';
                d.status_filter = $('#status-filter').val();
            }
        },
        columnDefs: [{
            orderable: false,
            targets: [0, 6]
        }],
        columns : [
            { 'data': null },
            { 
                'data': 'naran_pedidu',
                'render': function(data) {
                    return `<span class="font-weight-bold text-secondary">${data}</span>`;
                }
            },
            { 
                'data': 'pemohon',
                'render': function(data) {
                    return `<span class="font-weight-bold text-dark">${data}</span>`;
                }
            },
            { 
                'data': 'naran_aldeia',
                'render': function(data) {
                    return `<span class="badge badge-light badge-premium border">${data}</span>`;
                }
            },
            { 'data': 'data_pedidu' },
            { 
                'data': 'status',
                'render': function(data) {
                    if (data == 'Aprovadu') {
                        return '<span class="badge badge-success badge-premium"><i class="fas fa-check-circle mr-1"></i> Aprovadu (Sim)</span>';
                    } else if (data == 'Rezeitadu') {
                        return '<span class="badge badge-danger badge-premium"><i class="fas fa-times-circle mr-1"></i> Rezeitadu (Lae)</span>';
                    } else {
                        return '<span class="badge badge-warning text-white badge-premium"><i class="fas fa-history mr-1"></i> Pendiente</span>';
                    }
                }
            },
            {
                "data": function(data) {
                    let actions = `<div class="d-flex justify-content-center">`;
                    
                    // Xefe Suku / Admin can approve or reject
                    if (isApprover && data.status === 'Pendiente') {
                        actions += `<button class="btn btn-sm btn-success rounded-circle mr-2 shadow-sm btn-approve" data-id="${data.id_pedidu}" title="Aprova"><i class="fas fa-check"></i></button>
                                    <button class="btn btn-sm btn-danger text-white rounded-circle mr-2 shadow-sm btn-reject" data-id="${data.id_pedidu}" title="Rezeita"><i class="fas fa-times"></i></button>`;
                    }
                    
                    if (data.status === 'Aprovadu') {
                        actions += `<a href="<?= route_to('admin/pedidu') ?>/${data.id_pedidu}/print" target="_blank" class="btn btn-sm btn-success rounded-circle mr-2 shadow-sm" title="Imprime Karta"><i class="fas fa-print"></i></a>`;
                    }
                    
                    if (data.naran_pedidu === 'Deklarasaun Nascimentu' && data.status !== 'Aprovadu') {
                        actions += `<a href="<?= route_to('admin/pedidu') ?>/${data.id_pedidu}/edit" class="btn btn-sm btn-info rounded-circle mr-1 shadow-sm" title="Hadia"><i class="fas fa-edit"></i></a>`;
                    }
                    
                    if (isApprover) {
                        actions += `<button class="btn btn-sm btn-danger rounded-circle btn-delete shadow-sm" data-id="${data.id_pedidu}" title="Hamoos"><i class="fas fa-trash"></i></button>`;
                    }

                    actions += `</div>`;
                    return actions;
                }
            }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
        }
    });

    $('#status-filter').on('change', function() {
        tablePedidu.ajax.reload();
    });

    tablePedidu.on('draw.dt', function () {
        var PageInfo = $('#table-pedidu').DataTable().page.info();
        tablePedidu.column(0, { page: 'current' }).nodes().each( function (cell, i) {
            cell.innerHTML = i + 1 + PageInfo.start;
        });
    });

    // Action: Approve
    $(document).on('click', '.btn-approve', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Ita boot hakarak aprova pedidu ne\'e?',
            text: "Status pedidu sei sai Aprovadu ho ofisial!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#a18cd1',
            confirmButtonText: 'Sim, Aprova!',
            cancelButtonText: 'Kansela'
        }).then((result) => {
            if (result.value) {
                updateStatus(id, 'Aprovadu');
            }
        });
    });

    // Action: Reject
    $(document).on('click', '.btn-reject', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Ita boot hakarak rejeita pedidu ne\'e?',
            text: "Status pedidu sei sai Rezeitadu!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff5858',
            cancelButtonColor: '#a18cd1',
            confirmButtonText: 'Sim, Rejeita!',
            cancelButtonText: 'Kansela'
        }).then((result) => {
            if (result.value) {
                updateStatus(id, 'Rezeitadu');
            }
        });
    });

    function updateStatus(id, newStatus) {
        $.ajax({
            url: `<?= route_to('admin/pedidu') ?>/${id}/status`,
            method: 'POST',
            data: { status: newStatus, <?= csrf_token() ?>: '<?= csrf_hash() ?>' }
        }).done((response) => {
            Toast.fire({
                icon: 'success',
                title: response.message
            });
            tablePedidu.ajax.reload();
        }).fail((xhr) => {
            Toast.fire({
                icon: 'error',
                title: xhr.responseJSON.messages.error || 'Ita boot la iha kbiit/autorizasaun!'
            });
        });
    }
    
    $(document).on('click', '.btn-delete', function (e) {
        Swal.fire({
            title: 'Ita hakarak hamoos dadus ne\'e?',
            text: "Dadus ne'ebé hamoos tiha ona la bele foti fali!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff5858',
            cancelButtonColor: '#a18cd1',
            confirmButtonText: 'Sim, Hamoos!',
            cancelButtonText: 'Kansela'
        })
        .then((result) => {
            if (result.value) {
                $.ajax({
                    url: `<?= route_to('admin/pedidu') ?>/${$(this).attr('data-id')}`,
                    method: 'DELETE',
                }).done((data, textStatus, jqXHR) => {
                    Toast.fire({
                        icon: 'success',
                        title: 'Pedidu hamoos ho susesu!'
                    });
                    tablePedidu.ajax.reload();
                }).fail((error) => {
                    Toast.fire({
                        icon: 'error',
                        title: 'Kansela hamoos dadus!'
                    });
                })
            }
        })
    })
</script>
<?= $this->endSection() ?>
