<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AldeiaModel;
use App\Models\PediduModel;
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
        $this->pediduModel     = new PediduModel();
        $this->aldeiaModel     = new AldeiaModel();
        $this->populasaunModel = new PopulasaunModel();
    }

    public function index()
    {
        $naran_pedidu = $this->request->getGet('naran_pedidu');

        if ($this->request->isAJAX()) {
            $start         = $this->request->getGet('start');
            $length        = $this->request->getGet('length');
            $search        = $this->request->getGet('search[value]');
            $status_filter = $this->request->getGet('status_filter');
            $db            = \Config\Database::connect();

            if (empty($naran_pedidu)) {
                $status_condition = ['Pendiente'];
            } elseif (!empty($status_filter)) {
                $status_condition = [$status_filter];
            } else {
                $status_condition = ['Aprovadu', 'Rezeitadu'];
            }

            $baseBuilder = function() use ($db, $naran_pedidu, $status_condition) {
                $builder = $db->table('tabela_pedidu');

                if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                    $builder->where('tabela_pedidu.id_aldeia', user()->id_aldeia);
                }
                if (!empty($naran_pedidu)) {
                    $builder->where('tabela_pedidu.naran_pedidu', $naran_pedidu);
                }

                return $builder->whereIn('tabela_pedidu.status', $status_condition);
            };

            $recordsTotal = $baseBuilder()->countAllResults();

            $filterBuilder = $baseBuilder()
                ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_pedidu.id_aldeia', 'left');
            if (!empty($search)) {
                $filterBuilder->groupStart()
                    ->like('tabela_pedidu.naran_pedidu', $search)
                    ->orLike('tabela_pedidu.pemohon', $search)
                    ->orLike('tabela_aldeia.naran_aldeia', $search)
                    ->groupEnd();
            }
            $recordsFiltered = $filterBuilder->countAllResults();

            $dataBuilder = $baseBuilder()
                ->select('tabela_pedidu.*, tabela_aldeia.naran_aldeia')
                ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_pedidu.id_aldeia', 'left');
            if (!empty($search)) {
                $dataBuilder->groupStart()
                    ->like('tabela_pedidu.naran_pedidu', $search)
                    ->orLike('tabela_pedidu.pemohon', $search)
                    ->orLike('tabela_aldeia.naran_aldeia', $search)
                    ->groupEnd();
            }

            $data = $dataBuilder
                ->orderBy('tabela_pedidu.data_pedidu', 'desc')
                ->orderBy('tabela_pedidu.id_pedidu', 'desc')
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

        $title    = 'Jestaun Pedidu';
        $subtitle = 'Lista Permohonan Surat / Pedidu Suku Laisorolai de Baixo';

        if (!empty($naran_pedidu)) {
            $title    = 'Inventoriu ' . $naran_pedidu;
            $subtitle = 'Lista Inventoriu husi ' . $naran_pedidu;
        }

        return view('admin/pedidu/index', [
            'title'        => $title,
            'subtitle'     => $subtitle,
            'naran_pedidu' => $naran_pedidu,
        ]);
    }

    public function new()
    {
        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $tipuModel      = new \App\Models\TipuPediduModel();
        $profisaunModel = new \App\Models\ProfisaunModel();
        $relijiaunModel = new \App\Models\RelijiaunModel();
        $literaturaModel = new \App\Models\LiteraturaModel();

        return view('admin/pedidu/create', [
            'title'      => 'Kria Pedidu',
            'subtitle'   => 'Kria Permohonan Surat Foun',
            'aldeias'    => $aldeias,
            'tipus'      => $tipuModel->findAll(),
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

        if ($this->request->getPost('naran_pedidu') !== 'Deklarasaun Nascimentu') {
            $rules['id_populasaun'] = 'required|is_not_unique[tabela_populasaun.id_populasaun]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $idAldeia = (int) $this->request->getPost('id_aldeia');
        if (! $this->canAccessAldeia($idAldeia)) {
            return $this->redirectForbidden('Ita boot labele kria pedidu ba aldeia seluk.');
        }

        $idPopulasaun = $this->request->getPost('id_populasaun') ?: null;
        if ($idPopulasaun) {
            $resident = $this->populasaunModel->find($idPopulasaun);
            if (! $resident || (int) $resident['id_aldeia'] !== $idAldeia) {
                return redirect()->back()->withInput()->with('error', 'Sidadaun la validu ba aldeia nebe hili.');
            }
        }

        $this->pediduModel->save([
            'id_populasaun' => $idPopulasaun,
            'naran_pedidu'  => $this->request->getPost('naran_pedidu'),
            'pemohon'       => $this->request->getPost('pemohon'),
            'data_pedidu'   => $this->request->getPost('data_pedidu'),
            'id_aldeia'     => $idAldeia,
            'status'        => 'Pendiente',
        ]);

        return redirect()->to('/admin/pedidu')->with('sweet-success', 'Pedidu foun kria ho susesu! Hein aprovasaun husi Xefe Suku.');
    }

    public function edit($id = null)
    {
        $data = $this->pediduModel->find($id);
        if (! $data) {
            return redirect()->to('/admin/pedidu')->with('sweet-error', 'Dados pedidu la hetan!');
        }
        if (! $this->canAccessAldeia($data['id_aldeia'])) {
            return $this->redirectForbidden('Ita boot labele haree pedidu husi aldeia seluk.');
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $tipuModel       = new \App\Models\TipuPediduModel();
        $profisaunModel  = new \App\Models\ProfisaunModel();
        $relijiaunModel  = new \App\Models\RelijiaunModel();
        $literaturaModel = new \App\Models\LiteraturaModel();

        $meta = [];
        if ($data['naran_pedidu'] === 'Deklarasaun Nascimentu' && !empty($data['meta_data'])) {
            $meta = json_decode($data['meta_data'], true) ?: [];
        }

        $familias = [];
        if ($data['naran_pedidu'] === 'Deklarasaun Nascimentu') {
            $db      = \Config\Database::connect();
            $xefeSub = "(SELECT tp.naran_kompletu FROM tabela_populasaun tp WHERE tp.id_familia = tabela_familia.id_familia AND tp.relasaun_familia = 'Xefe Familia' LIMIT 1) as naran_xefe";
            $builder = $db->table('tabela_familia')->select("tabela_familia.*, {$xefeSub}");
            if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                $builder->where('tabela_familia.id_aldeia', user()->id_aldeia);
            }
            $familias = $builder->get()->getResultArray();
        }

        return view('admin/pedidu/edit', [
            'title'      => 'Hadia Pedidu',
            'subtitle'   => 'Hadia Dadus Pedidu',
            'pedidu'     => $data,
            'meta'       => $meta,
            'aldeias'    => $aldeias,
            'tipus'      => $tipuModel->findAll(),
            'profisaun'  => $profisaunModel->findAll(),
            'relijiaun'  => $relijiaunModel->findAll(),
            'literatura' => $literaturaModel->findAll(),
            'familias'   => $familias,
        ]);
    }

    public function update($id = null)
    {
        $oldData = $this->pediduModel->find($id);
        if (! $oldData) {
            return redirect()->to('/admin/pedidu')->with('sweet-error', 'Dados pedidu la hetan!');
        }
        if ($oldData['status'] !== 'Pendiente') {
            return redirect()->back()->with('error', 'Pedidu nebe aprovadu ka rezeitadu labele hadia fali.');
        }

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
        } else {
            $rules['id_populasaun'] = 'required|is_not_unique[tabela_populasaun.id_populasaun]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $idAldeia = (int) $this->request->getPost('id_aldeia');
        if (! $this->canAccessAldeia($idAldeia)) {
            return $this->redirectForbidden('Ita boot labele hadia pedidu ba aldeia seluk.');
        }

        $updateData = [
            'id_populasaun' => $this->request->getPost('id_populasaun') ?: null,
            'naran_pedidu'  => $naran_pedidu,
            'pemohon'       => $this->request->getPost('pemohon'),
            'data_pedidu'   => $this->request->getPost('data_pedidu'),
            'id_aldeia'     => $idAldeia,
        ];

        if ($naran_pedidu === 'Deklarasaun Nascimentu') {
            $familia = (new \App\Models\FamiliaModel())->find($this->request->getPost('id_familia'));
            if (! $familia || ! $this->canAccessAldeia($familia['id_aldeia'])) {
                return $this->redirectForbidden('Ita boot labele uza fixa familia husi aldeia seluk.');
            }

            $oldMeta = !empty($oldData['meta_data']) ? (json_decode($oldData['meta_data'], true) ?: []) : [];
            $nik     = !empty($oldMeta['nik']) ? $oldMeta['nik'] : ($this->request->getPost('nik') ?: $this->generateUniqueNip());

            $updateData['id_populasaun'] = null;
            $updateData['meta_data'] = json_encode([
                'id_familia'    => $this->request->getPost('id_familia'),
                'jeneru'        => $this->request->getPost('jeneru'),
                'fatin_moris'   => $this->request->getPost('fatin_moris'),
                'data_moris'    => $this->request->getPost('data_moris'),
                'id_relijiaun'  => $this->request->getPost('id_relijiaun'),
                'id_profisaun'  => $this->request->getPost('id_profisaun'),
                'id_literatura' => $this->request->getPost('id_literatura'),
                'nik'           => $nik,
            ]);
        }

        $this->pediduModel->update($id, $updateData);

        return redirect()->to('/admin/pedidu')->with('sweet-success', 'Pedidu hadia ho susesu!');
    }

    public function updateStatus($id = null)
    {
        if (! in_groups(['admin', 'xefe-suku'])) {
            return $this->failForbidden('Ita boot la iha kbiit/autorizasaun atu aprova ka rejeita pedidu!');
        }

        $status = $this->request->getPost('status');
        if (! in_array($status, ['Aprovadu', 'Rezeitadu', 'Pendiente'], true)) {
            return $this->fail('Status la loos!');
        }

        $data = $this->pediduModel->find($id);
        if (! $data) {
            return $this->failNotFound('Pedidu la hetan!');
        }

        if ($data['status'] !== 'Pendiente' && $status !== $data['status']) {
            return $this->fail('Pedidu nebe status ona labele muda fali. Kria prosedimentu koreksaun/cancel separadu.');
        }
        if ($status === 'Pendiente') {
            return $this->respond(['status' => true, 'message' => 'Pedidu seidauk muda tanba status nafatin Pendiente.']);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $now        = date('Y-m-d H:i:s');
            $statusData = ['status' => $status];
            if ($status === 'Aprovadu') {
                $statusData['approved_by'] = $this->currentUserId();
                $statusData['approved_at'] = $now;
            } else {
                $statusData['rejected_by'] = $this->currentUserId();
                $statusData['rejected_at'] = $now;
            }

            $this->pediduModel->update($id, $statusData);
            $data = array_merge($data, $statusData);

            if ($status === 'Aprovadu') {
                $this->applyApprovedPediduEffects((int) $id, $data);
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transasaun database falha.');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Pedidu approval failed: ' . $e->getMessage());

            return $this->fail($e->getMessage());
        }

        return $this->respond(['status' => true, 'message' => "Pedidu mudadu ba {$status} ho susesu!"]);
    }

    public function delete($id = null)
    {
        if (! in_groups(['admin', 'xefe-suku'])) {
            return $this->failForbidden('Ita boot la iha kbiit/autorizasaun atu hamoos pedidu!');
        }

        $pedidu = $this->pediduModel->find($id);
        if (! $pedidu) {
            return $this->failNotFound('Pedidu la hetan!');
        }
        if ($pedidu['status'] === 'Aprovadu') {
            return $this->fail('Pedidu aprovadu labele hamoos tanba presiza rai historiku no inventoriu.');
        }

        (new \App\Models\InventoriuModel())->where('id_pedidu', $id)->delete();
        $this->pediduModel->delete($id);
        $this->writeAudit('pedidu', (string) $id, 'delete', $pedidu, null);

        return $this->respondDeleted(['status' => true], 'Pedidu delekado ho susesu!');
    }

    public function print($id = null)
    {
        $pedidu = $this->pediduModel->find($id);
        if (! $pedidu) {
            return redirect()->to('/admin/pedidu')->with('sweet-error', 'Dados pedidu la hetan!');
        }
        if (! $this->canAccessAldeia($pedidu['id_aldeia'])) {
            return $this->redirectForbidden('Ita boot labele imprime pedidu husi aldeia seluk.');
        }
        if ($pedidu['status'] !== 'Aprovadu') {
            return redirect()->to('/admin/pedidu')->with('sweet-error', 'Pedidu tenki aprovadu uluk antes imprime.');
        }

        $tipuModel = new \App\Models\TipuPediduModel();
        $tipu      = $tipuModel->where('naran_tipu_pedidu', $pedidu['naran_pedidu'])->first();
        if (! $tipu || empty($tipu['template_formatu'])) {
            return redirect()->to('/admin/pedidu')->with('sweet-error', 'Template formatu deklarasaun seidauk ready! Hadia template uluk.');
        }

        $inventoriuModel = new \App\Models\InventoriuModel();
        $snapshot        = $inventoriuModel->where('id_pedidu', $id)->first();

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
            $resident = $this->findResidentForPedidu($pedidu) ?: $this->fallbackResidentForPedidu($pedidu);
            $this->createInventoriuSnapshot((int) $id, $pedidu, $resident);
        }

        $birthDate = new \DateTime($resident['data_moris']);
        $today     = new \DateTime($pedidu['data_pedidu']);
        $idade     = $birthDate->diff($today)->y;

        $sexText      = ($resident['jeneru'] === 'F' || strtolower($resident['jeneru']) === 'feminino' || strtolower($resident['jeneru']) === 'feto') ? 'Feminino' : 'Masculino';
        $birthDateStr = ($resident['fatin_moris'] ?? 'Laisorolai de Baixo') . ', ' . $this->getTetumDate($resident['data_moris']);
        $refNo        = esc($pedidu['id_pedidu']) . '/LSLB/Matebian/Baucau/' . date('m', strtotime($pedidu['data_pedidu'])) . '/' . date('Y', strtotime($pedidu['data_pedidu']));

        $template = $tipu['template_formatu'];
        foreach ([
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
            '[DATA_AGORA]'     => $this->getTetumDate($pedidu['data_pedidu']),
        ] as $key => $val) {
            $template = str_replace($key, $val, $template);
        }

        return view('admin/pedidu/print', [
            'pedidu'          => $pedidu,
            'parsed_template' => $template,
        ]);
    }

    public function populasaunList()
    {
        if (! $this->request->isAJAX()) {
            return $this->fail('Forbidden', 403);
        }

        $naran_pedidu = $this->request->getGet('naran_pedidu');
        $id_aldeia    = $this->request->getGet('id_aldeia');
        $start        = $this->request->getGet('start');
        $length       = $this->request->getGet('length');
        $search       = $this->request->getGet('search[value]');
        $db           = \Config\Database::connect();

        $baseBuilder = function() use ($db, $naran_pedidu, $id_aldeia) {
            $builder = $db->table('tabela_populasaun')
                ->where('tabela_populasaun.istadu', 'Moris');

            $oneTimeTypes = ['Deklarasaun Eleitoral', 'Deklarasaun Mortalidade', 'Deklarasaun Kbiit Laek'];
            if (in_array($naran_pedidu, $oneTimeTypes, true)) {
                $excludeSubquery = $db->table('tabela_pedidu')
                    ->select('id_populasaun')
                    ->where('naran_pedidu', $naran_pedidu)
                    ->where('id_populasaun IS NOT NULL')
                    ->whereIn('status', ['Aprovadu', 'Pendiente']);
                $builder->whereNotIn('tabela_populasaun.id_populasaun', $excludeSubquery);
            }

            if ($naran_pedidu === 'Deklarasaun Eleitoral') {
                $builder->where('tabela_populasaun.data_moris <=', date('Y-m-d', strtotime('-17 years')));
            }

            $requiresEleitoral = [
                'Deklarasaun Bom Comportamento',
                'Deklarasaun Kbiit Laek',
                'Deklarasaun Eleitoral Lakon',
                'Deklarasaun Muda Domisiliu',
            ];
            if (in_array($naran_pedidu, $requiresEleitoral, true)) {
                $builder->where('tabela_populasaun.no_eleitoral IS NOT NULL')
                    ->where('tabela_populasaun.no_eleitoral !=', '');
            }

            if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                $builder->where('tabela_populasaun.id_aldeia', user()->id_aldeia);
            } elseif (!empty($id_aldeia)) {
                $builder->where('tabela_populasaun.id_aldeia', $id_aldeia);
            }

            return $builder;
        };

        $recordsTotal = $baseBuilder()->countAllResults();

        $filterBuilder = $baseBuilder()
            ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left');
        if (!empty($search)) {
            $filterBuilder->groupStart()
                ->like('tabela_populasaun.naran_kompletu', $search)
                ->orLike('tabela_populasaun.nik', $search)
                ->orLike('tabela_aldeia.naran_aldeia', $search)
                ->groupEnd();
        }
        $recordsFiltered = $filterBuilder->countAllResults();

        $pendingSubquery = "(SELECT COUNT(*) FROM tabela_pedidu WHERE tabela_pedidu.id_populasaun = tabela_populasaun.id_populasaun AND tabela_pedidu.naran_pedidu = " . $db->escape($naran_pedidu) . " AND tabela_pedidu.status = 'Pendiente') as pending_count";
        $dataBuilder = $baseBuilder()
            ->select("tabela_populasaun.*, tabela_aldeia.naran_aldeia, {$pendingSubquery}")
            ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left');
        if (!empty($search)) {
            $dataBuilder->groupStart()
                ->like('tabela_populasaun.naran_kompletu', $search)
                ->orLike('tabela_populasaun.nik', $search)
                ->orLike('tabela_aldeia.naran_aldeia', $search)
                ->groupEnd();
        }
        $data = $dataBuilder->orderBy('tabela_populasaun.naran_kompletu', 'asc')->limit($length, $start)->get()->getResultArray();

        return $this->respond([
            'draw'            => $this->request->getGet('draw'),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function createAjax()
    {
        if (! $this->request->isAJAX()) {
            return $this->fail('Forbidden', 403);
        }

        $naran_pedidu = $this->request->getPost('naran_pedidu');
        $rules = [
            'naran_pedidu' => 'required',
            'id_aldeia'    => 'required|is_not_unique[tabela_aldeia.id_aldeia]',
        ];

        if ($naran_pedidu === 'Deklarasaun Nascimentu') {
            $rules['pemohon']       = 'required|min_length[3]|max_length[150]';
            $rules['id_familia']    = 'required|is_not_unique[tabela_familia.id_familia]';
            $rules['jeneru']        = 'required|in_list[Mane,Feto]';
            $rules['fatin_moris']   = 'required';
            $rules['data_moris']    = 'required|valid_date[Y-m-d]';
            $rules['id_relijiaun']  = 'required|is_not_unique[tabela_relijiaun.id_relijiaun]';
            $rules['id_profisaun']  = 'required|is_not_unique[tabela_profisaun.id_profisaun]';
            $rules['id_literatura'] = 'required|is_not_unique[tabela_literatura.id_literatura]';
            $rules['nik']           = 'permit_empty|is_unique[tabela_populasaun.nik]';
        } else {
            $rules['id_populasaun'] = 'required|is_not_unique[tabela_populasaun.id_populasaun]';
        }

        if (! $this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $idAldeia = (int) $this->request->getPost('id_aldeia');
        if (! $this->canAccessAldeia($idAldeia)) {
            return $this->failForbidden('Ita boot labele kria pedidu ba aldeia seluk.');
        }

        $data_pedidu  = date('Y-m-d');
        $meta_data    = $this->request->getPost('meta_data') ?: null;
        $idPopulasaun = null;
        $pemohon      = $this->request->getPost('pemohon');

        if ($naran_pedidu !== 'Deklarasaun Nascimentu') {
            $idPopulasaun = (int) $this->request->getPost('id_populasaun');
            $citizen      = $this->populasaunModel->find($idPopulasaun);

            if (! $citizen || (int) $citizen['id_aldeia'] !== $idAldeia) {
                return $this->fail('Sidadaun la validu ba aldeia nebe hili.');
            }
            if ($citizen['istadu'] === 'Mate') {
                return $this->fail('Sidadaun ho naran nee mate ona no labele halo pedidu foun!');
            }

            $pemohon = $citizen['naran_kompletu'];

            $requiresEleitoral = [
                'Deklarasaun Bom Comportamento',
                'Deklarasaun Kbiit Laek',
                'Deklarasaun Eleitoral Lakon',
                'Deklarasaun Muda Domisiliu',
            ];
            if (in_array($naran_pedidu, $requiresEleitoral, true) && empty($citizen['no_eleitoral'])) {
                return $this->fail('Sidadaun nee seidauk iha Kartaun Eleitoral! Tenki iha Kartaun Eleitoral uluk.');
            }

            if ($naran_pedidu === 'Deklarasaun Eleitoral') {
                $birthDate = new \DateTime($citizen['data_moris']);
                $age       = $birthDate->diff(new \DateTime())->y;
                if ($age < 17) {
                    return $this->fail('Sidadaun nee nia idade seidauk too tinan 17!');
                }
            }

            if ($naran_pedidu === 'Deklarasaun Mortalidade') {
                $meta = $meta_data ? json_decode($meta_data, true) : null;
                if (empty($meta['data_mate'])) {
                    return $this->fail('Tenki hatama data mate / tanggal kematian!');
                }
                if (strtotime($meta['data_mate']) > strtotime(date('Y-m-d'))) {
                    return $this->fail('Data mate labele liu data ohin.');
                }
            }

            $oneTimeTypes = ['Deklarasaun Eleitoral', 'Deklarasaun Mortalidade', 'Deklarasaun Kbiit Laek'];
            $existsBuilder = $this->pediduModel
                ->where('id_populasaun', $idPopulasaun)
                ->where('naran_pedidu', $naran_pedidu);
            if (in_array($naran_pedidu, $oneTimeTypes, true)) {
                $existsBuilder->whereIn('status', ['Aprovadu', 'Pendiente']);
            } else {
                $existsBuilder->where('status', 'Pendiente');
            }
            if ($existsBuilder->countAllResults() > 0) {
                return $this->fail('Sidadaun nee iha ona pedidu ativa ho tipu hanesan!');
            }
        } else {
            $familiaModel = new \App\Models\FamiliaModel();
            $familia      = $familiaModel->find($this->request->getPost('id_familia'));
            if (! $familia || (int) $familia['id_aldeia'] !== $idAldeia || ! $this->canAccessAldeia($familia['id_aldeia'])) {
                return $this->failForbidden('Fixa familia la validu ba aldeia nebe hili.');
            }

            $existsChild = $this->populasaunModel
                ->where('naran_kompletu', $pemohon)
                ->where('id_familia', $this->request->getPost('id_familia'))
                ->countAllResults();
            if ($existsChild > 0) {
                return $this->fail('Sidadaun ho naran nee rejistadu ona iha Fixa Familia nee!');
            }

            if ($this->hasPendingBirthForFamily($pemohon, (int) $this->request->getPost('id_familia'))) {
                return $this->fail('Iha ona pedidu pendiente Deklarasaun Nascimentu ba naran no familia nee!');
            }
            if (strtotime($this->request->getPost('data_moris')) > strtotime(date('Y-m-d'))) {
                return $this->fail('Data moris labele liu data ohin.');
            }

            $meta_data = json_encode([
                'id_familia'    => $this->request->getPost('id_familia'),
                'jeneru'        => $this->request->getPost('jeneru'),
                'fatin_moris'   => $this->request->getPost('fatin_moris'),
                'data_moris'    => $this->request->getPost('data_moris'),
                'id_relijiaun'  => $this->request->getPost('id_relijiaun'),
                'id_profisaun'  => $this->request->getPost('id_profisaun'),
                'id_literatura' => $this->request->getPost('id_literatura'),
                'nik'           => $this->request->getPost('nik') ?: $this->generateUniqueNip(),
            ]);
        }

        $this->pediduModel->save([
            'id_populasaun' => $idPopulasaun,
            'naran_pedidu'  => $naran_pedidu,
            'pemohon'       => $pemohon,
            'data_pedidu'   => $data_pedidu,
            'id_aldeia'     => $idAldeia,
            'status'        => 'Pendiente',
            'meta_data'     => $meta_data,
        ]);

        return $this->respond([
            'status'  => true,
            'message' => 'Pedidu foun kria ho susesu! Status pendiente.',
        ]);
    }

    public function familiaList()
    {
        if (! $this->request->isAJAX()) {
            return $this->fail('Forbidden', 403);
        }

        $id_aldeia = $this->request->getGet('id_aldeia');
        $start     = $this->request->getGet('start');
        $length    = $this->request->getGet('length');
        $search    = $this->request->getGet('search[value]');
        $db        = \Config\Database::connect();

        $baseBuilder = function() use ($db, $id_aldeia) {
            $builder = $db->table('tabela_familia')
                ->where("(SELECT COUNT(*) FROM tabela_populasaun WHERE tabela_populasaun.id_familia = tabela_familia.id_familia AND tabela_populasaun.relasaun_familia = 'Xefe Familia') > 0", null, false);

            if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                $builder->where('tabela_familia.id_aldeia', user()->id_aldeia);
            } elseif (!empty($id_aldeia)) {
                $builder->where('tabela_familia.id_aldeia', $id_aldeia);
            }

            return $builder;
        };

        $recordsTotal = $baseBuilder()->countAllResults();

        $filterBuilder = $baseBuilder()->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_familia.id_aldeia', 'left');
        if (!empty($search)) {
            $filterBuilder->groupStart()
                ->like('tabela_familia.numeru_kk', $search)
                ->orLike('tabela_aldeia.naran_aldeia', $search)
                ->orWhere("(SELECT tp.naran_kompletu FROM tabela_populasaun tp WHERE tp.id_familia = tabela_familia.id_familia AND tp.relasaun_familia = 'Xefe Familia' LIMIT 1) LIKE " . $db->escape("%{$search}%"), null, false)
                ->groupEnd();
        }
        $recordsFiltered = $filterBuilder->countAllResults();

        $xefeSub = "(SELECT tp.naran_kompletu FROM tabela_populasaun tp WHERE tp.id_familia = tabela_familia.id_familia AND tp.relasaun_familia = 'Xefe Familia' LIMIT 1) as xefe_familia";
        $dataBuilder = $baseBuilder()
            ->select("tabela_familia.*, tabela_aldeia.naran_aldeia, {$xefeSub}")
            ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_familia.id_aldeia', 'left');
        if (!empty($search)) {
            $dataBuilder->groupStart()
                ->like('tabela_familia.numeru_kk', $search)
                ->orLike('tabela_aldeia.naran_aldeia', $search)
                ->orWhere("(SELECT tp.naran_kompletu FROM tabela_populasaun tp WHERE tp.id_familia = tabela_familia.id_familia AND tp.relasaun_familia = 'Xefe Familia' LIMIT 1) LIKE " . $db->escape("%{$search}%"), null, false)
                ->groupEnd();
        }
        $data = $dataBuilder->orderBy('tabela_familia.numeru_kk', 'asc')->limit($length, $start)->get()->getResultArray();

        return $this->respond([
            'draw'            => $this->request->getGet('draw'),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    private function applyApprovedPediduEffects(int $id, array $data): void
    {
        $resident = $this->findResidentForPedidu($data);

        if ($data['naran_pedidu'] === 'Deklarasaun Eleitoral Lakon') {
            $this->createInventoriuSnapshot($id, $data, $resident);
            if ($resident) {
                $this->populasaunModel->update($resident['id_populasaun'], ['no_eleitoral' => null]);
                $this->writeAudit('populasaun', (string) $resident['id_populasaun'], 'clear_no_eleitoral', ['no_eleitoral' => $resident['no_eleitoral'] ?? null], ['no_eleitoral' => null]);
            }
            return;
        }

        if ($data['naran_pedidu'] === 'Deklarasaun Mortalidade') {
            if (! $resident) {
                throw new \RuntimeException('Sidadaun ba Mortalidade la hetan.');
            }
            $this->changeResidentStatus($resident, 'Mate', $id, 'Pedidu mortalidade aprovadu');
        } elseif ($data['naran_pedidu'] === 'Deklarasaun Muda Domisiliu') {
            if (! $resident) {
                throw new \RuntimeException('Sidadaun ba Muda Domisiliu la hetan.');
            }
            $this->changeResidentStatus($resident, 'Muda', $id, 'Pedidu muda domisiliu aprovadu');
        } elseif ($data['naran_pedidu'] === 'Deklarasaun Nascimentu') {
            $resident = $this->createResidentFromBirthPedidu($id, $data);
        }

        $this->createInventoriuSnapshot($id, $data, $resident);
    }

    private function createResidentFromBirthPedidu(int $idPedidu, array $data): array
    {
        $meta = !empty($data['meta_data']) ? (json_decode($data['meta_data'], true) ?: []) : [];
        if (empty($meta['id_familia'])) {
            throw new \RuntimeException('Meta nascimentu la kompletu.');
        }

        $existsChild = $this->populasaunModel
            ->where('naran_kompletu', $data['pemohon'])
            ->where('id_familia', $meta['id_familia'])
            ->first();

        if ($existsChild) {
            $this->pediduModel->update($idPedidu, ['id_populasaun' => $existsChild['id_populasaun']]);
            return $this->residentWithAldeia((int) $existsChild['id_populasaun']);
        }

        $insertId = $this->populasaunModel->insert([
            'nik'              => !empty($meta['nik']) ? $meta['nik'] : $this->generateUniqueNip(),
            'naran_kompletu'   => $data['pemohon'],
            'fatin_moris'      => $meta['fatin_moris'] ?? 'Suku Laisorolai',
            'data_moris'       => $meta['data_moris'] ?? date('Y-m-d'),
            'jeneru'           => $meta['jeneru'] ?? 'Mane',
            'status_kaza'      => 'Solteiru/a',
            'id_aldeia'        => $data['id_aldeia'],
            'id_profisaun'     => $meta['id_profisaun'] ?? 1,
            'id_relijiaun'     => $meta['id_relijiaun'] ?? 1,
            'id_literatura'    => $meta['id_literatura'] ?? 1,
            'id_familia'       => $meta['id_familia'],
            'relasaun_familia' => 'Oan',
            'istadu'           => 'Moris',
        ], true);

        $this->pediduModel->update($idPedidu, ['id_populasaun' => $insertId]);
        $this->writeAudit('populasaun', (string) $insertId, 'create_from_birth_pedidu', null, ['id_pedidu' => $idPedidu]);

        return $this->residentWithAldeia((int) $insertId);
    }

    private function changeResidentStatus(array $resident, string $newStatus, int $idPedidu, string $reason): void
    {
        if ($resident['istadu'] === $newStatus) {
            return;
        }

        $oldStatus = $resident['istadu'];
        $this->populasaunModel->update($resident['id_populasaun'], ['istadu' => $newStatus]);

        \Config\Database::connect()->table('tabela_populasaun_status_history')->insert([
            'id_populasaun' => $resident['id_populasaun'],
            'old_istadu'    => $oldStatus,
            'new_istadu'    => $newStatus,
            'id_pedidu'     => $idPedidu,
            'changed_by'    => $this->currentUserId(),
            'reason'        => $reason,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        $this->writeAudit('populasaun', (string) $resident['id_populasaun'], 'status_change', ['istadu' => $oldStatus], ['istadu' => $newStatus, 'id_pedidu' => $idPedidu]);
    }

    private function createInventoriuSnapshot(int $idPedidu, array $pedidu, ?array $resident = null): void
    {
        $inventoriuModel = new \App\Models\InventoriuModel();
        if ($inventoriuModel->where('id_pedidu', $idPedidu)->first()) {
            return;
        }

        $resident = $resident ?: $this->findResidentForPedidu($pedidu) ?: $this->fallbackResidentForPedidu($pedidu);
        if (empty($resident['naran_aldeia'])) {
            $aldeia = $this->aldeiaModel->find($pedidu['id_aldeia']);
            $resident['naran_aldeia'] = $aldeia['naran_aldeia'] ?? '-';
        }

        $inventoriuModel->save([
            'id_pedidu'      => $idPedidu,
            'naran_kompletu' => $resident['naran_kompletu'],
            'jeneru'         => $resident['jeneru'],
            'data_moris'     => $resident['data_moris'],
            'fatin_moris'    => $resident['fatin_moris'],
            'naran_aldeia'   => $resident['naran_aldeia'],
            'nik'            => $resident['nik'],
            'no_eleitoral'   => $resident['no_eleitoral'] ?? null,
            'no_kbiit_laek'  => $resident['no_kbiit_laek'] ?? null,
            'meta_data'      => $pedidu['meta_data'] ?? null,
        ]);
    }

    private function findResidentForPedidu(array $pedidu): ?array
    {
        if (!empty($pedidu['id_populasaun'])) {
            return $this->residentWithAldeia((int) $pedidu['id_populasaun']);
        }

        $matches = $this->populasaunModel
            ->select('tabela_populasaun.*, tabela_aldeia.naran_aldeia')
            ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left')
            ->where('tabela_populasaun.naran_kompletu', $pedidu['pemohon'])
            ->findAll();

        return count($matches) === 1 ? $matches[0] : null;
    }

    private function residentWithAldeia(int $idPopulasaun): ?array
    {
        return $this->populasaunModel
            ->select('tabela_populasaun.*, tabela_aldeia.naran_aldeia')
            ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left')
            ->where('tabela_populasaun.id_populasaun', $idPopulasaun)
            ->first();
    }

    private function fallbackResidentForPedidu(array $pedidu): array
    {
        $aldeia = $this->aldeiaModel->find($pedidu['id_aldeia']);

        return [
            'naran_kompletu' => $pedidu['pemohon'],
            'jeneru'         => 'Mane',
            'data_moris'     => date('Y-m-d', strtotime('-21 years')),
            'fatin_moris'    => 'Laisorolai de Baixo',
            'naran_aldeia'   => $aldeia['naran_aldeia'] ?? 'Laisorolai',
            'nik'            => '0000000000',
            'no_eleitoral'   => null,
            'no_kbiit_laek'  => null,
        ];
    }

    private function hasPendingBirthForFamily(string $pemohon, int $idFamilia): bool
    {
        $rows = $this->pediduModel
            ->where('pemohon', $pemohon)
            ->where('naran_pedidu', 'Deklarasaun Nascimentu')
            ->where('status', 'Pendiente')
            ->findAll();

        foreach ($rows as $row) {
            $meta = !empty($row['meta_data']) ? (json_decode($row['meta_data'], true) ?: []) : [];
            if ((int) ($meta['id_familia'] ?? 0) === $idFamilia) {
                return true;
            }
        }

        return false;
    }

    private function writeAudit(string $entityType, string $entityId, string $action, $oldValues, $newValues): void
    {
        if (! \Config\Database::connect()->tableExists('tabela_audit_log')) {
            return;
        }

        \Config\Database::connect()->table('tabela_audit_log')->insert([
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'action'      => $action,
            'old_values'  => $oldValues === null ? null : json_encode($oldValues),
            'new_values'  => $newValues === null ? null : json_encode($newValues),
            'changed_by'  => $this->currentUserId(),
            'created_at'  => date('Y-m-d H:i:s'),
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
        $day       = date('d', $timestamp);
        $monthNum  = (int) date('m', $timestamp);
        $year      = date('Y', $timestamp);

        $months = [
            1 => 'Janeiru', 2 => 'Fovereiru', 3 => 'Marsu', 4 => 'Abril',
            5 => 'Maiu', 6 => 'Junu', 7 => 'Juliu', 8 => 'Agostu',
            9 => 'Setembru', 10 => 'Outubru', 11 => 'Novembru', 12 => 'Dezembru',
        ];

        return "{$day} de " . ($months[$monthNum] ?? 'Maiu') . " de {$year}";
    }
}
