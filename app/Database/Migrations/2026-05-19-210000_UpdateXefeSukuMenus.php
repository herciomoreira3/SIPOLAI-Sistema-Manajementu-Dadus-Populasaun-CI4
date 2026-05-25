<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateXefeSukuMenus extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Get xefe-suku group ID
        $group = $db->table('auth_groups')->where('name', 'xefe-suku')->get()->getRow();
        if (!$group) {
            return;
        }

        // 2. Identify the menus to remove
        $routesToRemove = [
            'admin/estrutura',         // Membru Suku
            'admin/promosaun',          // Promosaun Membru
            'admin/estrutura/users',    // Jestaun User
            'admin/formatu-relatoriu'   // Formatu Relatoriu
        ];

        $menus = $db->table('menu')->whereIn('route', $routesToRemove)->get()->getResultArray();
        if (!empty($menus)) {
            $menuIds = array_column($menus, 'id');
            
            // Delete from groups_menu
            $db->table('groups_menu')
                ->where('group_id', $group->id)
                ->whereIn('menu_id', $menuIds)
                ->delete();
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Get xefe-suku group ID
        $group = $db->table('auth_groups')->where('name', 'xefe-suku')->get()->getRow();
        if (!$group) {
            return;
        }

        // Identify the menus to add back
        $routesToRemove = [
            'admin/estrutura',
            'admin/promosaun',
            'admin/estrutura/users',
            'admin/formatu-relatoriu'
        ];

        $menus = $db->table('menu')->whereIn('route', $routesToRemove)->get()->getResultArray();
        foreach ($menus as $menu) {
            // Check if already mapped
            $exists = $db->table('groups_menu')
                ->where(['group_id' => $group->id, 'menu_id' => $menu['id']])
                ->get()
                ->getRow();
            if (!$exists) {
                $db->table('groups_menu')->insert([
                    'group_id' => $group->id,
                    'menu_id'  => $menu['id']
                ]);
            }
        }
    }
}
