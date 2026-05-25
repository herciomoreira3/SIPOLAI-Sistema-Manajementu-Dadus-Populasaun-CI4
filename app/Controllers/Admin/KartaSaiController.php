<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KartaSaiModel;

class KartaSaiController extends BaseController
{
    protected $kartaModel;

    public function __construct()
    {
        $this->kartaModel = new KartaSaiModel();
    }

    public function index()
    {
        $data = [
            'title'    => 'Karta Sai',
            'subtitle' => 'Jestaun Karta Sai (Outgoing Letters) Suku Laisorolai de Baixo',
            'kartas'   => $this->kartaModel->findAll()
        ];

        return view('admin/karta/sai_index', $data);
    }

    public function new()
    {
        $data = [
            'title'    => 'Rejista Karta Sai Foun',
            'subtitle' => 'Kria dadus karta sai foun'
        ];

        return view('admin/karta/sai_create', $data);
    }

    public function create()
    {
        $rules = [
            'numeru_karta' => 'required',
            'destinatariu' => 'required',
            'asuntu'       => 'required',
            'data_sai'     => 'required|valid_date[Y-m-d]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kartaModel->save([
            'numeru_karta' => $this->request->getPost('numeru_karta'),
            'destinatariu' => $this->request->getPost('destinatariu'),
            'asuntu'       => $this->request->getPost('asuntu'),
            'data_sai'     => $this->request->getPost('data_sai')
        ]);

        return redirect()->to('admin/karta-sai')->with('message', 'Karta sai foun rejistadu ho susesu!');
    }

    public function edit($id = null)
    {
        $karta = $this->kartaModel->find($id);
        if (!$karta) {
            return redirect()->to('admin/karta-sai')->with('error', 'Karta sai la hetan!');
        }

        $data = [
            'title'    => 'Hadia Karta Sai',
            'subtitle' => 'Hadia dadus karta sai',
            'karta'    => $karta
        ];

        return view('admin/karta/sai_edit', $data);
    }

    public function update($id = null)
    {
        $rules = [
            'numeru_karta' => 'required',
            'destinatariu' => 'required',
            'asuntu'       => 'required',
            'data_sai'     => 'required|valid_date[Y-m-d]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kartaModel->update($id, [
            'numeru_karta' => $this->request->getPost('numeru_karta'),
            'destinatariu' => $this->request->getPost('destinatariu'),
            'asuntu'       => $this->request->getPost('asuntu'),
            'data_sai'     => $this->request->getPost('data_sai')
        ]);

        return redirect()->to('admin/karta-sai')->with('message', 'Dadus karta sai aktualizadu ho susesu!');
    }

    public function delete($id = null)
    {
        $this->kartaModel->delete($id);
        return redirect()->to('admin/karta-sai')->with('message', 'Dadus karta sai hasai ho susesu!');
    }
}
