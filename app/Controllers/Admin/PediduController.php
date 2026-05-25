<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PediduModel;
use App\Models\AldeiaModel;
use App\Models\PopulasaunModel;
use CodeIgniter\API\ResponseTrait;

class PediduController extends BaseController
{
    use ResponseTrait;

    protected $pediduModel;
    protected $aldeiaModel;
    protected $populasaunModel;

    public function __construct()
    {
        $this->pediduModel = new PediduModel();
        $this->aldeiaModel = new AldeiaModel();
        $this->populasaunModel = new PopulasaunModel();
    }

    public function index()
    {
        $naran_pedidu = $this->request->getGet('naran_pedidu');

        if ($this->request->isAJAX()) {
            $start = $this->request->getGet('start');
            $length = $this->request->getGet('length');
            $search = $this->request->getGet('search[value]');
            $status_filter = $this->request->getGet('status_filter');
            
            $db = \Config\Database::connect();
            
            // Set up base status conditions
            if (empty($naran_pedidu)) {
                // Jestaun Pedidu: show ONLY Pendiente
                $status_condition = ['Pendiente'];
            } else {
                // Inventoriu: show Aprovadu and/or Rezeitadu
                if (!empty($status_filter)) {
                    $status_condition = [$status_filter];
                } else {
                    $status_condition = ['Aprovadu', 'Rezeitadu'];
                }
            }
            
            // 1. recordsTotal
            $totalBuilder = $db->table('tabela_pedidu');
            if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                $totalBuilder->where('tabela_pedidu.id_aldeia', user()->id_aldeia);
            }
            if (!empty($naran_pedidu)) {
                $totalBuilder->where('tabela_pedidu.naran_pedidu', $naran_pedidu);
            }
            $totalBuilder->whereIn('tabela_pedidu.status', $status_condition);
            $recordsTotal = $totalBuilder->countAllResults();
            
            // 2. recordsFiltered
            $filterBuilder = $db->table('tabela_pedidu');
            $filterBuilder->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_pedidu.id_aldeia', 'left');
            if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                $filterBuilder->where('tabela_pedidu.id_aldeia', user()->id_aldeia);
            }
            if (!empty($naran_pedidu)) {
                $filterBuilder->where('tabela_pedidu.naran_pedidu', $naran_pedidu);
            }
            $filterBuilder->whereIn('tabela_pedidu.status', $status_condition);
            if (!empty($search)) {
                $filterBuilder->groupStart()
                    ->like('tabela_pedidu.naran_pedidu', $search)
                    ->orLike('tabela_pedidu.pemohon', $search)
                    ->orLike('tabela_aldeia.naran_aldeia', $search)
                    ->groupEnd();
            }
            $recordsFiltered = $filterBuilder->countAllResults();
            
            // 3. fetch data
            $dataBuilder = $db->table('tabela_pedidu');
            $dataBuilder->select('tabela_pedidu.*, tabela_aldeia.naran_aldeia')
                ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_pedidu.id_aldeia', 'left');
            if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                $dataBuilder->where('tabela_pedidu.id_aldeia', user()->id_aldeia);
            }
            if (!empty($naran_pedidu)) {
                $dataBuilder->where('tabela_pedidu.naran_pedidu', $naran_pedidu);
            }
            $dataBuilder->whereIn('tabela_pedidu.status', $status_condition);
            if (!empty($search)) {
                $dataBuilder->groupStart()
                    ->like('tabela_pedidu.naran_pedidu', $search)
                    ->orLike('tabela_pedidu.pemohon', $search)
                    ->orLike('tabela_aldeia.naran_aldeia', $search)
                    ->groupEnd();
            }
            $data = $dataBuilder->limit($length, $start)->get()->getResultArray();

            return $this->respond([
                'draw'            => $this->request->getGet('draw'),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ]);
        }

        $title = 'Jestaun Pedidu';
        $subtitle = 'Lista Permohonan Surat / Pedidu Suku Laisorolai de Baixo';

        if (!empty($naran_pedidu)) {
            $title = 'Inventoriu ' . $naran_pedidu;
            $subtitle = 'Lista Inventoriu husi ' . $naran_pedidu;
        }

        return view('admin/pedidu/index', [
            'title'        => $title,
            'subtitle'     => $subtitle,
            'naran_pedidu' => $naran_pedidu
        ]);
    }

    public function new()
    {
        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $tipuModel = new \App\Models\TipuPediduModel();
        $tipus = $tipuModel->findAll();

        $profisaunModel = new \App\Models\ProfisaunModel();
        $relijiaunModel = new \App\Models\RelijiaunModel();
        $literaturaModel = new \App\Models\LiteraturaModel();

        return view('admin/pedidu/create', [
            'title'      => 'Kria Pedidu',
            'subtitle'   => 'Kria Permohonan Surat Foun',
            'aldeias'    => $aldeias,
            'tipus'      => $tipus,
            'profisaun'  => $profisaunModel->findAll(),
            'relijiaun'  => $relijiaunModel->findAll(),
            'literatura' => $literaturaModel->findAll(),
        ]);
    }

    public function create()
    {
        $rules = [
            'naran_pedidu' => 'required|min_length[3]|max_length[150]',
            'pemohon'      => 'required|min_length[3]|max_length[150]',
            'data_pedidu'  => 'required|valid_date[Y-m-d]',
            'id_aldeia'    => 'required|is_not_unique[tabela_aldeia.id_aldeia]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $this->pediduModel->save([
            'naran_pedidu' => $this->request->getPost('naran_pedidu'),
            'pemohon'      => $this->request->getPost('pemohon'),
            'data_pedidu'  => $this->request->getPost('data_pedidu'),
            'id_aldeia'    => $this->request->getPost('id_aldeia'),
            'status'       => 'Pendiente', // Status default wainhira kria foun
        ]);

        return redirect()->to('/admin/pedidu')->with('sweet-success', 'Pedidu foun kria ho susesu! Hein aprovasaun husi Xefe Suku.');
    }

    public function edit($id = null)
    {
        $data = $this->pediduModel->find($id);
        if (!$data) {
            return redirect()->to('/admin/pedidu')->with('sweet-error', 'Dados pedidu la hetan!');
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $tipuModel = new \App\Models\TipuPediduModel();
        $tipus = $tipuModel->findAll();

        $profisaunModel = new \App\Models\ProfisaunModel();
        $relijiaunModel = new \App\Models\RelijiaunModel();
        $literaturaModel = new \App\Models\LiteraturaModel();

        $meta = [];
        if ($data['naran_pedidu'] === 'Deklarasaun Nascimentu' && !empty($data['meta_data'])) {
            $meta = json_decode($data['meta_data'], true);
        }

        $familias = [];
        if ($data['naran_pedidu'] === 'Deklarasaun Nascimentu') {
            $db = \Config\Database::connect();
            $xefeSub = "(SELECT tp.naran_kompletu FROM tabela_populasaun tp WHERE tp.id_familia = tabela_familia.id_familia AND tp.relasaun_familia = 'Xefe Familia' LIMIT 1) as naran_xefe";
            $familias = $db->table('tabela_familia')
                ->select("tabela_familia.*, {$xefeSub}")
                ->get()->getResultArray();
        }

        return view('admin/pedidu/edit', [
            'title'      => 'Hadia Pedidu',
            'subtitle'   => 'Hadia Dadus Pedidu',
            'pedidu'     => $data,
            'meta'       => $meta,
            'aldeias'    => $aldeias,
            'tipus'      => $tipus,
            'profisaun'  => $profisaunModel->findAll(),
            'relijiaun'  => $relijiaunModel->findAll(),
            'literatura' => $literaturaModel->findAll(),
            'familias'   => $familias,
        ]);
    }

    public function update($id = null)
    {
        $rules = [
            'naran_pedidu' => 'required|min_length[3]|max_length[150]',
            'pemohon'      => 'required|min_length[3]|max_length[150]',
            'data_pedidu'  => 'required|valid_date[Y-m-d]',
            'id_aldeia'    => 'required|is_not_unique[tabela_aldeia.id_aldeia]',
        ];

        $naran_pedidu = $this->request->getPost('naran_pedidu');

        if ($naran_pedidu === 'Deklarasaun Nascimentu') {
            $rules['id_familia']    = 'required|is_not_unique[tabela_familia.id_familia]';
            $rules['jeneru']        = 'required|in_list[Mane,Feto]';
            $rules['fatin_moris']   = 'required';
            $rules['data_moris']    = 'required|valid_date[Y-m-d]';
            $rules['id_relijiaun']  = 'required|is_not_unique[tabela_relijiaun.id_relijiaun]';
            $rules['id_profisaun']  = 'required|is_not_unique[tabela_profisaun.id_profisaun]';
            $rules['id_literatura'] = 'required|is_not_unique[tabela_literatura.id_literatura]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $updateData = [
            'naran_pedidu' => $this->request->getPost('naran_pedidu'),
            'pemohon'      => $this->request->getPost('pemohon'),
            'data_pedidu'  => $this->request->getPost('data_pedidu'),
            'id_aldeia'    => $this->request->getPost('id_aldeia'),
        ];

        if ($naran_pedidu === 'Deklarasaun Nascimentu') {
            $oldData = $this->pediduModel->find($id);
            $oldMeta = !empty($oldData['meta_data']) ? json_decode($oldData['meta_data'], true) : [];
            $nik = !empty($oldMeta['nik']) ? $oldMeta['nik'] : ($this->request->getPost('nik') ?: $this->generateUniqueNip());

            $updateData['meta_data'] = json_encode([
                'id_familia'    => $this->request->getPost('id_familia'),
                'jeneru'        => $this->request->getPost('jeneru'),
                'fatin_moris'   => $this->request->getPost('fatin_moris'),
                'data_moris'    => $this->request->getPost('data_moris'),
                'id_relijiaun'  => $this->request->getPost('id_relijiaun'),
                'id_profisaun'  => $this->request->getPost('id_profisaun'),
                'id_literatura' => $this->request->getPost('id_literatura'),
                'nik'           => $nik
            ]);
        }

        $this->pediduModel->update($id, $updateData);

        return redirect()->to('/admin/pedidu')->with('sweet-success', 'Pedidu hadia ho susesu!');
    }

    public function updateStatus($id = null)
    {
        // Hanya Xefe Suku atau Admin yang bisa approve/reject
        if (!in_groups(['admin', 'xefe-suku'])) {
            return $this->failForbidden('Ita boot la iha kbiit/autorizasaun atu aprova ka rejeita pedidu!');
        }

        $status = $this->request->getPost('status');
        if (!in_array($status, ['Aprovadu', 'Rezeitadu', 'Pendiente'], true)) {
            return $this->fail('Status la loos!');
        }

        $data = $this->pediduModel->find($id);
        if (!$data) {
            return $this->failNotFound('Pedidu la hetan!');
        }

        $this->pediduModel->update($id, ['status' => $status]);

        // Se Deklarasaun Mortalidade aprovadu, automatikamente muda estatutu populasaun ba Mate
        if ($status === 'Aprovadu' && $data['naran_pedidu'] === 'Deklarasaun Mortalidade') {
            $pop = $this->populasaunModel->where('naran_kompletu', $data['pemohon'])->first();
            if ($pop) {
                $this->populasaunModel->update($pop['id_populasaun'], [
                    'istadu' => 'Mate'
                ]);
            }
        }

        // Se Deklarasaun Muda Domisiliu aprovadu, automatikamente muda estatutu populasaun ba Muda
        if ($status === 'Aprovadu' && $data['naran_pedidu'] === 'Deklarasaun Muda Domisiliu') {
            $pop = $this->populasaunModel->where('naran_kompletu', $data['pemohon'])->first();
            if ($pop) {
                $this->populasaunModel->update($pop['id_populasaun'], [
                    'istadu' => 'Muda'
                ]);
            }
        }

        // Se Deklarasaun Eleitoral Lakon aprovadu, hura/clear no_eleitoral populasaun nian hodi bele prosesa fali foun
        if ($status === 'Aprovadu' && $data['naran_pedidu'] === 'Deklarasaun Eleitoral Lakon') {
            $pop = $this->populasaunModel->where('naran_kompletu', $data['pemohon'])->first();
            if ($pop) {
                $this->populasaunModel->update($pop['id_populasaun'], [
                    'no_eleitoral' => null
                ]);
            }
        }

        // Se Deklarasaun Nascimentu aprovadu, automatikamente rejista ba populasaun
        if ($status === 'Aprovadu' && $data['naran_pedidu'] === 'Deklarasaun Nascimentu') {
            if (!empty($data['meta_data'])) {
                $meta = json_decode($data['meta_data'], true);
                if (is_array($meta)) {
                    // Check if baby already exists in this family
                    $existsChild = $this->populasaunModel
                        ->where('naran_kompletu', $data['pemohon'])
                        ->where('id_familia', $meta['id_familia'])
                        ->countAllResults();
                    
                    if ($existsChild == 0) {
                        $nik = !empty($meta['nik']) ? $meta['nik'] : $this->generateUniqueNip();
                        $this->populasaunModel->save([
                            'nik'              => $nik,
                            'naran_kompletu'   => $data['pemohon'],
                            'fatin_moris'      => $meta['fatin_moris'] ?: 'Suku Laisorolai',
                            'data_moris'       => $meta['data_moris'] ?: date('Y-m-d'),
                            'jeneru'           => $meta['jeneru'] ?: 'Mane',
                            'status_kaza'      => 'Solteiru/a',
                            'id_aldeia'        => $data['id_aldeia'],
                            'id_profisaun'     => $meta['id_profisaun'] ?: 1,
                            'id_relijiaun'     => $meta['id_relijiaun'] ?: 1,
                            'id_literatura'    => $meta['id_literatura'] ?: 1,
                            'id_familia'       => $meta['id_familia'],
                            'relasaun_familia' => 'Oan',
                            'istadu'           => 'Moris',
                        ]);
                    }
                }
            }
        }

        // Snapshot and lock details in tabela_inventoriu when approved
        if ($status === 'Aprovadu') {
            $inventoriuModel = new \App\Models\InventoriuModel();
            $existsInv = $inventoriuModel->where('id_pedidu', $id)->first();
            if (!$existsInv) {
                $resident = $this->populasaunModel
                    ->select('tabela_populasaun.*, tabela_aldeia.naran_aldeia')
                    ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left')
                    ->where('tabela_populasaun.naran_kompletu', $data['pemohon'])
                    ->first();

                if (!$resident) {
                    $resident = [
                        'naran_kompletu' => $data['pemohon'],
                        'jeneru'         => 'Mane',
                        'data_moris'     => date('Y-m-d', strtotime('-21 years')),
                        'fatin_moris'    => 'Laisorolai de Baixo',
                        'naran_aldeia'   => 'Saua-Casa',
                        'nik'            => '0000000000',
                        'no_eleitoral'   => null,
                        'no_kbiit_laek'  => null,
                    ];
                    $aldeiaRec = $this->aldeiaModel->find($data['id_aldeia']);
                    if ($aldeiaRec) {
                        $resident['naran_aldeia'] = $aldeiaRec['naran_aldeia'];
                    }
                }

                $inventoriuModel->save([
                    'id_pedidu'      => $id,
                    'naran_kompletu' => $resident['naran_kompletu'],
                    'jeneru'         => $resident['jeneru'],
                    'data_moris'     => $resident['data_moris'],
                    'fatin_moris'    => $resident['fatin_moris'],
                    'naran_aldeia'   => $resident['naran_aldeia'],
                    'nik'            => $resident['nik'],
                    'no_eleitoral'   => $resident['no_eleitoral'] ?? null,
                    'no_kbiit_laek'  => $resident['no_kbiit_laek'] ?? null,
                    'meta_data'      => $data['meta_data'] ?? null
                ]);
            }
        }

        return $this->respond(['status' => true, 'message' => "Pedidu mudadu ba {$status} ho susesu!"]);
    }

    public function delete($id = null)
    {
        if (!$this->pediduModel->find($id)) {
            return $this->failNotFound('Pedidu la hetan!');
        }

        // Hamoos mos snapshot iha inventoriu se iha
        $inventoriuModel = new \App\Models\InventoriuModel();
        $inventoriuModel->where('id_pedidu', $id)->delete();

        $this->pediduModel->delete($id);

        return $this->respondDeleted(['status' => true], 'Pedidu delekado ho susesu!');
    }

    public function print($id = null)
    {
        $pedidu = $this->pediduModel->find($id);
        if (!$pedidu) {
            return redirect()->to('/admin/pedidu')->with('sweet-error', 'Dados pedidu la hetan!');
        }

        // Fetch corresponding type template
        $tipuModel = new \App\Models\TipuPediduModel();
        $tipu = $tipuModel->where('naran_tipu_pedidu', $pedidu['naran_pedidu'])->first();
        if (!$tipu || empty($tipu['template_formatu'])) {
            return redirect()->to('/admin/pedidu')->with('sweet-error', 'Template formatu deklarasaun seidauk ready! Hadia template uluk.');
        }

        // Try to load frozen snapshot from tabela_inventoriu
        $inventoriuModel = new \App\Models\InventoriuModel();
        $snapshot = $inventoriuModel->where('id_pedidu', $id)->first();

        if ($snapshot) {
            $resident = [
                'naran_kompletu' => $snapshot['naran_kompletu'],
                'jeneru'         => $snapshot['jeneru'],
                'data_moris'     => $snapshot['data_moris'],
                'fatin_moris'    => $snapshot['fatin_moris'],
                'naran_aldeia'   => $snapshot['naran_aldeia'],
                'nik'            => $snapshot['nik'],
                'no_eleitoral'   => $snapshot['no_eleitoral'],
                'no_kbiit_laek'  => $snapshot['no_kbiit_laek'],
            ];
        } else {
            // Fallback to fetch corresponding resident details dynamically if snapshot doesn't exist
            $resident = $this->populasaunModel
                ->select('tabela_populasaun.*, tabela_aldeia.naran_aldeia')
                ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left')
                ->where('tabela_populasaun.naran_kompletu', $pedidu['pemohon'])
                ->first();

            if (!$resident) {
                // Fallback values if resident doesn't exist in system
                $resident = [
                    'naran_kompletu' => $pedidu['pemohon'],
                    'jeneru'         => 'M',
                    'data_moris'     => date('Y-m-d', strtotime('-21 years')),
                    'fatin_moris'    => 'Laisorolai de Baixo',
                    'naran_aldeia'   => 'Saua-Casa',
                    'nik'            => '0000000000',
                    'no_eleitoral'   => null,
                    'no_kbiit_laek'  => null,
                ];
                // Try to set correct Aldeia based on the pedidu
                $aldeiaRec = $this->aldeiaModel->find($pedidu['id_aldeia']);
                if ($aldeiaRec) {
                    $resident['naran_aldeia'] = $aldeiaRec['naran_aldeia'];
                }
            }

            // Also, let's create a frozen snapshot now so that it is locked forever from now on!
            if ($pedidu['status'] === 'Aprovadu') {
                $inventoriuModel->save([
                    'id_pedidu'      => $id,
                    'naran_kompletu' => $resident['naran_kompletu'],
                    'jeneru'         => $resident['jeneru'],
                    'data_moris'     => $resident['data_moris'],
                    'fatin_moris'    => $resident['fatin_moris'],
                    'naran_aldeia'   => $resident['naran_aldeia'],
                    'nik'            => $resident['nik'],
                    'no_eleitoral'   => $resident['no_eleitoral'] ?? null,
                    'no_kbiit_laek'  => $resident['no_kbiit_laek'] ?? null,
                    'meta_data'      => $pedidu['meta_data'] ?? null
                ]);
            }
        }

        // Age calculation
        $birthDate = new \DateTime($resident['data_moris']);
        $today = new \DateTime($pedidu['data_pedidu']);
        $idade = $birthDate->diff($today)->y;

        // Gender text formatting
        $sexText = ($resident['jeneru'] === 'F' || strtolower($resident['jeneru']) === 'feminino' || strtolower($resident['jeneru']) === 'feto') ? 'Feminino' : 'Masculino';

        // Birth date string format
        $birthDateStr = ($resident['fatin_moris'] ?? 'Laisorolai de Baixo') . ', ' . $this->getTetumDate($resident['data_moris']);

        // Reference Number
        $refNo = esc($pedidu['id_pedidu']) . '/LSLB/Matebian/Baucau/' . date('m', strtotime($pedidu['data_pedidu'])) . '/' . date('Y', strtotime($pedidu['data_pedidu']));

        // Replace template variables
        $template = $tipu['template_formatu'];
        $replacements = [
            '[COP_IMAGE]'      => base_url('uploads/decei.jpg'),
            '[REF_NUMERU]'     => $refNo,
            '[NARAN_KOMPLETU]' => esc($resident['naran_kompletu']),
            '[SEXO]'           => esc($sexText),
            '[DATA_MORIS]'     => esc($birthDateStr),
            '[IDADE]'          => esc($idade),
            '[ALDEIA]'         => esc($resident['naran_aldeia']),
            '[NIK]'            => esc($resident['nik']),
            '[NO_ELEITORAL]'   => esc($resident['no_eleitoral'] ?? ''),
            '[NO_KBIIT_LAEK]'  => esc($resident['no_kbiit_laek'] ?? ''),
            '[DATA_AGORA]'     => $this->getTetumDate($pedidu['data_pedidu'])
        ];

        foreach ($replacements as $key => $val) {
            $template = str_replace($key, $val, $template);
        }

        return view('admin/pedidu/print', [
            'pedidu'          => $pedidu,
            'parsed_template' => $template
        ]);
    }

    public function populasaunList()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Forbidden', 403);
        }

        $naran_pedidu = $this->request->getGet('naran_pedidu');
        $id_aldeia = $this->request->getGet('id_aldeia');
        $start = $this->request->getGet('start');
        $length = $this->request->getGet('length');
        $search = $this->request->getGet('search[value]');

        $db = \Config\Database::connect();

        $baseBuilder = function() use ($db, $naran_pedidu, $id_aldeia) {
            $builder = $db->table('tabela_populasaun');
            
            // Only living population
            $builder->where('tabela_populasaun.istadu', 'Moris');

            // Exclude population who already have Approved or Pending request for One-Time types
            $oneTimeTypes = ['Deklarasaun Eleitoral', 'Deklarasaun Nascimentu', 'Deklarasaun Mortalidade', 'Deklarasaun Kbiit Laek'];
            if (in_array($naran_pedidu, $oneTimeTypes, true)) {
                $excludeSubquery = $db->table('tabela_pedidu')
                    ->select('pemohon')
                    ->where('naran_pedidu', $naran_pedidu)
                    ->whereIn('status', ['Aprovadu', 'Pendiente']);
                
                $builder->whereNotIn('tabela_populasaun.naran_kompletu', $excludeSubquery);
            }

            // Age >= 17 only if Deklarasaun Eleitoral
            if ($naran_pedidu === 'Deklarasaun Eleitoral') {
                $cutoffDate = date('Y-m-d', strtotime('-17 years'));
                $builder->where('tabela_populasaun.data_moris <=', $cutoffDate);
            }

            // Must have a card/no eleitoral for certain types
            $requiresEleitoral = [
                'Deklarasaun Bom Comportamento',
                'Deklarasaun Kbiit Laek',
                'Deklarasaun Eleitoral Lakon',
                'Deklarasaun Muda Domisiliu'
            ];
            if (in_array($naran_pedidu, $requiresEleitoral, true)) {
                $builder->groupStart()
                    ->where('tabela_populasaun.no_eleitoral IS NOT NULL')
                    ->where('tabela_populasaun.no_eleitoral !=', '')
                    ->groupEnd();
            }

            // Aldeia filter
            if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                $builder->where('tabela_populasaun.id_aldeia', user()->id_aldeia);
            } elseif (!empty($id_aldeia)) {
                $builder->where('tabela_populasaun.id_aldeia', $id_aldeia);
            }

            return $builder;
        };

        // 1. recordsTotal
        $recordsTotal = $baseBuilder()->countAllResults();

        // 2. recordsFiltered
        $filterBuilder = $baseBuilder();
        $filterBuilder->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left');
        if (!empty($search)) {
            $filterBuilder->groupStart()
                ->like('tabela_populasaun.naran_kompletu', $search)
                ->orLike('tabela_populasaun.nik', $search)
                ->orLike('tabela_aldeia.naran_aldeia', $search)
                ->groupEnd();
        }
        $recordsFiltered = $filterBuilder->countAllResults();

        // 3. fetch data
        $dataBuilder = $baseBuilder();
        $subquery = "(SELECT COUNT(*) FROM tabela_pedidu WHERE tabela_pedidu.pemohon = tabela_populasaun.naran_kompletu AND tabela_pedidu.naran_pedidu = " . $db->escape($naran_pedidu) . " AND tabela_pedidu.status = 'Pendiente') as pending_count";
        $dataBuilder->select("tabela_populasaun.*, tabela_aldeia.naran_aldeia, {$subquery}")
            ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left');
        if (!empty($search)) {
            $dataBuilder->groupStart()
                ->like('tabela_populasaun.naran_kompletu', $search)
                ->orLike('tabela_populasaun.nik', $search)
                ->orLike('tabela_aldeia.naran_aldeia', $search)
                ->groupEnd();
        }
        $data = $dataBuilder->limit($length, $start)->get()->getResultArray();

        return $this->respond([
            'draw'            => $this->request->getGet('draw'),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function createAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Forbidden', 403);
        }

        $rules = [
            'naran_pedidu' => 'required',
            'pemohon'      => 'required',
            'id_aldeia'    => 'required|is_not_unique[tabela_aldeia.id_aldeia]',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $naran_pedidu = $this->request->getPost('naran_pedidu');
        $pemohon = $this->request->getPost('pemohon');
        $id_aldeia = $this->request->getPost('id_aldeia');
        $data_pedidu = date('Y-m-d');

        // Check if the citizen is already deceased
        $db = \Config\Database::connect();
        $citizen = $db->table('tabela_populasaun')
            ->where('naran_kompletu', $pemohon)
            ->get()
            ->getRowArray();
            
        if ($citizen && $citizen['istadu'] === 'Mate') {
            return $this->fail('Sidadaun ho naran ne\'e mate ona no labele halo pedidu foun!');
        }

        // Check if has card/no eleitoral for certain types
        $requiresEleitoral = [
            'Deklarasaun Bom Comportamento',
            'Deklarasaun Kbiit Laek',
            'Deklarasaun Eleitoral Lakon',
            'Deklarasaun Muda Domisiliu'
        ];
        if (in_array($naran_pedidu, $requiresEleitoral, true)) {
            $pop = $this->populasaunModel->where('naran_kompletu', $pemohon)->first();
            if (!$pop || empty($pop['no_eleitoral'])) {
                return $this->fail('Sidadaun ne\'e seidauk iha Kartaun Eleitoral! Tenki iha Kartaun Eleitoral uluk.');
            }
        }

        // Check age if Deklarasaun Eleitoral
        if ($naran_pedidu === 'Deklarasaun Eleitoral') {
            $pop = $this->populasaunModel->where('naran_kompletu', $pemohon)->first();
            if ($pop) {
                $birthDate = new \DateTime($pop['data_moris']);
                $today = new \DateTime();
                $age = $birthDate->diff($today)->y;
                if ($age < 17) {
                    return $this->fail('Sidadaun ne\'e nia idade seidauk to\'o tinan 17!');
                }
            }
        }

        $meta_data = $this->request->getPost('meta_data') ?: null;
        if ($naran_pedidu === 'Deklarasaun Mortalidade') {
            $meta = $meta_data ? json_decode($meta_data, true) : null;
            if (empty($meta['data_mate'])) {
                return $this->fail('Tenki hatama data mate / tanggal kematian!');
            }
        }
        if ($naran_pedidu === 'Deklarasaun Nascimentu') {
            $rulesExtra = [
                'id_familia'    => 'required|is_not_unique[tabela_familia.id_familia]',
                'jeneru'        => 'required|in_list[Mane,Feto]',
                'fatin_moris'   => 'required',
                'data_moris'    => 'required|valid_date[Y-m-d]',
                'id_relijiaun'  => 'required|is_not_unique[tabela_relijiaun.id_relijiaun]',
                'id_profisaun'  => 'required|is_not_unique[tabela_profisaun.id_profisaun]',
                'id_literatura' => 'required|is_not_unique[tabela_literatura.id_literatura]',
            ];
            
            if (!$this->validate($rulesExtra)) {
                return $this->fail($this->validator->getErrors());
            }

            // Check if baby already exists in this family
            $db = \Config\Database::connect();
            $existsChild = $db->table('tabela_populasaun')
                ->where('naran_kompletu', $pemohon)
                ->where('id_familia', $this->request->getPost('id_familia'))
                ->countAllResults();
            if ($existsChild > 0) {
                return $this->fail('Sidadaun ho naran ne\'e rejistadu ona iha Fixa Familia ne\'e!');
            }

            $existsPendingBirth = $db->table('tabela_pedidu')
                ->where('pemohon', $pemohon)
                ->where('naran_pedidu', 'Deklarasaun Nascimentu')
                ->where('status', 'Pendiente')
                ->countAllResults();
            if ($existsPendingBirth > 0) {
                return $this->fail('Iha ona pedidu pendiente Deklarasaun Nascimentu ba naran ne\'e!');
            }

            $meta_data = json_encode([
                'id_familia'    => $this->request->getPost('id_familia'),
                'jeneru'        => $this->request->getPost('jeneru'),
                'fatin_moris'   => $this->request->getPost('fatin_moris'),
                'data_moris'    => $this->request->getPost('data_moris'),
                'id_relijiaun'  => $this->request->getPost('id_relijiaun'),
                'id_profisaun'  => $this->request->getPost('id_profisaun'),
                'id_literatura' => $this->request->getPost('id_literatura'),
                'nik'           => $this->request->getPost('nik') ?: $this->generateUniqueNip()
            ]);
        }

        // Check active pending or approved requests based on type
        $db = \Config\Database::connect();
        $oneTimeTypes = ['Deklarasaun Eleitoral', 'Deklarasaun Nascimentu', 'Deklarasaun Mortalidade', 'Deklarasaun Kbiit Laek'];
        if (in_array($naran_pedidu, $oneTimeTypes, true)) {
            $existsActive = $db->table('tabela_pedidu')
                ->where('pemohon', $pemohon)
                ->where('naran_pedidu', $naran_pedidu)
                ->whereIn('status', ['Aprovadu', 'Pendiente'])
                ->countAllResults();

            if ($existsActive > 0) {
                return $this->fail('Sidadaun ne\'e iha ona pedidu ne\'ebé Aprovadu ka Pendiente ho tipu ne\'ebé hanesan!');
            }
        } else {
            $existsPending = $db->table('tabela_pedidu')
                ->where('pemohon', $pemohon)
                ->where('naran_pedidu', $naran_pedidu)
                ->where('status', 'Pendiente')
                ->countAllResults();

            if ($existsPending > 0) {
                return $this->fail('Sidadaun ne\'e iha ona pedidu pendiente ho tipu ne\'ebé hanesan!');
            }
        }

        // Save
        $this->pediduModel->save([
            'naran_pedidu' => $naran_pedidu,
            'pemohon'      => $pemohon,
            'data_pedidu'  => $data_pedidu,
            'id_aldeia'    => $id_aldeia,
            'status'       => 'Pendiente',
            'meta_data'    => $meta_data,
        ]);

        return $this->respond([
            'status'  => true,
            'message' => 'Pedidu foun kria ho susesu! Status pendiente.'
        ]);
    }

    public function familiaList()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Forbidden', 403);
        }

        $id_aldeia = $this->request->getGet('id_aldeia');
        $start = $this->request->getGet('start');
        $length = $this->request->getGet('length');
        $search = $this->request->getGet('search[value]');

        $db = \Config\Database::connect();

        $baseBuilder = function() use ($db, $id_aldeia) {
            $builder = $db->table('tabela_familia');
            
            // Only families that HAVE a head of family (Xefe Familia)
            $builder->where("(SELECT COUNT(*) FROM tabela_populasaun WHERE tabela_populasaun.id_familia = tabela_familia.id_familia AND tabela_populasaun.relasaun_familia = 'Xefe Familia') > 0", null, false);

            // Aldeia filter
            if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                $builder->where('tabela_familia.id_aldeia', user()->id_aldeia);
            } elseif (!empty($id_aldeia)) {
                $builder->where('tabela_familia.id_aldeia', $id_aldeia);
            }

            return $builder;
        };

        // 1. recordsTotal
        $recordsTotal = $baseBuilder()->countAllResults();

        // 2. recordsFiltered
        $filterBuilder = $baseBuilder();
        $filterBuilder->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_familia.id_aldeia', 'left');
        if (!empty($search)) {
            $filterBuilder->groupStart()
                ->like('tabela_familia.numeru_kk', $search)
                ->orLike('tabela_aldeia.naran_aldeia', $search)
                ->orWhere("(SELECT tp.naran_kompletu FROM tabela_populasaun tp WHERE tp.id_familia = tabela_familia.id_familia AND tp.relasaun_familia = 'Xefe Familia' LIMIT 1) LIKE " . $db->escape("%{$search}%"), null, false)
                ->groupEnd();
        }
        $recordsFiltered = $filterBuilder->countAllResults();

        // 3. fetch data
        $dataBuilder = $baseBuilder();
        $xefeSub = "(SELECT tp.naran_kompletu FROM tabela_populasaun tp WHERE tp.id_familia = tabela_familia.id_familia AND tp.relasaun_familia = 'Xefe Familia' LIMIT 1) as xefe_familia";
        $dataBuilder->select("tabela_familia.*, tabela_aldeia.naran_aldeia, {$xefeSub}")
            ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_familia.id_aldeia', 'left');
        if (!empty($search)) {
            $dataBuilder->groupStart()
                ->like('tabela_familia.numeru_kk', $search)
                ->orLike('tabela_aldeia.naran_aldeia', $search)
                ->orWhere("(SELECT tp.naran_kompletu FROM tabela_populasaun tp WHERE tp.id_familia = tabela_familia.id_familia AND tp.relasaun_familia = 'Xefe Familia' LIMIT 1) LIKE " . $db->escape("%{$search}%"), null, false)
                ->groupEnd();
        }
        $data = $dataBuilder->limit($length, $start)->get()->getResultArray();

        return $this->respond([
            'draw'            => $this->request->getGet('draw'),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    private function generateUniqueNip()
    {
        $db = \Config\Database::connect();
        do {
            $nip = mt_rand(100000, 999999) . mt_rand(100000, 999999);
            $exists = $db->table('tabela_populasaun')->where('nik', $nip)->countAllResults();
        } while ($exists > 0);
        return $nip;
    }

    private function getTetumDate($dateStr)
    {
        $timestamp = strtotime($dateStr);
        $day = date('d', $timestamp);
        $monthNum = (int)date('m', $timestamp);
        $year = date('Y', $timestamp);
        
        $months = [
            1 => 'Janeiru', 2 => 'Fovereiru', 3 => 'Marsu', 4 => 'Abril',
            5 => 'Maiu', 6 => 'Juñu', 7 => 'Juliu', 8 => 'Agostu',
            9 => 'Setembru', 10 => 'Outubru', 11 => 'Novembru', 12 => 'Dezembru'
        ];
        
        return "{$day} de " . ($months[$monthNum] ?? 'Maiu') . " de {$year}";
    }
}
