<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEstruturaSukuTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_estrutura' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_populasaun' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'naran_membru' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'kargu' => [
                'type'       => 'VARCHAR',
                'constraint' => '100', // e.g., Xefe Suku, Sekretariu Suku, Xefe Aldeia Uaisa, etc.
            ],
            'periodo_hahula' => [
                'type' => 'DATE',
            ],
            'periodo_remata' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status_kargu' => [
                'type'       => 'VARCHAR',
                'constraint' => '15',
                'default'    => 'Ativu', // Ativu, Inativu
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
        $this->forge->addKey('id_estrutura', true);
        $this->forge->createTable('tabela_estrutura_suku');
    }

    public function down()
    {
        $this->forge->dropTable('tabela_estrutura_suku', true);
    }
}
