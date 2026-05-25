<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TipuPediduModel;

class TipuPediduController extends BaseController
{
    protected $tipuModel;

    public function __construct()
    {
        $this->tipuModel = new TipuPediduModel();
    }

    public function index()
    {
        $data = [
            'title'    => 'Tipu Pedidu',
            'subtitle' => 'Jestaun Tipu Karta Pedidu Suku Laisorolai de Baixo',
            'tipus'    => $this->tipuModel->findAll()
        ];

        return view('admin/pedidu/tipu_index', $data);
    }

    public function new()
    {
        $data = [
            'title'    => 'Aumenta Tipu Pedidu',
            'subtitle' => 'Kria Tipu Pedidu foun'
        ];

        return view('admin/pedidu/tipu_create', $data);
    }

    public function create()
    {
        $rules = [
            'naran_tipu_pedidu' => 'required|min_length[3]|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->tipuModel->save([
            'naran_tipu_pedidu' => $this->request->getPost('naran_tipu_pedidu'),
            'template_formatu'  => $this->request->getPost('template_formatu') ?: ''
        ]);

        return redirect()->to('admin/tipu-pedidu')->with('message', 'Tipu Pedidu foun kria ho susesu!');
    }

    public function edit($id = null)
    {
        $tipu = $this->tipuModel->find($id);
        if (!$tipu) {
            return redirect()->to('admin/tipu-pedidu')->with('error', 'Tipu Pedidu la hetan!');
        }

        $data = [
            'title'    => 'Hadia Tipu Pedidu',
            'subtitle' => 'Hadia dadus Tipu Pedidu',
            'tipu'     => $tipu
        ];

        return view('admin/pedidu/tipu_edit', $data);
    }

    public function update($id = null)
    {
        $rules = [
            'naran_tipu_pedidu' => 'required|min_length[3]|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->tipuModel->update($id, [
            'naran_tipu_pedidu' => $this->request->getPost('naran_tipu_pedidu'),
            'template_formatu'  => $this->request->getPost('template_formatu') ?: ''
        ]);

        return redirect()->to('admin/tipu-pedidu')->with('message', 'Tipu Pedidu aktualizadu ho susesu!');
    }

    public function delete($id = null)
    {
        $this->tipuModel->delete($id);
        return redirect()->to('admin/tipu-pedidu')->with('message', 'Tipu Pedidu hasai ho susesu!');
    }

    // Formatu Deklarasaun (Only Admin)
    public function formatuIndex()
    {
        $data = [
            'title'    => 'Formatu Deklarasaun',
            'subtitle' => 'Konfigurasaun Formatu Deklarasaun (Template)',
            'tipus'    => $this->tipuModel->findAll()
        ];

        return view('admin/pedidu/formatu_index', $data);
    }

    public function formatuEdit($id = null)
    {
        $tipu = $this->tipuModel->find($id);
        if (!$tipu) {
            return redirect()->to('admin/formatu-deklarasaun')->with('error', 'Formatu Deklarasaun la hetan!');
        }

        $data = [
            'title'    => 'Konfigura Formatu Deklarasaun',
            'subtitle' => 'Hadia template formatu ba ' . esc($tipu['naran_tipu_pedidu']),
            'tipu'     => $tipu
        ];

        return view('admin/pedidu/formatu_edit', $data);
    }

    public function formatuUpdate($id = null)
    {
        $this->tipuModel->update($id, [
            'template_formatu' => $this->request->getPost('template_formatu')
        ]);

        return redirect()->to('admin/formatu-deklarasaun')->with('message', 'Template formatu deklarasaun aktualizadu ho susesu!');
    }
}
