<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<style>
    .card-premium-relatoriu {
        border-radius: 16px;
        border: 1px solid #e4e4e7;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.02), 0 2px 8px -1px rgba(0, 0, 0, 0.01) !important;
        background: #fff;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
    }
    .card-premium-relatoriu:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px -2px rgba(0, 0, 0, 0.05), 0 8px 16px -1px rgba(0, 0, 0, 0.03) !important;
    }
    .icon-wrapper-relatoriu {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 20px;
        transition: all 0.25s ease;
        background: #f4f4f5;
        color: #18181b;
        border: 1px solid #e4e4e7;
    }
    
    /* Elegant soft background styling on hover */
    .card-premium-relatoriu:hover .icon-blue { background: #eff6ff; color: #3b82f6; border-color: #bfdbfe; }
    .card-premium-relatoriu:hover .icon-purple { background: #f5f3ff; color: #8b5cf6; border-color: #ddd6fe; }
    .card-premium-relatoriu:hover .icon-pink { background: #fdf2f8; color: #ec4899; border-color: #fbcfe8; }
    .card-premium-relatoriu:hover .icon-red { background: #fef2f2; color: #ef4444; border-color: #fee2e2; }

    .btn-rounded-premium {
        border-radius: 10px;
        font-weight: 600;
        letter-spacing: 0.2px;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }
    @media print { .no-print { display: none !important; } }
</style>

<div class="row pt-2">
    <!-- Card 1: Populasaun Suku -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card card-premium-relatoriu h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="icon-wrapper-relatoriu icon-blue">
                    <i class="fas fa-users"></i>
                </div>
                <h5 class="font-weight-bold text-secondary mb-2">Populasaun Suku</h5>
                <p class="text-muted small mb-0">Dadus kompletu no filtru populasaun bazeia ba Aldeia, Profisaun, no Literatura.</p>
            </div>
            <div class="mt-4">
                <a href="<?= route_to('relatoriu-populasaun') ?>" class="btn btn-outline-primary btn-block btn-rounded-premium">
                    Haree Relatoriu <i class="fas fa-arrow-right ml-1 small"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 2: Fixa Familia -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card card-premium-relatoriu h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="icon-wrapper-relatoriu icon-purple">
                    <i class="fas fa-id-card"></i>
                </div>
                <h5 class="font-weight-bold text-secondary mb-2">Fixa Familia</h5>
                <p class="text-muted small mb-0">Relatoriu no impressaun Fixa Familia Suku Laisorolai de Baixo.</p>
            </div>
            <div class="mt-4">
                <a href="<?= route_to('relatoriu-familia') ?>" class="btn btn-outline-primary btn-block btn-rounded-premium">
                    Haree Relatoriu <i class="fas fa-arrow-right ml-1 small"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 3: Nascimentu -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card card-premium-relatoriu h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="icon-wrapper-relatoriu icon-pink" style="background: #fff5f5; color: #ff6b6b; border-color: #ffd8d8;">
                     <i class="fas fa-baby"></i>
                </div>
                <h5 class="font-weight-bold text-secondary mb-2">Nascimentu</h5>
                <p class="text-muted small mb-0">Dadus rejistu no estatistika bebes/nascimentu foun iha Suku Laisorolai de Baixo.</p>
            </div>
            <div class="mt-4">
                <a href="<?= route_to('relatoriu-nascimentu') ?>" class="btn btn-outline-primary btn-block btn-rounded-premium">
                    Haree Relatoriu <i class="fas fa-arrow-right ml-1 small"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 4: Mortalidade -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card card-premium-relatoriu h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="icon-wrapper-relatoriu icon-red">
                     <i class="fas fa-book-dead"></i>
                </div>
                <h5 class="font-weight-bold text-secondary mb-2">Mortalidade</h5>
                <p class="text-muted small mb-0">Dadus relatoriu no impressaun populasaun ne'ebé mate ona iha Suku Laisorolai de Baixo.</p>
            </div>
            <div class="mt-4">
                <a href="<?= route_to('relatoriu-mortalidade') ?>" class="btn btn-outline-primary btn-block btn-rounded-premium">
                    Haree Relatoriu <i class="fas fa-arrow-right ml-1 small"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 5: Muda Domisiliu -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card card-premium-relatoriu h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="icon-wrapper-relatoriu icon-pink" style="background: #fffbeb; color: #d97706; border-color: #fde68a;">
                     <i class="fas fa-map-marker-alt"></i>
                </div>
                <h5 class="font-weight-bold text-secondary mb-2">Muda Domisiliu</h5>
                <p class="text-muted small mb-0">Estatistika no lista populasaun sira ne'ebé muda domisiliu sai husi Suku Laisorolai.</p>
            </div>
            <div class="mt-4">
                <a href="<?= route_to('relatoriu-muda') ?>" class="btn btn-outline-primary btn-block btn-rounded-premium">
                    Haree Relatoriu <i class="fas fa-arrow-right ml-1 small"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 6: Eleitores -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card card-premium-relatoriu h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="icon-wrapper-relatoriu icon-blue" style="background: #f0fdf4; color: #16a34a; border-color: #bbf7d0;">
                     <i class="fas fa-vote-yea"></i>
                </div>
                <h5 class="font-weight-bold text-secondary mb-2">Eleitores Active</h5>
                <p class="text-muted small mb-0">Estatutu no lista dadus eleitores (voters) ne'ebé ativu iha Suku Laisorolai de Baixo.</p>
            </div>
            <div class="mt-4">
                <a href="<?= route_to('relatoriu-eleitores') ?>" class="btn btn-outline-primary btn-block btn-rounded-premium">
                    Haree Relatoriu <i class="fas fa-arrow-right ml-1 small"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 7: Kbiit Laek -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card card-premium-relatoriu h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="icon-wrapper-relatoriu icon-purple" style="background: #faf5ff; color: #7c3aed; border-color: #e9d5ff;">
                     <i class="fas fa-hands-helping"></i>
                </div>
                <h5 class="font-weight-bold text-secondary mb-2">Kbiit Laek Active</h5>
                <p class="text-muted small mb-0">Relatoriu no lista benefisiariu apoiu sosial (kbiit laek) ativu iha Suku Laisorolai.</p>
            </div>
            <div class="mt-4">
                <a href="<?= route_to('relatoriu-kbiit-laek') ?>" class="btn btn-outline-primary btn-block btn-rounded-premium">
                    Haree Relatoriu <i class="fas fa-arrow-right ml-1 small"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 8: Pedidu / Dokumentu -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card card-premium-relatoriu h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="icon-wrapper-relatoriu icon-red" style="background: #fdf2f8; color: #db2777; border-color: #fbcfe8;">
                     <i class="fas fa-file-invoice"></i>
                </div>
                <h5 class="font-weight-bold text-secondary mb-2">Pedidu Dokumentu</h5>
                <p class="text-muted small mb-0">Relatoriu jestaun pedidu dokumentu no deklarasaun hotu-hotu iha Suku Laisorolai.</p>
            </div>
            <div class="mt-4">
                <a href="<?= route_to('relatoriu-pedidu') ?>" class="btn btn-outline-primary btn-block btn-rounded-premium">
                    Haree Relatoriu <i class="fas fa-arrow-right ml-1 small"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
