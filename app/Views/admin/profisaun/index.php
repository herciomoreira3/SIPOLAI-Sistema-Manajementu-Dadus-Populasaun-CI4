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
</style>

<div class="row">
    <div class="col-12">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-secondary mb-0"><i class="fas fa-briefcase mr-2"></i> Jestaun Profisaun</h3>
                <a href="<?= route_to('admin/profisaun/new') ?>" class="btn btn-primary btn-rounded shadow-sm">
                    <i class="fa fa-plus mr-1"></i> Aumenta Profisaun
                </a>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table id="table-profisaun" class="table table-hover va-middle" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 8%">#</th>
                                <th>Profisaun</th>
                                <th class="text-center" style="width: 15%">Aksaun</th>
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
    var tableProfisaun = $('#table-profisaun').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        order: [[1, 'asc']],
        ajax : {
            url: '<?= route_to('admin/profisaun') ?>',
            method: 'GET'
        },
        columnDefs: [{
            orderable: false,
            targets: [0, 2]
        }],
        columns : [
            { 'data': null },
            { 'data': 'naran_profisaun' },
            {
                "data": function(data) {
                    return `<div class="d-flex justify-content-center">
                                <a href="<?= route_to('admin/profisaun') ?>/${data.id_profisaun}/edit" class="btn btn-sm btn-info rounded-circle mr-1 shadow-sm" title="Hadia"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-sm btn-danger rounded-circle btn-delete shadow-sm" data-id="${data.id_profisaun}" title="Hamoos"><i class="fas fa-trash"></i></button>
                            </div>`
                }
            }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
        }
    });

    tableProfisaun.on('draw.dt', function () {
        var PageInfo = $('#table-profisaun').DataTable().page.info();
        tableProfisaun.column(0, { page: 'current' }).nodes().each( function (cell, i) {
            cell.innerHTML = i + 1 + PageInfo.start;
        });
    });
    
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
                    url: `<?= route_to('admin/profisaun') ?>/${$(this).attr('data-id')}`,
                    method: 'DELETE',
                }).done((data, textStatus, jqXHR) => {
                    Toast.fire({
                        icon: 'success',
                        title: 'Profisaun hamoos ho susesu!'
                    });
                    tableProfisaun.ajax.reload();
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
