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
        $this->aldeiaModel = new AldeiaModel();
    }

    public function index()
    {
        if ($this->request->isAJAX()) {
            $start = $this->request->getGet('start');
            $length = $this->request->getGet('length');
            $search = $this->request->getGet('search[value]');
            $id_aldeia = $this->request->getGet('id_aldeia');
            
            $db = \Config\Database::connect();
            
            $baseBuilder = function() use ($db, $id_aldeia) {
                $latestApprovedPedidu = "(SELECT MAX(tp.id_pedidu) FROM tabela_pedidu tp WHERE tp.id_populasaun = tabela_populasaun.id_populasaun AND tp.naran_pedidu = " . $db->escape('Deklarasaun Eleitoral') . " AND tp.status = 'Aprovadu')";
                $builder = $db->table('tabela_populasaun')
                    ->join('tabela_pedidu', "tabela_pedidu.id_pedidu = {$latestApprovedPedidu}", 'inner', false)
                    ->where('tabela_populasaun.istadu', 'Moris');

                if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                    $builder->where('tabela_populasaun.id_aldeia', user()->id_aldeia);
                }
                if (!empty($id_aldeia)) {
                    $builder->where('tabela_populasaun.id_aldeia', $id_aldeia);
                }
                return $builder;
            };

            // 1. recordsTotal
            $recordsTotal = $baseBuilder()->countAllResults();
            
            // 2. recordsFiltered (with search)
            $filterBuilder = $baseBuilder();
            $filterBuilder->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left');
            if (!empty($search)) {
                $filterBuilder->groupStart()
                    ->like('tabela_populasaun.naran_kompletu', $search)
                    ->orLike('tabela_populasaun.nik', $search)
                    ->orLike('tabela_populasaun.no_eleitoral', $search)
                    ->orLike('tabela_aldeia.naran_aldeia', $search)
                    ->groupEnd();
            }
            $recordsFiltered = $filterBuilder->countAllResults();
            
            // 3. fetch data
            $dataBuilder = $baseBuilder();
            $dataBuilder->select('tabela_populasaun.*, tabela_aldeia.naran_aldeia, tabela_pedidu.data_pedidu as data_aprovada, tabela_pedidu.id_pedidu')
                ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left');
            if (!empty($search)) {
                $dataBuilder->groupStart()
                    ->like('tabela_populasaun.naran_kompletu', $search)
                    ->orLike('tabela_populasaun.nik', $search)
                    ->orLike('tabela_populasaun.no_eleitoral', $search)
                    ->orLike('tabela_aldeia.naran_aldeia', $search)
                    ->groupEnd();
            }
            $data = $dataBuilder->limit($length, $start)->get()->getResultArray();

            return $this->respond([
                'draw'            => $this->request->getGet('draw'),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ]);
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        return view('admin/eleitor/index', [
            'title'    => 'Dados Eleitores',
            'subtitle' => 'Dadus Populasaun ne\'ebé hetan ona Deklarasaun Eleitoral husi Suku no halo ona Kartaun Eleitoral',
            'aldeias'  => $aldeias,
        ]);
    }

    public function update($id = null)
    {
        // Only Authorized Roles can update Voter Card Numbers
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

        $db = \Config\Database::connect();
        $approvedPedidu = $db->table('tabela_pedidu')
            ->where('id_populasaun', $id)
            ->where('naran_pedidu', 'Deklarasaun Eleitoral')
            ->where('status', 'Aprovadu')
            ->countAllResults();
        if ($approvedPedidu === 0) {
            return $this->fail('Sidadaun nee seidauk iha Deklarasaun Eleitoral aprovadu.');
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
