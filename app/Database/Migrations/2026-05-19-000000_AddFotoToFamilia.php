<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoToFamilia extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tabela_familia', [
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tabela_familia', 'foto');
    }
}
