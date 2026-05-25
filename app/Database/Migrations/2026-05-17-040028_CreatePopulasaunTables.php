<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePopulasaunTables extends Migration
{
    public function up()
    {
        // Tabela Familia (KK)
        $this->forge->addField([
            'id_familia' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'numeru_kk' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'id_aldeia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->addKey('id_familia', true);
        $this->forge->createTable('tabela_familia');

        // Tabela Populasaun
        $this->forge->addField([
            'id_populasaun' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'naran_kompletu' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'data_moris' => [
                'type' => 'DATE',
            ],
            'jeneru' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'status_kaza' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'id_aldeia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_profisaun' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_relijiaun' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_literatura' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_familia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'relasaun_familia' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'istadu' => [
                'type'       => 'VARCHAR',
                'constraint' => '15',
                'default'    => 'Moris', // Moris, Mate, Muda
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
        $this->forge->addKey('id_populasaun', true);
        $this->forge->createTable('tabela_populasaun');

        // Tabela Pedidu
        $this->forge->addField([
            'id_pedidu' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_pedidu' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'pemohon' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'data_pedidu' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '15',
                'default'    => 'Pendiente', // Pendiente, Aprovado, Rejeitado
            ],
            'id_aldeia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addKey('id_pedidu', true);
        $this->forge->createTable('tabela_pedidu');

        // Add id_aldeia to users table
        $this->forge->addColumn('users', [
            'id_aldeia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tabela_familia', true);
        $this->forge->dropTable('tabela_populasaun', true);
        $this->forge->dropTable('tabela_pedidu', true);
        $this->forge->dropColumn('users', 'id_aldeia');
    }
}
