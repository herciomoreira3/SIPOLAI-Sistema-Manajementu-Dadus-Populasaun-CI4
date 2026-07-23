<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * BackfillPediduIdPopulasaun
 *
 * Fixes production data where RealDataSeeder inserted tabela_pedidu rows
 * without id_populasaun, causing EleitorController and KbiitLaekController
 * INNER JOINs (which rely on tp.id_populasaun = tabela_populasaun.id_populasaun)
 * to return 0 rows and trigger HTTP 500 errors.
 *
 * Matches by (naran_pedidu, pemohon, id_aldeia) → id_populasaun.
 */
class BackfillPediduIdPopulasaun extends Migration
{
    public function up(): void
    {
        // Guard: column must exist
        if (! $this->db->fieldExists('id_populasaun', 'tabela_pedidu')) {
            return;
        }

        // Fetch only unlinked pedidu rows that could be matched
        $pedidus = $this->db->table('tabela_pedidu')
            ->select('id_pedidu, naran_pedidu, pemohon, id_aldeia')
            ->where('id_populasaun', null)
            ->whereIn('naran_pedidu', [
                'Deklarasaun Eleitoral',
                'Deklarasaun Eleitoral Lakon',
                'Deklarasaun Kbiit Laek',
                'Deklarasaun Bom Comportamentu',
            ])
            ->get()
            ->getResultArray();

        if (empty($pedidus)) {
            log_message('info', 'BackfillPediduIdPopulasaun: no unlinked pedidu rows found, nothing to do.');
            return;
        }

        $updated = 0;

        foreach ($pedidus as $pedidu) {
            if (empty($pedidu['pemohon'])) {
                continue;
            }

            // Match by full name + aldeia for precision
            $candidates = $this->db->table('tabela_populasaun')
                ->select('id_populasaun')
                ->where('naran_kompletu', $pedidu['pemohon'])
                ->where('id_aldeia', $pedidu['id_aldeia'])
                ->get()
                ->getResultArray();

            // Only link when there is exactly one unambiguous match
            if (count($candidates) === 1) {
                $this->db->table('tabela_pedidu')
                    ->where('id_pedidu', $pedidu['id_pedidu'])
                    ->update(['id_populasaun' => $candidates[0]['id_populasaun']]);
                $updated++;
            }
        }

        log_message('info', "BackfillPediduIdPopulasaun: linked {$updated} of " . count($pedidus) . " pedidu rows.");
    }

    public function down(): void
    {
        // Non-destructive — we cannot safely un-link without knowing which were
        // set by this migration vs. by the application itself.
    }
}
