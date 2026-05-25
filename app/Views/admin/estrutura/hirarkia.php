<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<style>
    .card-premium {
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        background: #f8fafc;
    }
    
    .btn-rounded {
        border-radius: 30px;
        padding: 6px 22px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    /* Premium Org Chart Styling */
    .org-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        overflow: hidden;
        max-width: 100%;
        padding: 20px 0;
    }

    /* Nodes layout & sizing */
    .org-row {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        position: relative;
        width: 100%;
        margin: 10px 0;
    }

    .org-level-2-wrapper, .org-level-3-wrapper {
        display: flex;
        justify-content: center;
        width: 100%;
        flex-wrap: nowrap;
        gap: 10px;
    }

    /* Connecting Lines */
    .org-line-v {
        width: 3px;
        height: 35px;
        background: #cbd5e1;
        position: relative;
    }

    .org-line-h-wrapper {
        position: relative;
        width: 100%;
        height: 2px;
        margin: 5px 0;
    }

    .org-line-h {
        position: absolute;
        top: 0;
        height: 3px;
        background: #cbd5e1;
    }

    /* Premium Card Design */
    .membru-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(148, 163, 184, 0.12);
        border: 1px solid rgba(226, 232, 240, 0.9);
        padding: 10px;
        width: 145px;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 0 6px;
        z-index: 2;
    }

    .membru-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 36px rgba(59, 130, 246, 0.15);
        border-color: #3b82f6;
    }

    /* Tier Colors */
    .card-tier-1 {
        background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);
        border: 2px solid #3b82f6;
        width: 175px;
    }
    
    .card-tier-1 .kargu-badge {
        background: linear-gradient(135deg, #1d4ed8, #3b82f6);
        font-size: 11px;
        padding: 3px 10px;
    }

    .card-tier-2 {
        border-top: 4px solid #10b981;
    }
    .card-tier-2 .kargu-badge {
        background: #10b981;
    }

    .card-tier-3 {
        border-top: 4px solid #f97316;
    }
    .card-tier-3 .kargu-badge {
        background: #f97316;
    }

    .card-tier-4 {
        border-top: 4px solid #8b5cf6;
        width: 110px;
        padding: 6px;
        margin: 4px 2px;
        box-shadow: 0 4px 12px rgba(148, 163, 184, 0.08);
    }
    .card-tier-4 .kargu-badge {
        background: #8b5cf6;
        font-size: 9px;
        padding: 2px 6px;
    }
    .card-tier-4 .avatar-wrapper {
        width: 44px;
        height: 44px;
    }
    .card-tier-4 .membru-name {
        font-size: 11px;
    }

    /* Vacant / Dotted Card */
    .card-vacant {
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        box-shadow: none;
    }
    .card-vacant .kargu-badge {
        background: #94a3b8;
    }
    .card-vacant:hover {
        border-color: #94a3b8;
        transform: none;
        box-shadow: none;
    }

    /* Avatar and image wrapper */
    .avatar-wrapper {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        overflow: hidden;
        margin-bottom: 12px;
        border: 2.5px solid #e2e8f0;
        background: #f1f5f9;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #ffffff;
    }
    .avatar-placeholder.avatar-mane {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }
    .avatar-placeholder.avatar-feto {
        background: linear-gradient(135deg, #ec4899, #be185d);
    }
    .avatar-placeholder.avatar-empty {
        background: linear-gradient(135deg, #cbd5e1, #94a3b8);
    }

    .kargu-badge {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #ffffff;
        padding: 3px 10px;
        border-radius: 20px;
        margin-bottom: 8px;
        display: inline-block;
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .membru-name {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .membru-period {
        font-size: 10px;
        color: #64748b;
        font-weight: 500;
    }

    /* Sub-branch for Aldeia Delegates */
    .aldeia-branch-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 0 15px;
    }

    .delegates-row {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        max-width: 320px;
        margin-top: 10px;
        border-top: 2px dotted #cbd5e1;
        padding-top: 10px;
    }
</style>

<!-- Parse and group the tiered positions dynamically -->
<div class="row">
    <div class="col-12">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-secondary mb-0"><i class="fas fa-sitemap mr-2"></i> Organograma Konselu Suku no Ofisial Apoiu</h3>
                <?php if (in_groups('admin')) : ?>
                    <a href="<?= base_url('admin/estrutura') ?>" class="btn btn-outline-secondary btn-rounded shadow-sm">
                        <i class="fas fa-list mr-1"></i> Fila ba Lista
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body px-2 pb-5">
                <div class="org-container">

                    <!-- ================== LEVEL 1: XEFE SUKU ================== -->
                    <div class="org-row">
                        <?php if ($xefeSuku) : ?>
                            <div class="membru-card card-tier-1">
                                <div class="avatar-wrapper" style="border-color: #3b82f6;">
                                    <?php if (!empty($xefeSuku['foto']) && file_exists(FCPATH . 'uploads/familia/' . $xefeSuku['foto'])) : ?>
                                        <img src="<?= base_url('uploads/familia/' . $xefeSuku['foto']) ?>" alt="Foto">
                                    <?php else : ?>
                                        <div class="avatar-placeholder <?= ($xefeSuku['jeneru'] ?? 'Mane') == 'Feto' ? 'avatar-feto' : 'avatar-mane' ?>">
                                            <i class="fas <?= ($xefeSuku['jeneru'] ?? 'Mane') == 'Feto' ? 'fa-female' : 'fa-user-tie' ?>"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <span class="kargu-badge">Xefe Suku</span>
                                <div class="membru-name" title="<?= esc($xefeSuku['naran_membru']) ?>"><?= esc($xefeSuku['naran_membru']) ?></div>
                                <div class="text-primary font-weight-bold" style="font-size: 11px; margin-top: 2px;">Suku Laisorolai de Baixo</div>
                                <div class="membru-period"><?= date('Y', strtotime($xefeSuku['periodo_hahula'])) ?> – <?= !empty($xefeSuku['periodo_remata']) ? date('Y', strtotime($xefeSuku['periodo_remata'])) : '2030' ?></div>
                            </div>

                        <?php else : ?>
                            <div class="membru-card card-tier-1 card-vacant">
                                <div class="avatar-wrapper">
                                    <div class="avatar-placeholder avatar-empty">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                </div>
                                <span class="kargu-badge">Xefe Suku</span>
                                <div class="membru-name text-muted">Seidauk Rejista</div>
                                <div class="membru-period">Vacant</div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Connect Level 1 to Level 2 -->
                    <div class="org-line-v"></div>

                    <!-- ================== LEVEL 2: OFISIAL APOIU SUKU (DYNAMIC CENTRAL OFFICERS) ================== -->
                    <div class="org-line-h-wrapper">
                        <div class="org-line-h" style="left: 15%; right: 15%;"></div>
                    </div>

                    <div class="org-row">
                        <div class="org-level-2-wrapper" style="gap: 40px;">
                            <!-- Other Central Officers (e.g. Adjuntu, Lia Nain, Juventude) -->
                            <?php foreach ($otherCentral as $membru) : ?>
                                <div class="d-flex flex-column align-items-center">
                                    <div class="org-line-v" style="height: 15px;"></div>
                                    <div class="membru-card card-tier-2" style="border-top-color: #10b981;">
                                        <div class="avatar-wrapper" style="border-color: #10b981;">
                                            <?php if (!empty($membru['foto']) && file_exists(FCPATH . 'uploads/familia/' . $membru['foto'])) : ?>
                                                <img src="<?= base_url('uploads/familia/' . $membru['foto']) ?>" alt="Foto">
                                            <?php else : ?>
                                                <div class="avatar-placeholder <?= ($membru['jeneru'] ?? 'Mane') == 'Feto' ? 'avatar-feto' : 'avatar-mane' ?>">
                                                    <i class="fas <?= ($membru['jeneru'] ?? 'Mane') == 'Feto' ? 'fa-female' : 'fa-user' ?>"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <span class="kargu-badge" style="background-color: #10b981;" title="<?= esc($membru['kargu']) ?>"><?= esc($membru['kargu']) ?></span>
                                        <div class="membru-name" title="<?= esc($membru['naran_membru']) ?>"><?= esc($membru['naran_membru']) ?></div>
                                        <div class="text-primary font-weight-bold" style="font-size: 10px; margin-top: 1px;">Suku Laisorolai de B.</div>
                                        <div class="membru-period"><?= date('Y', strtotime($membru['periodo_hahula'])) ?> – <?= !empty($membru['periodo_remata']) ? date('Y', strtotime($membru['periodo_remata'])) : '2030' ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- Secretaria Suku & Subordinates Column -->
                            <div class="d-flex flex-column align-items-center">
                                <div class="org-line-v" style="height: 15px;"></div>
                                <?php if ($secretariaSuku) : ?>
                                    <div class="membru-card card-tier-2" style="border-top-color: #3b82f6; width: 145px;">
                                        <div class="avatar-wrapper" style="border-color: #3b82f6;">
                                            <?php if (!empty($secretariaSuku['foto']) && file_exists(FCPATH . 'uploads/familia/' . $secretariaSuku['foto'])) : ?>
                                                <img src="<?= base_url('uploads/familia/' . $secretariaSuku['foto']) ?>" alt="Foto">
                                            <?php else : ?>
                                                <div class="avatar-placeholder <?= ($secretariaSuku['jeneru'] ?? 'Mane') == 'Feto' ? 'avatar-feto' : 'avatar-mane' ?>">
                                                    <i class="fas <?= ($secretariaSuku['jeneru'] ?? 'Mane') == 'Feto' ? 'fa-female' : 'fa-user-cog' ?>"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <span class="kargu-badge" style="background-color: #3b82f6;" title="<?= esc($secretariaSuku['kargu']) ?>"><?= esc($secretariaSuku['kargu']) ?></span>
                                        <div class="membru-name" title="<?= esc($secretariaSuku['naran_membru']) ?>"><?= esc($secretariaSuku['naran_membru']) ?></div>
                                        <div class="text-primary font-weight-bold" style="font-size: 10px; margin-top: 1px;">Suku Laisorolai de B.</div>
                                        <div class="membru-period"><?= date('Y', strtotime($secretariaSuku['periodo_hahula'])) ?> – <?= !empty($secretariaSuku['periodo_remata']) ? date('Y', strtotime($secretariaSuku['periodo_remata'])) : '2030' ?></div>
                                    </div>

                                <?php else : ?>
                                    <div class="membru-card card-tier-2 card-vacant" style="width: 145px;">
                                        <div class="avatar-wrapper">
                                            <div class="avatar-placeholder avatar-empty">
                                                <i class="fas fa-user-cog"></i>
                                            </div>
                                        </div>
                                        <span class="kargu-badge" style="background-color: #94a3b8;">Secretaria Suku</span>
                                        <div class="membru-name text-muted">Seidauk Rejista</div>
                                        <div class="membru-period">Vacant</div>
                                    </div>
                                <?php endif; ?>

                                <!-- Subordinates of Secretaria Suku (Admin, Finansa, A.Sosial) -->
                                <?php if (!empty($secretariaSubordinates)) : ?>
                                    <div class="org-line-v" style="height: 15px; background: #cbd5e1;"></div>
                                    <div class="org-line-h-wrapper" style="width: 75%; height: 2px;">
                                        <div class="org-line-h" style="left: 0; right: 0; background: #cbd5e1; height: 3px;"></div>
                                    </div>
                                    <div class="d-flex justify-content-center" style="margin-top: 10px; gap: 8px;">
                                        <?php foreach ($secretariaSubordinates as $sub) : ?>
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="org-line-v" style="height: 12px; background: #cbd5e1;"></div>
                                                <div class="membru-card card-tier-4" style="border-top: 3px solid #8b5cf6; width: 110px; padding: 6px;">
                                                    <div class="avatar-wrapper" style="width: 44px; height: 44px;">
                                                        <?php if (!empty($sub['foto']) && file_exists(FCPATH . 'uploads/familia/' . $sub['foto'])) : ?>
                                                            <img src="<?= base_url('uploads/familia/' . $sub['foto']) ?>" alt="Foto">
                                                        <?php else : ?>
                                                            <div class="avatar-placeholder <?= ($sub['jeneru'] ?? 'Mane') == 'Feto' ? 'avatar-feto' : 'avatar-mane' ?>" style="font-size: 16px;">
                                                                <i class="fas <?= ($sub['jeneru'] ?? 'Mane') == 'Feto' ? 'fa-female' : 'fa-user' ?>"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="kargu-badge" style="background-color: #8b5cf6; font-size: 8.5px; padding: 2px 6px;" title="<?= esc($sub['kargu']) ?>"><?= esc($sub['kargu']) ?></span>
                                                    <div class="membru-name" style="font-size: 11px;" title="<?= esc($sub['naran_membru']) ?>"><?= esc($sub['naran_membru']) ?></div>
                                                    <div class="text-primary font-weight-bold" style="font-size: 8px; margin-top: 1px;">Suku Laisorolai de B.</div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>


                    <!-- Connect Level 2 to Level 3 -->
                    <div class="org-line-v"></div>

                    <!-- ================== LEVEL 3 & 4: DYNAMIC ALDEIAS & THEIR DELEGATES ================== -->
                    <div class="org-line-h-wrapper">
                        <div class="org-line-h" style="left: 12.5%; right: 12.5%;"></div>
                    </div>

                    <div class="org-row">
                        <div class="org-level-3-wrapper">
                            <?php foreach ($aldeiaBranches as $branch) : ?>
                                <?php 
                                $ald = $branch['aldeia'];
                                $xefe = $branch['xefe'];
                                $membros = $branch['membros'];
                                ?>
                                <div class="aldeia-branch-wrapper">
                                    <div class="org-line-v" style="height: 15px;"></div>
                                    
                                    <?php if ($xefe) : ?>
                                        <div class="membru-card card-tier-3">
                                            <div class="avatar-wrapper" style="border-color: #f97316;">
                                                <?php if (!empty($xefe['foto']) && file_exists(FCPATH . 'uploads/familia/' . $xefe['foto'])) : ?>
                                                    <img src="<?= base_url('uploads/familia/' . $xefe['foto']) ?>" alt="Foto">
                                                <?php else : ?>
                                                    <div class="avatar-placeholder <?= ($xefe['jeneru'] ?? 'Mane') == 'Feto' ? 'avatar-feto' : 'avatar-mane' ?>">
                                                        <i class="fas <?= ($xefe['jeneru'] ?? 'Mane') == 'Feto' ? 'fa-female' : 'fa-user' ?>"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <span class="kargu-badge" title="<?= esc($xefe['kargu']) ?>"><?= esc($xefe['kargu']) ?></span>
                                            <div class="membru-name" title="<?= esc($xefe['naran_membru']) ?>"><?= esc($xefe['naran_membru']) ?></div>
                                            <div class="text-info font-weight-bold" style="font-size: 11px; margin-top: 1px;">Aldeia: <?= esc($ald['naran_aldeia']) ?></div>
                                            <div class="membru-period"><?= date('Y', strtotime($xefe['periodo_hahula'])) ?> – <?= !empty($xefe['periodo_remata']) ? date('Y', strtotime($xefe['periodo_remata'])) : '2030' ?></div>
                                        </div>
                                    <?php else : ?>
                                        <div class="membru-card card-tier-3 card-vacant">
                                            <div class="avatar-wrapper">
                                                <div class="avatar-placeholder avatar-empty">
                                                    <i class="fas fa-home"></i>
                                                </div>
                                            </div>
                                            <span class="kargu-badge" title="Xefe Aldeia <?= esc($ald['naran_aldeia']) ?>">Xefe Aldeia <?= esc($ald['naran_aldeia']) ?></span>
                                            <div class="membru-name text-muted">Seidauk Rejista</div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Delegates under this Aldeia -->
                                    <div class="delegates-row">
                                        <?php foreach ($membros as $del) : ?>
                                            <div class="membru-card card-tier-4">
                                                <div class="avatar-wrapper">
                                                    <?php if (!empty($del['foto']) && file_exists(FCPATH . 'uploads/familia/' . $del['foto'])) : ?>
                                                        <img src="<?= base_url('uploads/familia/' . $del['foto']) ?>" alt="Foto">
                                                    <?php else : ?>
                                                        <div class="avatar-placeholder <?= ($del['jeneru'] ?? 'Mane') == 'Feto' ? 'avatar-feto' : 'avatar-mane' ?>">
                                                            <i class="fas <?= ($del['jeneru'] ?? 'Mane') == 'Feto' ? 'fa-female' : 'fa-user' ?>"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="kargu-badge" title="<?= esc($del['kargu']) ?>"><?= esc($del['kargu']) ?></span>
                                                <div class="membru-name" title="<?= esc($del['naran_membru']) ?>"><?= esc($del['naran_membru']) ?></div>
                                                <div class="text-info font-weight-bold" style="font-size: 9px; margin-top: 1px;">Aldeia: <?= esc($ald['naran_aldeia']) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
