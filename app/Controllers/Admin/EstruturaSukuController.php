<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EstruturaSukuModel;
use App\Models\PopulasaunModel;

class EstruturaSukuController extends BaseController
{
    protected $estruturaModel;
    protected $populasaunModel;

    public function __construct()
    {
        $this->estruturaModel = new EstruturaSukuModel();
        $this->populasaunModel = new PopulasaunModel();
    }

    public function index()
    {
        $aldeiaModel = new \App\Models\AldeiaModel();
        $data = [
            'title'     => 'Struktura Suku',
            'estrutura' => $this->estruturaModel->getEstrutura(),
            'aldeias'   => $aldeiaModel->orderBy('naran_aldeia', 'ASC')->findAll(),
        ];

        return view('admin/estrutura/index', $data);
    }

    public function new()
    {
        $karguModel = new \App\Models\KarguModel();
        $aldeiaModel = new \App\Models\AldeiaModel();

        $db = \Config\Database::connect();
        $activeStructurePopIds = array_column($db->table('tabela_estrutura_suku')
            ->select('id_populasaun')
            ->where('status_kargu', 'Ativu')
            ->where('id_populasaun IS NOT NULL')
            ->get()
            ->getResultArray(), 'id_populasaun');

        $popQuery = $this->populasaunModel
            ->where('istadu', 'Moris')
            ->where('no_eleitoral IS NOT NULL')
            ->where('no_eleitoral !=', '');

        if (!empty($activeStructurePopIds)) {
            $popQuery->whereNotIn('id_populasaun', $activeStructurePopIds);
        }
        $populasaun = $popQuery->findAll();

        $activeMembers = $db->table('tabela_estrutura_suku')
            ->select('id_estrutura, kargu, id_aldeia')
            ->where('status_kargu', 'Ativu')
            ->get()
            ->getResultArray();

        $data = [
            'title'         => 'Rejista Struktura Suku',
            'populasaun'    => $populasaun,
            'activeMembers' => $activeMembers,
            'kargus'        => $karguModel->orderBy('naran_kargu', 'ASC')->findAll(),
            'aldeias'       => $aldeiaModel->orderBy('naran_aldeia', 'ASC')->findAll(),
        ];


        return view('admin/estrutura/new', $data);
    }

    public function create()
    {
        $rules = [
            'naran_membru'   => 'required|min_length[3]|max_length[150]',
            'kargu'          => 'required',
            'periodo_hahula' => 'required|valid_date',
            'foto'           => 'permit_empty|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]|max_size[foto,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idPopulasaun = $this->request->getPost('id_populasaun');
        $idAldeia = $this->request->getPost('id_aldeia');
        $kargu = $this->request->getPost('kargu');

        // Check if kargu requires Aldeia
        $karguLower = strtolower($kargu);
        $isAldeiaKargu = strpos($karguLower, 'xefe aldeia') !== false || 
                         strpos($karguLower, 'delgadu') !== false || 
                         strpos($karguLower, 'delegado') !== false || 
                         strpos($karguLower, 'delegada') !== false;

        if (!$isAldeiaKargu) {
            $idAldeia = null;
        } else {
            if (!empty($idPopulasaun) && !empty($idAldeia)) {
                $pop = $this->populasaunModel->find($idPopulasaun);
                if ($pop && $pop['id_aldeia'] != $idAldeia) {
                    return redirect()->back()->withInput()->with('error', 'Rezidente ne\'e nia Aldeia ketak husi Aldeia ne\'ebé hili!');
                }
            }
        }

        $fotoName = $this->handlePhotoUpload();

        $this->estruturaModel->save([
            'id_populasaun'  => !empty($idPopulasaun) ? $idPopulasaun : null,
            'id_aldeia'      => !empty($idAldeia) ? $idAldeia : null,
            'naran_membru'   => $this->request->getPost('naran_membru'),
            'kargu'          => $kargu,
            'periodo_hahula' => $this->request->getPost('periodo_hahula'),
            'periodo_remata' => !empty($this->request->getPost('periodo_remata')) ? $this->request->getPost('periodo_remata') : null,
            'status_kargu'   => $this->request->getPost('status_kargu') ?? 'Ativu',
            'foto'           => $fotoName,
        ]);

        return redirect()->to('admin/estrutura')->with('message', 'Membru rejistadu ho susesu!');
    }

    private function handlePhotoUpload($oldFoto = null)
    {
        $img = $this->request->getFile('foto');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            // Check if Cloudinary is configured
            $cloudinaryConfig = config('Cloudinary');
            $cloudinaryConfigured = !empty($cloudinaryConfig->cloudName) && !empty($cloudinaryConfig->apiKey) && !empty($cloudinaryConfig->apiSecret);
            
            if ($cloudinaryConfigured) {
                // Use Cloudinary REST API
                try {
                    $cloudName = $cloudinaryConfig->cloudName;
                    $apiKey = $cloudinaryConfig->apiKey;
                    $apiSecret = $cloudinaryConfig->apiSecret;

                    // First, compress the image locally using CodeIgniter's Image class
                    $tempDir = sys_get_temp_dir();
                    $tempName = uniqid('img_', true) . '.jpg';
                    $tempPath = $tempDir . '/' . $tempName;
                    $imageService = \Config\Services::image();
                    $imageService->withFile($img)
                        ->resize(800, 800, true, 'height')
                        ->convert(IMAGETYPE_JPEG)
                        ->save($tempPath, 75);

                    // Prepare Cloudinary upload parameters
                    $params = [
                        'file' => new \CURLFile($tempPath),
                        'folder' => 'sipolai/struktur',
                        'api_key' => $apiKey,
                        'timestamp' => time(),
                    ];
                    // Generate signature
                    $paramsToSign = $params;
                    unset($paramsToSign['file']);
                    ksort($paramsToSign);
                    $signature = '';
                    foreach ($paramsToSign as $key => $value) {
                        $signature .= $key . '=' . $value;
                    }
                    $signature .= $apiSecret;
                    $params['signature'] = sha1($signature);

                    // Upload to Cloudinary
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloudName/image/upload");
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    // Delete temporary file
                    @unlink($tempPath);

                    if ($httpCode === 200) {
                        $uploadResult = json_decode($response, true);
                        if (isset($uploadResult['secure_url'])) {
                            // Delete old photo from Cloudinary if exists
                            if (!empty($oldFoto) && strpos($oldFoto, 'cloudinary.com') !== false) {
                                $this->deleteCloudinaryPhoto($oldFoto, $apiKey, $apiSecret, $cloudName);
                            }
                            return $uploadResult['secure_url'];
                        }
                    } else {
                        log_message('error', 'Cloudinary upload failed with code ' . $httpCode . ': ' . $response);
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Cloudinary upload failed: ' . $e->getMessage());
                    // Fallback to local storage
                }
            }

            // Fallback to local storage
            // Delete old photo if exists
            if (!empty($oldFoto) && file_exists(ROOTPATH . 'public/uploads/familia/' . $oldFoto)) {
                @unlink(ROOTPATH . 'public/uploads/familia/' . $oldFoto);
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
                    ->resize(800, 800, true, 'height')
                    ->convert(IMAGETYPE_JPEG)
                    ->save($uploadDir . $newName, 75);
            } catch (\Exception $e) {
                log_message('error', 'Image compression failed: ' . $e->getMessage());
                $img->move($uploadDir, $newName);
            }

            return $newName;
        }

        return $oldFoto;
    }

    private function deleteCloudinaryPhoto($url, $apiKey, $apiSecret, $cloudName)
    {
        try {
            $publicId = $this->getCloudinaryPublicId($url);
            if (!$publicId) {
                return;
            }

            $params = [
                'public_id' => $publicId,
                'api_key' => $apiKey,
                'timestamp' => time(),
            ];
            // Generate signature
            ksort($params);
            $signature = '';
            foreach ($params as $key => $value) {
                $signature .= $key . '=' . $value;
            }
            $signature .= $apiSecret;
            $params['signature'] = sha1($signature);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloudName/image/destroy");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            log_message('error', 'Cloudinary delete failed: ' . $e->getMessage());
        }
    }

    private function getCloudinaryPublicId($url)
    {
        $pathParts = explode('/', parse_url($url, PHP_URL_PATH));
        $fileName = end($pathParts);
        $publicId = pathinfo($fileName, PATHINFO_FILENAME);
        $folderIndex = array_search('upload', $pathParts);
        if ($folderIndex !== false && isset($pathParts[$folderIndex + 2])) {
            $folder = implode('/', array_slice($pathParts, $folderIndex + 2, -1));
            $publicId = $folder . '/' . $publicId;
        }
        return $publicId;
    }

    public function edit($id = null)
    {
        $membru = $this->estruturaModel->find($id);
        if (!$membru) {
            return redirect()->to('admin/estrutura')->with('error', 'Membru la hetan!');
        }

        $karguModel = new \App\Models\KarguModel();
        $aldeiaModel = new \App\Models\AldeiaModel();

        $db = \Config\Database::connect();
        $activeStructurePopIds = array_column($db->table('tabela_estrutura_suku')
            ->select('id_populasaun')
            ->where('status_kargu', 'Ativu')
            ->where('id_populasaun IS NOT NULL')
            ->where('id_estrutura !=', $id)
            ->get()
            ->getResultArray(), 'id_populasaun');

        $popQuery = $this->populasaunModel
            ->where('istadu', 'Moris')
            ->where('no_eleitoral IS NOT NULL')
            ->where('no_eleitoral !=', '');

        if (!empty($activeStructurePopIds)) {
            $popQuery->whereNotIn('id_populasaun', $activeStructurePopIds);
        }
        $populasaun = $popQuery->findAll();

        $popAldeiaId = null;
        if (!empty($membru['id_populasaun'])) {
            $pop = $this->populasaunModel->find($membru['id_populasaun']);
            if ($pop) $popAldeiaId = $pop['id_aldeia'];
        }

        $activeMembers = $db->table('tabela_estrutura_suku')
            ->select('id_estrutura, kargu, id_aldeia')
            ->where('status_kargu', 'Ativu')
            ->get()
            ->getResultArray();

        $data = [
            'title'         => 'Edit Struktura Suku',
            'membru'        => $membru,
            'populasaun'    => $populasaun,
            'popAldeiaId'   => $popAldeiaId,
            'activeMembers' => $activeMembers,
            'kargus'        => $karguModel->orderBy('naran_kargu', 'ASC')->findAll(),
            'aldeias'       => $aldeiaModel->orderBy('naran_aldeia', 'ASC')->findAll(),
        ];

        return view('admin/estrutura/edit', $data);


    }

    public function update($id = null)
    {
        $rules = [
            'naran_membru'   => 'required|min_length[3]|max_length[150]',
            'kargu'          => 'required',
            'periodo_hahula' => 'required|valid_date',
            'foto'           => 'permit_empty|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]|max_size[foto,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idPopulasaun = $this->request->getPost('id_populasaun');
        $idAldeia = $this->request->getPost('id_aldeia');
        $kargu = $this->request->getPost('kargu');

        // Check if kargu requires Aldeia
        $karguLower = strtolower($kargu);
        $isAldeiaKargu = strpos($karguLower, 'xefe aldeia') !== false || 
                         strpos($karguLower, 'delgadu') !== false || 
                         strpos($karguLower, 'delegado') !== false || 
                         strpos($karguLower, 'delegada') !== false;

        if (!$isAldeiaKargu) {
            $idAldeia = null;
        } else {
            if (!empty($idPopulasaun) && !empty($idAldeia)) {
                $pop = $this->populasaunModel->find($idPopulasaun);
                if ($pop && $pop['id_aldeia'] != $idAldeia) {
                    return redirect()->back()->withInput()->with('error', 'Rezidente ne\'e nia Aldeia ketak husi Aldeia ne\'ebé hili!');
                }
            }
        }

        $membru = $this->estruturaModel->find($id);
        $fotoName = $this->handlePhotoUpload($membru['foto']);

        $this->estruturaModel->update($id, [
            'id_populasaun'  => !empty($idPopulasaun) ? $idPopulasaun : null,
            'id_aldeia'      => !empty($idAldeia) ? $idAldeia : null,
            'naran_membru'   => $this->request->getPost('naran_membru'),
            'kargu'          => $kargu,
            'periodo_hahula' => $this->request->getPost('periodo_hahula'),
            'periodo_remata' => !empty($this->request->getPost('periodo_remata')) ? $this->request->getPost('periodo_remata') : null,
            'status_kargu'   => $this->request->getPost('status_kargu') ?? 'Ativu',
            'foto'           => $fotoName,
        ]);

        return redirect()->to('admin/estrutura')->with('message', 'Dados membru aktualizadu ho susesu!');
    }

    public function delete($id = null)
    {
        if (empty($id)) {
            return redirect()->to('admin/estrutura')->with('error', 'ID membru la bele mamuk!');
        }

        try {
            $deleted = $this->estruturaModel->delete($id);
            if ($deleted) {
                return redirect()->to('admin/estrutura')->with('message', 'Membru hasai ho susesu!');
            } else {
                return redirect()->to('admin/estrutura')->with('error', 'La konsege hasai membru suku (Dados la hetan ka la bele hasai).');
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro delete membru: ' . $e->getMessage());
            return redirect()->to('admin/estrutura')->with('error', 'Erro bainhira hasai membru: ' . $e->getMessage());
        }
    }

    /**
     * Promosaun: Pick a resident and make them a structure member
     */
    public function promosaun()
    {
        $idPopulasaun = $this->request->getGet('id_populasaun');
        if ($idPopulasaun) {
            $pop = $this->populasaunModel->find($idPopulasaun);
            if ($pop) {
                return $this->response->setJSON([
                    'success'        => true,
                    'naran_kompletu' => $pop['naran_kompletu'],
                    'id_populasaun'  => $pop['id_populasaun'],
                    'id_aldeia'      => $pop['id_aldeia']
                ]);
            }
            return $this->response->setJSON(['success' => false]);
        }

        $karguModel = new \App\Models\KarguModel();
        $aldeiaModel = new \App\Models\AldeiaModel();

        $db = \Config\Database::connect();
        $activeStructurePopIds = array_column($db->table('tabela_estrutura_suku')
            ->select('id_populasaun')
            ->where('status_kargu', 'Ativu')
            ->where('id_populasaun IS NOT NULL')
            ->get()
            ->getResultArray(), 'id_populasaun');

        $popQuery = $this->populasaunModel
            ->select('tabela_populasaun.*, tabela_aldeia.naran_aldeia')
            ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left')
            ->where('tabela_populasaun.istadu', 'Moris')
            ->where('tabela_populasaun.no_eleitoral IS NOT NULL')
            ->where('tabela_populasaun.no_eleitoral !=', '');

        if (!empty($activeStructurePopIds)) {
            $popQuery->whereNotIn('tabela_populasaun.id_populasaun', $activeStructurePopIds);
        }
        $populasaun = $popQuery->findAll();

        $activeMembers = $db->table('tabela_estrutura_suku')
            ->select('id_estrutura, kargu, id_aldeia')
            ->where('status_kargu', 'Ativu')
            ->get()
            ->getResultArray();

        $data = [
            'title'         => 'Promosaun Membru Struktura',
            'populasaun'    => $populasaun,
            'activeMembers' => $activeMembers,
            'kargus'        => $karguModel->orderBy('naran_kargu', 'ASC')->findAll(),
            'aldeias'       => $aldeiaModel->orderBy('naran_aldeia', 'ASC')->findAll(),
        ];

        return view('admin/estrutura/promosaun', $data);


    }

    /**
     * Hierarchical Tree Visualization of Suku Laisorulai Structure
     */
    public function hirarkia()
    {
        $db = \Config\Database::connect();
        
        // Fetch all active structure members
        $estrutura = $db->table('tabela_estrutura_suku')
            ->select('tabela_estrutura_suku.*, tabela_populasaun.jeneru, tabela_aldeia.naran_aldeia')
            ->join('tabela_populasaun', 'tabela_populasaun.id_populasaun = tabela_estrutura_suku.id_populasaun', 'left')
            ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_estrutura_suku.id_aldeia', 'left')
            ->where('tabela_estrutura_suku.status_kargu', 'Ativu')
            ->get()
            ->getResultArray();

        // Fetch all Aldeias
        $aldeias = $db->table('tabela_aldeia')->orderBy('naran_aldeia', 'ASC')->get()->getResultArray();

        $xefeSuku = null;
        $secretariaSuku = null;
        $secretariaSubordinates = [];
        $otherCentral = [];
        
        // Group structure members
        foreach ($estrutura as $membru) {
            $karguLower = strtolower($membru['kargu']);
            
            if (strpos($karguLower, 'xefe suku') !== false) {
                $xefeSuku = $membru;
            } elseif (empty($membru['id_aldeia'])) {
                // Central officers
                if (strpos($karguLower, 'sekretaria') !== false || strpos($karguLower, 'sekretariu') !== false) {
                    $secretariaSuku = $membru;
                } elseif (
                    strpos($karguLower, 'admin') !== false || 
                    strpos($karguLower, 'finansa') !== false || 
                    strpos($karguLower, 'a.sosial') !== false || 
                    strpos($karguLower, 'a. sosial') !== false || 
                    strpos($karguLower, 'sosial') !== false || 
                    strpos($karguLower, 'social') !== false
                ) {
                    $secretariaSubordinates[] = $membru;

                } else {
                    $otherCentral[] = $membru;
                }
            }
        }

        // Build Aldeia branches dynamically
        $aldeiaBranches = [];
        foreach ($aldeias as $aldeia) {
            $xefe = null;
            $membros = [];
            
            foreach ($estrutura as $membru) {
                if ($membru['id_aldeia'] == $aldeia['id_aldeia']) {
                    $karguLower = strtolower($membru['kargu']);
                    if (strpos($karguLower, 'xefe') !== false) {
                        $xefe = $membru;
                    } else {
                        $membros[] = $membru;
                    }
                }
            }
            
            $aldeiaBranches[] = [
                'aldeia'  => $aldeia,
                'xefe'    => $xefe,
                'membros' => $membros
            ];
        }

        $data = [
            'title'                  => 'Vizualizasaun Hirarkia',
            'xefeSuku'               => $xefeSuku,
            'secretariaSuku'         => $secretariaSuku,
            'secretariaSubordinates' => $secretariaSubordinates,
            'otherCentral'           => $otherCentral,
            'aldeiaBranches'         => $aldeiaBranches,
        ];

        return view('admin/estrutura/hirarkia', $data);
    }

    /**
     * Dashboard to manage user accounts for Xefe Suku, Secretaria Suku, and Xefe Aldeia
     */
    public function manageUsers()
    {
        $db = \Config\Database::connect();
        
        $members = $db->table('tabela_estrutura_suku')
            ->select('tabela_estrutura_suku.*, tabela_populasaun.nik, tabela_populasaun.jeneru, tabela_populasaun.id_populasaun, tabela_aldeia.naran_aldeia')
            ->join('tabela_populasaun', 'tabela_populasaun.id_populasaun = tabela_estrutura_suku.id_populasaun', 'left')
            ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_estrutura_suku.id_aldeia', 'left')
            ->where('tabela_estrutura_suku.status_kargu', 'Ativu')
            ->groupStart()
                ->like('tabela_estrutura_suku.kargu', 'Xefe Suku', 'both')
                ->orLike('tabela_estrutura_suku.kargu', 'Secretaria', 'both')
                ->orLike('tabela_estrutura_suku.kargu', 'Sekretariu', 'both')
                ->orLike('tabela_estrutura_suku.kargu', 'Sekretaria', 'both')
                ->orLike('tabela_estrutura_suku.kargu', 'Xefe Aldeia', 'both')
            ->groupEnd()
            ->get()
            ->getResultArray();

        $linkedUsers = [];
        if (!empty($members)) {
            $linkedUsers = $db->table('users')
                ->groupStart()
                    ->whereIn('id_estrutura', array_column($members, 'id_estrutura'))
                    ->orWhereIn('id_populasaun', array_column($members, 'id_populasaun'))
                ->groupEnd()
                ->get()
                ->getResultArray();
        }

        $usersByEstrutura = [];
        foreach ($linkedUsers as $usr) {
            if ($usr['id_estrutura']) {
                $usersByEstrutura[$usr['id_estrutura']] = $usr;
            } elseif ($usr['id_populasaun']) {
                $usersByEstrutura['pop_' . $usr['id_populasaun']] = $usr;
            }
        }

        $data = [
            'title'            => 'Jestaun Membru ba User',
            'members'          => $members,
            'usersByEstrutura' => $usersByEstrutura,
        ];

        return view('admin/estrutura/users', $data);
    }

    /**
     * Create user account for structure member
     */
    public function createUser()
    {
        $rules = [
            'id_estrutura'  => 'required|integer',
            'username'      => 'required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'password'      => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id_estrutura = $this->request->getPost('id_estrutura');
        $db = \Config\Database::connect();
        
        $member = $db->table('tabela_estrutura_suku')
            ->where('id_estrutura', $id_estrutura)
            ->get()
            ->getRowArray();

        if (!$member) {
            return redirect()->back()->with('error', 'Dadus membru la hetan!');
        }

        $kargu = strtolower($member['kargu']);
        $groupId = null;
        if (strpos($kargu, 'xefe suku') !== false) {
            $groupId = 3; // xefe-suku
        } elseif (strpos($kargu, 'secretar') !== false || strpos($kargu, 'sekretar') !== false) {
            $groupId = 5; // sekretaria
        } elseif (strpos($kargu, 'xefe aldeia') !== false) {
            $groupId = 4; // xefe-aldeia
        }

        if (!$groupId) {
            return redirect()->back()->with('error', 'Kargu membru ne\'e la permiti atu kria user account!');
        }

        $passwordHash = \Myth\Auth\Password::hash($this->request->getPost('password'));

        $userData = [
            'username'      => $this->request->getPost('username'),
            'email'         => $this->request->getPost('email'),
            'password_hash' => $passwordHash,
            'id_aldeia'     => $member['id_aldeia'] ?: null,
            'id_populasaun' => $member['id_populasaun'],
            'id_estrutura'  => $id_estrutura,
            'active'        => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $db->transStart();
        
        $db->table('users')->insert($userData);
        $userId = $db->insertID();

        $db->table('auth_groups_users')->insert([
            'group_id' => $groupId,
            'user_id'  => $userId,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Falla kria konta user membru!');
        }

        return redirect()->back()->with('message', 'Konta user membru kria ho susesu!');
    }

    /**
     * Delete user account of structure member
     */
    public function deleteUser($userId = null)
    {
        $db = \Config\Database::connect();
        
        $user = $db->table('users')->where('id', $userId)->get()->getRowArray();
        if (!$user) {
            return redirect()->back()->with('error', 'Dadus user la hetan!');
        }

        if ($userId == 1) {
            return redirect()->back()->with('error', 'Labele hasai konta administrator principal!');
        }

        $db->transStart();
        
        $db->table('auth_groups_users')->where('user_id', $userId)->delete();
        $db->table('users')->where('id', $userId)->delete();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Falla hasai konta user membru!');
        }

        return redirect()->back()->with('message', 'Konta user membru hasai ho susesu!');
    }
}
