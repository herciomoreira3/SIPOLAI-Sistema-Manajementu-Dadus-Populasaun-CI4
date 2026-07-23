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
        $this->aldeiaModel     = new AldeiaModel();
    }

    public function index()
    {
        // DataTables server-side always sends 'draw' param.
        // We use this instead of isAJAX() because reverse proxies (Render.com)
        // strip the X-Requested-With header, making isAJAX() always return false.
        if ($this->request->getGet('draw') !== null) {
            try {
                $db     = \Config\Database::connect();
                $start  = (int) ($this->request->getGet('start') ?? 0);
                $length = (int) ($this->request->getGet('length') ?? 10);
                $search = trim((string) ($this->request->getGet('search[value]') ?? ''));
                $filterAldeia = (int) ($this->request->getGet('id_aldeia') ?? 0);

                // ----------------------------------------------------------------
                // Build optional WHERE fragments (safe, parameterised via escape)
                // ----------------------------------------------------------------
                $extraWhere = '';

                // Role-based aldeia restriction
                if (function_exists('in_groups') && in_groups('xefe-aldeia') && !empty(user()->id_aldeia)) {
                    $extraWhere .= ' AND p.id_aldeia = ' . (int) user()->id_aldeia;
                }

                // Filter by selected aldeia
                if ($filterAldeia > 0) {
                    $extraWhere .= ' AND p.id_aldeia = ' . $filterAldeia;
                }

                // Search
                $searchWhere = '';
                if ($search !== '') {
                    $s = $db->escapeString($search);
                    $searchWhere = " AND (p.naran_kompletu LIKE '%{$s}%'
                                      OR p.nik            LIKE '%{$s}%'
                                      OR p.no_kbiit_laek  LIKE '%{$s}%'
                                      OR a.naran_aldeia   LIKE '%{$s}%')";
                }

                // ----------------------------------------------------------------
                // Step 1 — collect id_populasaun that have an approved declaration.
                //   Primary: linked via tabela_pedidu.id_populasaun
                //   Fallback: linked via pemohon + id_aldeia (for rows inserted
                //             by seeder before the id_populasaun column existed)
                // ----------------------------------------------------------------
                $idRows = $db->query("
                    SELECT DISTINCT
                        COALESCE(tp.id_populasaun, p2.id_populasaun) AS id_pop
                    FROM tabela_pedidu tp
                    LEFT JOIN tabela_populasaun p2
                        ON tp.id_populasaun IS NULL
                        AND tp.pemohon    = p2.naran_kompletu
                        AND tp.id_aldeia  = p2.id_aldeia
                    WHERE tp.naran_pedidu = 'Deklarasaun Kbiit Laek'
                      AND tp.status       = 'Aprovadu'
                      AND COALESCE(tp.id_populasaun, p2.id_populasaun) IS NOT NULL
                ")->getResultArray();

                $approvedIds = array_column($idRows, 'id_pop');

                // No approved declarations at all → return empty DataTables response
                if (empty($approvedIds)) {
                    return $this->response->setJSON([
                        'draw'            => (int) $this->request->getGet('draw'),
                        'recordsTotal'    => 0,
                        'recordsFiltered' => 0,
                        'data'            => [],
                    ]);
                }

                $inList = implode(',', array_map('intval', $approvedIds));

                // ----------------------------------------------------------------
                // Step 2 — total count (all matching, before search filter)
                // ----------------------------------------------------------------
                $totalRow = $db->query("
                    SELECT COUNT(*) AS cnt
                    FROM tabela_populasaun p
                    LEFT JOIN tabela_aldeia a ON a.id_aldeia = p.id_aldeia
                    WHERE p.istadu        = 'Moris'
                      AND p.id_populasaun IN ({$inList})
                    {$extraWhere}
                ")->getRowArray();
                $recordsTotal = (int) ($totalRow['cnt'] ?? 0);

                // ----------------------------------------------------------------
                // Step 3 — filtered count (with search)
                // ----------------------------------------------------------------
                $filteredRow = $db->query("
                    SELECT COUNT(*) AS cnt
                    FROM tabela_populasaun p
                    LEFT JOIN tabela_aldeia a ON a.id_aldeia = p.id_aldeia
                    WHERE p.istadu        = 'Moris'
                      AND p.id_populasaun IN ({$inList})
                    {$extraWhere}
                    {$searchWhere}
                ")->getRowArray();
                $recordsFiltered = (int) ($filteredRow['cnt'] ?? 0);

                // ----------------------------------------------------------------
                // Step 4 — fetch page data with latest approved pedidu details
                // ----------------------------------------------------------------
                $data = $db->query("
                    SELECT
                        p.id_populasaun,
                        p.nik,
                        p.naran_kompletu,
                        p.jeneru,
                        p.no_kbiit_laek,
                        p.istadu,
                        a.naran_aldeia,
                        tp.data_pedidu  AS data_aprovada,
                        tp.id_pedidu
                    FROM tabela_populasaun p
                    LEFT JOIN tabela_aldeia a ON a.id_aldeia = p.id_aldeia
                    LEFT JOIN tabela_pedidu tp ON tp.id_pedidu = (
                        SELECT MAX(tp2.id_pedidu)
                        FROM tabela_pedidu tp2
                        WHERE tp2.naran_pedidu = 'Deklarasaun Kbiit Laek'
                          AND tp2.status       = 'Aprovadu'
                          AND (
                              tp2.id_populasaun = p.id_populasaun
                              OR (
                                  tp2.id_populasaun IS NULL
                                  AND tp2.pemohon   = p.naran_kompletu
                                  AND tp2.id_aldeia = p.id_aldeia
                              )
                          )
                    )
                    WHERE p.istadu        = 'Moris'
                      AND p.id_populasaun IN ({$inList})
                    {$extraWhere}
                    {$searchWhere}
                    ORDER BY p.naran_kompletu ASC
                    LIMIT {$length} OFFSET {$start}
                ")->getResultArray();

                return $this->response->setJSON([
                    'draw'            => (int) $this->request->getGet('draw'),
                    'recordsTotal'    => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data'            => $data,
                ]);

            } catch (\Throwable $e) {
                log_message('error', '[KbiitLaekController::index] ' . $e->getMessage()
                    . ' | ' . $e->getFile() . ':' . $e->getLine());
                // Return valid DataTables error payload (HTTP 200) so we can
                // surface the real message in the browser console instead of TN/7.
                return $this->response->setStatusCode(200)->setJSON([
                    'draw'            => (int) ($this->request->getGet('draw') ?? 1),
                    'recordsTotal'    => 0,
                    'recordsFiltered' => 0,
                    'data'            => [],
                    'error'           => 'Erro internu: ' . $e->getMessage(),
                ]);
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
