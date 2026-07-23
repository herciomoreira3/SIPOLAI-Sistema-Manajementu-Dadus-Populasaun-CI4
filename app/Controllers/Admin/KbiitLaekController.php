<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PopulasaunModel;
use App\Models\AldeiaModel;
use CodeIgniter\API\ResponseTrait;

class KbiitLaekController extends BaseController
{
    use ResponseTrait;

    protected $populasaunModel;
    protected $aldeiaModel;

    public function __construct()
    {
        $this->populasaunModel = new PopulasaunModel();
        $this->aldeiaModel = new AldeiaModel();
    }

    public function index()
    {
        // Use 'draw' param (always sent by DataTables server-side) instead of isAJAX()
        // because Render.com's reverse proxy strips X-Requested-With header.
        if ($this->request->getGet('draw') !== null) {
            try {
                $start     = (int) ($this->request->getGet('start') ?? 0);
                $length    = (int) ($this->request->getGet('length') ?? 10);
                $search    = $this->request->getGet('search[value]') ?? '';
                $id_aldeia = $this->request->getGet('id_aldeia');

                $db = \Config\Database::connect();

                $baseBuilder = function () use ($db, $id_aldeia) {
                    // Dual-condition match: prefer id_populasaun link (set by migration/app),
                    // fall back to pemohon=naran_kompletu for rows the backfill could not link
                    // (e.g. ambiguous names). Both conditions are scoped to the same aldeia
                    // to avoid cross-aldeia false matches.
                    $latestApprovedPedidu = '(SELECT MAX(tp.id_pedidu) FROM tabela_pedidu tp'
                        . ' WHERE (tp.id_populasaun = tabela_populasaun.id_populasaun'
                        . '        OR (tp.id_populasaun IS NULL'
                        . '            AND tp.pemohon = tabela_populasaun.naran_kompletu'
                        . '            AND tp.id_aldeia = tabela_populasaun.id_aldeia))'
                        . " AND tp.naran_pedidu = " . $db->escape('Deklarasaun Kbiit Laek')
                        . " AND tp.status = 'Aprovadu')";

                    $builder = $db->table('tabela_populasaun')
                        ->join('tabela_pedidu', "tabela_pedidu.id_pedidu = {$latestApprovedPedidu}", 'inner', false)
                        ->where('tabela_populasaun.istadu', 'Moris');

                    if (function_exists('in_groups') && in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                        $builder->where('tabela_populasaun.id_aldeia', user()->id_aldeia);
                    }
                    if (!empty($id_aldeia)) {
                        $builder->where('tabela_populasaun.id_aldeia', $id_aldeia);
                    }
                    return $builder;
                };

                // 1. recordsTotal
                $recordsTotal = $baseBuilder()->countAllResults();

                // 2. recordsFiltered (with search)
                $filterBuilder = $baseBuilder();
                $filterBuilder->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left');
                if (!empty($search)) {
                    $filterBuilder->groupStart()
                        ->like('tabela_populasaun.naran_kompletu', $search)
                        ->orLike('tabela_populasaun.nik', $search)
                        ->orLike('tabela_populasaun.no_kbiit_laek', $search)
                        ->orLike('tabela_aldeia.naran_aldeia', $search)
                        ->groupEnd();
                }
                $recordsFiltered = $filterBuilder->countAllResults();

                // 3. fetch data
                $dataBuilder = $baseBuilder();
                $dataBuilder
                    ->select('tabela_populasaun.id_populasaun, tabela_populasaun.nik, tabela_populasaun.naran_kompletu, tabela_populasaun.jeneru, tabela_populasaun.no_kbiit_laek, tabela_populasaun.istadu, tabela_aldeia.naran_aldeia, tabela_pedidu.data_pedidu as data_aprovada, tabela_pedidu.id_pedidu')
                    ->join('tabela_aldeia', 'tabela_aldeia.id_aldeia = tabela_populasaun.id_aldeia', 'left');
                if (!empty($search)) {
                    $dataBuilder->groupStart()
                        ->like('tabela_populasaun.naran_kompletu', $search)
                        ->orLike('tabela_populasaun.nik', $search)
                        ->orLike('tabela_populasaun.no_kbiit_laek', $search)
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

            } catch (\Throwable $e) {
                log_message('error', '[KbiitLaekController::index] ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
                return $this->failServerError('Erro internu: ' . $e->getMessage());
            }
        }

        $aldeias = $this->aldeiaModel->findAll();
        if (in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
            $aldeias = $this->aldeiaModel->where('id_aldeia', user()->id_aldeia)->findAll();
        }

        return view('admin/kbiit-laek/index', [
            'title'    => 'Dados Kbiit Laek',
            'subtitle' => 'Dadus Populasaun ne\'ebé hetan ona Deklarasaun Kbiit Laek husi Suku no rejistradu hanesan Família Kbiit Laek',
            'aldeias'  => $aldeias,
        ]);
    }

    public function update($id = null)
    {
        if (!in_groups(['admin', 'xefe-suku', 'sekretaria'])) {
            return $this->failForbidden('Ita boot la iha kbiit/autorizasaun atu atualiza dadus kbiit laek!');
        }

        $populasaun = $this->populasaunModel->find($id);
        if (!$populasaun) {
            return $this->failNotFound('Dadus populasaun la hetan!');
        }
        if (! $this->canAccessAldeia($populasaun['id_aldeia'])) {
            return $this->failForbidden('Ita boot labele atualiza kbiit laek husi aldeia seluk.');
        }

        $db = \Config\Database::connect();
        $approvedPedidu = $db->table('tabela_pedidu')
            ->where('id_populasaun', $id)
            ->where('naran_pedidu', 'Deklarasaun Kbiit Laek')
            ->where('status', 'Aprovadu')
            ->countAllResults();
        if ($approvedPedidu === 0) {
            return $this->fail('Sidadaun nee seidauk iha Deklarasaun Kbiit Laek aprovadu.');
        }

        $noKbiitLaek = trim((string) $this->request->getPost('no_kbiit_laek'));
        if ($noKbiitLaek !== '' && strlen($noKbiitLaek) > 50) {
            return $this->fail('Numeru Kartaun/Sertifikadu Kbiit Laek labele liu karakter 50.');
        }
        if ($noKbiitLaek !== '') {
            $exists = $this->populasaunModel
                ->where('no_kbiit_laek', $noKbiitLaek)
                ->where('id_populasaun !=', $id)
                ->first();
            if ($exists) {
                return $this->fail('Numeru Kartaun/Sertifikadu Kbiit Laek nee uza ona husi sidadaun seluk.');
            }
        }

        $this->populasaunModel->update($id, [
            'no_kbiit_laek' => $noKbiitLaek !== '' ? $noKbiitLaek : null
        ]);

        return $this->respond([
            'status'  => true,
            'message' => 'Númeru Kartaun/Sertifikadu Kbiit Laek atualizadu ho susesu!'
        ]);
    }
}
