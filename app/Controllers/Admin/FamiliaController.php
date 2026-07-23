<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FamiliaModel;
use App\Models\AldeiaModel;
use App\Models\PopulasaunModel;
use CodeIgniter\API\ResponseTrait;

class FamiliaController extends BaseController
{
    use ResponseTrait;
    protected $familiaModel;
    protected $aldeiaModel;
    protected $populasaunModel;

    public function __construct()
    {
        $this->familiaModel = new FamiliaModel();
        $this->aldeiaModel = new AldeiaModel();
        $this->populasaunModel = new PopulasaunModel();
    }

    public function index()
    {
        $data = [
            'title'    => 'Jestaun Familia',
            'subtitle' => 'Dadus Fixa Familia Suku Laisorolai de Baixo',
        ];

        return view('admin/familia/index', $data);
    }

    public function ajaxData()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Forbidden', 403);
        }

        $type = $this->request->getGet('type'); // 'foun' or 'existente'
        $start = $this->request->getGet('start');
        $length = $this->request->getGet('length');
        $search = $this->request->getGet('search[value]');

        $db = \Config\Database::connect();

        $baseBuilder = function() use ($db, $type) {
            $builder = $db->table('tabela_familia');
            
            // Filter by Xefe Aldeia's assigned Aldeia
            if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                $builder->where('tabela_familia.id_aldeia', user()->id_aldeia);
            }

            // Separator between foun (0 members) and existente (>0 members)
            if ($type === 'foun') {
                $builder->where("(SELECT COUNT(*) FROM tabela_populasaun WHERE tabela_populasaun.id_familia = tabela_familia.id_familia) =", 0);
            } else {
                $builder->where("(SELECT COUNT(*) FROM tabela_populasaun WHERE tabela_populasaun.id_familia = tabela_familia.id_familia) >", 0);
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
                ->orLike('tabela_aldeia.naran_aldeia', $search);
            
            if ($type !== 'foun') {
                $filterBuilder->orWhere("(SELECT tp.naran_kompletu FROM tabela_populasaun tp WHERE tp.id_familia = tabela_familia.id_familia AND tp.relasaun_familia = 'Xefe Familia' LIMIT 1) LIKE " . $db->escape("%{$search}%"), null, false);
            }
            $filterBuilder->groupEnd();
        }
        $recordsFiltered = $filterBuilder->countAllResults();

        // 3. Data query
        $dataBuilder = $baseBuilder();
        $totalMembrosSub = "(SELECT COUNT(*) FROM tabela_populasaun tp WHERE tp.id_familia = tabela_familia.id_familia) as total_membros";
        $xefeFamiliaSub = "(SELECT tp.naran_kompletu FROM tabela_populasaun tp WHERE tp.id_familia = tabela_familia.id_familia AND tp.relasaun_familia = 'Xefe Familia' LIMIT 1) as xefe_familia";
        
        $dataBuilder->select("tabela_familia.*, tabela_aldeia.naran_aldeia, {$totalMembrosSub}, {$xefeFamiliaSub}")
            ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_familia.id_aldeia', 'left');

        if (!empty($search)) {
            $dataBuilder->groupStart()
                ->like('tabela_familia.numeru_kk', $search)
                ->orLike('tabela_aldeia.naran_aldeia', $search);
            if ($type !== 'foun') {
                $dataBuilder->orWhere("(SELECT tp.naran_kompletu FROM tabela_populasaun tp WHERE tp.id_familia = tabela_familia.id_familia AND tp.relasaun_familia = 'Xefe Familia' LIMIT 1) LIKE " . $db->escape("%{$search}%"), null, false);
            }
            $dataBuilder->groupEnd();
        }

        // Limit and order
        $data = $dataBuilder->limit($length, $start)->orderBy('tabela_familia.numeru_kk', 'asc')->get()->getResultArray();

        return $this->respond([
            'draw'            => $this->request->getGet('draw'),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function new()
    {
        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $data = [
            'title'    => 'Rejistu Fixa Familia Foun',
            'subtitle' => 'Kria Fixa Familia foun',
            'aldeias'  => $aldeias
        ];

        return view('admin/familia/create', $data);
    }

    public function create()
    {
        $rules = [
            'id_aldeia' => 'required|is_not_unique[tabela_aldeia.id_aldeia]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idAldeia = (int) $this->request->getPost('id_aldeia');
        if (! $this->canAccessAldeia($idAldeia)) {
            return $this->redirectForbidden('Ita boot labele kria fixa familia ba aldeia seluk.');
        }

        $numeruFixa = $this->generateUniqueNumeruFixa();

        $this->familiaModel->save([
            'numeru_kk' => $numeruFixa,
            'id_aldeia' => $idAldeia
        ]);

        return redirect()->to('/admin/familia')->with('message', 'Fixa Familia foun kria ho susesu!');
    }

    public function edit($id = null)
    {
        $familia = $this->familiaModel->find($id);
        if (!$familia) {
            return redirect()->to('/admin/familia')->with('error', 'Fixa Familia la hetan!');
        }
        if (! $this->canAccessAldeia($familia['id_aldeia'])) {
            return $this->redirectForbidden('Ita boot labele hadia fixa familia husi aldeia seluk.');
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $data = [
            'title'    => 'Hadia Fixa Familia',
            'subtitle' => 'Hadia dadus Fixa Familia',
            'familia'  => $familia,
            'aldeias'  => $aldeias
        ];

        return view('admin/familia/edit', $data);
    }

    public function update($id = null)
    {
        $familia = $this->familiaModel->find($id);
        if (!$familia) {
            return redirect()->to('/admin/familia')->with('error', 'Fixa Familia la hetan!');
        }
        if (! $this->canAccessAldeia($familia['id_aldeia'])) {
            return $this->redirectForbidden('Ita boot labele hadia fixa familia husi aldeia seluk.');
        }

        $rules = [
            'id_aldeia' => 'required|is_not_unique[tabela_aldeia.id_aldeia]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idAldeia = (int) $this->request->getPost('id_aldeia');
        if (! $this->canAccessAldeia($idAldeia)) {
            return $this->redirectForbidden('Ita boot labele muda fixa familia ba aldeia seluk.');
        }

        $memberCount = $this->populasaunModel->where('id_familia', $id)->countAllResults();
        if ($memberCount > 0 && (int) $familia['id_aldeia'] !== $idAldeia) {
            return redirect()->back()->withInput()->with('error', 'Fixa Familia iha membru ona, labele muda aldeia.');
        }

        $this->familiaModel->update($id, [
            'id_aldeia' => $idAldeia
        ]);

        return redirect()->to('/admin/familia')->with('message', 'Fixa Familia aktualizadu ho susesu!');
    }

    public function show($id = null)
    {
        $familia = $this->familiaModel->select('tabela_familia.*, tabela_aldeia.naran_aldeia')
                                      ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_familia.id_aldeia', 'left')
                                      ->find($id);
                                      
        if (!$familia) {
            return redirect()->to('/admin/familia')->with('error', 'Fixa Familia la hetan!');
        }
        if (! $this->canAccessAldeia($familia['id_aldeia'])) {
            return $this->redirectForbidden('Ita boot labele haree fixa familia husi aldeia seluk.');
        }

        $membros = $this->populasaunModel->select('tabela_populasaun.*, tabela_profisaun.naran_profisaun, tabela_relijiaun.naran_relijiaun, tabela_literatura.naran_literatura')
                                         ->join('tabela_profisaun', 'tabela_profisaun.id_profisaun = tabela_populasaun.id_profisaun', 'left')
                                         ->join('tabela_relijiaun', 'tabela_relijiaun.id_relijiaun = tabela_populasaun.id_relijiaun', 'left')
                                         ->join('tabela_literatura', 'tabela_literatura.id_literatura = tabela_populasaun.id_literatura', 'left')
                                         ->where('id_familia', $id)
                                         ->findAll();
        
        $hasXefe = false;
        foreach ($membros as $m) {
            if ($m['relasaun_familia'] === 'Xefe Familia') {
                $hasXefe = true;
                break;
            }
        }

        // Find residents from the same Aldeia who are not assigned to any family yet, to easily add them
        $queryArr = [
            'id_aldeia'  => $familia['id_aldeia'],
            'id_familia' => null,
            'istadu'     => 'Moris'
        ];
        if (!$hasXefe) {
            $queryArr['jeneru'] = 'Mane';
        }
        $unassignedResidents = $this->populasaunModel->where($queryArr)->findAll();

        $formatuModel = new \App\Models\FormatuRelatoriuModel();
        $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Fixa Familia')->first();

        $data = [
            'title'               => 'Detaillu Fixa Familia',
            'subtitle'            => 'Numeru Fixa Familia: ' . esc($familia['numeru_kk']),
            'familia'             => $familia,
            'membros'             => $membros,
            'unassignedResidents' => $unassignedResidents,
            'cop_temp'            => $formatu ? $formatu['template_cop'] : '',
        ];

        return view('admin/familia/show', $data);
    }

    public function addMembro($idFamilia)
    {
        $familia = $this->familiaModel->find($idFamilia);
        if (!$familia) {
            return redirect()->to('/admin/familia')->with('error', 'Fixa Familia la hetan!');
        }
        if (! $this->canAccessAldeia($familia['id_aldeia'])) {
            return $this->redirectForbidden('Ita boot labele aumenta membru ba fixa familia husi aldeia seluk.');
        }

        $idPopulasaun = $this->request->getPost('id_populasaun');
        $relasaun = $this->request->getPost('relasaun_familia');
        $allowedRelasaun = ['Xefe Familia', 'Fen', 'Oan', 'Seluk'];

        if (empty($idPopulasaun) || empty($relasaun) || ! in_array($relasaun, $allowedRelasaun, true)) {
            return redirect()->back()->with('error', 'Membru no Relasaun tenki hili!');
        }

        $pop = $this->populasaunModel->find($idPopulasaun);
        if (!$pop) {
            return redirect()->back()->with('error', 'Sidadaun la hetan!');
        }
        if ((int) $pop['id_aldeia'] !== (int) $familia['id_aldeia'] || ! $this->canAccessAldeia($pop['id_aldeia'])) {
            return redirect()->back()->with('error', 'Sidadaun nee la pertense ba aldeia fixa familia nian.');
        }
        if ($pop['istadu'] !== 'Moris') {
            return redirect()->back()->with('error', 'Sidadaun nebe Mate ka Muda labele aumenta ba fixa familia ativa.');
        }
        if (! empty($pop['id_familia']) && (int) $pop['id_familia'] !== (int) $idFamilia) {
            return redirect()->back()->with('error', 'Sidadaun nee pertense ona ba fixa familia seluk.');
        }

        $hasXefe = $this->familiaHasXefe((int) $idFamilia);

        if ($relasaun === 'Xefe Familia' && $pop['jeneru'] !== 'Mane') {
            return redirect()->back()->with('error', 'Xefe Familia tenki Mane de\'it!');
        }
        if ($relasaun === 'Xefe Familia' && $hasXefe) {
            return redirect()->back()->with('error', 'Fixa Familia nee iha ona Xefe Familia.');
        }
        if ($relasaun !== 'Xefe Familia' && ! $hasXefe) {
            return redirect()->back()->with('error', 'Tenki aumenta Xefe Familia uluk antes aumenta membru seluk.');
        }

        $this->populasaunModel->update($idPopulasaun, [
            'id_familia'       => $idFamilia,
            'relasaun_familia' => $relasaun
        ]);

        return redirect()->to("/admin/familia/{$idFamilia}")->with('message', 'Membru foun aumenta ho susesu!');
    }

    public function removeMembro($idFamilia, $idPopulasaun)
    {
        $familia = $this->familiaModel->find($idFamilia);
        if (!$familia) {
            return redirect()->to('/admin/familia')->with('error', 'Fixa Familia la hetan!');
        }
        if (! $this->canAccessAldeia($familia['id_aldeia'])) {
            return $this->redirectForbidden('Ita boot labele hasai membru husi fixa familia aldeia seluk.');
        }

        $pop = $this->populasaunModel->find($idPopulasaun);
        if (!$pop || (int) $pop['id_familia'] !== (int) $idFamilia) {
            return redirect()->to("/admin/familia/{$idFamilia}")->with('error', 'Membru la pertense ba fixa familia nee.');
        }

        $this->populasaunModel->where('id_populasaun', $idPopulasaun)->set([
            'id_familia'       => null,
            'relasaun_familia' => null
        ])->update();

        return redirect()->to("/admin/familia/{$idFamilia}")->with('message', 'Membru hasai ho susesu!');
    }

    public function uploadFoto($id)
    {
        $familia = $this->familiaModel->find($id);
        if (!$familia) {
            return redirect()->back()->with('error', 'Fixa Familia la hetan!');
        }
        if (! $this->canAccessAldeia($familia['id_aldeia'])) {
            return $this->redirectForbidden('Ita boot labele upload foto ba fixa familia aldeia seluk.');
        }
        if (! $this->familiaHasXefe((int) $id)) {
            return redirect()->back()->with('error', 'Upload foto bele halo depois fixa familia iha Xefe Familia.');
        }

        $rules = [
            'foto' => 'uploaded[foto]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]|max_size[foto,2048]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $img = $this->request->getFile('foto');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            // Delete old photo if exists
            if (!empty($familia['foto']) && file_exists(ROOTPATH . 'public/uploads/familia/' . $familia['foto'])) {
                @unlink(ROOTPATH . 'public/uploads/familia/' . $familia['foto']);
            }

            // Generate a random name
            $newName = $img->getRandomName();
            // Ensure directory exists
            $uploadDir = ROOTPATH . 'public/uploads/familia/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Use CodeIgniter's Image class to compress the image
            $imageService = \Config\Services::image();
            try {
                $imageService->withFile($img)
                    ->resize(800, 800, true, 'height') // Resize to max 800px height/width, maintain aspect ratio
                    ->convert(IMAGETYPE_JPEG) // Convert to JPEG for smaller size
                    ->save($uploadDir . $newName, 75); // 75% quality
            } catch (\Exception $e) {
                log_message('error', 'Image compression failed: ' . $e->getMessage());
                // Fallback to original upload if compression fails
                $img->move($uploadDir, $newName);
            }

            // Update in DB
            $this->familiaModel->update($id, [
                'foto' => $newName
            ]);

            return redirect()->to("/admin/familia/{$id}")->with('message', 'Upload foto Xefe Familia susesu!');
        }

        return redirect()->back()->with('error', 'Upload foto falha! Hili de\'it fail imajen ne\'ebé loos.');
    }

    public function delete($id = null)
    {
        $familia = $this->familiaModel->find($id);
        if (!$familia) {
            return redirect()->to('/admin/familia')->with('error', 'Fixa Familia la hetan!');
        }
        if (! $this->canAccessAldeia($familia['id_aldeia'])) {
            return $this->redirectForbidden('Ita boot labele hamoos fixa familia husi aldeia seluk.');
        }

        $memberCount = $this->populasaunModel->where('id_familia', $id)->countAllResults();
        if ($memberCount > 0) {
            return redirect()->to('/admin/familia')->with('error', 'Fixa Familia nee iha membru. Hasai membru hotu uluk antes hamoos.');
        }

        $this->familiaModel->delete($id);

        return redirect()->to('/admin/familia')->with('message', 'Fixa Familia hasai ho susesu!');
    }

    private function generateUniqueNumeruFixa()
    {
        $db = \Config\Database::connect();
        do {
            // Generate a random 16-digit unique number for Numeru Fixa
            $num = mt_rand(10000000, 99999999) . mt_rand(10000000, 99999999);
            $exists = $db->table('tabela_familia')->where('numeru_kk', $num)->countAllResults();
        } while ($exists > 0);
        return $num;
    }

    private function familiaHasXefe(int $idFamilia): bool
    {
        return $this->populasaunModel
            ->where('id_familia', $idFamilia)
            ->where('relasaun_familia', 'Xefe Familia')
            ->countAllResults() > 0;
    }
}
