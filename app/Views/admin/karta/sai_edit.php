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
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-edit mr-2"></i> Hadia Karta Sai</h3>
            </div>
            <form action="<?= base_url('admin/karta-sai/' . $karta['id_karta_sai']) ?>" method="POST" class="px-4 pb-4 pt-2">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <div class="form-group mb-3">
                    <label for="numeru_karta" class="font-weight-bold text-muted">Numeru Karta</label>
                    <input type="text" name="numeru_karta" id="numeru_karta" class="form-control <?= session('errors.numeru_karta') ? 'is-invalid' : '' ?>" value="<?= old('numeru_karta') ?? esc($karta['numeru_karta']) ?>" placeholder="Fila numeru karta..." required>
                    <?php if (session('errors.numeru_karta')) : ?>
                        <div class="invalid-feedback"><?= session('errors.numeru_karta') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="destinatariu" class="font-weight-bold text-muted">Destinatariu (Ba)</label>
                    <input type="text" name="destinatariu" id="destinatariu" class="form-control <?= session('errors.destinatariu') ? 'is-invalid' : '' ?>" value="<?= old('destinatariu') ?? esc($karta['destinatariu']) ?>" placeholder="Fila destinatariu/ba..." required>
                    <?php if (session('errors.destinatariu')) : ?>
                        <div class="invalid-feedback"><?= session('errors.destinatariu') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="asuntu" class="font-weight-bold text-muted">Asuntu (Deskrisaun)</label>
                    <input type="text" name="asuntu" id="asuntu" class="form-control <?= session('errors.asuntu') ? 'is-invalid' : '' ?>" value="<?= old('asuntu') ?? esc($karta['asuntu']) ?>" placeholder="Fila asuntu..." required>
                    <?php if (session('errors.asuntu')) : ?>
                        <div class="invalid-feedback"><?= session('errors.asuntu') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="data_sai" class="font-weight-bold text-muted">Data Sai</label>
                    <input type="date" name="data_sai" id="data_sai" class="form-control <?= session('errors.data_sai') ? 'is-invalid' : '' ?>" value="<?= old('data_sai') ?? esc($karta['data_sai']) ?>" required>
                    <?php if (session('errors.data_sai')) : ?>
                        <div class="invalid-feedback"><?= session('errors.data_sai') ?></div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= base_url('admin/karta-sai') ?>" class="btn btn-outline-secondary btn-rounded">Fila</a>
                    <button type="submit" class="btn btn-primary btn-rounded">Aktualiza</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
