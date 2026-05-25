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
</style>

<div class="row">
    <div class="col-12">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-secondary mb-0"><i class="fas fa-level-up-alt mr-2"></i> Promosaun Populasaun ba Membru Struktura</h3>
                <a href="<?= base_url('admin/estrutura') ?>" class="btn btn-outline-secondary btn-rounded">Fila</a>
            </div>
            <div class="card-body px-4 pb-4">
                <p class="text-muted mb-3">Hili rezidente husi lista iha kraik atu foti/promove sai membru struktura suku nian sem presiza ketik fila fali naran.</p>

                <!-- Aldeia Filter Dropdown -->
                <div class="row mb-4 align-items-center">
                    <div class="col-md-4">
                        <label for="filter-aldeia" class="font-weight-bold text-muted mb-1"><i class="fas fa-filter mr-1"></i> Filtra tuir Aldeia</label>
                        <select id="filter-aldeia" class="form-control" style="border-radius: 8px;">
                            <option value="">-- Hatudu hotu (All Aldeias) --</option>
                            <?php foreach ($aldeias as $ald) : ?>
                                <option value="<?= esc($ald['naran_aldeia']) ?>"><?= esc($ald['naran_aldeia']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="table-populasaun-promosaun">
                        <thead>
                            <tr>
                                <th>NIP</th>
                                <th>Naran Kompletu</th>
                                <th>Aldeia</th>
                                <th>Sexu</th>
                                <th>Data Moris</th>
                                <th>Estadu Sivil</th>
                                <th class="text-center">Aksaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($populasaun as $pop) : ?>
                                <tr>
                                    <td class="font-weight-bold"><?= esc($pop['nik']) ?></td>
                                    <td class="text-secondary font-weight-bold"><?= esc($pop['naran_kompletu']) ?></td>
                                    <td class="text-secondary font-weight-semibold"><?= esc($pop['naran_aldeia'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge badge-light p-2"><?= esc($pop['jeneru']) ?></span>
                                    </td>
                                    <td><?= date('d-m-Y', strtotime($pop['data_moris'])) ?></td>
                                    <td><?= esc($pop['status_kaza']) ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning btn-rounded text-white btn-promote" 
                                                data-id="<?= $pop['id_populasaun'] ?>" 
                                                data-name="<?= esc($pop['naran_kompletu']) ?>"
                                                data-aldeia-id="<?= $pop['id_aldeia'] ?>">
                                            <i class="fas fa-level-up-alt mr-1"></i> Promove
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Promotion Modal -->
<div class="modal fade" id="promoteModal" tabindex="-1" role="dialog" aria-labelledby="promoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content card-premium">
            <div class="modal-header bg-transparent border-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-secondary" id="promoteModalLabel"><i class="fas fa-id-card mr-2"></i> Konfigura Posisaun / Kargu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/estrutura') ?>" method="POST" class="px-4 pb-4 pt-2">
                <?= csrf_field() ?>
                <input type="hidden" name="id_populasaun" id="modal-id-populasaun">

                <div class="form-group">
                    <label class="font-weight-bold text-muted">Naran Kompletu</label>
                    <input type="text" name="naran_membru" id="modal-naran-membru" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label for="modal-kargu" class="font-weight-bold text-muted">Kargu / Posisaun</label>
                    <select name="kargu" id="modal-kargu" class="form-select" required>
                        <option value="">-- Hili Kargu --</option>
                        <?php foreach ($kargus as $kg) : ?>
                            <option value="<?= esc($kg['naran_kargu']) ?>"><?= esc($kg['naran_kargu']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" id="modal-aldeia-wrapper">
                    <label for="modal-aldeia" class="font-weight-bold text-muted">Aldeia</label>
                    <select name="id_aldeia" id="modal-aldeia" class="form-select">
                        <option value="">-- Hili Aldeia --</option>
                        <?php foreach ($aldeias as $ald) : ?>
                            <option value="<?= $ald['id_aldeia'] ?>"><?= esc($ald['naran_aldeia']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Hili Aldeia se membru ne'e mak Xefe Aldeia ka reprezentante Aldeia ruma.</small>
                </div>

                <div class="form-group">
                    <label for="modal-hahula" class="font-weight-bold text-muted">Data Hahula</label>
                    <input type="date" name="periodo_hahula" id="modal-hahula" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label for="modal-remata" class="font-weight-bold text-muted">Data Remata (Opcional)</label>
                    <input type="date" name="periodo_remata" id="modal-remata" class="form-control">
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary btn-rounded" data-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-primary btn-rounded">Konfirma Promosaun</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        var table = $('#table-populasaun-promosaun').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
            }
        });

        // Filter by Aldeia column (index 2)
        $('#filter-aldeia').on('change', function() {
            var selected = $(this).val();
            // exact regex search
            table.column(2).search(selected ? '^' + $.fn.dataTable.util.escapeRegex(selected) + '$' : '', true, false).draw();
        });


        var activeMembers = <?= json_encode($activeMembers) ?>;

        function filterModalKargu(popAldeiaId) {
            $('#modal-kargu option').each(function() {
                var karguText = $(this).val();
                if (karguText === "") {
                    $(this).show().prop('disabled', false);
                    return;
                }
                var karguVal = karguText.toLowerCase();

                var isAldeia = karguVal.indexOf('xefe aldeia') !== -1 || 
                               karguVal.indexOf('delgadu') !== -1 || 
                               karguVal.indexOf('delegado') !== -1 || 
                               karguVal.indexOf('delegada') !== -1;

                // Check if this kargu is taken
                var isTaken = false;
                for (var i = 0; i < activeMembers.length; i++) {
                    var member = activeMembers[i];
                    if (member.kargu.toLowerCase() === karguVal) {
                        if (isAldeia) {
                            // Unique per Aldeia
                            if (member.id_aldeia == popAldeiaId) {
                                isTaken = true;
                                break;
                            }
                        } else {
                            // Unique globally (Suku level)
                            isTaken = true;
                            break;
                        }
                    }
                }

                if (isTaken) {
                    $(this).hide().prop('disabled', true);
                } else {
                    $(this).show().prop('disabled', false);
                }
            });
        }

        // Trigger Promotion Modal
        $('#table-populasaun-promosaun').on('click', '.btn-promote', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var aldeiaId = $(this).data('aldeia-id');

            $('#modal-id-populasaun').val(id);
            $('#modal-naran-membru').val(name);
            $('#modal-kargu').data('pop-aldeia-id', aldeiaId);
            
            // Filter kargu dropdown based on the resident's Aldeia ID
            filterModalKargu(aldeiaId);

            $('#promoteModal').modal('show');

            // Trigger kargu change check
            $('#modal-kargu').val('').trigger('change');
        });

        // Kargu change listener in modal
        $('#modal-kargu').on('change', function() {
            var karguVal = $(this).val().toLowerCase();
            var popAldeiaId = $(this).data('pop-aldeia-id');

            var isAldeiaKargu = karguVal.indexOf('xefe aldeia') !== -1 || 
                                karguVal.indexOf('delgadu') !== -1 || 
                                karguVal.indexOf('delegado') !== -1 || 
                                karguVal.indexOf('delegada') !== -1;

            if (isAldeiaKargu) {
                $('#modal-aldeia-wrapper').show();
                $('#modal-aldeia').prop('required', true);
                if (popAldeiaId) {
                    $('#modal-aldeia option').each(function() {
                        var optVal = $(this).val();
                        if (optVal === "" || optVal == popAldeiaId) {
                            $(this).prop('disabled', false);
                        } else {
                            $(this).prop('disabled', true);
                        }
                    });
                    $('#modal-aldeia').val(popAldeiaId);
                } else {
                    $('#modal-aldeia option').prop('disabled', false);
                }
            } else {
                $('#modal-aldeia').val('').prop('required', false);
                $('#modal-aldeia option').prop('disabled', false);
                $('#modal-aldeia-wrapper').hide();
            }
        });

    });
</script>
<?= $this->endSection() ?>
