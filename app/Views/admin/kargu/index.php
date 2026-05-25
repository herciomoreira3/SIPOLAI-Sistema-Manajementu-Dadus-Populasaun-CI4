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
        font-size: 12px;
        letter-spacing: 1px;
        color: #748a9e;
        padding: 15px 10px;
    }
    .table td {
        vertical-align: middle !important;
        padding: 15px 10px;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-secondary mb-0"><i class="fas fa-briefcase mr-2"></i> Jestaun Kargu / Posisaun</h3>
                <a href="<?= base_url('admin/kargu/new') ?>" class="btn btn-primary btn-rounded shadow-sm">
                    <i class="fa fa-plus mr-1"></i> Aumenta Kargu
                </a>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table id="table-kargu" class="table table-hover va-middle" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 8%">#</th>
                                <th>Naran Kargu / Posisaun</th>
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
    var tableKargu = $('#table-kargu').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        order: [[1, 'asc']],
        ajax : {
            url: '<?= base_url('admin/kargu') ?>',
            method: 'GET'
        },
        columnDefs: [{
            orderable: false,
            targets: [0, 2]
        }],
        columns : [
            { 'data': null },
            { 'data': 'naran_kargu' },
            {
                "data": function(data) {
                    return `<div class="d-flex justify-content-center">
                                <a href="<?= base_url('admin/kargu') ?>/${data.id_kargu}/edit" class="btn btn-sm btn-info rounded-circle mr-1 shadow-sm" title="Hadia"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-sm btn-danger rounded-circle btn-delete shadow-sm" data-id="${data.id_kargu}" title="Hamoos"><i class="fas fa-trash"></i></button>
                            </div>`
                }
            }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
        }
    });

    tableKargu.on('draw.dt', function () {
        var PageInfo = $('#table-kargu').DataTable().page.info();
        tableKargu.column(0, { page: 'current' }).nodes().each( function (cell, i) {
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
                    url: `<?= base_url('admin/kargu') ?>/${$(this).attr('data-id')}`,
                    method: 'DELETE',
                }).done((data, textStatus, jqXHR) => {
                    Toast.fire({
                        icon: 'success',
                        title: 'Kargu hamoos ho susesu!'
                    });
                    tableKargu.ajax.reload();
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
