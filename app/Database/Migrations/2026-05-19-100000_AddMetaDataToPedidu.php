<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMetaDataToPedidu extends Migration
{
    public function up()
    {
        $fields = [
            'meta_data' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'id_aldeia'
            ]
        ];
        $this->forge->addColumn('tabela_pedidu', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tabela_pedidu', 'meta_data');
    }
}
