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
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-plus mr-2"></i> Rejista Karta Tama Foun</h3>
            </div>
            <form action="<?= base_url('admin/karta-tama') ?>" method="POST" class="px-4 pb-4 pt-2">
                <?= csrf_field() ?>

                <div class="form-group mb-3">
                    <label for="numeru_karta" class="font-weight-bold text-muted">Numeru Karta</label>
                    <input type="text" name="numeru_karta" id="numeru_karta" class="form-control <?= session('errors.numeru_karta') ? 'is-invalid' : '' ?>" value="<?= old('numeru_karta') ?>" placeholder="Fila numeru karta..." required>
                    <?php if (session('errors.numeru_karta')) : ?>
                        <div class="invalid-feedback"><?= session('errors.numeru_karta') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="emitente" class="font-weight-bold text-muted">Emitente (Husi)</label>
                    <input type="text" name="emitente" id="emitente" class="form-control <?= session('errors.emitente') ? 'is-invalid' : '' ?>" value="<?= old('emitente') ?>" placeholder="Fila emitente/husi..." required>
                    <?php if (session('errors.emitente')) : ?>
                        <div class="invalid-feedback"><?= session('errors.emitente') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="asuntu" class="font-weight-bold text-muted">Asuntu (Deskrisaun)</label>
                    <input type="text" name="asuntu" id="asuntu" class="form-control <?= session('errors.asuntu') ? 'is-invalid' : '' ?>" value="<?= old('asuntu') ?>" placeholder="Fila asuntu..." required>
                    <?php if (session('errors.asuntu')) : ?>
                        <div class="invalid-feedback"><?= session('errors.asuntu') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="data_tama" class="font-weight-bold text-muted">Data Tama</label>
                    <input type="date" name="data_tama" id="data_tama" class="form-control <?= session('errors.data_tama') ? 'is-invalid' : '' ?>" value="<?= old('data_tama') ?? date('Y-m-d') ?>" required>
                    <?php if (session('errors.data_tama')) : ?>
                        <div class="invalid-feedback"><?= session('errors.data_tama') ?></div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= base_url('admin/karta-tama') ?>" class="btn btn-outline-secondary btn-rounded">Fila</a>
                    <button type="submit" class="btn btn-primary btn-rounded">Kria Karta Tama</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
