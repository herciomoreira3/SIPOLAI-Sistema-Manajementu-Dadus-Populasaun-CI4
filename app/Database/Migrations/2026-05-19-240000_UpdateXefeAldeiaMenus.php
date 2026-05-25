<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateXefeAldeiaMenus extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Get xefe-aldeia group ID
        $group = $db->table('auth_groups')->where('name', 'xefe-aldeia')->get()->getRow();
        if (!$group) {
            return;
        }

        // 2. Submenus to remove from xefe-aldeia role
        $routesToRemove = [
            'admin/estrutura',         // Membru Suku
            'admin/promosaun',          // Promosaun Membru
            'admin/kargu',              // Jestaun Kargu
            'admin/estrutura/users'     // Jestaun User
        ];

        $menus = $db->table('menu')->whereIn('route', $routesToRemove)->get()->getResultArray();
        if (!empty($menus)) {
            $menuIds = array_column($menus, 'id');
            $db->table('groups_menu')
                ->where('group_id', $group->id)
                ->whereIn('menu_id', $menuIds)
                ->delete();
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $group = $db->table('auth_groups')->where('name', 'xefe-aldeia')->get()->getRow();
        if (!$group) {
            return;
        }

        // Add back the removed submenus
        $routesToRemove = [
            'admin/estrutura',
            'admin/promosaun',
            'admin/kargu',
            'admin/estrutura/users'
        ];

        $menus = $db->table('menu')->whereIn('route', $routesToRemove)->get()->getResultArray();
        foreach ($menus as $menu) {
            $exists = $db->table('groups_menu')->where(['group_id' => $group->id, 'menu_id' => $menu['id']])->get()->getRow();
            if (!$exists) {
                $db->table('groups_menu')->insert([
                    'group_id' => $group->id,
                    'menu_id'  => $menu['id']
                ]);
            }
        }
    }
}
