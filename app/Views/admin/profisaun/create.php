<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <form action="<?= route_to('admin/profisaun') ?>" method="post" class="form-horizontal">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="naran_profisaun" class="col-sm-2 col-form-label">Naran Profisaun</label>
                        <div class="col-sm-10">
                            <input type="text" 
                                   name="naran_profisaun" 
                                   class="form-control <?= session('error.naran_profisaun') ? 'is-invalid' : '' ?>" 
                                   id="naran_profisaun" 
                                   placeholder="Prenxe naran profisaun..." 
                                   value="<?= old('naran_profisaun') ?>" 
                                   required>
                            <?php if (session('error.naran_profisaun')) : ?>
                                <span class="error invalid-feedback"><?= session('error.naran_profisaun') ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Rai Dadus</button>
                    <a href="<?= route_to('admin/profisaun') ?>" class="btn btn-default float-right">Kansela</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
