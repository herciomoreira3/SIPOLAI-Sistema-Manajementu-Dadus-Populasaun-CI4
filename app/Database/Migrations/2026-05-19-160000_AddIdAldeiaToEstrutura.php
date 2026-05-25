<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdAldeiaToEstrutura extends Migration
{
    public function up()
    {
        $fields = [
            'id_aldeia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_populasaun'
            ]
        ];
        $this->forge->addColumn('tabela_estrutura_suku', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tabela_estrutura_suku', 'id_aldeia');
    }
}
