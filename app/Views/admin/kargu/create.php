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
    <div class="col-md-8 mx-auto">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary mb-0"><i class="fas fa-plus mr-2"></i> Aumenta Kargu / Posisaun</h3>
            </div>
            <form action="<?= base_url('admin/kargu') ?>" method="post" class="px-4 pb-4 pt-2">
                <?= csrf_field() ?>
                
                <div class="form-group mb-4">
                    <label for="naran_kargu" class="font-weight-bold text-muted mb-2">Naran Kargu / Posisaun</label>
                    <input type="text" 
                           name="naran_kargu" 
                           class="form-control <?= session('error.naran_kargu') ? 'is-invalid' : '' ?>" 
                           id="naran_kargu" 
                           placeholder="Ex: Adjuntu Xefe Aldeia..." 
                           value="<?= old('naran_kargu') ?>" 
                           required>
                    <?php if (session('error.naran_kargu')) : ?>
                        <span class="error invalid-feedback"><?= session('error.naran_kargu') ?></span>
                    <?php endif ?>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= base_url('admin/kargu') ?>" class="btn btn-outline-secondary btn-rounded">Fila</a>
                    <button type="submit" class="btn btn-primary btn-rounded shadow-sm">Mete Dadus</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
