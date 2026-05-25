<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <form action="<?= route_to('admin/populasaun') ?>" method="post" class="form-horizontal">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="naran_kompletu" class="col-sm-2 col-form-label">Naran</label>
                        <div class="col-sm-10">
                            <input type="text" name="naran_kompletu" class="form-control <?= session('error.naran_kompletu') ? 'is-invalid' : '' ?>" id="naran_kompletu" placeholder="Prenxe naran..." value="<?= old('naran_kompletu') ?>" required>
                            <?php if (session('error.naran_kompletu')) : ?>
                                <span class="error invalid-feedback"><?= session('error.naran_kompletu') ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="fatin_moris" class="col-sm-2 col-form-label">Fatin Moris</label>
                        <div class="col-sm-10">
                            <input type="text" name="fatin_moris" class="form-control <?= session('error.fatin_moris') ? 'is-invalid' : '' ?>" id="fatin_moris" placeholder="Prenxe fatin moris..." value="<?= old('fatin_moris') ?>" required>
                            <?php if (session('error.fatin_moris')) : ?>
                                <span class="error invalid-feedback"><?= session('error.fatin_moris') ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="data_moris" class="col-sm-2 col-form-label">Data Moris</label>
                        <div class="col-sm-10">
                            <input type="date" name="data_moris" class="form-control <?= session('error.data_moris') ? 'is-invalid' : '' ?>" id="data_moris" value="<?= old('data_moris') ?>" required>
                            <?php if (session('error.data_moris')) : ?>
                                <span class="error invalid-feedback"><?= session('error.data_moris') ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="jeneru" class="col-sm-2 col-form-label">Sexu</label>
                        <div class="col-sm-10">
                            <select name="jeneru" class="form-control" id="jeneru" required>
                                <option value="Mane" <?= old('jeneru') == 'Mane' ? 'selected' : '' ?>>Mane</option>
                                <option value="Feto" <?= old('jeneru') == 'Feto' ? 'selected' : '' ?>>Feto</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="status_kaza" class="col-sm-2 col-form-label">Estadu Sivil</label>
                        <div class="col-sm-10">
                            <select name="status_kaza" class="form-control" id="status_kaza" required>
                                <option value="Solteiru/a" <?= old('status_kaza') == 'Solteiru/a' ? 'selected' : '' ?>>Solteiru/a</option>
                                <option value="Kabe-Nain" <?= old('status_kaza') == 'Kabe-Nain' ? 'selected' : '' ?>>Kabe-Nain</option>
                                <option value="Faluk" <?= old('status_kaza') == 'Faluk' ? 'selected' : '' ?>>Faluk</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_aldeia" class="col-sm-2 col-form-label">Aldeia</label>
                        <div class="col-sm-10">
                            <select name="id_aldeia" class="form-control" id="id_aldeia" required>
                                <?php foreach ($aldeias as $aldeia) : ?>
                                    <option value="<?= $aldeia['id_aldeia'] ?>" <?= old('id_aldeia') == $aldeia['id_aldeia'] ? 'selected' : '' ?>><?= $aldeia['naran_aldeia'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_profisaun" class="col-sm-2 col-form-label">Profisaun</label>
                        <div class="col-sm-10">
                            <select name="id_profisaun" class="form-control" id="id_profisaun" required>
                                <?php foreach ($profisaun as $p) : ?>
                                    <option value="<?= $p['id_profisaun'] ?>" <?= old('id_profisaun') == $p['id_profisaun'] ? 'selected' : '' ?>><?= $p['naran_profisaun'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_relijiaun" class="col-sm-2 col-form-label">Relijiaun</label>
                        <div class="col-sm-10">
                            <select name="id_relijiaun" class="form-control" id="id_relijiaun" required>
                                <?php foreach ($relijiaun as $r) : ?>
                                    <option value="<?= $r['id_relijiaun'] ?>" <?= old('id_relijiaun') == $r['id_relijiaun'] ? 'selected' : '' ?>><?= $r['naran_relijiaun'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_literatura" class="col-sm-2 col-form-label">Literatura</label>
                        <div class="col-sm-10">
                            <select name="id_literatura" class="form-control" id="id_literatura" required>
                                <?php foreach ($literatura as $l) : ?>
                                    <option value="<?= $l['id_literatura'] ?>" <?= old('id_literatura') == $l['id_literatura'] ? 'selected' : '' ?>><?= $l['naran_literatura'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Rai Dadus</button>
                    <a href="<?= route_to('admin/populasaun') ?>" class="btn btn-default float-right">Kansela</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
