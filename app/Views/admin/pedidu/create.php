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
    .form-control-premium {
        border-radius: 8px;
        border: 1px solid #e4e4e7;
        padding: 10px 12px;
        font-size: 14px;
        transition: all 0.2s ease;
        height: auto;
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
                    <i class="fas fa-file-signature text-primary mr-2"></i> Kria Pedidu Foun
                </h3>
                <a href="<?= route_to('admin/pedidu') ?>" class="btn btn-light btn-rounded shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Fila ba Lista
                </a>
            </div>
            <div class="card-body px-4 pb-4">
                
                <!-- Filters and Options Selection Bar -->
                <div class="row mb-4 align-items-end">
                    <!-- Dropdown Tipu Deklarasaun -->
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="select-tipu" class="font-weight-bold text-secondary small text-uppercase">1. Hili Tipu Deklarasaun</label>
                        <select id="select-tipu" class="form-control form-control-premium select2 shadow-sm">
                            <?php foreach ($tipus as $t) : ?>
                                <option value="<?= esc($t['naran_tipu_pedidu']) ?>"><?= esc($t['naran_tipu_pedidu']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Dropdown Aldeia Filter -->
                    <div class="col-md-6">
                        <label for="filter-aldeia" class="font-weight-bold text-secondary small text-uppercase">2. Filtru bazeia ba Aldeia</label>
                        <select id="filter-aldeia" class="form-control form-control-premium select2 shadow-sm">
                            <option value="">-- Haree Aldeia Hotu --</option>
                            <?php foreach ($aldeias as $aldeia) : ?>
                                <option value="<?= $aldeia['id_aldeia'] ?>"><?= esc($aldeia['naran_aldeia']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Info Alert (Dynamic) -->
                <div id="eleitoral-notice" class="alert alert-info border-0 shadow-sm mb-4 d-none" style="border-radius: 10px; background-color: #f0fdf4; color: #166534;">
                    <i class="fas fa-info-circle mr-2"></i> <strong>Informasaun:</strong> Bazeia ba regras eleitorais Timor-Leste, populasaun ne'ebé bele halo Deklarasaun Eleitoral mak sira ne'ebé ho tinan <strong>17 ba leten</strong> de'it.
                </div>

                <!-- Container 1: Standard Population Datatables (For Eleitoral, Nascimento, Mortalidade, Bom Comportamentu) -->
                <div id="container-populasaun" class="table-responsive">
                    <table id="table-populasaun-pedidu" class="table table-hover va-middle" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 4%;">#</th>
                                <th>NIP/NIK</th>
                                <th>Naran Kompletu</th>
                                <th>Sexu</th>
                                <th>Aldeia</th>
                                <th>Idade</th>
                                <th class="text-center" style="width: 18%;">Aksaun</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <!-- Container 2: Family Datatables (For Deklarasaun Nascimentu / Adding Newborn child to head of family) -->
                <div id="container-familia" class="table-responsive d-none">
                    <table id="table-familia-pedidu-nascimentu" class="table table-hover va-middle" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 4%;">#</th>
                                <th>Numeru Fixa Familia (KK)</th>
                                <th>Aldeia</th>
                                <th>Xefe Familia</th>
                                <th class="text-center" style="width: 20%;">Aksaun</th>
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

<!-- Modal Aumenta Membru (Nascimentu / Newborn Registration) -->
<div class="modal fade" id="modal-aumenta-membru" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 pt-4 px-4 pb-2">
                <h5 class="modal-title font-weight-bold text-secondary" id="modalLabel">
                    <i class="fas fa-baby text-primary mr-2"></i> Rejistu Oan Foun (Nascimentu)
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-nascimentu">
                <input type="hidden" id="nas-id-familia" name="id_familia">
                <input type="hidden" id="nas-id-aldeia" name="id_aldeia">
                <div class="modal-body px-4 py-2">
                    
                    <!-- Alert KK context -->
                    <div class="alert alert-light border shadow-sm mb-4 d-flex align-items-center" style="border-radius: 10px; background-color: #fafafa;">
                        <i class="fas fa-id-card text-success mr-3 fa-lg"></i>
                        <div>
                            <span class="text-muted d-block small text-uppercase font-weight-bold" style="letter-spacing: 0.5px; font-size: 10px;">Numeru KK</span>
                            <strong id="nas-kk-text" class="text-secondary">-</strong>
                        </div>
                        <div class="ml-auto pl-4 border-left">
                            <span class="text-muted d-block small text-uppercase font-weight-bold" style="letter-spacing: 0.5px; font-size: 10px;">Xefe Familia</span>
                            <strong id="nas-xefe-text" class="text-secondary">-</strong>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-secondary">Naran Kompletu Oan</label>
                            <input type="text" name="pemohon" class="form-control form-control-premium" placeholder="Ex: Abel da Costa" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-secondary">Sexu (Jeneru)</label>
                            <select name="jeneru" class="form-control form-control-premium" required>
                                <option value="Mane">Mane</option>
                                <option value="Feto">Feto</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-secondary">Fatin Moris</label>
                            <input type="text" name="fatin_moris" class="form-control form-control-premium" placeholder="Ex: Laisorolai de Baixo" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-secondary">Data Moris</label>
                            <input type="date" name="data_moris" class="form-control form-control-premium" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-secondary">Relijiaun</label>
                            <select name="id_relijiaun" class="form-control form-control-premium" required>
                                <?php foreach ($relijiaun as $r) : ?>
                                    <option value="<?= $r['id_relijiaun'] ?>"><?= esc($r['naran_relijiaun']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-secondary">Profisaun</label>
                            <select name="id_profisaun" class="form-control form-control-premium" required>
                                <?php foreach ($profisaun as $p) : ?>
                                    <option value="<?= $p['id_profisaun'] ?>" <?= esc($p['naran_profisaun']) == 'Estudante' ? 'selected' : '' ?>><?= esc($p['naran_profisaun']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-secondary">Literatura</label>
                            <select name="id_literatura" class="form-control form-control-premium" required>
                                <?php foreach ($literatura as $l) : ?>
                                    <option value="<?= $l['id_literatura'] ?>" <?= esc($l['naran_literatura']) == 'Seidauk Eskola' ? 'selected' : '' ?>><?= esc($l['naran_literatura']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-secondary">NIK (Oan) <span class="text-muted">(Optional)</span></label>
                            <input type="text" name="nik" class="form-control form-control-premium" placeholder="Se mamuk sei auto-generate...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light btn-rounded" data-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-primary btn-rounded shadow-sm">
                        <i class="fas fa-save mr-1"></i> Rai Pedidu
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
        
        // 1. DataTable for Standard Population Requests
        var tablePopulasaun = $('#table-populasaun-pedidu').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [[2, 'asc']],
            ajax : {
                url: '<?= route_to('pedidu-populasaun-list') ?>',
                method: 'GET',
                data: function(d) {
                    d.naran_pedidu = $('#select-tipu').val();
                    d.id_aldeia = $('#filter-aldeia').val();
                }
            },
            columnDefs: [{
                orderable: false,
                targets: [0, 5, 6]
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
                    'data': 'data_moris',
                    'render': function(data) {
                        if (!data) return '-';
                        let birth = new Date(data);
                        let today = new Date();
                        let age = today.getFullYear() - birth.getFullYear();
                        let m = today.getMonth() - birth.getMonth();
                        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
                            age--;
                        }
                        return `<span class="font-weight-bold text-secondary">${age} Tinan</span>`;
                    }
                },
                {
                    "data": function(data) {
                        if (parseInt(data.pending_count) > 0) {
                            return `<div class="text-center">
                                        <span class="badge badge-warning text-white badge-premium py-2 px-3">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Pedidu Pendente
                                        </span>
                                    </div>`;
                        }
                        
                        return `<div class="d-flex justify-content-center">
                                    <button class="btn btn-sm btn-primary btn-rounded btn-create-pedidu shadow-sm" 
                                            data-id="${data.id_populasaun}"
                                            data-naran="${data.naran_kompletu}" 
                                            data-aldeia="${data.id_aldeia}" 
                                            title="Kria Pedidu">
                                        <i class="fas fa-file-signature mr-1"></i> Kria Pedidu
                                    </button>
                                </div>`;
                    }
                }
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
            }
        });

        tablePopulasaun.on('draw.dt', function () {
            var PageInfo = $('#table-populasaun-pedidu').DataTable().page.info();
            tablePopulasaun.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });
        });

        // 2. DataTable for Family Requests (For Deklarasaun Nascimentu)
        var tableFamilia = $('#table-familia-pedidu-nascimentu').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [[1, 'asc']],
            ajax : {
                url: '<?= route_to('pedidu-familia-list') ?>',
                method: 'GET',
                data: function(d) {
                    d.id_aldeia = $('#filter-aldeia').val();
                }
            },
            columnDefs: [{
                orderable: false,
                targets: [0, 4]
            }],
            columns : [
                { 'data': null },
                { 
                    'data': 'numeru_kk',
                    'render': function(data) {
                        return `<span class="font-weight-bold text-primary">${data}</span>`;
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
                        return `<span class="font-weight-bold text-secondary">${data || '-'}</span>`;
                    }
                },
                {
                    "data": function(row) {
                        return `<div class="d-flex justify-content-center">
                                    <button class="btn btn-sm btn-success btn-rounded btn-aumenta-membru shadow-sm" 
                                            data-id="${row.id_familia}" 
                                            data-kk="${row.numeru_kk}" 
                                            data-xefe="${row.xefe_familia || '-'}" 
                                            data-aldeia="${row.id_aldeia}" 
                                            title="Aumenta Membru">
                                        <i class="fas fa-baby mr-1"></i> Aumenta Membru
                                    </button>
                                </div>`;
                    }
                }
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
            }
        });

        tableFamilia.on('draw.dt', function () {
            var PageInfo = $('#table-familia-pedidu-nascimentu').DataTable().page.info();
            tableFamilia.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });
        });

        // 3. Dynamic Datatables switcher based on type
        function toggleTable() {
            var selectedTipu = $('#select-tipu').val();
            if (selectedTipu === 'Deklarasaun Nascimentu') {
                $('#container-populasaun').addClass('d-none');
                $('#container-familia').removeClass('d-none');
                tableFamilia.columns.adjust().draw();
            } else {
                $('#container-familia').addClass('d-none');
                $('#container-populasaun').removeClass('d-none');
                tablePopulasaun.columns.adjust().draw();
            }
        }

        $('#select-tipu').on('change', function() {
            if ($(this).val() === 'Deklarasaun Eleitoral') {
                $('#eleitoral-notice').removeClass('d-none');
            } else {
                $('#eleitoral-notice').addClass('d-none');
            }
            toggleTable();
        });

        $('#filter-aldeia').on('change', function() {
            tablePopulasaun.ajax.reload();
            tableFamilia.ajax.reload();
        });

        // Parse GET parameters on load
        const urlParams = new URLSearchParams(window.location.search);
        const getPedidu = urlParams.get('naran_pedidu');
        const getPemohon = urlParams.get('pemohon');
        const getAldeia = urlParams.get('id_aldeia');

        if (getPedidu) {
            $('#select-tipu').val(getPedidu).trigger('change');
        }
        if (getAldeia) {
            $('#filter-aldeia').val(getAldeia).trigger('change');
        }
        if (getPemohon) {
            tablePopulasaun.search(getPemohon).draw();
        }

        // Run check on load
        if ($('#select-tipu').val() === 'Deklarasaun Eleitoral') {
            $('#eleitoral-notice').removeClass('d-none');
        }
        toggleTable();

        // 4. Action: Create standard request with SweetAlert2
        $(document).on('click', '.btn-create-pedidu', function() {
            var idPopulasaun = $(this).data('id');
            var naran = $(this).data('naran');
            var idAldeia = $(this).data('aldeia');
            var tipu = $('#select-tipu').val();

            if (tipu === 'Deklarasaun Mortalidade') {
                Swal.fire({
                    title: 'Hatama Data Mate / Tanggal Kematian',
                    html: `
                        <div class="text-left mt-3">
                            <p class="mb-2 text-secondary font-weight-bold small text-uppercase">Data Mate ba sidadaun: <strong>${naran}</strong></p>
                            <label for="swal-data-mate" class="font-weight-bold small text-secondary">Hili Data Mate / Tanggal Kematian:</label>
                            <input type="date" id="swal-data-mate" class="form-control form-control-premium shadow-sm" value="${new Date().toISOString().substring(0, 10)}" required>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#71717a',
                    confirmButtonText: 'Kria Pedidu',
                    cancelButtonText: 'Kansela',
                    preConfirm: () => {
                        const val = document.getElementById('swal-data-mate').value;
                        if (!val) {
                            Swal.showValidationMessage('Ita boot tenki hatama data mate!');
                        }
                        return val;
                    }
                }).then((dateResult) => {
                    if (dateResult.value) {
                        var dataMate = dateResult.value;
                        
                        $.ajax({
                            url: '<?= route_to('pedidu-create-ajax') ?>',
                            method: 'POST',
                            data: {
                                naran_pedidu: tipu,
                                id_populasaun: idPopulasaun,
                                pemohon: naran,
                                id_aldeia: idAldeia,
                                meta_data: JSON.stringify({ data_mate: dataMate }),
                                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                            }
                        }).done((response) => {
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                            
                            setTimeout(function() {
                                window.location.href = '<?= route_to('admin/pedidu') ?>';
                            }, 1200);
                        }).fail((xhr) => {
                            var errorMsg = xhr.responseJSON ? xhr.responseJSON.message : "Falla kria pedidu!";
                            if (xhr.responseJSON && typeof xhr.responseJSON.messages === 'object') {
                                errorMsg = Object.values(xhr.responseJSON.messages).join("<br>");
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Kansela kria pedidu!',
                                html: errorMsg,
                                confirmButtonColor: '#09090b'
                            });
                        });
                    }
                });
                return;
            }

            Swal.fire({
                title: 'Kria Pedidu Foun?',
                text: `Ita boot hakarak kria pedidu '${tipu}' ba sidadaun '${naran}'?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#09090b',
                cancelButtonColor: '#71717a',
                confirmButtonText: 'Sim, Kria!',
                cancelButtonText: 'Kansela'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '<?= route_to('pedidu-create-ajax') ?>',
                        method: 'POST',
                        data: {
                            naran_pedidu: tipu,
                            id_populasaun: idPopulasaun,
                            pemohon: naran,
                            id_aldeia: idAldeia,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        }
                    }).done((response) => {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        
                        setTimeout(function() {
                            window.location.href = '<?= route_to('admin/pedidu') ?>';
                        }, 1200);
                    }).fail((xhr) => {
                        var errorMsg = xhr.responseJSON ? xhr.responseJSON.message : "Falla kria pedidu!";
                        if (xhr.responseJSON && typeof xhr.responseJSON.messages === 'object') {
                            errorMsg = Object.values(xhr.responseJSON.messages).join("<br>");
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Kansela kria pedidu!',
                            html: errorMsg,
                            confirmButtonColor: '#09090b'
                        });
                    });
                }
            });
        });

        // 5. Action: Open Modal to add newborn child details
        $(document).on('click', '.btn-aumenta-membru', function() {
            var idFamilia = $(this).data('id');
            var numeruKk = $(this).data('kk');
            var xefe = $(this).data('xefe');
            var idAldeia = $(this).data('aldeia');

            $('#nas-id-familia').val(idFamilia);
            $('#nas-id-aldeia').val(idAldeia);
            $('#nas-kk-text').text(numeruKk);
            $('#nas-xefe-text').text(xefe);
            
            // Reset form
            $('#form-nascimentu')[0].reset();

            // Set current date as default birth date
            var todayStr = new Date().toISOString().substring(0, 10);
            $('input[name="data_moris"]').val(todayStr);

            $('#modal-aumenta-membru').modal('show');
        });

        // 6. Action: Submit Newborn Request Form
        $('#form-nascimentu').on('submit', function(e) {
            e.preventDefault();
            
            var formData = $(this).serializeArray();
            formData.push({ name: 'naran_pedidu', value: 'Deklarasaun Nascimentu' });
            formData.push({ name: '<?= csrf_token() ?>', value: '<?= csrf_hash() ?>' });

            Swal.fire({
                title: 'Kria Pedidu Nascimentu?',
                text: "Ita boot hakarak kria pedidu Deklarasaun Nascimentu foun ba oan ne'e?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#09090b',
                cancelButtonColor: '#71717a',
                confirmButtonText: 'Sim, Kria!',
                cancelButtonText: 'Kansela'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '<?= route_to('pedidu-create-ajax') ?>',
                        method: 'POST',
                        data: formData
                    }).done((response) => {
                        $('#modal-aumenta-membru').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        
                        setTimeout(function() {
                            window.location.href = '<?= route_to('admin/pedidu') ?>';
                        }, 1200);
                    }).fail((xhr) => {
                        var errorMsg = xhr.responseJSON ? xhr.responseJSON.message : "Falla kria pedidu!";
                        if (xhr.responseJSON && typeof xhr.responseJSON.messages === 'object') {
                            errorMsg = Object.values(xhr.responseJSON.messages).join("<br>");
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Kansela kria pedidu!',
                            html: errorMsg,
                            confirmButtonColor: '#09090b'
                        });
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
