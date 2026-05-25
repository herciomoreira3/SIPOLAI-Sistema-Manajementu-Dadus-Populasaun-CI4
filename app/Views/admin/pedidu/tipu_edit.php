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
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-edit mr-2"></i> Hadia Tipu Pedidu</h3>
            </div>
            <form action="<?= base_url('admin/tipu-pedidu/' . $tipu['id_tipu_pedidu']) ?>" method="POST" class="px-4 pb-4 pt-2">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <div class="form-group">
                    <label for="naran_tipu_pedidu" class="font-weight-bold text-muted">Naran Tipu Pedidu</label>
                    <input type="text" name="naran_tipu_pedidu" id="naran_tipu_pedidu" class="form-control <?= session('errors.naran_tipu_pedidu') ? 'is-invalid' : '' ?>" value="<?= old('naran_tipu_pedidu') ?? esc($tipu['naran_tipu_pedidu']) ?>" placeholder="Fila naran tipu pedidu..." required>
                    <?php if (session('errors.naran_tipu_pedidu')) : ?>
                        <div class="invalid-feedback"><?= session('errors.naran_tipu_pedidu') ?></div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= base_url('admin/tipu-pedidu') ?>" class="btn btn-outline-secondary btn-rounded">Fila</a>
                    <button type="submit" class="btn btn-primary btn-rounded">Aktualiza</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
