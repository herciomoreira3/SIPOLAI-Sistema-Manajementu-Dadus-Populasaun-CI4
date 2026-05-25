<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLinkColumnsToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'id_populasaun' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'id_aldeia',
            ],
            'id_estrutura' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'id_populasaun',
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'id_populasaun');
        $this->forge->dropColumn('users', 'id_estrutura');
    }
}
