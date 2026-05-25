<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterTables extends Migration
{
    public function up()
    {
        // Tabela Aldeia
        $this->forge->addField([
            'id_aldeia' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_aldeia' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'id_suku' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1, // Default Laisorulai
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
        $this->forge->addKey('id_aldeia', true);
        $this->forge->createTable('tabela_aldeia');

        // Tabela Profisaun
        $this->forge->addField([
            'id_profisaun' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_profisaun' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->addKey('id_profisaun', true);
        $this->forge->createTable('tabela_profisaun');

        // Tabela Relijiaun
        $this->forge->addField([
            'id_relijiaun' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_relijiaun' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->addKey('id_relijiaun', true);
        $this->forge->createTable('tabela_relijiaun');

        // Tabela Literatura
        $this->forge->addField([
            'id_literatura' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_literatura' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->addKey('id_literatura', true);
        $this->forge->createTable('tabela_literatura');
    }

    public function down()
    {
        $this->forge->dropTable('tabela_aldeia', true);
        $this->forge->dropTable('tabela_profisaun', true);
        $this->forge->dropTable('tabela_relijiaun', true);
        $this->forge->dropTable('tabela_literatura', true);
    }
}
