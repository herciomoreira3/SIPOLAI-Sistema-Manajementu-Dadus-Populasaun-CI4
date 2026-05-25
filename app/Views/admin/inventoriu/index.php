<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<style>
    .card-premium-inventoriu {
        border-radius: 16px;
        border: 1px solid #e4e4e7;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.02), 0 2px 8px -1px rgba(0, 0, 0, 0.01) !important;
        background: #fff;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
    }
    .card-premium-inventoriu:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px -2px rgba(0, 0, 0, 0.05), 0 8px 16px -1px rgba(0, 0, 0, 0.03) !important;
    }
    .icon-wrapper-inventoriu {
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
    .btn-rounded-premium {
        border-radius: 10px;
        font-weight: 600;
        letter-spacing: 0.2px;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }
</style>

<?php
$cardConfig = [
    'Deklarasaun Eleitoral' => [
        'icon'  => 'fas fa-vote-yea',
        'style' => 'background: #f0fdf4; color: #16a34a; border-color: #bbf7d0;',
        'desc'  => 'Dadus inventoriu no relatoriu deklarasaun eleitoral ba sidadaun tinan 17 ba leten.'
    ],
    'Deklarasaun Nascimentu' => [
        'icon'  => 'fas fa-baby',
        'style' => 'background: #fff5f5; color: #ff6b6b; border-color: #ffd8d8;',
        'desc'  => 'Dadus rejistu inventoriu ba labarik sira ne\'ebé foin moris iha suku Laisorolai.'
    ],
    'Deklarasaun Mortalidade' => [
        'icon'  => 'fas fa-book-dead',
        'style' => 'background: #fef2f2; color: #ef4444; border-color: #fee2e2;',
        'desc'  => 'Dadus inventoriu ba deklarasaun mate/obituario populasaun suku nian.'
    ],
    'Deklarasaun Bom Comportamento' => [
        'icon'  => 'fas fa-user-shield',
        'style' => 'background: #eff6ff; color: #3b82f6; border-color: #bfdbfe;',
        'desc'  => 'Dadus inventoriu ba deklarasaun hahalok di\'ak/bom comportamento sidadaun nian.'
    ],
    'Deklarasaun Kbiit Laek' => [
        'icon'  => 'fas fa-hands-helping',
        'style' => 'background: #faf5ff; color: #7c3aed; border-color: #e9d5ff;',
        'desc'  => 'Dadus inventoriu ba deklarasaun kbiit-laek/apoio social ba sidadaun vulneravel.'
    ],
    'Deklarasaun Muda Domisiliu' => [
        'icon'  => 'fas fa-map-marker-alt',
        'style' => 'background: #fffbeb; color: #d97706; border-color: #fde68a;',
        'desc'  => 'Dadus inventoriu ba deklarasaun muda fatin hela/muda domisiliu sidadaun nian.'
    ],
    'Deklarasaun Eleitoral Lakon' => [
        'icon'  => 'fas fa-id-card-alt',
        'style' => 'background: #fdf2f8; color: #db2777; border-color: #fbcfe8;',
        'desc'  => 'Dadus inventoriu ba deklarasaun kartaun eleitoral lakon ka estraga.'
    ],
];
?>

<div class="row pt-2">
    <?php foreach ($tipus as $t) : 
        $name = $t['naran_tipu_pedidu'];
        $cfg = $cardConfig[$name] ?? [
            'icon'  => 'fas fa-file-alt',
            'style' => 'background: #f4f4f5; color: #18181b; border-color: #e4e4e7;',
            'desc'  => 'Dadus inventoriu no relatoriu ba tipu deklarasaun ' . esc($name) . '.'
        ];
    ?>
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card card-premium-inventoriu h-100 p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="icon-wrapper-inventoriu" style="<?= $cfg['style'] ?>">
                    <i class="<?= $cfg['icon'] ?>"></i>
                </div>
                <h5 class="font-weight-bold text-secondary mb-2"><?= esc($name) ?></h5>
                <p class="text-muted small mb-0"><?= esc($cfg['desc']) ?></p>
            </div>
            <div class="mt-4">
                <a href="<?= route_to('admin/pedidu') ?>?naran_pedidu=<?= urlencode($name) ?>" class="btn btn-outline-primary btn-block btn-rounded-premium">
                    Haree Inventoriu <i class="fas fa-arrow-right ml-1 small"></i>
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
