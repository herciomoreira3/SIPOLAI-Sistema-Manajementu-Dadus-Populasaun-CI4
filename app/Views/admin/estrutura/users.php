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
                <h3 class="card-title font-weight-bold text-secondary mb-0">
                    <i class="fas fa-users-cog mr-2 text-primary"></i> Jestaun Membru ba User
                </h3>
                <a href="<?= base_url('admin/estrutura') ?>" class="btn btn-outline-secondary btn-rounded">
                    <i class="fas fa-arrow-left mr-1"></i> Fila
                </a>
            </div>
            <div class="card-body px-4 pb-4">
                <p class="text-muted mb-4">
                    Kria no maneja konta user ba membru estrutura ne'ebé okupa kargu <strong>Xefe Suku</strong>, <strong>Secretaria Suku</strong>, no <strong>Xefe Aldeia</strong> atu sira bele asesu no login ba sistema.
                </p>

                <?php if (session()->getFlashdata('message')) : ?>
                    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                        <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('message') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (session('errors')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <i class="fas fa-exclamation-circle mr-1"></i> Ita-boot sira iha erro balu:
                        <ul class="mb-0 mt-1 pl-4">
                            <?php foreach (session('errors') as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover" id="table-users-membru">
                        <thead>
                            <tr>
                                <th>Naran Kompletu</th>
                                <th>Kargu</th>
                                <th>Aldeia</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Status Konta</th>
                                <th class="text-center">Aksaun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $mb) : 
                                $hasUser = isset($usersByEstrutura[$mb['id_estrutura']]) || isset($usersByEstrutura['pop_' . $mb['id_populasaun']]);
                                $user = null;
                                if (isset($usersByEstrutura[$mb['id_estrutura']])) {
                                    $user = $usersByEstrutura[$mb['id_estrutura']];
                                } elseif (isset($usersByEstrutura['pop_' . $mb['id_populasaun']])) {
                                    $user = $usersByEstrutura['pop_' . $mb['id_populasaun']];
                                }
                            ?>
                                <tr>
                                    <td class="font-weight-bold text-secondary"><?= esc($mb['naran_membru']) ?></td>
                                    <td>
                                        <span class="badge badge-light p-2 font-weight-bold text-dark border">
                                            <?= esc($mb['kargu']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-info font-weight-bold">
                                            <?= esc($mb['naran_aldeia'] ?? 'Suku Level') ?>
                                        </span>
                                    </td>
                                    <td><?= $hasUser ? esc($user['username']) : '<span class="text-muted italic">-</span>' ?></td>
                                    <td><?= $hasUser ? esc($user['email']) : '<span class="text-muted italic">-</span>' ?></td>
                                    <td>
                                        <?php if ($hasUser) : ?>
                                            <span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle mr-1"></i> Konta Kria Ona</span>
                                        <?php else : ?>
                                            <span class="badge badge-warning px-3 py-2 text-dark font-weight-bold"><i class="fas fa-exclamation-triangle mr-1"></i> Seidauk iha Konta</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($hasUser) : ?>
                                            <form action="<?= base_url('admin/estrutura/users/delete/' . $user['id']) ?>" method="POST" class="d-inline delete-user-form">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger btn-rounded">
                                                    <i class="fas fa-trash-alt mr-1"></i> Hasai Konta
                                                </button>
                                            </form>
                                        <?php else : ?>
                                            <button type="button" class="btn btn-sm btn-primary btn-rounded btn-create-account"
                                                    data-id="<?= $mb['id_estrutura'] ?>"
                                                    data-name="<?= esc($mb['naran_membru']) ?>"
                                                    data-email="">
                                                <i class="fas fa-user-plus mr-1"></i> Kria Konta
                                            </button>
                                        <?php endif; ?>
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

<!-- Modal Create Account -->
<div class="modal fade" id="createAccountModal" tabindex="-1" role="dialog" aria-labelledby="createAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content card-premium">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-secondary" id="createAccountModalLabel">
                    <i class="fas fa-user-shield text-primary mr-2"></i> Konfigura Konta User
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/estrutura/users/create') ?>" method="POST" class="px-4 pb-4 pt-2">
                <?= csrf_field() ?>
                <input type="hidden" name="id_estrutura" id="modal-id-estrutura">

                <div class="form-group mt-3">
                    <label class="font-weight-bold text-muted">Naran Kompletu</label>
                    <input type="text" id="modal-naran-membru" class="form-control" readonly style="border-radius: 8px;">
                </div>

                <div class="form-group">
                    <label for="modal-username" class="font-weight-bold text-muted">Username</label>
                    <input type="text" name="username" id="modal-username" class="form-control" required style="border-radius: 8px;">
                    <small class="text-muted">Username de'it ne'ebé úniku no la uza espasu.</small>
                </div>

                <div class="form-group">
                    <label for="modal-email" class="font-weight-bold text-muted">Email</label>
                    <input type="email" name="email" id="modal-email" class="form-control" required style="border-radius: 8px;">
                </div>

                <div class="form-group">
                    <label for="modal-password" class="font-weight-bold text-muted">Password Foun</label>
                    <input type="password" name="password" id="modal-password" class="form-control" required style="border-radius: 8px;" placeholder="Minimu karakteru 8">
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-outline-secondary btn-rounded mr-2" data-dismiss="modal">Kansela</button>
                    <button type="submit" class="btn btn-primary btn-rounded">
                        <i class="fas fa-check mr-1"></i> Kria Konta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        $('#table-users-membru').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
            }
        });

        // Trigger Create Account Modal
        $('.btn-create-account').on('click', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var email = $(this).data('email');

            // Generate clean, slug-like username
            var cleanUsername = name.toLowerCase()
                                    .replace(/[^a-z0-9]/g, '')
                                    .substring(0, 15);

            // If email is empty, generate structured one
            var cleanEmail = email ? email : (cleanUsername + '@sipolai.com');

            $('#modal-id-estrutura').val(id);
            $('#modal-naran-membru').val(name);
            $('#modal-username').val(cleanUsername);
            $('#modal-email').val(cleanEmail);
            $('#modal-password').val('sipolai123'); // Default suggested password

            $('#createAccountModal').modal('show');
        });

        // SweetAlert Delete Confirmation
        $(document).on('submit', '.delete-user-form', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Ita boot sira certeza?',
                text: "Konta user membru ne'e sei hasai no sira labele login tan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff5858',
                cancelButtonColor: '#cbd5e1',
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
