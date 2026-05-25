<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KartaTamaModel;

class KartaTamaController extends BaseController
{
    protected $kartaModel;

    public function __construct()
    {
        $this->kartaModel = new KartaTamaModel();
    }

    public function index()
    {
        $data = [
            'title'    => 'Karta Tama',
            'subtitle' => 'Jestaun Karta Tama (Incoming Letters) Suku Laisorolai de Baixo',
            'kartas'   => $this->kartaModel->findAll()
        ];

        return view('admin/karta/tama_index', $data);
    }

    public function new()
    {
        $data = [
            'title'    => 'Rejista Karta Tama Foun',
            'subtitle' => 'Kria dadus karta tama foun'
        ];

        return view('admin/karta/tama_create', $data);
    }

    public function create()
    {
        $rules = [
            'numeru_karta' => 'required',
            'emitente'     => 'required',
            'asuntu'       => 'required',
            'data_tama'    => 'required|valid_date[Y-m-d]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kartaModel->save([
            'numeru_karta' => $this->request->getPost('numeru_karta'),
            'emitente'     => $this->request->getPost('emitente'),
            'asuntu'       => $this->request->getPost('asuntu'),
            'data_tama'    => $this->request->getPost('data_tama')
        ]);

        return redirect()->to('admin/karta-tama')->with('message', 'Karta tama foun rejistadu ho susesu!');
    }

    public function edit($id = null)
    {
        $karta = $this->kartaModel->find($id);
        if (!$karta) {
            return redirect()->to('admin/karta-tama')->with('error', 'Karta tama la hetan!');
        }

        $data = [
            'title'    => 'Hadia Karta Tama',
            'subtitle' => 'Hadia dadus karta tama',
            'karta'    => $karta
        ];

        return view('admin/karta/tama_edit', $data);
    }

    public function update($id = null)
    {
        $rules = [
            'numeru_karta' => 'required',
            'emitente'     => 'required',
            'asuntu'       => 'required',
            'data_tama'    => 'required|valid_date[Y-m-d]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kartaModel->update($id, [
            'numeru_karta' => $this->request->getPost('numeru_karta'),
            'emitente'     => $this->request->getPost('emitente'),
            'asuntu'       => $this->request->getPost('asuntu'),
            'data_tama'    => $this->request->getPost('data_tama')
        ]);

        return redirect()->to('admin/karta-tama')->with('message', 'Dadus karta tama aktualizadu ho susesu!');
    }

    public function delete($id = null)
    {
        $this->kartaModel->delete($id);
        return redirect()->to('admin/karta-tama')->with('message', 'Dadus karta tama hasai ho susesu!');
    }
}
