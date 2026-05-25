<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LiteraturaModel;
use CodeIgniter\API\ResponseTrait;

class LiteraturaController extends BaseController
{
    use ResponseTrait;

    protected $literaturaModel;

    public function __construct()
    {
        $this->literaturaModel = new LiteraturaModel();
    }

    public function index()
    {
        if ($this->request->isAJAX()) {
            $start = $this->request->getGet('start');
            $length = $this->request->getGet('length');
            $search = $this->request->getGet('search[value]');
            
            $recordsTotal = $this->literaturaModel->countAllResults();
            
            $builder = $this->literaturaModel->builder();
            if (!empty($search)) {
                $builder->like('naran_literatura', $search);
            }
            $recordsFiltered = $builder->countAllResults(false);
            
            $builder = $this->literaturaModel->builder();
            if (!empty($search)) {
                $builder->like('naran_literatura', $search);
            }
            $data = $builder->select('id_literatura, naran_literatura')
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

        return view('admin/literatura/index', [
            'title'    => 'Jestaun Literatura',
            'subtitle' => 'Lista Literatura/Edukasaun iha Suku Laisorolai de Baixo',
        ]);
    }

    public function new()
    {
        return view('admin/literatura/create', [
            'title'    => 'Aumenta Literatura',
            'subtitle' => 'Kria Literatura Foun',
        ]);
    }

    public function create()
    {
        $rules = [
            'naran_literatura' => 'required|min_length[3]|max_length[100]|is_unique[tabela_literatura.naran_literatura]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $this->literaturaModel->save([
            'naran_literatura' => $this->request->getPost('naran_literatura'),
        ]);

        return redirect()->to('/admin/literatura')->with('sweet-success', 'Literatura foun aumenta ho susesu!');
    }

    public function edit($id = null)
    {
        $data = $this->literaturaModel->find($id);
        if (!$data) {
            return redirect()->to('/admin/literatura')->with('sweet-error', 'Dados Literatura la hetan!');
        }

        return view('admin/literatura/edit', [
            'title'    => 'Hadia Literatura',
            'subtitle' => 'Hadia Dadus Literatura',
            'literatura'=> $data,
        ]);
    }

    public function update($id = null)
    {
        $rules = [
            'naran_literatura' => "required|min_length[3]|max_length[100]|is_unique[tabela_literatura.naran_literatura,id_literatura,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $this->literaturaModel->update($id, [
            'naran_literatura' => $this->request->getPost('naran_literatura'),
        ]);

        return redirect()->to('/admin/literatura')->with('sweet-success', 'Literatura hadia ho susesu!');
    }

    public function delete($id = null)
    {
        if (!$this->literaturaModel->find($id)) {
            return $this->failNotFound('Literatura la hetan!');
        }

        $this->literaturaModel->delete($id);

        return $this->respondDeleted(['status' => true], 'Literatura delekado ho susesu!');
    }
}
