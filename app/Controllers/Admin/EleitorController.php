<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PopulasaunModel;
use App\Models\AldeiaModel;
use CodeIgniter\API\ResponseTrait;

class EleitorController extends BaseController
{
    use ResponseTrait;

    protected $populasaunModel;
    protected $aldeiaModel;

    public function __construct()
    {
        $this->populasaunModel = new PopulasaunModel();
        $this->aldeiaModel     = new AldeiaModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('tabela_populasaun p')
            ->select('p.id_populasaun, p.nik, p.naran_kompletu, p.jeneru, p.no_eleitoral, p.istadu, p.id_aldeia, a.naran_aldeia, tp.data_pedidu as data_aprovada')
            ->join('tabela_aldeia a', 'a.id_aldeia = p.id_aldeia', 'left')
            ->join('tabela_pedidu tp', '(tp.id_populasaun = p.id_populasaun OR (tp.id_populasaun IS NULL AND tp.pemohon = p.naran_kompletu AND tp.id_aldeia = p.id_aldeia)) AND tp.naran_pedidu = \'Deklarasaun Eleitoral\' AND tp.status = \'Aprovadu\'', 'left', false)
            ->where('p.istadu', 'Moris')
            ->groupStart()
                ->where('tp.id_pedidu IS NOT NULL')
                ->orWhere('p.no_eleitoral IS NOT NULL')
                ->orWhere("p.no_eleitoral != ''")
            ->groupEnd()
            ->groupBy('p.id_populasaun')
            ->orderBy('p.naran_kompletu', 'ASC');

        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $builder->where('p.id_aldeia', user()->id_aldeia);
        }

        $eleitores = $builder->get()->getResultArray();

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        return view('admin/eleitor/index', [
            'title'     => 'Dados Eleitores',
            'subtitle'  => 'Dadus Populasaun ne\'ebé hetan ona Deklarasaun Eleitoral husi Suku no halo ona Kartaun Eleitoral',
            'aldeias'   => $aldeias,
            'eleitores' => $eleitores,
        ]);
    }

    public function update($id = null)
    {
        if (!in_groups(['admin', 'xefe-suku', 'sekretaria'])) {
            return $this->failForbidden('Ita boot la iha kbiit/autorizasaun atu atualiza dadus eleitor!');
        }

        $populasaun = $this->populasaunModel->find($id);
        if (!$populasaun) {
            return $this->failNotFound('Dadus populasaun la hetan!');
        }
        if (! $this->canAccessAldeia($populasaun['id_aldeia'])) {
            return $this->failForbidden('Ita boot labele atualiza eleitor husi aldeia seluk.');
        }

        $noEleitoral = trim((string) $this->request->getPost('no_eleitoral'));
        if ($noEleitoral !== '' && strlen($noEleitoral) > 50) {
            return $this->fail('Numeiru Kartaun Eleitoral labele liu karakter 50.');
        }
        if ($noEleitoral !== '') {
            $exists = $this->populasaunModel
                ->where('no_eleitoral', $noEleitoral)
                ->where('id_populasaun !=', $id)
                ->first();
            if ($exists) {
                return $this->fail('Numeiru Kartaun Eleitoral nee uza ona husi sidadaun seluk.');
            }
        }

        $this->populasaunModel->update($id, [
            'no_eleitoral' => $noEleitoral !== '' ? $noEleitoral : null
        ]);

        return $this->respond([
            'status'  => true,
            'message' => 'Numeiru Kartaun Eleitoral atualizadu ho susesu!'
        ]);
    }
}
