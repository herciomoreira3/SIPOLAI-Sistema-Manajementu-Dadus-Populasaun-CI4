<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PopulasaunModel;
use App\Models\AldeiaModel;
use CodeIgniter\API\ResponseTrait;

class KbiitLaekController extends BaseController
{
    use ResponseTrait;

    protected $populasaunModel;
    protected $aldeiaModel;

    public function __construct()
    {
        $this->populasaunModel = new PopulasaunModel();
        $this->aldeiaModel     = new AldeiaModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Fetch approved Deklarasaun Kbiit Laek pedidu by id_populasaun
        $pediduByPop = $db->table('tabela_pedidu')
            ->select('id_populasaun, MAX(data_pedidu) as data_aprovada')
            ->where('naran_pedidu', 'Deklarasaun Kbiit Laek')
            ->where('status', 'Aprovadu')
            ->where('id_populasaun IS NOT NULL')
            ->groupBy('id_populasaun')
            ->get()->getResultArray();

        $approvedPopMap = [];
        foreach ($pediduByPop as $row) {
            $approvedPopMap[(int)$row['id_populasaun']] = $row['data_aprovada'];
        }

        // 2. Fetch approved Deklarasaun Kbiit Laek pedidu by pemohon + id_aldeia (fallback for legacy seeder data)
        $pediduByName = $db->table('tabela_pedidu')
            ->select('pemohon, id_aldeia, MAX(data_pedidu) as data_aprovada')
            ->where('naran_pedidu', 'Deklarasaun Kbiit Laek')
            ->where('status', 'Aprovadu')
            ->where('id_populasaun IS NULL')
            ->where('pemohon IS NOT NULL')
            ->groupBy('pemohon, id_aldeia')
            ->get()->getResultArray();

        $approvedNameMap = [];
        foreach ($pediduByName as $row) {
            $key = mb_strtolower(trim($row['pemohon'])) . '_' . (int)$row['id_aldeia'];
            $approvedNameMap[$key] = $row['data_aprovada'];
        }

        // 3. Simple query for population with standard LEFT JOIN on tabela_aldeia
        $builder = $db->table('tabela_populasaun p')
            ->select('p.id_populasaun, p.nik, p.naran_kompletu, p.jeneru, p.no_kbiit_laek, p.istadu, p.id_aldeia, a.naran_aldeia')
            ->join('tabela_aldeia a', 'a.id_aldeia = p.id_aldeia', 'left')
            ->where('p.istadu', 'Moris')
            ->orderBy('p.naran_kompletu', 'ASC');

        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $builder->where('p.id_aldeia', user()->id_aldeia);
        }

        $allPop = $builder->get()->getResultArray();

        // 4. Combine in PHP (100% compatible with TiDB and all DB engines)
        $kbiitLaeks = [];
        foreach ($allPop as $p) {
            $id  = (int) $p['id_populasaun'];
            $key = mb_strtolower(trim($p['naran_kompletu'])) . '_' . (int) $p['id_aldeia'];

            $hasCard   = !empty($p['no_kbiit_laek']);
            $hasPedidu = isset($approvedPopMap[$id]) || isset($approvedNameMap[$key]);

            if ($hasCard || $hasPedidu) {
                $p['data_aprovada'] = $approvedPopMap[$id] ?? ($approvedNameMap[$key] ?? null);
                $kbiitLaeks[] = $p;
            }
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        return view('admin/kbiit-laek/index', [
            'title'      => 'Dados Kbiit Laek',
            'subtitle'   => 'Dadus Populasaun ne\'ebé hetan ona Deklarasaun Kbiit Laek husi Suku no rejistradu hanesan Família Kbiit Laek',
            'aldeias'    => $aldeias,
            'kbiitLaeks' => $kbiitLaeks,
        ]);
    }

    public function update($id = null)
    {
        if (!in_groups(['admin', 'xefe-suku', 'sekretaria'])) {
            return $this->failForbidden('Ita boot la iha kbiit/autorizasaun atu atualiza dadus kbiit laek!');
        }

        $populasaun = $this->populasaunModel->find($id);
        if (!$populasaun) {
            return $this->failNotFound('Dadus populasaun la hetan!');
        }
        if (! $this->canAccessAldeia($populasaun['id_aldeia'])) {
            return $this->failForbidden('Ita boot labele atualiza kbiit laek husi aldeia seluk.');
        }

        $noKbiitLaek = trim((string) $this->request->getPost('no_kbiit_laek'));
        if ($noKbiitLaek !== '' && strlen($noKbiitLaek) > 50) {
            return $this->fail('Numeru Kartaun/Sertifikadu Kbiit Laek labele liu karakter 50.');
        }
        if ($noKbiitLaek !== '') {
            $exists = $this->populasaunModel
                ->where('no_kbiit_laek', $noKbiitLaek)
                ->where('id_populasaun !=', $id)
                ->first();
            if ($exists) {
                return $this->fail('Numeru Kartaun/Sertifikadu Kbiit Laek nee uza ona husi sidadaun seluk.');
            }
        }

        $this->populasaunModel->update($id, [
            'no_kbiit_laek' => $noKbiitLaek !== '' ? $noKbiitLaek : null
        ]);

        return $this->respond([
            'status'  => true,
            'message' => 'Númeru Kartaun/Sertifikadu Kbiit Laek atualizadu ho susesu!'
        ]);
    }
}
