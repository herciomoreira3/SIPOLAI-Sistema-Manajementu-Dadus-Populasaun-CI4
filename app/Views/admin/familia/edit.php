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
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-edit mr-2"></i> Hadia Fixa Familia</h3>
            </div>
            <form action="<?= base_url('admin/familia/' . $familia['id_familia']) ?>" method="POST" class="px-4 pb-4 pt-2">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <div class="form-group">
                    <label for="numeru_kk" class="font-weight-bold text-muted">Numeru Fixa Familia</label>
                    <input type="text" name="numeru_kk" id="numeru_kk" class="form-control" value="<?= esc($familia['numeru_kk']) ?>" readonly style="background-color: #f8f9fa;">
                </div>

                <div class="form-group">
                    <label for="id_aldeia" class="font-weight-bold text-muted">Aldeia</label>
                    <select name="id_aldeia" id="id_aldeia" class="form-select <?= session('errors.id_aldeia') ? 'is-invalid' : '' ?>" required>
                        <option value="">-- Hili Aldeia --</option>
                        <?php foreach ($aldeias as $ald) : ?>
                            <option value="<?= $ald['id_aldeia'] ?>" <?= (old('id_aldeia') ?? $familia['id_aldeia']) == $ald['id_aldeia'] ? 'selected' : '' ?>>
                                <?= esc($ald['naran_aldeia']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (session('errors.id_aldeia')) : ?>
                        <div class="invalid-feedback"><?= session('errors.id_aldeia') ?></div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= base_url('admin/familia') ?>" class="btn btn-outline-secondary btn-rounded">Fila</a>
                    <button type="submit" class="btn btn-primary btn-rounded">Aktualiza</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
