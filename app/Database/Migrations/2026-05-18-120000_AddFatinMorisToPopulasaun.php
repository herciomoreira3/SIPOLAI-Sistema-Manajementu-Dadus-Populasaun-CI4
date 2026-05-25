<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFatinMorisToPopulasaun extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tabela_populasaun', [
            'fatin_moris' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'naran_kompletu',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tabela_populasaun', 'fatin_moris');
    }
}
