<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateEstatuToEstatutuMenu extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        $db->table('menu')
           ->groupStart()
               ->where('title', 'Estatu Populasaun')
               ->orWhere('title', 'Estutu Populasaun')
               ->orWhere('route', 'admin/populasaun?type=estatu')
               ->orWhere('route', 'admin/populasaun?type=estutu')
           ->groupEnd()
           ->update([
               'title' => 'Estatutu Populasaun',
               'route' => 'admin/populasaun?type=estatutu',
               'icon'  => 'fas fa-heartbeat'
           ]);
    }

    public function down()
    {
        // No down needed
    }
}
