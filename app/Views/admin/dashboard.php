<?= $this->extend('Boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<style>
    .info-box {
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.02), 0 2px 8px -1px rgba(0, 0, 0, 0.01) !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        transition: all 0.2s ease;
    }
    .info-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px -2px rgba(0, 0, 0, 0.04), 0 4px 12px -1px rgba(0, 0, 0, 0.02) !important;
    }
    .info-box-icon {
        border-radius: 10px !important;
        margin: 10px !important;
        color: #ffffff !important;
        width: 50px !important;
        height: 50px !important;
        min-width: 50px !important;
        font-size: 20px !important;
    }
    .bg-populasaun-premium {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
    }
    .bg-familia-premium {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%) !important;
    }
    .bg-aldeia-premium {
        background: linear-gradient(135deg, #06b6d4 0%, #0e7490 100%) !important;
    }
    .bg-pedidu-premium {
        background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important;
    }
    .card-premium {
        border-radius: 16px;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.02), 0 2px 8px -1px rgba(0, 0, 0, 0.01) !important;
        background: #ffffff !important;
        margin-bottom: 24px;
    }
</style>

<?php if (!in_groups('xefe-aldeia')): ?>
    <!-- Filter Bar for Xefe Suku / Sekretaria / Admin -->
    <div class="row mb-3">
        <div class="col-12 col-md-4">
            <div class="card card-premium shadow-sm p-3 mb-0">
                <label for="dashboard-filter-aldeia" class="font-weight-bold text-secondary mb-2">
                    <i class="fas fa-filter text-primary mr-1"></i> Filtru bazeia ba Aldeia
                </label>
                <select id="dashboard-filter-aldeia" class="form-control select2 shadow-sm" style="border-radius: 8px;">
                    <option value="">-- Haree Aldeia Hotu --</option>
                    <?php foreach ($aldeias as $aldeia) : ?>
                        <option value="<?= $aldeia['id_aldeia'] ?>" <?= ($selectedAldeia == $aldeia['id_aldeia']) ? 'selected' : '' ?>>
                            <?= esc($aldeia['naran_aldeia']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Row 1: Core KPIs -->
<div class="row">
    <!-- Box 1: Total Populasaun -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-populasaun-premium elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted font-weight-bold">TOTAL POPULASAUN</span>
                <span class="info-box-number h3 text-secondary font-weight-bolder mb-0"><?= $totalPopulasaun ?> <small>Membru</small></span>
            </div>
        </div>
    </div>
    
    <!-- Box 2: Total Familia -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-familia-premium elevation-1"><i class="fas fa-home"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted font-weight-bold">TOTAL FAMILIA</span>
                <span class="info-box-number h3 text-secondary font-weight-bolder mb-0"><?= $totalFamilia ?> <small>KK</small></span>
            </div>
        </div>
    </div>

    <!-- Box 3: Total Aldeia -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-aldeia-premium elevation-1"><i class="fas fa-map-marked-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted font-weight-bold">TOTAL ALDEIA</span>
                <span class="info-box-number h3 text-secondary font-weight-bolder mb-0"><?= $totalAldeia ?> <small>Aldeia</small></span>
            </div>
        </div>
    </div>

    <!-- Box 4: Pedidu Pendiente -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-pedidu-premium elevation-1"><i class="fas fa-file-invoice"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted font-weight-bold">PEDIDU PENDIENTE</span>
                <span class="info-box-number h3 text-secondary font-weight-bolder mb-0"><?= $totalPedidu ?> <small>Pedidu</small></span>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Demographics A -->
<div class="row mt-3">
    <!-- Chart: Male vs Female -->
    <div class="col-md-4">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-venus-mars mr-2"></i> Estatistika Jeneru</h3>
            </div>
            <div class="card-body p-4">
                <div class="position-relative mb-4">
                    <canvas id="gender-chart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Age Group -->
    <div class="col-md-4">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-birthday-cake mr-2"></i> Kategoria Idade</h3>
            </div>
            <div class="card-body p-4">
                <div class="position-relative mb-4">
                    <canvas id="age-chart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Eleitoral -->
    <div class="col-md-4">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-id-card mr-2"></i> Status Eleitoral</h3>
            </div>
            <div class="card-body p-4">
                <div class="position-relative mb-4">
                    <canvas id="eleitor-chart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Geographic Distribution -->
<div class="row">
    <!-- Chart: Population per Aldeia -->
    <div class="col-md-4">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-chart-bar mr-2"></i> Populasaun kada Aldeia</h3>
            </div>
            <div class="card-body p-4">
                <div class="position-relative mb-4">
                    <canvas id="aldeia-chart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Familia per Aldeia -->
    <div class="col-md-4">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-home mr-2"></i> Familia (KK) kada Aldeia</h3>
            </div>
            <div class="card-body p-4">
                <div class="position-relative mb-4">
                    <canvas id="aldeia-fam-chart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Eleitores per Aldeia -->
    <div class="col-md-4">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-id-card mr-2"></i> Eleitores kada Aldeia</h3>
            </div>
            <div class="card-body p-4">
                <div class="position-relative mb-4">
                    <canvas id="aldeia-eleitor-chart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Education, Profession & Religion -->
<div class="row">
    <!-- Chart: Literatura -->
    <div class="col-md-4">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-graduation-cap mr-2"></i> Nivel Literatura</h3>
            </div>
            <div class="card-body p-4">
                <div class="position-relative mb-4">
                    <canvas id="literatura-chart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Profisaun -->
    <div class="col-md-4">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-briefcase mr-2"></i> Distribusaun Profisaun</h3>
            </div>
            <div class="card-body p-4">
                <div class="position-relative mb-4">
                    <canvas id="profisaun-chart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Relijiaun -->
    <div class="col-md-4">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-pray mr-2"></i> Distribusaun Relijiaun</h3>
            </div>
            <div class="card-body p-4">
                <div class="position-relative mb-4">
                    <canvas id="relijiaun-chart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 5: Services Trend -->
<div class="row">
    <!-- Chart: Trend Pedidu -->
    <div class="col-md-8">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-chart-line mr-2"></i> Trend Pedidu (Fulan 6 Ikus)</h3>
            </div>
            <div class="card-body p-4">
                <div class="position-relative mb-4">
                    <canvas id="trend-pedidu-chart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Status Pedidu -->
    <div class="col-md-4">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-tasks mr-2"></i> Status Pedidu</h3>
            </div>
            <div class="card-body p-4">
                <div class="position-relative mb-4">
                    <canvas id="status-pedidu-chart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 6: Population Status Trends & Vulnerability -->
<div class="row">
    <!-- Chart: Trend Estatutu Populasaun -->
    <div class="col-md-8">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-chart-area mr-2"></i> Trend Estatutu Populasaun (Fulan 6 Ikus)</h3>
            </div>
            <div class="card-body p-4">
                <div class="position-relative mb-4">
                    <canvas id="trend-estatutu-chart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Kbiit Laek per Aldeia -->
    <div class="col-md-4">
        <div class="card card-premium card-outline card-primary">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h3 class="card-title font-weight-bold text-secondary"><i class="fas fa-hand-holding-heart mr-2"></i> Kbiit Laek kada Aldeia</h3>
            </div>
            <div class="card-body p-4">
                <div class="position-relative mb-4">
                    <canvas id="kbiit-laek-aldeia-chart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Gender Chart (Doughnut)
    var ctxGender = document.getElementById('gender-chart').getContext('2d');
    var genderChart = new Chart(ctxGender, {
        type: 'doughnut',
        data: {
            labels: ['Mane', 'Feto'],
            datasets: [{
                data: [<?= $totalMane ?>, <?= $totalFeto ?>],
                backgroundColor: ['#3b82f6', '#ec4899'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: { family: 'Outfit, sans-serif', size: 12 }
                    }
                }
            },
            cutout: '70%'
        }
    });

    // 2. Age Group Chart (Bar)
    var ctxAge = document.getElementById('age-chart').getContext('2d');
    var ageChart = new Chart(ctxAge, {
        type: 'bar',
        data: {
            labels: <?= json_encode($ageGroupLabels) ?>,
            datasets: [{
                label: 'Total Populasaun',
                data: <?= json_encode($ageGroupValues) ?>,
                backgroundColor: ['#fbbf24', '#34d399', '#6366f1', '#f43f5e'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 3. Eleitor Chart (Pie)
    var ctxEleitor = document.getElementById('eleitor-chart').getContext('2d');
    var eleitorChart = new Chart(ctxEleitor, {
        type: 'pie',
        data: {
            labels: ['Eleitor', 'Seidauk Eleitor'],
            datasets: [{
                data: [<?= $totalEleitor ?>, <?= $totalNonEleitor ?>],
                backgroundColor: ['#10b981', '#f43f5e'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: { family: 'Outfit, sans-serif', size: 12 }
                    }
                }
            }
        }
    });

    // 4. Aldeia Population Chart (Bar Chart)
    var ctxAldeia = document.getElementById('aldeia-chart').getContext('2d');
    var aldeiaLabels = [];
    var aldeiaData = [];
    <?php foreach ($aldeiaPopulasaun as $ald) : ?>
        aldeiaLabels.push('<?= $ald['naran'] ?>');
        aldeiaData.push(<?= $ald['total'] ?>);
    <?php endforeach; ?>

    var aldeiaChart = new Chart(ctxAldeia, {
        type: 'bar',
        data: {
            labels: aldeiaLabels,
            datasets: [{
                label: 'Total Populasaun',
                data: aldeiaData,
                backgroundColor: '#0ea5e9',
                hoverBackgroundColor: '#0284c7',
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 5. Aldeia Familia Chart (Bar Chart)
    var ctxAldeiaFam = document.getElementById('aldeia-fam-chart').getContext('2d');
    var aldeiaFamLabels = [];
    var aldeiaFamData = [];
    <?php foreach ($aldeiaFamilia as $ald) : ?>
        aldeiaFamLabels.push('<?= $ald['naran'] ?>');
        aldeiaFamData.push(<?= $ald['total'] ?>);
    <?php endforeach; ?>

    var aldeiaFamChart = new Chart(ctxAldeiaFam, {
        type: 'bar',
        data: {
            labels: aldeiaFamLabels,
            datasets: [{
                label: 'Familia (KK)',
                data: aldeiaFamData,
                backgroundColor: '#8b5cf6',
                hoverBackgroundColor: '#7c3aed',
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 6. Literatura Chart (Horizontal Bar)
    var ctxLit = document.getElementById('literatura-chart').getContext('2d');
    var litChart = new Chart(ctxLit, {
        type: 'bar',
        data: {
            labels: <?= json_encode($literaturaLabels) ?>,
            datasets: [{
                label: 'Total',
                data: <?= json_encode($literaturaData) ?>,
                backgroundColor: '#06b6d4',
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                y: { grid: { display: false } }
            }
        }
    });

    // 7. Profisaun Chart (Horizontal Bar)
    var ctxProf = document.getElementById('profisaun-chart').getContext('2d');
    var profChart = new Chart(ctxProf, {
        type: 'bar',
        data: {
            labels: <?= json_encode($profisaunLabels) ?>,
            datasets: [{
                label: 'Total',
                data: <?= json_encode($profisaunData) ?>,
                backgroundColor: '#f97316',
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                y: { grid: { display: false } }
            }
        }
    });

    // 8. Relijiaun Chart (Doughnut)
    var ctxRel = document.getElementById('relijiaun-chart').getContext('2d');
    var relChart = new Chart(ctxRel, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($relijiaunLabels) ?>,
            datasets: [{
                data: <?= json_encode($relijiaunData) ?>,
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 10,
                        usePointStyle: true,
                        font: { family: 'Outfit, sans-serif', size: 11 }
                    }
                }
            },
            cutout: '60%'
        }
    });

    // 9. Trend Pedidu Chart (Line/Area)
    var ctxTrend = document.getElementById('trend-pedidu-chart').getContext('2d');
    var trendChart = new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: <?= json_encode($pediduTrendLabels) ?>,
            datasets: [{
                label: 'Total Pedidu',
                data: <?= json_encode($pediduTrendData) ?>,
                fill: true,
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderColor: '#4f46e5',
                borderWidth: 2,
                tension: 0.3,
                pointRadius: 4,
                pointBackgroundColor: '#4f46e5'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 10. Status Pedidu Chart (Doughnut)
    var ctxStatusPed = document.getElementById('status-pedidu-chart').getContext('2d');
    var statusPedChart = new Chart(ctxStatusPed, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($statusPediduLabels) ?>,
            datasets: [{
                data:  <?= json_encode($statusPediduData) ?>,
                backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: { family: 'Outfit, sans-serif', size: 12 }
                    }
                }
            },
            cutout: '70%'
        }
    });

    // 11. Kbiit Laek per Aldeia Chart (Bar)
    var ctxKlAld = document.getElementById('kbiit-laek-aldeia-chart').getContext('2d');
    var klAldLabels = [];
    var klAldData = [];
    <?php foreach ($aldeiaKbiitLaek as $ald) : ?>
        klAldLabels.push('<?= $ald['naran'] ?>');
        klAldData.push(<?= $ald['total'] ?>);
    <?php endforeach; ?>

    var klAldChart = new Chart(ctxKlAld, {
        type: 'bar',
        data: {
            labels: klAldLabels,
            datasets: [{
                label: 'Membru Kbiit Laek',
                data: klAldData,
                backgroundColor: '#f43f5e',
                hoverBackgroundColor: '#e11d48',
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 12. Aldeia Eleitores Chart (Bar)
    var ctxAldeiaEleitor = document.getElementById('aldeia-eleitor-chart').getContext('2d');
    var aldeiaEleitorLabels = [];
    var aldeiaEleitorData = [];
    <?php foreach ($aldeiaEleitores as $ald) : ?>
        aldeiaEleitorLabels.push('<?= $ald['naran'] ?>');
        aldeiaEleitorData.push(<?= $ald['total'] ?>);
    <?php endforeach; ?>

    var aldeiaEleitorChart = new Chart(ctxAldeiaEleitor, {
        type: 'bar',
        data: {
            labels: aldeiaEleitorLabels,
            datasets: [{
                label: 'Eleitor',
                data: aldeiaEleitorData,
                backgroundColor: '#10b981',
                hoverBackgroundColor: '#059669',
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 13. Trend Estatutu Populasaun Chart (Line)
    var ctxTrendEstatutu = document.getElementById('trend-estatutu-chart').getContext('2d');
    var trendEstatutuChart = new Chart(ctxTrendEstatutu, {
        type: 'line',
        data: {
            labels: <?= json_encode($estatutuTrendLabels) ?>,
            datasets: [
                {
                    label: 'Nascimentu (Moris Mai)',
                    data: <?= json_encode($estatutuTrendNasc) ?>,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.05)',
                    fill: true,
                    borderWidth: 2.5,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#22c55e'
                },
                {
                    label: 'Mortalidade (Mate)',
                    data: <?= json_encode($estatutuTrendMort) ?>,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.05)',
                    fill: true,
                    borderWidth: 2.5,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#ef4444'
                },
                {
                    label: 'Muda Domisíliu',
                    data: <?= json_encode($estatutuTrendMuda) ?>,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.05)',
                    fill: true,
                    borderWidth: 2.5,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#f59e0b'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        font: { family: 'Outfit, sans-serif', size: 12 }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Filtru bazeia ba Aldeia event listener
    $('#dashboard-filter-aldeia').on('change', function() {
        let val = $(this).val();
        if (val) {
            window.location.href = '<?= base_url('admin') ?>?id_aldeia=' + val;
        } else {
            window.location.href = '<?= base_url('admin') ?>';
        }
    });
</script>
<?= $this->endSection() ?>

