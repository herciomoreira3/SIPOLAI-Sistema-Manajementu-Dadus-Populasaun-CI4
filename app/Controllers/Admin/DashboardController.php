<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PopulasaunModel;
use App\Models\AldeiaModel;
use App\Models\PediduModel;
use App\Models\FamiliaModel;
use App\Models\LiteraturaModel;
use App\Models\ProfisaunModel;
use App\Models\RelijiaunModel;

class DashboardController extends BaseController
{
    protected $populasaunModel;
    protected $aldeiaModel;
    protected $pediduModel;
    protected $familiaModel;
    protected $literaturaModel;
    protected $profisaunModel;
    protected $relijiaunModel;

    public function __construct()
    {
        $this->populasaunModel = new PopulasaunModel();
        $this->aldeiaModel = new AldeiaModel();
        $this->pediduModel = new PediduModel();
        $this->familiaModel = new FamiliaModel();
        $this->literaturaModel = new LiteraturaModel();
        $this->profisaunModel = new ProfisaunModel();
        $this->relijiaunModel = new RelijiaunModel();
    }

    public function index()
    {
        $idAldeia = null;
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $idAldeia = user()->id_aldeia;
        } else {
            $paramAldeia = $this->request->getGet('id_aldeia');
            if (!empty($paramAldeia)) {
                $idAldeia = $paramAldeia;
            }
        }

        // --- STATS OVERVIEW ---
        $popQuery = $this->populasaunModel;
        if ($idAldeia) {
            $popQuery = $popQuery->where('id_aldeia', $idAldeia);
        }
        $totalPopulasaun = $popQuery->countAllResults();

        $aldeiaQuery = $this->aldeiaModel;
        if ($idAldeia) {
            $aldeiaQuery = $aldeiaQuery->where('id_aldeia', $idAldeia);
        }
        $totalAldeia = $aldeiaQuery->countAllResults();

        $pediduQuery = $this->pediduModel->where('status', 'Pendiente');
        if ($idAldeia) {
            $pediduQuery = $pediduQuery->where('id_aldeia', $idAldeia);
        }
        $totalPedidu = $pediduQuery->countAllResults();

        // New KPI: Total Familia
        $famQuery = $this->familiaModel;
        if ($idAldeia) {
            $famQuery = $famQuery->where('id_aldeia', $idAldeia);
        }
        $totalFamilia = $famQuery->countAllResults();

        // New KPI: Kbiit Laek
        $klQuery = $this->populasaunModel->where('no_kbiit_laek !=', null)->where('no_kbiit_laek !=', '');
        if ($idAldeia) {
            $klQuery = $klQuery->where('id_aldeia', $idAldeia);
        }
        $totalKbiitLaek = $klQuery->countAllResults();

        // --- CHART: MALE VS FEMALE ---
        $popQueryMane = $this->populasaunModel->where('jeneru', 'Mane');
        if ($idAldeia) {
            $popQueryMane = $popQueryMane->where('id_aldeia', $idAldeia);
        }
        $totalMane = $popQueryMane->countAllResults();

        $popQueryFeto = $this->populasaunModel->where('jeneru', 'Feto');
        if ($idAldeia) {
            $popQueryFeto = $popQueryFeto->where('id_aldeia', $idAldeia);
        }
        $totalFeto = $popQueryFeto->countAllResults();

        // --- CHART: POPULASAUN & FAMILIA PER ALDEIA ---
        $aldeiaPopulasaun = [];
        $aldeiaFamilia = [];
        $aldeiaKbiitLaek = [];
        $aldeiaEleitores = [];
        $aldeias = $this->aldeiaModel->findAll();
        foreach ($aldeias as $ald) {
            if ($idAldeia && $ald['id_aldeia'] != $idAldeia) {
                continue;
            }
            // Populasaun
            $countPop = $this->populasaunModel->where('id_aldeia', $ald['id_aldeia'])->countAllResults();
            $aldeiaPopulasaun[] = [
                'naran' => $ald['naran_aldeia'],
                'total' => $countPop
            ];
            // Familia
            $countFam = $this->familiaModel->where('id_aldeia', $ald['id_aldeia'])->countAllResults();
            $aldeiaFamilia[] = [
                'naran' => $ald['naran_aldeia'],
                'total' => $countFam
            ];
            // Kbiit Laek
            $countKL = $this->populasaunModel
                ->where('id_aldeia', $ald['id_aldeia'])
                ->where('no_kbiit_laek !=', null)
                ->where('no_kbiit_laek !=', '')
                ->countAllResults();
            $aldeiaKbiitLaek[] = [
                'naran' => $ald['naran_aldeia'],
                'total' => $countKL
            ];
            // Eleitores
            $countEleitor = $this->populasaunModel
                ->join('tabela_pedidu', 'tabela_pedidu.pemohon = tabela_populasaun.naran_kompletu')
                ->where('tabela_pedidu.naran_pedidu', 'Deklarasaun Eleitoral')
                ->where('tabela_pedidu.status', 'Aprovadu')
                ->where('tabela_populasaun.istadu', 'Moris')
                ->where('tabela_populasaun.id_aldeia', $ald['id_aldeia'])
                ->countAllResults();
            $aldeiaEleitores[] = [
                'naran' => $ald['naran_aldeia'],
                'total' => $countEleitor
            ];
        }

        // --- CHART: AGE GROUP ---
        $popAgeQuery = $this->populasaunModel->select('data_moris');
        if ($idAldeia) {
            $popAgeQuery = $popAgeQuery->where('id_aldeia', $idAldeia);
        }
        $popList = $popAgeQuery->findAll();
        $ageGroups = [
            'Labarik (0-5)' => 0,
            'Adolesente (6-17)' => 0,
            'Adultu (18-59)' => 0,
            'Ferik/Katuas (60+)' => 0
        ];
        foreach ($popList as $pop) {
            if (!empty($pop['data_moris'])) {
                $birthDate = new \DateTime($pop['data_moris']);
                $today = new \DateTime();
                $age = $today->diff($birthDate)->y;
                if ($age <= 5) {
                    $ageGroups['Labarik (0-5)']++;
                } elseif ($age <= 17) {
                    $ageGroups['Adolesente (6-17)']++;
                } elseif ($age <= 59) {
                    $ageGroups['Adultu (18-59)']++;
                } else {
                    $ageGroups['Ferik/Katuas (60+)']++;
                }
            }
        }
        $ageGroupLabels = array_keys($ageGroups);
        $ageGroupValues = array_values($ageGroups);

        // --- CHART: LITERATURA ---
        $literaturas = $this->literaturaModel->findAll();
        $literaturaLabels = [];
        $literaturaData = [];
        foreach ($literaturas as $lit) {
            $litQuery = $this->populasaunModel->where('id_literatura', $lit['id_literatura']);
            if ($idAldeia) {
                $litQuery = $litQuery->where('id_aldeia', $idAldeia);
            }
            $count = $litQuery->countAllResults();
            $literaturaLabels[] = $lit['naran_literatura'];
            $literaturaData[] = $count;
        }

        // --- CHART: PROFISAUN ---
        $profisauns = $this->profisaunModel->findAll();
        $profisaunLabels = [];
        $profisaunData = [];
        foreach ($profisauns as $prof) {
            $profQuery = $this->populasaunModel->where('id_profisaun', $prof['id_profisaun']);
            if ($idAldeia) {
                $profQuery = $profQuery->where('id_aldeia', $idAldeia);
            }
            $count = $profQuery->countAllResults();
            $profisaunLabels[] = $prof['naran_profisaun'];
            $profisaunData[] = $count;
        }

        // --- CHART: RELIJIAUN ---
        $relijiauns = $this->relijiaunModel->findAll();
        $relijiaunLabels = [];
        $relijiaunData = [];
        foreach ($relijiauns as $rel) {
            $relQuery = $this->populasaunModel->where('id_relijiaun', $rel['id_relijiaun']);
            if ($idAldeia) {
                $relQuery = $relQuery->where('id_aldeia', $idAldeia);
            }
            $count = $relQuery->countAllResults();
            $relijiaunLabels[] = $rel['naran_relijiaun'];
            $relijiaunData[] = $count;
        }

        // --- CHART: STATUS KAZA ---
        $statusKazaQuery = $this->populasaunModel->select('status_kaza, COUNT(*) as total');
        if ($idAldeia) {
            $statusKazaQuery = $statusKazaQuery->where('id_aldeia', $idAldeia);
        }
        $statusKazaRaw = $statusKazaQuery->groupBy('status_kaza')->findAll();
        $statusKazaLabels = [];
        $statusKazaData = [];
        foreach ($statusKazaRaw as $sk) {
            $statusKazaLabels[] = $sk['status_kaza'] ?: 'La Hatene';
            $statusKazaData[] = (int)$sk['total'];
        }

        // --- CHART: ELEITOR VS NON-ELEITOR ---
        $eleitorQuery = $this->populasaunModel->where('no_eleitoral !=', null)->where('no_eleitoral !=', '');
        if ($idAldeia) {
            $eleitorQuery = $eleitorQuery->where('id_aldeia', $idAldeia);
        }
        $totalEleitor = $eleitorQuery->countAllResults();

        $nonEleitorQuery = $this->populasaunModel->groupStart()
            ->where('no_eleitoral', null)
            ->orWhere('no_eleitoral', '')
            ->groupEnd();
        if ($idAldeia) {
            $nonEleitorQuery = $nonEleitorQuery->where('id_aldeia', $idAldeia);
        }
        $totalNonEleitor = $nonEleitorQuery->countAllResults();

        // --- CHART: TREND PEDIDU 6 BULAN ---
        $pediduTrendQuery = $this->pediduModel->select('data_pedidu');
        if ($idAldeia) {
            $pediduTrendQuery = $pediduTrendQuery->where('id_aldeia', $idAldeia);
        }
        $sixMonthsAgo = date('Y-m-d', strtotime('-5 months'));
        $pedidus = $pediduTrendQuery->where('data_pedidu >=', $sixMonthsAgo)->findAll();

        $trendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = date('Y-m', strtotime("-$i months"));
            $trendData[$m] = 0;
        }
        foreach ($pedidus as $p) {
            if (!empty($p['data_pedidu'])) {
                $m = date('Y-m', strtotime($p['data_pedidu']));
                if (isset($trendData[$m])) {
                    $trendData[$m]++;
                }
            }
        }
        $pediduTrendLabels = [];
        $pediduTrendData = [];
        foreach ($trendData as $bulan => $total) {
            $pediduTrendLabels[] = date('M Y', strtotime($bulan . '-01'));
            $pediduTrendData[] = $total;
        }

        // --- CHART: STATUS PEDIDU ---
        $statusPediduQuery = $this->pediduModel->select('status, COUNT(*) as total');
        if ($idAldeia) {
            $statusPediduQuery = $statusPediduQuery->where('id_aldeia', $idAldeia);
        }
        $statusPediduRaw = $statusPediduQuery->groupBy('status')->findAll();
        $statusPediduLabels = [];
        $statusPediduData = [];
        foreach ($statusPediduRaw as $sp) {
            $statusPediduLabels[] = $sp['status'] ?: 'La Hatene';
            $statusPediduData[] = (int)$sp['total'];
        }

        // --- CHART: POPULASAUN STATUSTU TREND ---
        $db = \Config\Database::connect();
        
        $trendMonths = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = date('Y-m', strtotime("-$i months"));
            $trendMonths[$m] = [
                'nascimentu' => 0,
                'mortalidade' => 0,
                'muda' => 0
            ];
        }

        // 1. Births (Nascimentu)
        $nascQuery = $db->table('tabela_pedidu')
            ->where('naran_pedidu', 'Deklarasaun Nascimentu')
            ->where('status', 'Aprovadu')
            ->where('data_pedidu >=', $sixMonthsAgo);
        if ($idAldeia) {
            $nascQuery = $nascQuery->where('id_aldeia', $idAldeia);
        }
        $nascList = $nascQuery->get()->getResultArray();
        foreach ($nascList as $n) {
            $m = date('Y-m', strtotime($n['data_pedidu']));
            if (isset($trendMonths[$m])) {
                $trendMonths[$m]['nascimentu']++;
            }
        }

        // 2. Deaths (Mortalidade)
        $mortQuery = $db->table('tabela_pedidu')
            ->where('naran_pedidu', 'Deklarasaun Mortalidade')
            ->where('status', 'Aprovadu')
            ->where('data_pedidu >=', $sixMonthsAgo);
        if ($idAldeia) {
            $mortQuery = $mortQuery->where('id_aldeia', $idAldeia);
        }
        $mortList = $mortQuery->get()->getResultArray();
        foreach ($mortList as $m) {
            $mKey = date('Y-m', strtotime($m['data_pedidu']));
            if (isset($trendMonths[$mKey])) {
                $trendMonths[$mKey]['mortalidade']++;
            }
        }

        // 3. Migrations (Muda)
        $mudaQuery = $db->table('tabela_pedidu')
            ->where('naran_pedidu', 'Deklarasaun Muda Domisiliu')
            ->where('status', 'Aprovadu')
            ->where('data_pedidu >=', $sixMonthsAgo);
        if ($idAldeia) {
            $mudaQuery = $mudaQuery->where('id_aldeia', $idAldeia);
        }
        $mudaList = $mudaQuery->get()->getResultArray();
        foreach ($mudaList as $mu) {
            $mKey = date('Y-m', strtotime($mu['data_pedidu']));
            if (isset($trendMonths[$mKey])) {
                $trendMonths[$mKey]['muda']++;
            }
        }

        $estatutuTrendLabels = [];
        $estatutuTrendNasc = [];
        $estatutuTrendMort = [];
        $estatutuTrendMuda = [];
        foreach ($trendMonths as $month => $vals) {
            $estatutuTrendLabels[] = date('M Y', strtotime($month . '-01'));
            $estatutuTrendNasc[] = $vals['nascimentu'];
            $estatutuTrendMort[] = $vals['mortalidade'];
            $estatutuTrendMuda[] = $vals['muda'];
        }

        return view('admin/dashboard', [
            'title'              => 'Dashboard',
            'totalPopulasaun'    => $totalPopulasaun,
            'totalAldeia'        => $totalAldeia,
            'totalPedidu'        => $totalPedidu,
            'totalFamilia'       => $totalFamilia,
            'totalMane'          => $totalMane,
            'totalFeto'          => $totalFeto,
            'aldeiaPopulasaun'   => $aldeiaPopulasaun,
            'aldeiaFamilia'      => $aldeiaFamilia,
            'aldeiaKbiitLaek'    => $aldeiaKbiitLaek,
            'aldeiaEleitores'    => $aldeiaEleitores,
            'ageGroupLabels'     => $ageGroupLabels,
            'ageGroupValues'     => $ageGroupValues,
            'literaturaLabels'   => $literaturaLabels,
            'literaturaData'     => $literaturaData,
            'profisaunLabels'    => $profisaunLabels,
            'profisaunData'      => $profisaunData,
            'relijiaunLabels'    => $relijiaunLabels,
            'relijiaunData'      => $relijiaunData,
            'statusKazaLabels'   => $statusKazaLabels,
            'statusKazaData'     => $statusKazaData,
            'totalEleitor'       => $totalEleitor,
            'totalNonEleitor'    => $totalNonEleitor,
            'pediduTrendLabels'  => $pediduTrendLabels,
            'pediduTrendData'    => $pediduTrendData,
            'statusPediduLabels' => $statusPediduLabels,
            'statusPediduData'   => $statusPediduData,
            'estatutuTrendLabels'=> $estatutuTrendLabels,
            'estatutuTrendNasc'  => $estatutuTrendNasc,
            'estatutuTrendMort'  => $estatutuTrendMort,
            'estatutuTrendMuda'  => $estatutuTrendMuda,
            'aldeias'            => $aldeias,
            'selectedAldeia'     => $idAldeia,
        ]);
    }
}

