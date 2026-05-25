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
                <h3 class="card-title font-weight-bold text-secondary mb-0"><i class="fas fa-sitemap mr-2"></i> Struktura Suku Laisorolai de Baixo</h3>
                <div>
                    <a href="<?= base_url('admin/hirarkia') ?>" class="btn btn-outline-info btn-rounded mr-2">
                        <i class="fas fa-project-diagram mr-1"></i> Hirarkia
                    </a>
                    <a href="<?= base_url('admin/kargu') ?>" class="btn btn-secondary btn-rounded mr-2 text-white">
                        <i class="fas fa-briefcase mr-1"></i> Kargu
                    </a>
                    <a href="<?= base_url('admin/promosaun') ?>" class="btn btn-warning btn-rounded mr-2 text-white">
                        <i class="fas fa-level-up-alt mr-1"></i> Promosaun
                    </a>
                    <a href="<?= base_url('admin/estrutura/users') ?>" class="btn btn-primary btn-rounded">
                        <i class="fas fa-users-cog mr-1"></i> User
                    </a>
                </div>
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

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Aldeia Filter Dropdown -->
                <div class="row mb-4 align-items-center">
                    <div class="col-md-4">
                        <label for="filter-aldeia" class="font-weight-bold text-muted mb-1"><i class="fas fa-filter mr-1"></i> Filtra tuir Aldeia</label>
                        <select id="filter-aldeia" class="form-control select2" style="border-radius: 8px;">
                            <option value="">-- Hatudu hotu (All Aldeias) --</option>
                            <?php foreach ($aldeias as $ald) : ?>
                                <option value="<?= esc($ald['naran_aldeia']) ?>"><?= esc($ald['naran_aldeia']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="table-estrutura">
                        <thead>
                            <tr>
                                <th>Naran Kompletu</th>
                                <th>NIP</th>
                                <th>Kargu</th>
                                <th>Aldeia</th>
                                <th>Periodo Hahula</th>
                                <th>Periodo Remata</th>
                                <th>Status</th>
                                <th class="text-center">Aksaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estrutura as $membru) : ?>
                                <tr>
                                    <td class="font-weight-bold text-secondary"><?= esc($membru['naran_membru']) ?></td>
                                    <td><?= esc($membru['nik'] ?? '-') ?></td>
                                    <td><span class="badge badge-light p-2 font-weight-bold"><?= esc($membru['kargu']) ?></span></td>
                                    <td><span class="text-info font-weight-bold"><?= esc($membru['naran_aldeia'] ?? '-') ?></span></td>
                                    <td><?= date('d-m-Y', strtotime($membru['periodo_hahula'])) ?></td>
                                    <td><?= $membru['periodo_remata'] ? date('d-m-Y', strtotime($membru['periodo_remata'])) : '-' ?></td>
                                    <td>
                                        <?php if ($membru['status_kargu'] == 'Ativu') : ?>
                                            <span class="badge badge-success px-3 py-1">Ativu</span>
                                        <?php else : ?>
                                            <span class="badge badge-secondary px-3 py-1">Inativu</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/estrutura/' . $membru['id_estrutura'] . '/edit') ?>" class="btn btn-sm btn-info rounded-circle mr-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?= base_url('admin/estrutura/' . $membru['id_estrutura'] . '/delete') ?>" method="POST" class="d-inline delete-form">
                                            <?= csrf_field() ?>
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
        var table = $('#table-estrutura').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
            }
        });

        // Filter by Aldeia column (index 3)
        $('#filter-aldeia').on('change', function() {
            var selected = $(this).val();
            // exact regex search on Aldeia column
            table.column(3).search(selected ? '^' + $.fn.dataTable.util.escapeRegex(selected) + '$' : '', true, false).draw();
        });

        // SweetAlert Delete Confirmation
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
