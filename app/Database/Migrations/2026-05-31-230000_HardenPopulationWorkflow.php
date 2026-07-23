<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HardenPopulationWorkflow extends Migration
{
    public function up()
    {
        $this->addPediduColumns();
        $this->createStatusHistoryTable();
        $this->createAuditLogTable();
        $this->backfillPediduPopulationIds();
        $this->normalizeCardNumbers();
        $this->addIndexes();
    }

    public function down()
    {
        $this->dropIndexIfExists('tabela_populasaun', 'idx_populasaun_aldeia_istadu');
        $this->dropIndexIfExists('tabela_populasaun', 'idx_populasaun_familia_relasaun');
        $this->dropIndexIfExists('tabela_pedidu', 'idx_pedidu_populasaun_tipu_status');
        $this->dropIndexIfExists('tabela_pedidu', 'idx_pedidu_tipu_status_aldeia');
        $this->dropIndexIfExists('tabela_pedidu', 'idx_pedidu_data');
        $this->dropIndexIfExists('tabela_populasaun', 'uniq_populasaun_no_eleitoral');
        $this->dropIndexIfExists('tabela_populasaun', 'uniq_populasaun_no_kbiit_laek');

        foreach ([
            'id_populasaun',
            'approved_by',
            'approved_at',
            'rejected_by',
            'rejected_at',
            'voided_by',
            'voided_at',
            'void_reason',
        ] as $column) {
            if ($this->db->fieldExists($column, 'tabela_pedidu')) {
                $this->forge->dropColumn('tabela_pedidu', $column);
            }
        }

        $this->forge->dropTable('tabela_populasaun_status_history', true);
        $this->forge->dropTable('tabela_audit_log', true);
    }

    private function addPediduColumns(): void
    {
        $fields = [];

        if (! $this->db->fieldExists('id_populasaun', 'tabela_pedidu')) {
            $fields['id_populasaun'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_pedidu',
            ];
        }

        foreach ([
            'approved_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'approved_at' => ['type' => 'DATETIME', 'null' => true],
            'rejected_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'rejected_at' => ['type' => 'DATETIME', 'null' => true],
            'voided_by'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'voided_at'   => ['type' => 'DATETIME', 'null' => true],
            'void_reason' => ['type' => 'TEXT', 'null' => true],
        ] as $name => $definition) {
            if (! $this->db->fieldExists($name, 'tabela_pedidu')) {
                $fields[$name] = $definition;
            }
        }

        if ($fields !== []) {
            $this->forge->addColumn('tabela_pedidu', $fields);
        }
    }

    private function createStatusHistoryTable(): void
    {
        if ($this->db->tableExists('tabela_populasaun_status_history')) {
            return;
        }

        $this->forge->addField([
            'id_history' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_populasaun' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'old_istadu' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'null'       => true,
            ],
            'new_istadu' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
            ],
            'id_pedidu' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'changed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_history', true);
        $this->forge->addKey(['id_populasaun', 'created_at']);
        $this->forge->createTable('tabela_populasaun_status_history');
    }

    private function createAuditLogTable(): void
    {
        if ($this->db->tableExists('tabela_audit_log')) {
            return;
        }

        $this->forge->addField([
            'id_audit' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'entity_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'entity_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'old_values' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'new_values' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'changed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_audit', true);
        $this->forge->addKey(['entity_type', 'entity_id']);
        $this->forge->createTable('tabela_audit_log');
    }

    private function backfillPediduPopulationIds(): void
    {
        if (! $this->db->fieldExists('id_populasaun', 'tabela_pedidu')) {
            return;
        }

        $pedidus = $this->db->table('tabela_pedidu')
            ->select('id_pedidu, pemohon')
            ->where('id_populasaun', null)
            ->get()
            ->getResultArray();

        foreach ($pedidus as $pedidu) {
            if (empty($pedidu['pemohon'])) {
                continue;
            }

            $matches = $this->db->table('tabela_populasaun')
                ->select('id_populasaun')
                ->where('naran_kompletu', $pedidu['pemohon'])
                ->get()
                ->getResultArray();

            if (count($matches) === 1) {
                $this->db->table('tabela_pedidu')
                    ->where('id_pedidu', $pedidu['id_pedidu'])
                    ->update(['id_populasaun' => $matches[0]['id_populasaun']]);
            }
        }
    }

    private function normalizeCardNumbers(): void
    {
        if ($this->db->fieldExists('no_eleitoral', 'tabela_populasaun')) {
            $this->db->table('tabela_populasaun')
                ->where('no_eleitoral', '')
                ->update(['no_eleitoral' => null]);
        }

        if ($this->db->fieldExists('no_kbiit_laek', 'tabela_populasaun')) {
            $this->db->table('tabela_populasaun')
                ->where('no_kbiit_laek', '')
                ->update(['no_kbiit_laek' => null]);
        }
    }

    private function addIndexes(): void
    {
        $this->addIndexIfMissing('tabela_populasaun', 'idx_populasaun_aldeia_istadu', ['id_aldeia', 'istadu']);
        $this->addIndexIfMissing('tabela_populasaun', 'idx_populasaun_familia_relasaun', ['id_familia', 'relasaun_familia']);
        $this->addIndexIfMissing('tabela_pedidu', 'idx_pedidu_populasaun_tipu_status', ['id_populasaun', 'naran_pedidu', 'status']);
        $this->addIndexIfMissing('tabela_pedidu', 'idx_pedidu_tipu_status_aldeia', ['naran_pedidu', 'status', 'id_aldeia']);
        $this->addIndexIfMissing('tabela_pedidu', 'idx_pedidu_data', ['data_pedidu']);

        if ($this->db->fieldExists('no_eleitoral', 'tabela_populasaun')) {
            if ($this->hasDuplicateValues('tabela_populasaun', 'no_eleitoral')) {
                log_message('warning', 'Skip unique index uniq_populasaun_no_eleitoral because duplicate no_eleitoral values exist.');
            } else {
                $this->addIndexIfMissing('tabela_populasaun', 'uniq_populasaun_no_eleitoral', ['no_eleitoral'], true);
            }
        }

        if ($this->db->fieldExists('no_kbiit_laek', 'tabela_populasaun')) {
            if ($this->hasDuplicateValues('tabela_populasaun', 'no_kbiit_laek')) {
                log_message('warning', 'Skip unique index uniq_populasaun_no_kbiit_laek because duplicate no_kbiit_laek values exist.');
            } else {
                $this->addIndexIfMissing('tabela_populasaun', 'uniq_populasaun_no_kbiit_laek', ['no_kbiit_laek'], true);
            }
        }
    }

    private function addIndexIfMissing(string $table, string $name, array $columns, bool $unique = false): void
    {
        foreach ($this->db->getIndexData($table) as $index) {
            if ($this->indexName($index) === $name) {
                return;
            }
        }

        $columnList = implode(', ', array_map(static fn ($column) => "`{$column}`", $columns));
        $uniqueSql = $unique ? 'UNIQUE ' : '';
        $this->db->query("CREATE {$uniqueSql}INDEX `{$name}` ON `{$table}` ({$columnList})");
    }

    private function dropIndexIfExists(string $table, string $name): void
    {
        foreach ($this->db->getIndexData($table) as $index) {
            if ($this->indexName($index) !== $name) {
                continue;
            }

            $this->db->query("DROP INDEX `{$name}` ON `{$table}`");
            return;
        }
    }

    private function indexName($index): string
    {
        if (is_array($index)) {
            return $index['name'] ?? '';
        }

        return $index->name ?? '';
    }

    private function hasDuplicateValues(string $table, string $column): bool
    {
        $result = $this->db->query(
            "SELECT `{$column}` FROM `{$table}` WHERE `{$column}` IS NOT NULL AND `{$column}` <> '' GROUP BY `{$column}` HAVING COUNT(*) > 1 LIMIT 1"
        )->getRowArray();

        return ! empty($result);
    }
}
