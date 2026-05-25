<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InventoriuModel;
use CodeIgniter\API\ResponseTrait;

class InventoriuController extends BaseController
{
    use ResponseTrait;

    protected $inventoriuModel;

    public function __construct()
    {
        $this->inventoriuModel = new InventoriuModel();
    }

    public function index()
    {
        if ($this->request->isAJAX()) {
            $start = $this->request->getGet('start');
            $length = $this->request->getGet('length');
            $search = $this->request->getGet('search[value]');
            
            $db = \Config\Database::connect();
            
            // 1. recordsTotal
            $totalBuilder = $db->table('tabela_inventoriu');
            $recordsTotal = $totalBuilder->countAllResults();
            
            // 2. recordsFiltered
            $filterBuilder = $db->table('tabela_inventoriu');
            $filterBuilder->join('tabela_pedidu', 'tabela_pedidu.id_pedidu = tabela_inventoriu.id_pedidu', 'left');
            if (!empty($search)) {
                $filterBuilder->groupStart()
                    ->like('tabela_inventoriu.naran_kompletu', $search)
                    ->orLike('tabela_inventoriu.nik', $search)
                    ->orLike('tabela_inventoriu.naran_aldeia', $search)
                    ->orLike('tabela_pedidu.naran_pedidu', $search)
                    ->groupEnd();
            }
            $recordsFiltered = $filterBuilder->countAllResults();
            
            // 3. fetch data
            $dataBuilder = $db->table('tabela_inventoriu');
            $dataBuilder->select('tabela_inventoriu.*, tabela_pedidu.naran_pedidu, tabela_pedidu.data_pedidu')
                ->join('tabela_pedidu', 'tabela_pedidu.id_pedidu = tabela_inventoriu.id_pedidu', 'left');
            if (!empty($search)) {
                $dataBuilder->groupStart()
                    ->like('tabela_inventoriu.naran_kompletu', $search)
                    ->orLike('tabela_inventoriu.nik', $search)
                    ->orLike('tabela_inventoriu.naran_aldeia', $search)
                    ->orLike('tabela_pedidu.naran_pedidu', $search)
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

        $tipuModel = new \App\Models\TipuPediduModel();
        $tipus = $tipuModel->findAll();

        $data = [
            'title'    => 'Inventoriu Deklarasaun',
            'subtitle' => 'Sentru Jestaun Inventoriu Deklarasaun Suku Laisorolai de Baixo',
            'tipus'    => $tipus
        ];

        return view('admin/inventoriu/index', $data);
    }

    public function delete($id = null)
    {
        if (!in_groups(['admin', 'xefe-suku'])) {
            return $this->failForbidden('Ita boot la iha kbiit/autorizasaun atu hamoos dadus inventoriu!');
        }

        if (!$this->inventoriuModel->find($id)) {
            return $this->failNotFound('Dadus inventoriu la hetan!');
        }

        $this->inventoriuModel->delete($id);

        return $this->respondDeleted(['status' => true], 'Dadus inventoriu hamoos ho susesu!');
    }
}

