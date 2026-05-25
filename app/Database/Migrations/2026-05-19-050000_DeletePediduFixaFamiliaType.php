<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DeletePediduFixaFamiliaType extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Delete from tabela_tipu_pedidu
        $db->table('tabela_tipu_pedidu')
           ->where('naran_tipu_pedidu', 'Pedidu Fixa Familia')
           ->delete();

        // 2. Delete any pedidu record with this name
        $db->table('tabela_pedidu')
           ->where('naran_pedidu', 'Pedidu Fixa Familia')
           ->delete();
    }

    public function down()
    {
        // No down needed
    }
}
