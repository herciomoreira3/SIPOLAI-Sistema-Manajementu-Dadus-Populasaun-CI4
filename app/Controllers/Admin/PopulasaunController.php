<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PopulasaunModel;
use App\Models\AldeiaModel;
use App\Models\ProfisaunModel;
use App\Models\RelijiaunModel;
use App\Models\LiteraturaModel;
use App\Models\FamiliaModel;
use CodeIgniter\API\ResponseTrait;

class PopulasaunController extends BaseController
{
    use ResponseTrait;

    protected $populasaunModel;
    protected $aldeiaModel;
    protected $profisaunModel;
    protected $relijiaunModel;
    protected $literaturaModel;
    protected $familiaModel;

    public function __construct()
    {
        $this->populasaunModel = new PopulasaunModel();
        $this->aldeiaModel = new AldeiaModel();
        $this->profisaunModel = new ProfisaunModel();
        $this->relijiaunModel = new RelijiaunModel();
        $this->literaturaModel = new LiteraturaModel();
        $this->familiaModel = new FamiliaModel();

        // Auto-fix Mene to Mane for data consistency
        $db = \Config\Database::connect();
        $db->simpleQuery("UPDATE tabela_populasaun SET jeneru = 'Mane' WHERE jeneru = 'Mene'");
    }

    public function index()
    {
        $type = $this->request->getGet('type') ?? 'all';

        if ($this->request->isAJAX()) {
            $start = $this->request->getGet('start');
            $length = $this->request->getGet('length');
            $search = $this->request->getGet('search[value]');
            $id_aldeia = $this->request->getGet('id_aldeia');
            $istadu = $this->request->getGet('istadu');
            
            $db = \Config\Database::connect();

            if ($type === 'estatutu_nascimentu') {
                $baseBuilder = function() use ($db, $id_aldeia) {
                    $builder = $db->table('tabela_pedidu');
                    $builder->where('tabela_pedidu.naran_pedidu', 'Deklarasaun Nascimentu');
                    $builder->where('tabela_pedidu.status', 'Aprovadu');
                    
                    if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                        $builder->where('tabela_pedidu.id_aldeia', user()->id_aldeia);
                    } elseif (!empty($id_aldeia)) {
                        $builder->where('tabela_pedidu.id_aldeia', $id_aldeia);
                    }
                    return $builder;
                };

                // recordsTotal
                $recordsTotal = $baseBuilder()->countAllResults();

                // recordsFiltered
                $filterBuilder = $baseBuilder();
                $filterBuilder->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_pedidu.id_aldeia', 'left');
                if (!empty($search)) {
                    $filterBuilder->groupStart()
                        ->like('tabela_pedidu.pemohon', $search)
                        ->orLike('tabela_aldeia.naran_aldeia', $search)
                        ->groupEnd();
                }
                $recordsFiltered = $filterBuilder->countAllResults();

                // fetch data
                $dataBuilder = $baseBuilder();
                $dataBuilder->select('tabela_pedidu.*, tabela_aldeia.naran_aldeia')
                    ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_pedidu.id_aldeia', 'left');
                if (!empty($search)) {
                    $dataBuilder->groupStart()
                        ->like('tabela_pedidu.pemohon', $search)
                        ->orLike('tabela_aldeia.naran_aldeia', $search)
                        ->groupEnd();
                }
                $data = $dataBuilder->limit($length, $start)->orderBy('tabela_pedidu.id_pedidu', 'desc')->get()->getResultArray();

                // Map and inject meta_data values
                foreach ($data as &$row) {
                    $row['jeneru'] = '-';
                    $row['data_moris'] = '-';
                    $row['fatin_moris'] = '-';
                    if (!empty($row['meta_data'])) {
                        $meta = json_decode($row['meta_data'], true);
                        if (is_array($meta)) {
                            $row['jeneru'] = $meta['jeneru'] ?? '-';
                            $row['data_moris'] = $meta['data_moris'] ?? '-';
                            $row['fatin_moris'] = $meta['fatin_moris'] ?? '-';
                        }
                    }
                }

                return $this->respond([
                    'draw'            => $this->request->getGet('draw'),
                    'recordsTotal'    => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data'            => $data,
                ]);
            }
            
            // 1. recordsTotal
            $totalBuilder = $db->table('tabela_populasaun');
            if ($type === 'moris' || $type === 'estatutu_moris') {
                $totalBuilder->where('tabela_populasaun.istadu', 'Moris');
            } elseif ($type === 'estatutu') {
                $totalBuilder->whereIn('tabela_populasaun.istadu', ['Mate', 'Muda']);
            } elseif ($type === 'estatutu_mate') {
                $totalBuilder->where('tabela_populasaun.istadu', 'Mate');
            } elseif ($type === 'estatutu_muda') {
                $totalBuilder->where('tabela_populasaun.istadu', 'Muda');
            }
            if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                $totalBuilder->where('tabela_populasaun.id_aldeia', user()->id_aldeia);
            }
            if (!empty($id_aldeia)) {
                $totalBuilder->where('tabela_populasaun.id_aldeia', $id_aldeia);
            }
            if (!empty($istadu)) {
                $totalBuilder->where('tabela_populasaun.istadu', $istadu);
            }
            $recordsTotal = $totalBuilder->countAllResults();
            
            // 2. recordsFiltered
            $filterBuilder = $db->table('tabela_populasaun');
            $filterBuilder->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left');
            if ($type === 'moris' || $type === 'estatutu_moris') {
                $filterBuilder->where('tabela_populasaun.istadu', 'Moris');
            } elseif ($type === 'estatutu') {
                $filterBuilder->whereIn('tabela_populasaun.istadu', ['Mate', 'Muda']);
            } elseif ($type === 'estatutu_mate') {
                $filterBuilder->where('tabela_populasaun.istadu', 'Mate');
            } elseif ($type === 'estatutu_muda') {
                $filterBuilder->where('tabela_populasaun.istadu', 'Muda');
            }
            if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                $filterBuilder->where('tabela_populasaun.id_aldeia', user()->id_aldeia);
            }
            if (!empty($id_aldeia)) {
                $filterBuilder->where('tabela_populasaun.id_aldeia', $id_aldeia);
            }
            if (!empty($istadu)) {
                $filterBuilder->where('tabela_populasaun.istadu', $istadu);
            }
            if (!empty($search)) {
                $filterBuilder->groupStart()
                    ->like('tabela_populasaun.naran_kompletu', $search)
                    ->orLike('tabela_populasaun.nik', $search)
                    ->orLike('tabela_aldeia.naran_aldeia', $search)
                    ->groupEnd();
            }
            $recordsFiltered = $filterBuilder->countAllResults();
            
            // 3. fetch data
            $dataBuilder = $db->table('tabela_populasaun');
            $dataBuilder->select('tabela_populasaun.*, tabela_aldeia.naran_aldeia, tabela_profisaun.naran_profisaun, tabela_relijiaun.naran_relijiaun, tabela_literatura.naran_literatura')
                ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left')
                ->join('tabela_profisaun', 'tabela_profisaun.id_profisaun = tabela_populasaun.id_profisaun', 'left')
                ->join('tabela_relijiaun', 'tabela_relijiaun.id_relijiaun = tabela_populasaun.id_relijiaun', 'left')
                ->join('tabela_literatura', 'tabela_literatura.id_literatura = tabela_populasaun.id_literatura', 'left');
            if ($type === 'moris' || $type === 'estatutu_moris') {
                $dataBuilder->where('tabela_populasaun.istadu', 'Moris');
            } elseif ($type === 'estatutu') {
                $dataBuilder->whereIn('tabela_populasaun.istadu', ['Mate', 'Muda']);
            } elseif ($type === 'estatutu_mate') {
                $dataBuilder->where('tabela_populasaun.istadu', 'Mate');
            } elseif ($type === 'estatutu_muda') {
                $dataBuilder->where('tabela_populasaun.istadu', 'Muda');
            }
            if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                $dataBuilder->where('tabela_populasaun.id_aldeia', user()->id_aldeia);
            }
            if (!empty($id_aldeia)) {
                $dataBuilder->where('tabela_populasaun.id_aldeia', $id_aldeia);
            }
            if (!empty($istadu)) {
                $dataBuilder->where('tabela_populasaun.istadu', $istadu);
            }
            if (!empty($search)) {
                $dataBuilder->groupStart()
                    ->like('tabela_populasaun.naran_kompletu', $search)
                    ->orLike('tabela_populasaun.nik', $search)
                    ->orLike('tabela_aldeia.naran_aldeia', $search)
                    ->groupEnd();
            }
            $data = $dataBuilder->limit($length, $start)->get()->getResultArray();

            if ($type === 'estatutu_mate') {
                foreach ($data as &$row) {
                    $row['data_mate'] = '-';
                    $pedidu = $db->table('tabela_pedidu')
                        ->where('pemohon', $row['naran_kompletu'])
                        ->where('naran_pedidu', 'Deklarasaun Mortalidade')
                        ->where('status', 'Aprovadu')
                        ->orderBy('id_pedidu', 'desc')
                        ->limit(1)
                        ->get()->getRowArray();
                    if ($pedidu) {
                        if (!empty($pedidu['meta_data'])) {
                            $meta = json_decode($pedidu['meta_data'], true);
                            if (!empty($meta['data_mate'])) {
                                $row['data_mate'] = $meta['data_mate'];
                            } else {
                                $row['data_mate'] = $pedidu['data_pedidu'];
                            }
                        } else {
                            $row['data_mate'] = $pedidu['data_pedidu'];
                        }
                    }
                }
            }

            if ($type === 'estatutu_muda') {
                foreach ($data as &$row) {
                    $row['data_muda'] = '-';
                    $pedidu = $db->table('tabela_pedidu')
                        ->where('pemohon', $row['naran_kompletu'])
                        ->where('naran_pedidu', 'Deklarasaun Muda Domisiliu')
                        ->where('status', 'Aprovadu')
                        ->orderBy('id_pedidu', 'desc')
                        ->limit(1)
                        ->get()->getRowArray();
                    if ($pedidu) {
                        $row['data_muda'] = $pedidu['data_pedidu'];
                    }
                }
            }

            return $this->respond([
                'draw'            => $this->request->getGet('draw'),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ]);
        }

        $title = 'Jestaun Populasaun';
        $subtitle = 'Dadus Kompletu Populasaun Suku Laisorolai de Baixo';

        if ($type === 'moris') {
            $title = 'Jestaun Moris';
            $subtitle = 'Dadus Populasaun Laisorolai de Baixo ne\'ebé Moris';
        } elseif ($type === 'estatutu') {
            $db = \Config\Database::connect();
            $aldeias = $this->aldeiaModel->findAll();
            if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
            }
            
            // Calculate premium dashboard stats
            $statNascimentu = $db->table('tabela_pedidu')->where('naran_pedidu', 'Deklarasaun Nascimentu')->where('status', 'Aprovadu')->countAllResults();
            $statMoris = $db->table('tabela_populasaun')->where('istadu', 'Moris')->countAllResults();
            $statMate = $db->table('tabela_populasaun')->where('istadu', 'Mate')->countAllResults();
            $statMuda = $db->table('tabela_populasaun')->where('istadu', 'Muda')->countAllResults();

            return view('admin/populasaun/estatutu', [
                'title'           => 'Jestaun Estatutu Populasaun',
                'subtitle'        => 'Jestaun Estatutu Moris, Mate ka Muda ba Populasaun',
                'type'            => $type,
                'aldeias'         => $aldeias,
                'statNascimentu' => $statNascimentu,
                'statMoris'       => $statMoris,
                'statMate'        => $statMate,
                'statMuda'        => $statMuda
            ]);
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        return view('admin/populasaun/index', [
            'title'    => $title,
            'subtitle' => $subtitle,
            'type'     => $type,
            'aldeias'  => $aldeias
        ]);
    }

    public function new()
    {
        $aldeias = $this->aldeiaModel->findAll();
        // If Xefe Aldeia, limit to their assigned Aldeia
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        return view('admin/populasaun/create', [
            'title'      => 'Aumenta Populasaun',
            'subtitle'   => 'Rejista Populasaun Foun',
            'aldeias'    => $aldeias,
            'profisaun'  => $this->profisaunModel->findAll(),
            'relijiaun'  => $this->relijiaunModel->findAll(),
            'literatura' => $this->literaturaModel->findAll(),
            'familias'   => $this->familiaModel->findAll(),
        ]);
    }

    public function create()
    {
        $rules = [
            'naran_kompletu' => 'required|min_length[3]|max_length[150]',
            'fatin_moris'    => 'required|min_length[2]|max_length[100]',
            'data_moris'     => 'required|valid_date[Y-m-d]',
            'jeneru'         => 'required|in_list[Mane,Feto]',
            'status_kaza'    => 'required|in_list[Solteiru/a,Kabe-Nain,Faluk]',
            'id_aldeia'      => 'required|is_not_unique[tabela_aldeia.id_aldeia]',
            'id_profisaun'   => 'required|is_not_unique[tabela_profisaun.id_profisaun]',
            'id_relijiaun'   => 'required|is_not_unique[tabela_relijiaun.id_relijiaun]',
            'id_literatura'  => 'required|is_not_unique[tabela_literatura.id_literatura]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $jeneru = $this->request->getPost('jeneru');
        $relasaun_familia = $this->request->getPost('relasaun_familia');

        if ($relasaun_familia === 'Xefe Familia' && $jeneru !== 'Mane') {
            return redirect()->back()->withInput()->with('error', 'Xefe Familia tenki Mane de\'it!');
        }

        $nip = $this->generateUniqueNip();

        $this->populasaunModel->save([
            'nik'              => $nip,
            'naran_kompletu'   => $this->request->getPost('naran_kompletu'),
            'fatin_moris'      => $this->request->getPost('fatin_moris'),
            'data_moris'       => $this->request->getPost('data_moris'),
            'jeneru'           => $this->request->getPost('jeneru'),
            'status_kaza'      => $this->request->getPost('status_kaza'),
            'id_aldeia'        => $this->request->getPost('id_aldeia'),
            'id_profisaun'     => $this->request->getPost('id_profisaun'),
            'id_relijiaun'     => $this->request->getPost('id_relijiaun'),
            'id_literatura'    => $this->request->getPost('id_literatura'),
            'id_familia'       => $this->request->getPost('id_familia') ?: null,
            'relasaun_familia' => $this->request->getPost('relasaun_familia') ?: null,
            'istadu'           => 'Moris',
        ]);

        return redirect()->to('/admin/populasaun')->with('sweet-success', 'Wainhira populasaun foun aumenta ho susesu!');
    }

    public function edit($id = null)
    {
        $data = $this->populasaunModel->find($id);
        if (!$data) {
            return redirect()->to('/admin/populasaun')->with('sweet-error', 'Dados populasaun la hetan!');
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        return view('admin/populasaun/edit', [
            'title'      => 'Hadia Populasaun',
            'subtitle'   => 'Hadia Dadus Populasaun',
            'populasaun' => $data,
            'aldeias'    => $aldeias,
            'profisaun'  => $this->profisaunModel->findAll(),
            'relijiaun'  => $this->relijiaunModel->findAll(),
            'literatura' => $this->literaturaModel->findAll(),
            'familias'   => $this->familiaModel->findAll(),
        ]);
    }

    public function update($id = null)
    {
        $rules = [
            'nik'            => "required|is_unique[tabela_populasaun.nik,id_populasaun,{$id}]",
            'naran_kompletu' => 'required|min_length[3]|max_length[150]',
            'fatin_moris'    => 'required|min_length[2]|max_length[100]',
            'data_moris'     => 'required|valid_date[Y-m-d]',
            'jeneru'         => 'required|in_list[Mane,Feto]',
            'status_kaza'    => 'required|in_list[Solteiru/a,Kabe-Nain,Faluk]',
            'id_aldeia'      => 'required|is_not_unique[tabela_aldeia.id_aldeia]',
            'id_profisaun'   => 'required|is_not_unique[tabela_profisaun.id_profisaun]',
            'id_relijiaun'   => 'required|is_not_unique[tabela_relijiaun.id_relijiaun]',
            'id_literatura'  => 'required|is_not_unique[tabela_literatura.id_literatura]',
            'istadu'         => 'required|in_list[Moris,Mate,Muda]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $jeneru = $this->request->getPost('jeneru');
        $relasaun_familia = $this->request->getPost('relasaun_familia');

        if ($relasaun_familia === 'Xefe Familia' && $jeneru !== 'Mane') {
            return redirect()->back()->withInput()->with('error', 'Xefe Familia tenki Mane de\'it!');
        }

        $this->populasaunModel->update($id, [
            'nik'              => $this->request->getPost('nik'),
            'naran_kompletu'   => $this->request->getPost('naran_kompletu'),
            'fatin_moris'      => $this->request->getPost('fatin_moris'),
            'data_moris'       => $this->request->getPost('data_moris'),
            'jeneru'           => $this->request->getPost('jeneru'),
            'status_kaza'      => $this->request->getPost('status_kaza'),
            'id_aldeia'        => $this->request->getPost('id_aldeia'),
            'id_profisaun'     => $this->request->getPost('id_profisaun'),
            'id_relijiaun'     => $this->request->getPost('id_relijiaun'),
            'id_literatura'    => $this->request->getPost('id_literatura'),
            'id_familia'       => $this->request->getPost('id_familia') ?: null,
            'relasaun_familia' => $this->request->getPost('relasaun_familia') ?: null,
            'istadu'           => $this->request->getPost('istadu'),
        ]);

        return redirect()->to('/admin/populasaun')->with('sweet-success', 'Dadus populasaun hadia ho susesu!');
    }

    public function updateStatus($id = null)
    {
        // Only admin, xefe-suku, or sekretaria can change the status
        if (!in_groups(['admin', 'xefe-suku', 'sekretaria'])) {
            return $this->failForbidden('Ita boot la iha kbiit/autorizasaun atu muda estatutu populasaun!');
        }

        $istadu = $this->request->getPost('istadu');
        if (!in_array($istadu, ['Moris', 'Mate', 'Muda'])) {
            return $this->fail('Estatutu la loos!');
        }

        $data = $this->populasaunModel->find($id);
        if (!$data) {
            return $this->failNotFound('Populasaun la hetan!');
        }

        $this->populasaunModel->update($id, ['istadu' => $istadu]);

        return $this->respond(['status' => true, 'message' => "Estatutu populasaun mudadu ba {$istadu} ho susesu!"]);
    }

    public function delete($id = null)
    {
        if (!$this->populasaunModel->find($id)) {
            return $this->failNotFound('Populasaun la hetan!');
        }

        $this->populasaunModel->delete($id);

        return $this->respondDeleted(['status' => true], 'Populasaun delekado ho susesu!');
    }

    private function generateUniqueNip()
    {
        $db = \Config\Database::connect();
        do {
            // Generate a random 12-digit number for unique NIP
            $nip = mt_rand(100000, 999999) . mt_rand(100000, 999999);
            $exists = $db->table('tabela_populasaun')->where('nik', $nip)->countAllResults();
        } while ($exists > 0);
        return $nip;
    }
}
