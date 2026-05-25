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
</style>

<?php
$title = 'Jestaun Populasaun';
$icon = 'fas fa-users';
if (isset($type)) {
    if ($type === 'moris') {
        $title = 'Jestaun Moris';
        $icon = 'fas fa-heartbeat';
    } elseif ($type === 'estatutu') {
        $title = 'Estatutu Populasaun (Mortalidade)';
        $icon = 'fas fa-heartbeat';
    }
}
?>

<div class="row">
    <div class="col-12">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-secondary mb-0"><i class="<?= $icon ?> mr-2"></i> <?= $title ?></h3>
                <a href="<?= route_to('admin/populasaun/new') ?>" class="btn btn-primary btn-rounded shadow-sm">
                    <i class="fa fa-plus mr-1"></i> Aumenta Populasaun
                </a>
            </div>
            <div class="card-body px-4 pb-4">
                <!-- Filters Bar -->
                <div class="row mb-4 align-items-end">
                    <!-- Dropdown Aldeia -->
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label for="filter-aldeia" class="font-weight-bold text-muted small text-uppercase">Filtru bazeia ba Aldeia</label>
                        <select id="filter-aldeia" class="form-control select2 shadow-sm" style="border-radius: 8px;">
                            <option value="">-- Haree Aldeia Hotu --</option>
                            <?php foreach ($aldeias as $aldeia) : ?>
                                <option value="<?= $aldeia['id_aldeia'] ?>"><?= esc($aldeia['naran_aldeia']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Dropdown Estatutu -->
                    <div class="col-md-4">
                        <label for="filter-status" class="font-weight-bold text-muted small text-uppercase">Filtru bazeia ba Estatutu</label>
                        <select id="filter-status" class="form-control select2 shadow-sm" style="border-radius: 8px;">
                            <option value="">-- Haree Estatutu Hotu --</option>
                            <option value="Moris">Moris</option>
                            <option value="Mate">Mate</option>
                            <option value="Muda">Muda</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="table-populasaun" class="table table-hover va-middle" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 4%;">#</th>
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
                                <th class="text-center" style="width: 10%;">Aksaun</th>
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
    var tablePopulasaun = $('#table-populasaun').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        order: [[2, 'asc']],
        ajax : {
            url: '<?= route_to('admin/populasaun') ?>?type=<?= $type ?? 'all' ?>',
            method: 'GET',
            data: function(d) {
                d.id_aldeia = $('#filter-aldeia').val();
                d.istadu = $('#filter-status').val();
            }
        },
        columnDefs: [{
            orderable: false,
            targets: [0, 11]
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
            { 'data': 'fatin_moris' },
            { 'data': 'data_moris' },
            { 'data': 'status_kaza' },
            { 'data': 'naran_profisaun' },
            { 'data': 'naran_relijiaun' },
            { 'data': 'naran_literatura' },
            { 
                'data': 'naran_aldeia',
                'render': function(data) {
                    return `<span class="badge badge-light badge-premium border">${data}</span>`;
                }
            },
            {
                "data": function(data) {
                    return `<div class="d-flex justify-content-center">
                                <a href="<?= route_to('admin/populasaun') ?>/${data.id_populasaun}/edit" class="btn btn-sm btn-info rounded-circle mr-1 shadow-sm" title="Hadia"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-sm btn-danger rounded-circle btn-delete shadow-sm" data-id="${data.id_populasaun}" title="Hamoos"><i class="fas fa-trash"></i></button>
                            </div>`
                }
            }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
        }
    });

    tablePopulasaun.on('draw.dt', function () {
        var PageInfo = $('#table-populasaun').DataTable().page.info();
        tablePopulasaun.column(0, { page: 'current' }).nodes().each( function (cell, i) {
            cell.innerHTML = i + 1 + PageInfo.start;
        });
    });

    // Reload trigger listeners on filter change
    $('#filter-aldeia, #filter-status').on('change', function() {
        tablePopulasaun.ajax.reload();
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
                    url: `<?= route_to('admin/populasaun') ?>/${$(this).attr('data-id')}`,
                    method: 'DELETE',
                }).done((data, textStatus, jqXHR) => {
                    Toast.fire({
                        icon: 'success',
                        title: 'Populasaun hamoos ho susesu!'
                    });
                    tablePopulasaun.ajax.reload();
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
