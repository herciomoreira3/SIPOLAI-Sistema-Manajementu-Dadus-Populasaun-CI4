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
        padding: 10px 20px;
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
</style>

<div class="row">
    <div class="col-12">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary mb-0">
                    <i class="fas fa-heartbeat text-danger mr-2"></i> Jestaun Estatutu Populasaun
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

                <!-- Custom Tabs (Nascimentu is now first and active by default) -->
                <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active mr-2 px-4 py-2 border-0 shadow-sm" id="pills-nascimentu-tab" data-toggle="pill" data-target="#pills-nascimentu" type="button" role="tab" aria-controls="pills-nascimentu" aria-selected="true">
                            <i class="fas fa-baby mr-2 text-success"></i> Nascimentu <span class="badge badge-success ml-1 rounded-circle px-2 py-0.5" style="font-size: 10px; font-weight: 700; background-color: #22c55e; color: #fff;"><?= $statNascimentu ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link mr-2 px-4 py-2 border-0 shadow-sm" id="pills-moris-tab" data-toggle="pill" data-target="#pills-moris" type="button" role="tab" aria-controls="pills-moris" aria-selected="false">
                            <i class="fas fa-heartbeat mr-2 text-primary"></i> Populasaun Moris <span class="badge badge-primary ml-1 rounded-circle px-2 py-0.5" style="font-size: 10px; font-weight: 700; background-color: #3b82f6; color: #fff;"><?= $statMoris ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link mr-2 px-4 py-2 border-0 shadow-sm" id="pills-mate-tab" data-toggle="pill" data-target="#pills-mate" type="button" role="tab" aria-controls="pills-mate" aria-selected="false">
                            <i class="fas fa-skull-crossbones mr-2 text-muted"></i> Populasaun Mate <span class="badge badge-secondary ml-1 rounded-circle px-2 py-0.5" style="font-size: 10px; font-weight: 700; background-color: #6b7280; color: #fff;"><?= $statMate ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-2 border-0 shadow-sm" id="pills-muda-tab" data-toggle="pill" data-target="#pills-muda" type="button" role="tab" aria-controls="pills-muda" aria-selected="false">
                            <i class="fas fa-map-marker-alt mr-2 text-warning"></i> Populasaun Muda <span class="badge badge-warning text-white ml-1 rounded-circle px-2 py-0.5" style="font-size: 10px; font-weight: 700; background-color: #f59e0b; color: #fff;"><?= $statMuda ?></span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <!-- Tab 1: Birth Statistics (Nascimentu) -->
                    <div class="tab-pane fade show active" id="pills-nascimentu" role="tabpanel" aria-labelledby="pills-nascimentu-tab">
                        <div class="table-responsive">
                            <table id="table-populasaun-nascimentu" class="table table-hover va-middle" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 4%;">#</th>
                                        <th>Naran Kompletu Oan</th>
                                        <th>Sexu</th>
                                        <th>Aldeia</th>
                                        <th>Fatin Moris</th>
                                        <th>Data Moris</th>
                                        <th>Tinan Moris</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Living Population (Populasaun Moris) -->
                    <div class="tab-pane fade" id="pills-moris" role="tabpanel" aria-labelledby="pills-moris-tab">
                        <div class="table-responsive">
                            <table id="table-populasaun-moris" class="table table-hover va-middle" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 4%;">#</th>
                                        <th>NIP</th>
                                        <th>Naran</th>
                                        <th>Sexu</th>
                                        <th>Aldeia</th>
                                        <th>Estatutu</th>
                                        <th class="text-center" style="width: 15%;">Aksaun</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 3: Deceased Population (Populasaun Mate) -->
                    <div class="tab-pane fade" id="pills-mate" role="tabpanel" aria-labelledby="pills-mate-tab">
                        <div class="table-responsive">
                            <table id="table-populasaun-mate" class="table table-hover va-middle" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 4%;">#</th>
                                        <th>NIP</th>
                                        <th>Naran</th>
                                        <th>Sexu</th>
                                        <th>Aldeia</th>
                                        <th>Estatutu</th>
                                        <th>Data Mate</th>
                                        <?php if (in_groups('admin')) : ?>
                                            <th class="text-center" style="width: 15%;">Aksaun</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 4: Moved Population (Populasaun Muda) -->
                    <div class="tab-pane fade" id="pills-muda" role="tabpanel" aria-labelledby="pills-muda-tab">
                        <div class="table-responsive">
                            <table id="table-populasaun-muda" class="table table-hover va-middle" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 4%;">#</th>
                                        <th>NIP</th>
                                        <th>Naran</th>
                                        <th>Sexu</th>
                                        <th>Aldeia</th>
                                        <th>Estatutu</th>
                                        <th>Data Muda</th>
                                        <?php if (in_groups('admin')) : ?>
                                            <th class="text-center" style="width: 15%;">Aksaun</th>
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
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        // Table 1: Nascimentu
        var tableNascimentu = $('#table-populasaun-nascimentu').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [[1, 'asc']],
            ajax : {
                url: '<?= route_to('admin/populasaun') ?>?type=estatutu_nascimentu',
                method: 'GET',
                data: function(d) {
                    d.id_aldeia = $('#filter-aldeia').val();
                }
            },
            columnDefs: [{
                orderable: false,
                targets: [0]
            }],
            columns : [
                { 'data': null },
                { 
                    'data': 'pemohon',
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
                { 'data': 'fatin_moris' },
                { 
                    'data': 'data_moris',
                    'render': function(data) {
                        if (data === '-') return data;
                        let dateObj = new Date(data);
                        let day = String(dateObj.getDate()).padStart(2, '0');
                        let months = ['Janeiru', 'Fovereiru', 'Marsu', 'Abril', 'Maiu', 'Juñu', 'Juliu', 'Agostu', 'Setembru', 'Outubru', 'Novembru', 'Dezembru'];
                        let month = months[dateObj.getMonth()];
                        let year = dateObj.getFullYear();
                        return `<span class="font-weight-bold">${day} ${month} ${year}</span>`;
                    }
                },
                { 
                    'data': 'data_moris',
                    'render': function(data) {
                        if (data === '-') return data;
                        let dateObj = new Date(data);
                        return `<span class="badge badge-dark badge-premium">${dateObj.getFullYear()}</span>`;
                    }
                }
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
            }
        });

        tableNascimentu.on('draw.dt', function () {
            var PageInfo = $('#table-populasaun-nascimentu').DataTable().page.info();
            tableNascimentu.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });
        });

        // Table 2: Moris (Redirects to pedidu foun for mortalidade)
        var tableMoris = $('#table-populasaun-moris').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [[2, 'asc']],
            ajax : {
                url: '<?= route_to('admin/populasaun') ?>?type=estatutu_moris',
                method: 'GET',
                data: function(d) {
                    d.id_aldeia = $('#filter-aldeia').val();
                }
            },
            columnDefs: [{
                orderable: false,
                targets: [0, 6]
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
                    'data': 'istadu',
                    'render': function(data) {
                        return `<span class="badge badge-success badge-premium"><i class="fas fa-heartbeat mr-1"></i> ${data}</span>`;
                    }
                },
                {
                    "data": function(data) {
                        let createMortUrl = `<?= route_to('admin/pedidu') ?>/new?naran_pedidu=Deklarasaun%20Mortalidade&pemohon=${encodeURIComponent(data.naran_kompletu)}&id_aldeia=${data.id_aldeia}`;
                        let createMudaUrl = `<?= route_to('admin/pedidu') ?>/new?naran_pedidu=Deklarasaun%20Muda%20Domisiliu&pemohon=${encodeURIComponent(data.naran_kompletu)}&id_aldeia=${data.id_aldeia}`;
                        return `<div class="d-flex justify-content-center">
                                    <a class="btn btn-sm btn-danger btn-rounded shadow-sm px-3 mr-2" href="${createMortUrl}" title="Kria Pedidu Mortalidade">
                                        <i class="fas fa-skull-crossbones mr-1"></i> Kria Pedidu Mate
                                    </a>
                                    <a class="btn btn-sm btn-primary btn-rounded shadow-sm px-3" href="${createMudaUrl}" title="Kria Pedidu Muda Domisiliu">
                                        <i class="fas fa-map-marker-alt mr-1"></i> Kria Pedidu Muda
                                    </a>
                                </div>`;
                    }
                }
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
            }
        });

        tableMoris.on('draw.dt', function () {
            var PageInfo = $('#table-populasaun-moris').DataTable().page.info();
            tableMoris.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });
        });

        // Table 3: Mate
        var tableMate = $('#table-populasaun-mate').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [[2, 'asc']],
            ajax : {
                url: '<?= route_to('admin/populasaun') ?>?type=estatutu_mate',
                method: 'GET',
                data: function(d) {
                    d.id_aldeia = $('#filter-aldeia').val();
                }
            },
            columnDefs: [{
                orderable: false,
                targets: <?= in_groups('admin') ? '[0, 7]' : '[0]' ?>
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
                    'data': 'istadu',
                    'render': function(data) {
                        return `<span class="badge badge-secondary badge-premium"><i class="fas fa-skull-crossbones mr-1"></i> ${data}</span>`;
                    }
                },
                { 
                    'data': 'data_mate',
                    'render': function(data) {
                        if (data === '-') return data;
                        let dateObj = new Date(data);
                        let day = String(dateObj.getDate()).padStart(2, '0');
                        let months = ['Janeiru', 'Fovereiru', 'Marsu', 'Abril', 'Maiu', 'Juñu', 'Juliu', 'Agostu', 'Setembru', 'Outubru', 'Novembru', 'Dezembru'];
                        let month = months[dateObj.getMonth()];
                        let year = dateObj.getFullYear();
                        return `<span class="font-weight-bold text-danger"><i class="fas fa-calendar-alt mr-1"></i> ${day} ${month} ${year}</span>`;
                    }
                }
                <?php if (in_groups('admin')) : ?>,
                {
                    "data": function(data) {
                        return `<div class="d-flex justify-content-center">
                                    <button class="btn btn-sm btn-success btn-rounded shadow-sm px-3 btn-muda-moris" data-id="${data.id_populasaun}" data-naran="${data.naran_kompletu}" title="Muda ba Moris">
                                        <i class="fas fa-heartbeat mr-1"></i> Moris Fali
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

        tableMate.on('draw.dt', function () {
            var PageInfo = $('#table-populasaun-mate').DataTable().page.info();
            tableMate.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });
        });

        // Table 4: Muda
        var tableMuda = $('#table-populasaun-muda').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [[2, 'asc']],
            ajax : {
                url: '<?= route_to('admin/populasaun') ?>?type=estatutu_muda',
                method: 'GET',
                data: function(d) {
                    d.id_aldeia = $('#filter-aldeia').val();
                }
            },
            columnDefs: [{
                orderable: false,
                targets: <?= in_groups('admin') ? '[0, 7]' : '[0]' ?>
            }],
            columns: [
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
                    'data': 'istadu',
                    'render': function(data) {
                        return `<span class="badge badge-warning text-white badge-premium"><i class="fas fa-map-marker-alt mr-1"></i> ${data}</span>`;
                    }
                },
                { 
                    'data': 'data_muda',
                    'render': function(data) {
                        if (data === '-') return data;
                        let dateObj = new Date(data);
                        let day = String(dateObj.getDate()).padStart(2, '0');
                        let months = ['Janeiru', 'Fovereiru', 'Marsu', 'Abril', 'Maiu', 'Juñu', 'Juliu', 'Agostu', 'Setembru', 'Outubru', 'Novembru', 'Dezembru'];
                        let month = months[dateObj.getMonth()];
                        let year = dateObj.getFullYear();
                        return `<span class="font-weight-bold text-warning"><i class="fas fa-calendar-alt mr-1"></i> ${day} ${month} ${year}</span>`;
                    }
                }
                <?php if (in_groups('admin')) : ?>,
                {
                    "data": function(data) {
                        return `<div class="d-flex justify-content-center">
                                    <button class="btn btn-sm btn-success btn-rounded shadow-sm px-3 btn-muda-moris" data-id="${data.id_populasaun}" data-naran="${data.naran_kompletu}" title="Muda ba Moris">
                                        <i class="fas fa-heartbeat mr-1"></i> Moris Fali
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

        tableMuda.on('draw.dt', function () {
            var PageInfo = $('#table-populasaun-muda').DataTable().page.info();
            tableMuda.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });
        });

        // Action: Change Deceased to Living Again
        $(document).on('click', '.btn-muda-moris', function() {
            let id = $(this).data('id');
            let naran = $(this).data('naran');
            Swal.fire({
                title: 'Ita boot hakarak muda status sidadaun ne\'e?',
                text: `Muda status "${naran}" ba Moris fali!`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#a18cd1',
                confirmButtonText: 'Sim, Moris Fali!',
                cancelButtonText: 'Kansela'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `<?= base_url('admin/populasaun') ?>/${id}/status`,
                        method: 'POST',
                        data: {
                            istadu: 'Moris',
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        }
                    }).done((response) => {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        tableMate.ajax.reload();
                        tableMuda.ajax.reload();
                        tableMoris.ajax.reload();
                    }).fail((xhr) => {
                        Toast.fire({
                            icon: 'error',
                            title: xhr.responseJSON.messages.error || 'Ita boot la iha kbiit/autorizasaun!'
                        });
                    });
                }
            });
        });

        // Filter reload on change
        $('#filter-aldeia').on('change', function() {
            tableNascimentu.ajax.reload();
            tableMoris.ajax.reload();
            tableMate.ajax.reload();
            tableMuda.ajax.reload();
        });

        // Tab changes refresh adjustments
        $('button[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            tableNascimentu.columns.adjust().draw();
            tableMoris.columns.adjust().draw();
            tableMate.columns.adjust().draw();
            tableMuda.columns.adjust().draw();
        });
    });
</script>
<?= $this->endSection() ?>
