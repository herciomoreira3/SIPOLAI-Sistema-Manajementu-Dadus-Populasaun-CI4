<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <form action="<?= route_to('admin/aldeia') ?>/<?= $aldeia['id_aldeia'] ?>" method="post" class="form-horizontal">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT" />
                <div class="card-body">
                    <div class="form-group row">
                        <label for="naran_aldeia" class="col-sm-2 col-form-label">Naran Aldeia</label>
                        <div class="col-sm-10">
                            <input type="text" 
                                   name="naran_aldeia" 
                                   class="form-control <?= session('error.naran_aldeia') ? 'is-invalid' : '' ?>" 
                                   id="naran_aldeia" 
                                   placeholder="Prenxe naran aldeia..." 
                                   value="<?= old('naran_aldeia', $aldeia['naran_aldeia']) ?>" 
                                   required>
                            <?php if (session('error.naran_aldeia')) : ?>
                                <span class="error invalid-feedback"><?= session('error.naran_aldeia') ?></span>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Hadia Dadus</button>
                    <a href="<?= route_to('admin/aldeia') ?>" class="btn btn-default float-right">Kansela</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
