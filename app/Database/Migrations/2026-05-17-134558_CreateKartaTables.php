<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKartaTables extends Migration
{
    public function up()
    {
        // 1. Tabela Tipu Pedidu & Formatu
        $this->forge->addField([
            'id_tipu_pedidu' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_tipu_pedidu' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'template_formatu' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_tipu_pedidu', true);
        $this->forge->createTable('tabela_tipu_pedidu');

        // 2. Tabela Karta Tama (Incoming Letters)
        $this->forge->addField([
            'id_karta_tama' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'numeru_karta' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'emitente' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'asuntu' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'data_tama' => [
                'type' => 'DATE',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_karta_tama', true);
        $this->forge->createTable('tabela_karta_tama');

        // 3. Tabela Karta Sai (Outgoing Letters)
        $this->forge->addField([
            'id_karta_sai' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'numeru_karta' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'destinatariu' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'asuntu' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'data_sai' => [
                'type' => 'DATE',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_karta_sai', true);
        $this->forge->createTable('tabela_karta_sai');
    }

    public function down()
    {
        $this->forge->dropTable('tabela_tipu_pedidu', true);
        $this->forge->dropTable('tabela_karta_tama', true);
        $this->forge->dropTable('tabela_karta_sai', true);
    }
}
