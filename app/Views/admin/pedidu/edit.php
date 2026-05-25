<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<style>
    .card-premium {
        border-radius: 16px;
        border: none;
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.04);
        background: #fff;
    }
    .form-control-premium {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 10px 14px;
        height: auto;
        transition: all 0.2s ease;
    }
    .form-control-premium:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }
    .section-title {
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #4b5563;
        border-bottom: 2px solid #f3f4f6;
        padding-bottom: 8px;
        margin-bottom: 20px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card card-premium card-outline card-primary">
            <form action="<?= route_to('admin/pedidu') ?>/<?= $pedidu['id_pedidu'] ?>" method="post" class="form-horizontal">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT" />
                <input type="hidden" name="naran_pedidu" value="<?= esc($pedidu['naran_pedidu']) ?>" />

                <div class="card-body px-4 py-4">
                    <div class="row">
                        <!-- Left Column: Request Details -->
                        <div class="col-md-4 border-right pr-4">
                            <h5 class="section-title"><i class="fas fa-file-alt mr-2 text-primary"></i> Detallu Pedidu</h5>
                            
                            <div class="form-group">
                                <label class="font-weight-bold small text-secondary">Tipu Deklarasaun</label>
                                <input type="text" class="form-control form-control-premium bg-light" value="<?= esc($pedidu['naran_pedidu']) ?>" readonly />
                            </div>

                            <div class="form-group">
                                <label for="data_pedidu" class="font-weight-bold small text-secondary">Data Pedidu</label>
                                <input type="date" name="data_pedidu" class="form-control form-control-premium <?= session('error.data_pedidu') ? 'is-invalid' : '' ?>" id="data_pedidu" value="<?= old('data_pedidu', $pedidu['data_pedidu']) ?>" required>
                                <?php if (session('error.data_pedidu')) : ?>
                                    <span class="error invalid-feedback"><?= session('error.data_pedidu') ?></span>
                                <?php endif ?>
                            </div>

                            <div class="form-group">
                                <label for="id_aldeia" class="font-weight-bold small text-secondary">Aldeia</label>
                                <select name="id_aldeia" class="form-control form-control-premium select2" id="id_aldeia" required>
                                    <?php foreach ($aldeias as $aldeia) : ?>
                                        <option value="<?= $aldeia['id_aldeia'] ?>" <?= old('id_aldeia', $pedidu['id_aldeia']) == $aldeia['id_aldeia'] ? 'selected' : '' ?>><?= esc($aldeia['naran_aldeia']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Right Column: Baby/Population Details -->
                        <div class="col-md-8 pl-4">
                            <h5 class="section-title"><i class="fas fa-baby mr-2 text-success"></i> Dadus Populasaun (Oan Foun)</h5>
                            
                            <div class="alert alert-light border shadow-sm mb-4 d-flex align-items-center" style="border-radius: 10px; background-color: #fafafa;">
                                <i class="fas fa-info-circle text-info mr-3 fa-lg"></i>
                                <div>
                                    <span class="text-secondary small d-block">Dadus populasaun ne'ebé hadia iha ne'e mak sei rejista ba <strong>tabela_populasaun</strong> automatikamente wainhira pedidu ne'e hetan aprovasaun husi Xefe Suku.</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="pemohon" class="font-weight-bold small text-secondary">Naran Kompletu Oan</label>
                                    <input type="text" name="pemohon" class="form-control form-control-premium <?= session('error.pemohon') ? 'is-invalid' : '' ?>" id="pemohon" placeholder="Ex: Abel da Costa" value="<?= old('pemohon', $pedidu['pemohon']) ?>" required>
                                    <?php if (session('error.pemohon')) : ?>
                                        <span class="error invalid-feedback"><?= session('error.pemohon') ?></span>
                                    <?php endif ?>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="jeneru" class="font-weight-bold small text-secondary">Sexu (Jeneru)</label>
                                    <select name="jeneru" class="form-control form-control-premium" id="jeneru" required>
                                        <option value="Mane" <?= old('jeneru', $meta['jeneru'] ?? '') == 'Mane' ? 'selected' : '' ?>>Mane</option>
                                        <option value="Feto" <?= old('jeneru', $meta['jeneru'] ?? '') == 'Feto' ? 'selected' : '' ?>>Feto</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="fatin_moris" class="font-weight-bold small text-secondary">Fatin Moris</label>
                                    <input type="text" name="fatin_moris" class="form-control form-control-premium" id="fatin_moris" placeholder="Ex: Laisorolai de Baixo" value="<?= old('fatin_moris', $meta['fatin_moris'] ?? '') ?>" required>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="data_moris" class="font-weight-bold small text-secondary">Data Moris</label>
                                    <input type="date" name="data_moris" class="form-control form-control-premium" id="data_moris" value="<?= old('data_moris', $meta['data_moris'] ?? '') ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="id_relijiaun" class="font-weight-bold small text-secondary">Relijiaun</label>
                                    <select name="id_relijiaun" class="form-control form-control-premium select2" id="id_relijiaun" required>
                                        <?php foreach ($relijiaun as $r) : ?>
                                            <option value="<?= $r['id_relijiaun'] ?>" <?= old('id_relijiaun', $meta['id_relijiaun'] ?? '') == $r['id_relijiaun'] ? 'selected' : '' ?>><?= esc($r['naran_relijiaun']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="id_profisaun" class="font-weight-bold small text-secondary">Profisaun</label>
                                    <select name="id_profisaun" class="form-control form-control-premium select2" id="id_profisaun" required>
                                        <?php foreach ($profisaun as $p) : ?>
                                            <option value="<?= $p['id_profisaun'] ?>" <?= old('id_profisaun', $meta['id_profisaun'] ?? '') == $p['id_profisaun'] ? 'selected' : '' ?>><?= esc($p['naran_profisaun']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="id_literatura" class="font-weight-bold small text-secondary">Literatura</label>
                                    <select name="id_literatura" class="form-control form-control-premium select2" id="id_literatura" required>
                                        <?php foreach ($literatura as $l) : ?>
                                            <option value="<?= $l['id_literatura'] ?>" <?= old('id_literatura', $meta['id_literatura'] ?? '') == $l['id_literatura'] ? 'selected' : '' ?>><?= esc($l['naran_literatura']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="id_familia" class="font-weight-bold small text-secondary">KK / Xefe Familia</label>
                                    <select name="id_familia" class="form-control form-control-premium select2" id="id_familia" required>
                                        <?php foreach ($familias as $fam) : ?>
                                            <option value="<?= $fam['id_familia'] ?>" <?= old('id_familia', $meta['id_familia'] ?? '') == $fam['id_familia'] ? 'selected' : '' ?>>
                                                KK: <?= esc($fam['numeru_kk']) ?> - Xefe: <?= esc($fam['naran_xefe']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="nik" class="font-weight-bold small text-secondary">NIK (Oan) <span class="text-muted">(Optional)</span></label>
                                    <input type="text" name="nik" class="form-control form-control-premium" id="nik" placeholder="Se mamuk sei auto-generate..." value="<?= old('nik', $meta['nik'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 px-4 pb-4">
                    <button type="submit" class="btn btn-primary btn-rounded shadow-sm px-4"><i class="fas fa-save mr-1"></i> Hadia Oan Foun</button>
                    <a href="<?= route_to('admin/pedidu') ?>" class="btn btn-secondary btn-rounded float-right shadow-sm px-4">Kansela</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
