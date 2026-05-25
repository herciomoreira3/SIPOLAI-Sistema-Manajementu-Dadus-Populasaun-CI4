<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProfisaunModel;
use CodeIgniter\API\ResponseTrait;

class ProfisaunController extends BaseController
{
    use ResponseTrait;

    protected $profisaunModel;

    public function __construct()
    {
        $this->profisaunModel = new ProfisaunModel();
    }

    public function index()
    {
        if ($this->request->isAJAX()) {
            $start = $this->request->getGet('start');
            $length = $this->request->getGet('length');
            $search = $this->request->getGet('search[value]');
            
            $recordsTotal = $this->profisaunModel->countAllResults();
            
            $builder = $this->profisaunModel->builder();
            if (!empty($search)) {
                $builder->like('naran_profisaun', $search);
            }
            $recordsFiltered = $builder->countAllResults(false);
            
            $builder = $this->profisaunModel->builder();
            if (!empty($search)) {
                $builder->like('naran_profisaun', $search);
            }
            $data = $builder->select('id_profisaun, naran_profisaun')
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

        return view('admin/profisaun/index', [
            'title'    => 'Jestaun Profisaun',
            'subtitle' => 'Lista Profisaun iha Suku Laisorolai de Baixo',
        ]);
    }

    public function new()
    {
        return view('admin/profisaun/create', [
            'title'    => 'Aumenta Profisaun',
            'subtitle' => 'Kria Profisaun Foun',
        ]);
    }

    public function create()
    {
        $rules = [
            'naran_profisaun' => 'required|min_length[3]|max_length[100]|is_unique[tabela_profisaun.naran_profisaun]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $this->profisaunModel->save([
            'naran_profisaun' => $this->request->getPost('naran_profisaun'),
        ]);

        return redirect()->to('/admin/profisaun')->with('sweet-success', 'Profisaun foun aumenta ho susesu!');
    }

    public function edit($id = null)
    {
        $data = $this->profisaunModel->find($id);
        if (!$data) {
            return redirect()->to('/admin/profisaun')->with('sweet-error', 'Dados Profisaun la hetan!');
        }

        return view('admin/profisaun/edit', [
            'title'    => 'Hadia Profisaun',
            'subtitle' => 'Hadia Dadus Profisaun',
            'profisaun'=> $data,
        ]);
    }

    public function update($id = null)
    {
        $rules = [
            'naran_profisaun' => "required|min_length[3]|max_length[100]|is_unique[tabela_profisaun.naran_profisaun,id_profisaun,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $this->profisaunModel->update($id, [
            'naran_profisaun' => $this->request->getPost('naran_profisaun'),
        ]);

        return redirect()->to('/admin/profisaun')->with('sweet-success', 'Profisaun hadia ho susesu!');
    }

    public function delete($id = null)
    {
        if (!$this->profisaunModel->find($id)) {
            return $this->failNotFound('Profisaun la hetan!');
        }

        $this->profisaunModel->delete($id);

        return $this->respondDeleted(['status' => true], 'Profisaun delekado ho susesu!');
    }
}
