<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJestaunUserMenu extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Get the parent menu "Estrutura Suku"
        $parent = $db->table('menu')->where('title', 'Estrutura Suku')->get()->getRow();
        if ($parent) {
            $parentId = $parent->id;

            // Insert child menu "Jestaun User"
            $db->table('menu')->insert([
                'parent_id'  => $parentId,
                'title'      => 'Jestaun User',
                'icon'       => 'fas fa-users-cog',
                'route'      => 'admin/estrutura/users',
                'sequence'   => 21,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $menuId = $db->insertID();

            // Map to the relevant user groups
            $groups = $db->table('auth_groups')
                ->whereIn('name', ['admin', 'xefe-suku', 'xefe-aldeia', 'sekretaria'])
                ->get()
                ->getResultObject();

            foreach ($groups as $grp) {
                $db->table('groups_menu')->insert([
                    'group_id' => $grp->id,
                    'menu_id'  => $menuId,
                ]);
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $menu = $db->table('menu')->where('title', 'Jestaun User')->get()->getRow();
        if ($menu) {
            $db->table('groups_menu')->where('menu_id', $menu->id)->delete();
            $db->table('menu')->where('id', $menu->id)->delete();
        }
    }
}
