<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AldeiaModel;
use CodeIgniter\API\ResponseTrait;

class AldeiaController extends BaseController
{
    use ResponseTrait;

    protected $aldeiaModel;

    public function __construct()
    {
        $this->aldeiaModel = new AldeiaModel();
    }

    public function index()
    {
        if ($this->request->isAJAX()) {
            $start = $this->request->getGet('start');
            $length = $this->request->getGet('length');
            $search = $this->request->getGet('search[value]');
            
            $recordsTotal = $this->aldeiaModel->countAllResults();
            
            $builder = $this->aldeiaModel->builder();
            if (!empty($search)) {
                $builder->like('naran_aldeia', $search);
            }
            $recordsFiltered = $builder->countAllResults(false);
            
            $builder = $this->aldeiaModel->builder();
            if (!empty($search)) {
                $builder->like('naran_aldeia', $search);
            }
            $data = $builder->select('id_aldeia, naran_aldeia, id_suku')
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

        return view('admin/aldeia/index', [
            'title'    => 'Jestaun Aldeia',
            'subtitle' => 'Lista Aldeia iha Suku Laisorolai de Baixo',
        ]);
    }

    public function new()
    {
        return view('admin/aldeia/create', [
            'title'    => 'Aumenta Aldeia',
            'subtitle' => 'Kria Aldeia Foun',
        ]);
    }

    public function create()
    {
        $rules = [
            'naran_aldeia' => 'required|min_length[3]|max_length[100]|is_unique[tabela_aldeia.naran_aldeia]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $this->aldeiaModel->save([
            'naran_aldeia' => $this->request->getPost('naran_aldeia'),
            'id_suku'      => 1, // Default Laisorolai de Baixo
        ]);

        return redirect()->to('/admin/aldeia')->with('sweet-success', 'Aldeia foun aumenta ho susesu!');
    }

    public function edit($id = null)
    {
        $data = $this->aldeiaModel->find($id);
        if (!$data) {
            return redirect()->to('/admin/aldeia')->with('sweet-error', 'Dados Aldeia la hetan!');
        }

        return view('admin/aldeia/edit', [
            'title'    => 'Hadia Aldeia',
            'subtitle' => 'Hadia Dadus Aldeia',
            'aldeia'   => $data,
        ]);
    }

    public function update($id = null)
    {
        $rules = [
            'naran_aldeia' => "required|min_length[3]|max_length[100]|is_unique[tabela_aldeia.naran_aldeia,id_aldeia,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $this->aldeiaModel->update($id, [
            'naran_aldeia' => $this->request->getPost('naran_aldeia'),
        ]);

        return redirect()->to('/admin/aldeia')->with('sweet-success', 'Aldeia hadia ho susesu!');
    }

    public function delete($id = null)
    {
        if (!$this->aldeiaModel->find($id)) {
            return $this->failNotFound('Aldeia la hetan!');
        }

        $this->aldeiaModel->delete($id);

        return $this->respondDeleted(['status' => true], 'Aldeia delekado ho susesu!');
    }
}
