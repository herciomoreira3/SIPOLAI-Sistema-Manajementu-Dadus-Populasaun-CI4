<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PopulasaunModel;
use App\Models\FamiliaModel;
use App\Models\AldeiaModel;
use App\Models\ProfisaunModel;
use App\Models\LiteraturaModel;

class RelatoriuController extends BaseController
{
    protected $populasaunModel;
    protected $familiaModel;
    protected $aldeiaModel;
    protected $profisaunModel;
    protected $literaturaModel;

    public function __construct()
    {
        $this->populasaunModel = new PopulasaunModel();
        $this->familiaModel = new FamiliaModel();
        $this->aldeiaModel = new AldeiaModel();
        $this->profisaunModel = new ProfisaunModel();
        $this->literaturaModel = new LiteraturaModel();

        // Automatically clean up deleted Maternidade template
        $db = \Config\Database::connect();
        $db->table('tabela_formatu_relatoriu')->where('naran_relatoriu', 'Relatoriu Maternidade')->delete();
    }

    public function index()
    {
        $data = [
            'title'    => 'Relatoriu Suku',
            'subtitle' => 'Sentru Relatoriu SIPOLAI Suku Laisorolai de Baixo'
        ];

        return view('admin/relatoriu/index', $data);
    }

    public function populasaun()
    {
        $idAldeia = $this->request->getGet('id_aldeia');

        $query = $this->populasaunModel->select('tabela_populasaun.*, tabela_aldeia.naran_aldeia, tabela_profisaun.naran_profisaun, tabela_literatura.naran_literatura, tabela_relijiaun.naran_relijiaun')
                                        ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left')
                                        ->join('tabela_profisaun', 'tabela_profisaun.id_profisaun = tabela_populasaun.id_profisaun', 'left')
                                        ->join('tabela_literatura', 'tabela_literatura.id_literatura = tabela_populasaun.id_literatura', 'left')
                                        ->join('tabela_relijiaun', 'tabela_relijiaun.id_relijiaun = tabela_populasaun.id_relijiaun', 'left')
                                        ->where('tabela_populasaun.istadu', 'Moris');

        // Role restriction for Xefe Aldeia
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $idAldeia = user()->id_aldeia;
        }

        if (!empty($idAldeia)) {
            $query = $query->where('tabela_populasaun.id_aldeia', $idAldeia);
        }

        $populasaun = $query->findAll();

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $formatuModel = new \App\Models\FormatuRelatoriuModel();
        $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Populasaun Suku')->first();

        $data = [
            'title'        => 'Relatoriu Total Populasaun Suku',
            'subtitle'     => 'Filtru no imprime dadus populasaun Laisorolai de Baixo',
            'populasaun'   => $populasaun,
            'aldeias'      => $aldeias,
            'filter_aldeia'=> $idAldeia,
            'cop_temp'     => $formatu ? $formatu['template_cop'] : '',
        ];

        return view('admin/relatoriu/populasaun', $data);
    }

    public function familia()
    {
        $idAldeia = $this->request->getGet('id_aldeia');

        $query = $this->familiaModel->select('tabela_familia.*, tabela_aldeia.naran_aldeia')
                                    ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_familia.id_aldeia', 'left');

        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $idAldeia = user()->id_aldeia;
        }

        if (!empty($idAldeia)) {
            $query = $query->where('tabela_familia.id_aldeia', $idAldeia);
        }

        $familias = $query->findAll();

        foreach ($familias as &$f) {
            $f['total_membros'] = $this->populasaunModel->where('id_familia', $f['id_familia'])->countAllResults();
            $xefe = $this->populasaunModel->where([
                'id_familia' => $f['id_familia'],
                'relasaun_familia' => 'Xefe Familia'
            ])->first();
            $f['xefe_familia'] = $xefe ? $xefe['naran_kompletu'] : '-';
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $formatuModel = new \App\Models\FormatuRelatoriuModel();
        $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Fixa Familia')->first();
        if (!$formatu) {
            $defaultCop = '<div class="text-center mb-4" style="font-family: \'Times New Roman\', Times, serif; color: #1e293b;">
    <h4 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 18px;">REPÚBLICA DEMOCRÁTICA DE TIMOR-LESTE</h4>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 16px;">MINISTÉRIO DA ADMINISTRAÇÃO ESTATAL</h5>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 15px;">MUNICÍPIO DE BAUCAU</h5>
    <h6 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 14px;">POSTO ADMINISTRATIVO DE MATEBIAN</h6>
    <h6 style="font-weight: bold; margin-bottom: 12px; text-transform: uppercase; font-size: 14px;">SUCO LAISOROLAI DE BAIXO</h6>
    <div style="border-bottom: 3px double #000000; width: 100%; margin-top: 5px; margin-bottom: 15px;"></div>
</div>';
            $formatuModel->insert([
                'naran_relatoriu' => 'Relatoriu Fixa Familia',
                'template_cop'    => $defaultCop
            ]);
            $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Fixa Familia')->first();
        }

        $data = [
            'title'         => 'Relatoriu Fixa Familia',
            'subtitle'      => 'Lista Fixa Familia Suku Laisorolai de Baixo',
            'familias'      => $familias,
            'aldeias'       => $aldeias,
            'filter_aldeia' => $idAldeia,
            'cop_temp'      => $formatu ? $formatu['template_cop'] : '',
        ];

        return view('admin/relatoriu/familia', $data);
    }


    public function mortalidade()
    {
        $idAldeia = $this->request->getGet('id_aldeia');

        // Deceased population (istadu = 'Mate')
        $query = $this->populasaunModel->select('tabela_populasaun.*, tabela_aldeia.naran_aldeia')
                                        ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left')
                                        ->where('tabela_populasaun.istadu', 'Mate');

        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $idAldeia = user()->id_aldeia;
        }

        if (!empty($idAldeia)) {
            $query = $query->where('tabela_populasaun.id_aldeia', $idAldeia);
        }

        $mortalidade = $query->findAll();

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $formatuModel = new \App\Models\FormatuRelatoriuModel();
        $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Mortalidade')->first();
        if (!$formatu) {
            $defaultCop = '<div class="text-center mb-4" style="font-family: \'Times New Roman\', Times, serif; color: #1e293b;">
    <h4 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 18px;">REPÚBLICA DEMOCRÁTICA DE TIMOR-LESTE</h4>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 16px;">MINISTÉRIO DA ADMINISTRAÇÃO ESTATAL</h5>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 15px;">MUNICÍPIO DE BAUCAU</h5>
    <h6 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 14px;">POSTO ADMINISTRATIVO DE MATEBIAN</h6>
    <h6 style="font-weight: bold; margin-bottom: 12px; text-transform: uppercase; font-size: 14px;">SUCO LAISOROLAI DE BAIXO</h6>
    <div style="border-bottom: 3px double #000000; width: 100%; margin-top: 5px; margin-bottom: 15px;"></div>
</div>';
            $formatuModel->insert([
                'naran_relatoriu' => 'Relatoriu Mortalidade',
                'template_cop'    => $defaultCop
            ]);
            $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Mortalidade')->first();
        }

        $data = [
            'title'         => 'Relatoriu Numeru Mortalidade',
            'subtitle'      => 'Dadus populasaun ne\'ebé mate ona Suku Laisorolai de Baixo',
            'membros'       => $mortalidade,
            'aldeias'       => $aldeias,
            'filter_aldeia' => $idAldeia,
            'cop_temp'      => $formatu ? $formatu['template_cop'] : '',
        ];

        return view('admin/relatoriu/mortalidade', $data);
    }

    public function nascimentu()
    {
        $idAldeia = $this->request->getGet('id_aldeia');
        $db = \Config\Database::connect();
        
        $builder = $db->table('tabela_pedidu');
        $builder->where('tabela_pedidu.naran_pedidu', 'Deklarasaun Nascimentu');
        $builder->where('tabela_pedidu.status', 'Aprovadu');
        
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $idAldeia = user()->id_aldeia;
        }
        
        if (!empty($idAldeia)) {
            $builder->where('tabela_pedidu.id_aldeia', $idAldeia);
        }
        
        $builder->select('tabela_pedidu.*, tabela_aldeia.naran_aldeia')
                ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_pedidu.id_aldeia', 'left');
        
        $nascimentu = $builder->get()->getResultArray();

        foreach ($nascimentu as &$row) {
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

        $formatuModel = new \App\Models\FormatuRelatoriuModel();
        $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Nascimentu')->first();
        if (!$formatu) {
            $defaultCop = '<div class="text-center mb-4" style="font-family: \'Times New Roman\', Times, serif; color: #1e293b;">
    <h4 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 18px;">REPÚBLICA DEMOCRÁTICA DE TIMOR-LESTE</h4>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 16px;">MINISTÉRIO DA ADMINISTRAÇÃO ESTATAL</h5>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 15px;">MUNICÍPIO DE BAUCAU</h5>
    <h6 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 14px;">POSTO ADMINISTRATIVO DE MATEBIAN</h6>
    <h6 style="font-weight: bold; margin-bottom: 12px; text-transform: uppercase; font-size: 14px;">SUCO LAISOROLAI DE BAIXO</h6>
    <div style="border-bottom: 3px double #000000; width: 100%; margin-top: 5px; margin-bottom: 15px;"></div>
</div>';
            $formatuModel->insert([
                'naran_relatoriu' => 'Relatoriu Nascimentu',
                'template_cop'    => $defaultCop
            ]);
            $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Nascimentu')->first();
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $data = [
            'title'         => 'Relatoriu Nascimentu Suku',
            'subtitle'      => 'Dadus rejistu no estatistika bebes/nascimentu foun iha Suku Laisorolai de Baixo',
            'membros'       => $nascimentu,
            'aldeias'       => $aldeias,
            'filter_aldeia' => $idAldeia,
            'cop_temp'      => $formatu ? $formatu['template_cop'] : '',
        ];

        return view('admin/relatoriu/nascimentu', $data);
    }

    public function muda()
    {
        $idAldeia = $this->request->getGet('id_aldeia');
        $db = \Config\Database::connect();
        
        $query = $this->populasaunModel->select('tabela_populasaun.*, tabela_aldeia.naran_aldeia')
                                        ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left')
                                        ->where('tabela_populasaun.istadu', 'Muda');
        
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $idAldeia = user()->id_aldeia;
        }
        
        if (!empty($idAldeia)) {
            $query = $query->where('tabela_populasaun.id_aldeia', $idAldeia);
        }
        
        $muda = $query->findAll();

        foreach ($muda as &$row) {
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

        $formatuModel = new \App\Models\FormatuRelatoriuModel();
        $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Muda Domisiliu')->first();
        if (!$formatu) {
            $defaultCop = '<div class="text-center mb-4" style="font-family: \'Times New Roman\', Times, serif; color: #1e293b;">
    <h4 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 18px;">REPÚBLICA DEMOCRÁTICA DE TIMOR-LESTE</h4>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 16px;">MINISTÉRIO DA ADMINISTRAÇÃO ESTATAL</h5>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 15px;">MUNICÍPIO DE BAUCAU</h5>
    <h6 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 14px;">POSTO ADMINISTRATIVO DE MATEBIAN</h6>
    <h6 style="font-weight: bold; margin-bottom: 12px; text-transform: uppercase; font-size: 14px;">SUCO LAISOROLAI DE BAIXO</h6>
    <div style="border-bottom: 3px double #000000; width: 100%; margin-top: 5px; margin-bottom: 15px;"></div>
</div>';
            $formatuModel->insert([
                'naran_relatoriu' => 'Relatoriu Muda Domisiliu',
                'template_cop'    => $defaultCop
            ]);
            $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Muda Domisiliu')->first();
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $data = [
            'title'         => 'Relatoriu Muda Domisiliu',
            'subtitle'      => 'Estatistika no lista populasaun sira ne\'ebé muda domisiliu sai husi Suku Laisorolai',
            'membros'       => $muda,
            'aldeias'       => $aldeias,
            'filter_aldeia' => $idAldeia,
            'cop_temp'      => $formatu ? $formatu['template_cop'] : '',
        ];

        return view('admin/relatoriu/muda', $data);
    }

    public function eleitores()
    {
        $idAldeia = $this->request->getGet('id_aldeia');
        
        $query = $this->populasaunModel->select('tabela_populasaun.*, tabela_aldeia.naran_aldeia')
                                        ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left')
                                        ->where('tabela_populasaun.istadu', 'Moris')
                                        ->where('tabela_populasaun.no_eleitoral !=', '');
        
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $idAldeia = user()->id_aldeia;
        }
        
        if (!empty($idAldeia)) {
            $query = $query->where('tabela_populasaun.id_aldeia', $idAldeia);
        }
        
        $eleitores = $query->findAll();

        foreach ($eleitores as &$row) {
            $birthDate = new \DateTime($row['data_moris']);
            $today = new \DateTime('today');
            $row['tinan'] = $birthDate->diff($today)->y;
        }

        $formatuModel = new \App\Models\FormatuRelatoriuModel();
        $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Eleitores')->first();
        if (!$formatu) {
            $defaultCop = '<div class="text-center mb-4" style="font-family: \'Times New Roman\', Times, serif; color: #1e293b;">
    <h4 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 18px;">REPÚBLICA DEMOCRÁTICA DE TIMOR-LESTE</h4>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 16px;">MINISTÉRIO DA ADMINISTRAÇÃO ESTATAL</h5>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 15px;">MUNICÍPIO DE BAUCAU</h5>
    <h6 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 14px;">POSTO ADMINISTRATIVO DE MATEBIAN</h6>
    <h6 style="font-weight: bold; margin-bottom: 12px; text-transform: uppercase; font-size: 14px;">SUCO LAISOROLAI DE BAIXO</h6>
    <div style="border-bottom: 3px double #000000; width: 100%; margin-top: 5px; margin-bottom: 15px;"></div>
</div>';
            $formatuModel->insert([
                'naran_relatoriu' => 'Relatoriu Eleitores',
                'template_cop'    => $defaultCop
            ]);
            $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Eleitores')->first();
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $data = [
            'title'         => 'Relatoriu Eleitores Active',
            'subtitle'      => 'Estatutu no lista dadus eleitores (voters) ne\'ebé ativu iha Suku Laisorolai de Baixo',
            'membros'       => $eleitores,
            'aldeias'       => $aldeias,
            'filter_aldeia' => $idAldeia,
            'cop_temp'      => $formatu ? $formatu['template_cop'] : '',
        ];

        return view('admin/relatoriu/eleitores', $data);
    }

    public function kbiitLaek()
    {
        $idAldeia = $this->request->getGet('id_aldeia');
        
        $query = $this->populasaunModel->select('tabela_populasaun.*, tabela_aldeia.naran_aldeia, tabela_profisaun.naran_profisaun')
                                        ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left')
                                        ->join('tabela_profisaun', 'tabela_profisaun.id_profisaun = tabela_populasaun.id_profisaun', 'left')
                                        ->where('tabela_populasaun.istadu', 'Moris')
                                        ->where('tabela_populasaun.no_kbiit_laek !=', '');
        
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $idAldeia = user()->id_aldeia;
        }
        
        if (!empty($idAldeia)) {
            $query = $query->where('tabela_populasaun.id_aldeia', $idAldeia);
        }
        
        $kbiitLaek = $query->findAll();

        $formatuModel = new \App\Models\FormatuRelatoriuModel();
        $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Kbiit Laek')->first();
        if (!$formatu) {
            $defaultCop = '<div class="text-center mb-4" style="font-family: \'Times New Roman\', Times, serif; color: #1e293b;">
    <h4 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 18px;">REPÚBLICA DEMOCRÁTICA DE TIMOR-LESTE</h4>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 16px;">MINISTÉRIO DA ADMINISTRAÇÃO ESTATAL</h5>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 15px;">MUNICÍPIO DE BAUCAU</h5>
    <h6 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 14px;">POSTO ADMINISTRATIVO DE MATEBIAN</h6>
    <h6 style="font-weight: bold; margin-bottom: 12px; text-transform: uppercase; font-size: 14px;">SUCO LAISOROLAI DE BAIXO</h6>
    <div style="border-bottom: 3px double #000000; width: 100%; margin-top: 5px; margin-bottom: 15px;"></div>
</div>';
            $formatuModel->insert([
                'naran_relatoriu' => 'Relatoriu Kbiit Laek',
                'template_cop'    => $defaultCop
            ]);
            $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Kbiit Laek')->first();
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $data = [
            'title'         => 'Relatoriu Kbiit Laek Active',
            'subtitle'      => 'Relatoriu no lista benefisiariu apoiu sosial (kbiit laek) ativu iha Suku Laisorolai',
            'membros'       => $kbiitLaek,
            'aldeias'       => $aldeias,
            'filter_aldeia' => $idAldeia,
            'cop_temp'      => $formatu ? $formatu['template_cop'] : '',
        ];

        return view('admin/relatoriu/kbiit_laek', $data);
    }

    public function pedidu()
    {
        $idAldeia = $this->request->getGet('id_aldeia');
        $status = $this->request->getGet('status');
        $db = \Config\Database::connect();
        
        $builder = $db->table('tabela_pedidu');
        
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $idAldeia = user()->id_aldeia;
        }
        
        if (!empty($idAldeia)) {
            $builder->where('tabela_pedidu.id_aldeia', $idAldeia);
        }
        
        if (!empty($status)) {
            $builder->where('tabela_pedidu.status', $status);
        }
        
        $builder->select('tabela_pedidu.*, tabela_aldeia.naran_aldeia')
                ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_pedidu.id_aldeia', 'left');
        
        $pedidu = $builder->get()->getResultArray();

        $formatuModel = new \App\Models\FormatuRelatoriuModel();
        $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Pedidu')->first();
        if (!$formatu) {
            $defaultCop = '<div class="text-center mb-4" style="font-family: \'Times New Roman\', Times, serif; color: #1e293b;">
    <h4 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 18px;">REPÚBLICA DEMOCRÁTICA DE TIMOR-LESTE</h4>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 16px;">MINISTÉRIO DA ADMINISTRAÇÃO ESTATAL</h5>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 15px;">MUNICÍPIO DE BAUCAU</h5>
    <h6 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 14px;">POSTO ADMINISTRATIVO DE MATEBIAN</h6>
    <h6 style="font-weight: bold; margin-bottom: 12px; text-transform: uppercase; font-size: 14px;">SUCO LAISOROLAI DE BAIXO</h6>
    <div style="border-bottom: 3px double #000000; width: 100%; margin-top: 5px; margin-bottom: 15px;"></div>
</div>';
            $formatuModel->insert([
                'naran_relatoriu' => 'Relatoriu Pedidu',
                'template_cop'    => $defaultCop
            ]);
            $formatu = $formatuModel->where('naran_relatoriu', 'Relatoriu Pedidu')->first();
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        $data = [
            'title'         => 'Relatoriu Pedidu Dokumentu',
            'subtitle'      => 'Relatoriu jestaun pedidu dokumentu no deklarasaun hotu-hotu iha Suku Laisorolai',
            'membros'       => $pedidu,
            'aldeias'       => $aldeias,
            'filter_aldeia' => $idAldeia,
            'filter_status' => $status,
            'cop_temp'      => $formatu ? $formatu['template_cop'] : '',
        ];

        return view('admin/relatoriu/pedidu', $data);
    }

    // Formatu Relatoriu Index
    public function formatuIndex()
    {
        $formatuModel = new \App\Models\FormatuRelatoriuModel();
        $data = [
            'title'    => 'Formatu Relatoriu (COP)',
            'subtitle' => 'Konfigurasaun Formatu Header/COP ba hotu-hotu Relatoriu Suku',
            'formats'  => $formatuModel->findAll()
        ];

        return view('admin/relatoriu/formatu_index', $data);
    }

    // Formatu Relatoriu Edit
    public function formatuEdit($id = null)
    {
        $formatuModel = new \App\Models\FormatuRelatoriuModel();
        $format = $formatuModel->find($id);
        if (!$format) {
            return redirect()->to('admin/formatu-relatoriu')->with('error', 'Formatu Relatoriu la hetan!');
        }

        $data = [
            'title'    => 'Konfigura Formatu COP Relatoriu',
            'subtitle' => 'Hadia template header/COP ba ' . esc($format['naran_relatoriu']),
            'format'   => $format
        ];

        return view('admin/relatoriu/formatu_edit', $data);
    }

    // Formatu Relatoriu Update
    public function formatuUpdate($id = null)
    {
        $formatuModel = new \App\Models\FormatuRelatoriuModel();
        $formatuModel->update($id, [
            'template_cop' => $this->request->getPost('template_cop')
        ]);

        return redirect()->to('admin/formatu-relatoriu')->with('message', 'Formatu COP relatoriu aktualizadu ho susesu!');
    }
}
