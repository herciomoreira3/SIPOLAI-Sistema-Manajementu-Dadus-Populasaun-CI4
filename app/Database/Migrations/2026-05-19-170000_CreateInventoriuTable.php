<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventoriuTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_inventoriu' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_pedidu' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'naran_kompletu' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'jeneru' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'data_moris' => [
                'type' => 'DATE',
            ],
            'fatin_moris' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'naran_aldeia' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'no_eleitoral' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'no_kbiit_laek' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'meta_data' => [
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

        $this->forge->addKey('id_inventoriu', true);
        $this->forge->addKey('id_pedidu');
        $this->forge->createTable('tabela_inventoriu');
    }

    public function down()
    {
        $this->forge->dropTable('tabela_inventoriu');
    }
}
