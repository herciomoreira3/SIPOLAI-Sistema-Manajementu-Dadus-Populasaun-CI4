<?= $this->include('Boilerplate\Views\load\datatables') ?>
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
    <div class="col-12">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-secondary mb-0"><i class="fas fa-paper-plane mr-2"></i> Jestaun Karta Sai</h3>
                <a href="<?= base_url('admin/karta-sai/new') ?>" class="btn btn-primary btn-rounded">
                    <i class="fas fa-plus mr-1"></i> Karta Sai Foun
                </a>
            </div>
            <div class="card-body px-4 pb-4">
                <?php if (session()->getFlashdata('message')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('message') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover" id="table-karta-sai">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Numeru Karta</th>
                                <th>Destinatariu (Ba)</th>
                                <th>Asuntu (Deskrisaun)</th>
                                <th>Data Sai</th>
                                <th class="text-center">Aksaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($kartas as $k) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="font-weight-bold text-primary"><?= esc($k['numeru_karta']) ?></td>
                                    <td><span class="badge badge-light p-2 font-weight-bold"><?= esc($k['destinatariu']) ?></span></td>
                                    <td class="text-secondary"><?= esc($k['asuntu']) ?></td>
                                    <td><?= date('d-m-Y', strtotime($k['data_sai'])) ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/karta-sai/' . $k['id_karta_sai'] . '/edit') ?>" class="btn btn-sm btn-info rounded-circle mr-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?= base_url('admin/karta-sai/' . $k['id_karta_sai']) ?>" method="POST" class="d-inline delete-form">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-danger rounded-circle" title="Hasai">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        $('#table-karta-sai').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
            }
        });

        $(document).on('submit', '.delete-form', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Ita boot sira certeza?',
                text: "Dadus ne'e sei hasai permanentemente!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff5858',
                cancelButtonColor: '#a18cd1',
                confirmButtonText: 'Sim, Hasai!',
                cancelButtonText: 'Kansela'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
