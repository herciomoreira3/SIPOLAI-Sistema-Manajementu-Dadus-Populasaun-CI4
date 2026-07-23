<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<style>
    .card-premium {
        border-radius: 16px;
        border: none;
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.04);
        background: #fff;
    }
</style>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-edit mr-2"></i> Edit Dadus Membru Struktura</h3>
            </div>
            <form action="<?= base_url('admin/estrutura/' . $membru['id_estrutura']) ?>" method="POST" class="px-4 pb-4 pt-2" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <div class="form-group">
                    <label for="id_populasaun" class="font-weight-bold text-muted">Liga ba Populasaun (Opcional)</label>
                    <select name="id_populasaun" id="id_populasaun" class="form-select select2" style="width: 100%;">
                        <option value="">-- Hili se liga ba rezidente --</option>
                        <?php foreach ($populasaun as $pop) : ?>
                            <option value="<?= $pop['id_populasaun'] ?>" <?= (old('id_populasaun') ?? $membru['id_populasaun']) == $pop['id_populasaun'] ? 'selected' : '' ?>>
                                <?= esc($pop['naran_kompletu']) ?> (NIP: <?= esc($pop['nik']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="naran_membru" class="font-weight-bold text-muted">Naran Kompletu</label>
                    <input type="text" name="naran_membru" id="naran_membru" class="form-control <?= session('errors.naran_membru') ? 'is-invalid' : '' ?>" value="<?= old('naran_membru') ?? esc($membru['naran_membru']) ?>" placeholder="Fila naran kompletu..." required>
                    <?php if (session('errors.naran_membru')) : ?>
                        <div class="invalid-feedback"><?= session('errors.naran_membru') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="kargu" class="font-weight-bold text-muted">Kargu / Posisaun</label>
                    <select name="kargu" id="kargu" class="form-select <?= session('errors.kargu') ? 'is-invalid' : '' ?>" data-pop-aldeia-id="<?= $popAldeiaId ?>" required>
                        <option value="">-- Hili Kargu --</option>
                        <?php foreach ($kargus as $kg) : ?>
                            <option value="<?= esc($kg['naran_kargu']) ?>" <?= (old('kargu') ?? $membru['kargu']) == $kg['naran_kargu'] ? 'selected' : '' ?>><?= esc($kg['naran_kargu']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (session('errors.kargu')) : ?>
                        <div class="invalid-feedback"><?= session('errors.kargu') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group" id="aldeia-wrapper">
                    <label for="id_aldeia" class="font-weight-bold text-muted">Aldeia</label>
                    <select name="id_aldeia" id="id_aldeia" class="form-select">
                        <option value="">-- Hili Aldeia --</option>
                        <?php foreach ($aldeias as $ald) : ?>
                            <option value="<?= $ald['id_aldeia'] ?>" <?= (old('id_aldeia') ?? $membru['id_aldeia']) == $ald['id_aldeia'] ? 'selected' : '' ?>><?= esc($ald['naran_aldeia']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Hili Aldeia se membru ne'e mak Xefe Aldeia ka reprezentante Aldeia ruma.</small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="periodo_hahula" class="font-weight-bold text-muted">Data Hahula</label>
                            <input type="date" name="periodo_hahula" id="periodo_hahula" class="form-control <?= session('errors.periodo_hahula') ? 'is-invalid' : '' ?>" value="<?= old('periodo_hahula') ?? $membru['periodo_hahula'] ?>" required>
                            <?php if (session('errors.periodo_hahula')) : ?>
                                <div class="invalid-feedback"><?= session('errors.periodo_hahula') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="periodo_remata" class="font-weight-bold text-muted">Data Remata (Opcional)</label>
                            <input type="date" name="periodo_remata" id="periodo_remata" class="form-control" value="<?= old('periodo_remata') ?? $membru['periodo_remata'] ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status_kargu" class="font-weight-bold text-muted">Status Kargu</label>
                    <select name="status_kargu" id="status_kargu" class="form-select">
                        <option value="Ativu" <?= (old('status_kargu') ?? $membru['status_kargu']) == 'Ativu' ? 'selected' : '' ?>>Ativu</option>
                        <option value="Inativu" <?= (old('status_kargu') ?? $membru['status_kargu']) == 'Inativu' ? 'selected' : '' ?>>Inativu</option>
                    </select>
                </div>

                <?php if (!empty($membru['foto']) && file_exists(FCPATH . 'uploads/familia/' . $membru['foto'])) : ?>
                    <div class="form-group">
                        <label class="font-weight-bold text-muted">Foto Saat Ini</label><br>
                        <img src="<?= base_url('uploads/familia/' . $membru['foto']) ?>" alt="Foto Membru" style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px;">
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="foto" class="font-weight-bold text-muted">Foto (Opsional)</label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= base_url('admin/estrutura') ?>" class="btn btn-outline-secondary btn-rounded">Fila</a>
                    <button type="submit" class="btn btn-primary btn-rounded">Aktualiza Dadus</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- Include Select2 CDN -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4'
        });

        var activeMembers = <?= json_encode($activeMembers) ?>;
        var currentMemberId = "<?= $membru['id_estrutura'] ?>";

        function filterKargu(popAldeiaId) {
            var selectedKargu = $('#kargu').val();
            $('#kargu option').each(function() {
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

                var isTaken = false;
                for (var i = 0; i < activeMembers.length; i++) {
                    var member = activeMembers[i];
                    if (member.id_estrutura == currentMemberId) {
                        continue; // Ignore current member
                    }
                    if (member.kargu.toLowerCase() === karguVal) {
                        if (isAldeia) {
                            if (member.id_aldeia == popAldeiaId) {
                                isTaken = true;
                                break;
                            }
                        } else {
                            isTaken = true;
                            break;
                        }
                    }
                }

                if (isTaken && karguText !== selectedKargu) {
                    $(this).hide().prop('disabled', true);
                } else {
                    $(this).show().prop('disabled', false);
                }
            });
        }

        // Initialize kargu filtering on page load
        var initialPopAldeiaId = $('#kargu').data('pop-aldeia-id');
        filterKargu(initialPopAldeiaId);

        // When a resident is selected, copy their name to naran_membru and save their Aldeia ID
        $('#id_populasaun').on('change', function() {
            var selectedId = $(this).val();
            if (selectedId) {
                $.ajax({
                    url: '<?= route_to('promosaun') ?>',
                    type: 'GET',
                    data: { id_populasaun: selectedId },
                    success: function(response) {
                        if (response.success) {
                            $('#naran_membru').val(response.naran_kompletu);
                            $('#kargu').data('pop-aldeia-id', response.id_aldeia);
                            filterKargu(response.id_aldeia);
                            $('#kargu').trigger('change');
                        }
                    }
                });
            } else {
                $('#naran_membru').val('');
                $('#kargu').removeData('pop-aldeia-id');
                filterKargu(null);
                $('#kargu').trigger('change');
            }
        });

        // Kargu change listener
        $('#kargu').on('change', function() {
            var karguVal = $(this).val().toLowerCase();
            var popAldeiaId = $(this).data('pop-aldeia-id');

            var isAldeiaKargu = karguVal.indexOf('xefe aldeia') !== -1 || 
                                karguVal.indexOf('delgadu') !== -1 || 
                                karguVal.indexOf('delegado') !== -1 || 
                                karguVal.indexOf('delegada') !== -1;

            if (isAldeiaKargu) {
                $('#aldeia-wrapper').show();
                $('#id_aldeia').prop('required', true);
                if (popAldeiaId) {
                    $('#id_aldeia option').each(function() {
                        var optVal = $(this).val();
                        if (optVal === "" || optVal == popAldeiaId) {
                            $(this).prop('disabled', false);
                        } else {
                            $(this).prop('disabled', true);
                        }
                    });
                    $('#id_aldeia').val(popAldeiaId);
                } else {
                    $('#id_aldeia option').prop('disabled', false);
                }
            } else {
                $('#id_aldeia').val('').prop('required', false);
                $('#id_aldeia option').prop('disabled', false);
                $('#aldeia-wrapper').hide();
            }
        });

        // Trigger initial check on load if kargu has old value
        if ($('#kargu').val()) {
            $('#kargu').trigger('change');
        }
    });
</script>
<?= $this->endSection() ?>
