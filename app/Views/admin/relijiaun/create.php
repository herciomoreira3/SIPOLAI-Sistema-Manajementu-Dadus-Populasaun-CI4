<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <form action="<?= route_to('admin/relijiaun') ?>" method="post" class="form-horizontal">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="naran_relijiaun" class="col-sm-2 col-form-label">Naran Relijiaun</label>
                        <div class="col-sm-10">
                            <input type="text" 
                                   name="naran_relijiaun" 
                                   class="form-control <?= session('error.naran_relijiaun') ? 'is-invalid' : '' ?>" 
                                   id="naran_relijiaun" 
                                   placeholder="Skrebe naran relijiaun..." 
                                   value="<?= old('naran_relijiaun') ?>" 
                                   required>
                            <?php if (session('error.naran_relijiaun')) : ?>
                                <span class="error invalid-feedback"><?= session('error.naran_relijiaun') ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Rai Dadus</button>
                    <a href="<?= route_to('admin/relijiaun') ?>" class="btn btn-default float-right">Kansela</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
