<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLinkColumnsToUsers extends Migration
{
    public function up()
    {
        $field1 = [
            'id_populasaun' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ]
        ];
        $this->forge->addColumn('users', $field1);

        $field2 = [
            'id_estrutura' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ]
        ];
        $this->forge->addColumn('users', $field2);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'id_populasaun');
        $this->forge->dropColumn('users', 'id_estrutura');
    }
}
