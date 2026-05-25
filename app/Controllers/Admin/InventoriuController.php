<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class InventoriuController extends BaseController
{
    public function index()
    {
        $data = [
            'title'    => 'Inventoriu Deklarasaun',
            'subtitle' => 'Sentru Jestaun Inventoriu Deklarasaun Suku Laisorolai de Baixo'
        ];

        return view('admin/inventoriu/index', $data);
    }
}
