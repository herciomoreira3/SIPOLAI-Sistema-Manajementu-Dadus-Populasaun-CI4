<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RelijiaunModel;
use CodeIgniter\API\ResponseTrait;

class RelijiaunController extends BaseController
{
    use ResponseTrait;

    protected $relijiaunModel;

    public function __construct()
    {
        $this->relijiaunModel = new RelijiaunModel();
    }

    public function index()
    {
        if ($this->request->isAJAX()) {
            $start = $this->request->getGet('start');
            $length = $this->request->getGet('length');
            $search = $this->request->getGet('search[value]');
            
            $recordsTotal = $this->relijiaunModel->countAllResults();
            
            $builder = $this->relijiaunModel->builder();
            if (!empty($search)) {
                $builder->like('naran_relijiaun', $search);
            }
            $recordsFiltered = $builder->countAllResults(false);
            
            $builder = $this->relijiaunModel->builder();
            if (!empty($search)) {
                $builder->like('naran_relijiaun', $search);
            }
            $data = $builder->select('id_relijiaun, naran_relijiaun')
                            ->limit($length, $start)
                            ->get()
                            ->getResultArray();

            return $this->respond([
                'draw'            => $this->request->getGet('draw'),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ]);
        }

        return view('admin/relijiaun/index', [
            'title'    => 'Jestaun Relijiaun',
            'subtitle' => 'Lista Relijiaun iha Suku Laisorolai de Baixo',
        ]);
    }

    public function new()
    {
        return view('admin/relijiaun/create', [
            'title'    => 'Aumenta Relijiaun',
            'subtitle' => 'Kria Relijiaun Foun',
        ]);
    }

    public function create()
    {
        $rules = [
            'naran_relijiaun' => 'required|min_length[3]|max_length[100]|is_unique[tabela_relijiaun.naran_relijiaun]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $this->relijiaunModel->save([
            'naran_relijiaun' => $this->request->getPost('naran_relijiaun'),
        ]);

        return redirect()->to('/admin/relijiaun')->with('sweet-success', 'Relijiaun foun aumenta ho susesu!');
    }

    public function edit($id = null)
    {
        $data = $this->relijiaunModel->find($id);
        if (!$data) {
            return redirect()->to('/admin/relijiaun')->with('sweet-error', 'Dados Relijiaun la hetan!');
        }

        return view('admin/relijiaun/edit', [
            'title'    => 'Hadia Relijiaun',
            'subtitle' => 'Hadia Dadus Relijiaun',
            'relijiaun'=> $data,
        ]);
    }

    public function update($id = null)
    {
        $rules = [
            'naran_relijiaun' => "required|min_length[3]|max_length[100]|is_unique[tabela_relijiaun.naran_relijiaun,id_relijiaun,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $this->relijiaunModel->update($id, [
            'naran_relijiaun' => $this->request->getPost('naran_relijiaun'),
        ]);

        return redirect()->to('/admin/relijiaun')->with('sweet-success', 'Relijiaun hadia ho susesu!');
    }

    public function delete($id = null)
    {
        if (!$this->relijiaunModel->find($id)) {
            return $this->failNotFound('Relijiaun la hetan!');
        }

        $this->relijiaunModel->delete($id);

        return $this->respondDeleted(['status' => true], 'Relijiaun delekado ho susesu!');
    }
}
