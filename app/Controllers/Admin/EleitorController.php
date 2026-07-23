<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PopulasaunModel;
use App\Models\AldeiaModel;
use CodeIgniter\API\ResponseTrait;

class EleitorController extends BaseController
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

        // 1. Fetch approved Deklarasaun Eleitoral pedidu by id_populasaun
        $pediduByPop = $db->table('tabela_pedidu')
            ->select('id_populasaun, MAX(data_pedidu) as data_aprovada')
            ->where('naran_pedidu', 'Deklarasaun Eleitoral')
            ->where('status', 'Aprovadu')
            ->where('id_populasaun IS NOT NULL')
            ->groupBy('id_populasaun')
            ->get()->getResultArray();

        $approvedPopMap = [];
        foreach ($pediduByPop as $row) {
            $approvedPopMap[(int)$row['id_populasaun']] = $row['data_aprovada'];
        }

        // 2. Fetch approved Deklarasaun Eleitoral pedidu by pemohon + id_aldeia (fallback for legacy seeder data)
        $pediduByName = $db->table('tabela_pedidu')
            ->select('pemohon, id_aldeia, MAX(data_pedidu) as data_aprovada')
            ->where('naran_pedidu', 'Deklarasaun Eleitoral')
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
            ->select('p.id_populasaun, p.nik, p.naran_kompletu, p.jeneru, p.no_eleitoral, p.istadu, p.id_aldeia, a.naran_aldeia')
            ->join('tabela_aldeia a', 'a.id_aldeia = p.id_aldeia', 'left')
            ->where('p.istadu', 'Moris')
            ->orderBy('p.naran_kompletu', 'ASC');

        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $builder->where('p.id_aldeia', user()->id_aldeia);
        }

        $allPop = $builder->get()->getResultArray();

        // 4. Combine in PHP (100% compatible with TiDB and all DB engines)
        $eleitores = [];
        foreach ($allPop as $p) {
            $id  = (int) $p['id_populasaun'];
            $key = mb_strtolower(trim($p['naran_kompletu'])) . '_' . (int) $p['id_aldeia'];

            $hasCard   = !empty($p['no_eleitoral']);
            $hasPedidu = isset($approvedPopMap[$id]) || isset($approvedNameMap[$key]);

            if ($hasCard || $hasPedidu) {
                $p['data_aprovada'] = $approvedPopMap[$id] ?? ($approvedNameMap[$key] ?? null);
                $eleitores[] = $p;
            }
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        return view('admin/eleitor/index', [
            'title'     => 'Dados Eleitores',
            'subtitle'  => 'Dadus Populasaun ne\'ebé hetan ona Deklarasaun Eleitoral husi Suku no halo ona Kartaun Eleitoral',
            'aldeias'   => $aldeias,
            'eleitores' => $eleitores,
        ]);
    }

    public function update($id = null)
    {
        if (!in_groups(['admin', 'xefe-suku', 'sekretaria'])) {
            return $this->failForbidden('Ita boot la iha kbiit/autorizasaun atu atualiza dadus eleitor!');
        }

        $populasaun = $this->populasaunModel->find($id);
        if (!$populasaun) {
            return $this->failNotFound('Dadus populasaun la hetan!');
        }
        if (! $this->canAccessAldeia($populasaun['id_aldeia'])) {
            return $this->failForbidden('Ita boot labele atualiza eleitor husi aldeia seluk.');
        }

        $noEleitoral = trim((string) $this->request->getPost('no_eleitoral'));
        if ($noEleitoral !== '' && strlen($noEleitoral) > 50) {
            return $this->fail('Numeiru Kartaun Eleitoral labele liu karakter 50.');
        }
        if ($noEleitoral !== '') {
            $exists = $this->populasaunModel
                ->where('no_eleitoral', $noEleitoral)
                ->where('id_populasaun !=', $id)
                ->first();
            if ($exists) {
                return $this->fail('Numeiru Kartaun Eleitoral nee uza ona husi sidadaun seluk.');
            }
        }

        $this->populasaunModel->update($id, [
            'no_eleitoral' => $noEleitoral !== '' ? $noEleitoral : null
        ]);

        return $this->respond([
            'status'  => true,
            'message' => 'Numeiru Kartaun Eleitoral atualizadu ho susesu!'
        ]);
    }
}
