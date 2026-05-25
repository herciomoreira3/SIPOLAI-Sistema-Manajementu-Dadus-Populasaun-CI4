<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KarguModel;
use CodeIgniter\API\ResponseTrait;

class KarguController extends BaseController
{
    use ResponseTrait;

    protected $karguModel;

    public function __construct()
    {
        $this->karguModel = new KarguModel();
    }

    public function index()
    {
        if ($this->request->isAJAX()) {
            $start = $this->request->getGet('start');
            $length = $this->request->getGet('length');
            $search = $this->request->getGet('search[value]');
            
            $recordsTotal = $this->karguModel->countAllResults();
            
            $builder = $this->karguModel->builder();
            if (!empty($search)) {
                $builder->like('naran_kargu', $search);
            }
            $recordsFiltered = $builder->countAllResults(false);
            
            $builder = $this->karguModel->builder();
            if (!empty($search)) {
                $builder->like('naran_kargu', $search);
            }
            $data = $builder->select('id_kargu, naran_kargu')
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

        return view('admin/kargu/index', [
            'title'    => 'Jestaun Kargu',
            'subtitle' => 'Lista Kargu / Posisaun iha Suku Laisorolai de Baixo',
        ]);
    }

    public function new()
    {
        return view('admin/kargu/create', [
            'title'    => 'Aumenta Kargu',
            'subtitle' => 'Kria Kargu Foun',
        ]);
    }

    public function create()
    {
        $rules = [
            'naran_kargu' => 'required|min_length[2]|max_length[100]|is_unique[tabela_kargu.naran_kargu]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $this->karguModel->save([
            'naran_kargu' => $this->request->getPost('naran_kargu'),
        ]);

        return redirect()->to('/admin/kargu')->with('sweet-success', 'Kargu foun aumenta ho susesu!');
    }

    public function edit($id = null)
    {
        $data = $this->karguModel->find($id);
        if (!$data) {
            return redirect()->to('/admin/kargu')->with('sweet-error', 'Dados Kargu la hetan!');
        }

        return view('admin/kargu/edit', [
            'title'    => 'Hadia Kargu',
            'subtitle' => 'Hadia Dadus Kargu',
            'kargu'    => $data,
        ]);
    }

    public function update($id = null)
    {
        $rules = [
            'naran_kargu' => "required|min_length[2]|max_length[100]|is_unique[tabela_kargu.naran_kargu,id_kargu,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $this->karguModel->update($id, [
            'naran_kargu' => $this->request->getPost('naran_kargu'),
        ]);

        return redirect()->to('/admin/kargu')->with('sweet-success', 'Kargu hadia ho susesu!');
    }

    public function delete($id = null)
    {
        if (!$this->karguModel->find($id)) {
            return $this->failNotFound('Kargu la hetan!');
        }

        $this->karguModel->delete($id);

        return $this->respondDeleted(['status' => true], 'Kargu delekado ho susesu!');
    }
}
