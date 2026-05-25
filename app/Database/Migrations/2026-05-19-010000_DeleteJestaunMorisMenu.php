<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DeleteJestaunMorisMenu extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Find the "Jestaun Moris" menu record
        $menu = $db->table('menu')->where('title', 'Jestaun Moris')->get()->getRow();
        if ($menu) {
            // Delete group mappings first
            $db->table('groups_menu')->where('menu_id', $menu->id)->delete();
            // Delete menu record
            $db->table('menu')->where('id', $menu->id)->delete();
        }
    }

    public function down()
    {
        // No rollback is needed because the removal is intentional and permanent
    }
}
