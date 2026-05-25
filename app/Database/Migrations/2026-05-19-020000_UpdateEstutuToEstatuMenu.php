<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateEstutuToEstatuMenu extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Find by title or route
        $db->table('menu')
           ->where('title', 'Estutu Populasaun')
           ->orWhere('route', 'admin/populasaun?type=estutu')
           ->update([
               'title' => 'Estatu Populasaun',
               'route' => 'admin/populasaun?type=estatu',
               'icon'  => 'fas fa-heartbeat'
           ]);
    }

    public function down()
    {
        // No down needed
    }
}
