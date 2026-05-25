<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConsolidateInventoriuMenu extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Find the parent menu "Inventoriu Deklarasaun" or "Inventoriu"
        $parent = $db->table('menu')
            ->groupStart()
                ->where('title', 'Inventoriu Deklarasaun')
                ->orWhere('title', 'Inventoriu')
            ->groupEnd()
            ->get()
            ->getRow();

        if ($parent) {
            $parentId = $parent->id;

            // Update parent menu to point directly to "admin/inventoriu" and rename to "Inventoriu"
            $db->table('menu')
               ->where('id', $parentId)
               ->update([
                   'title' => 'Inventoriu',
                   'route' => 'admin/inventoriu',
                   'icon'  => 'fas fa-box-open'
               ]);

            // 2. Find all child submenus under this parent
            $children = $db->table('menu')
                           ->where('parent_id', $parentId)
                           ->get()
                           ->getResultArray();

            // 3. Delete all children and their mappings in groups_menu
            if (!empty($children)) {
                $childIds = array_column($children, 'id');
                $db->table('groups_menu')->whereIn('menu_id', $childIds)->delete();
                $db->table('menu')->whereIn('id', $childIds)->delete();
            }
        }
    }

    public function down()
    {
        // No down needed as it's a consolidation migration
    }
}
