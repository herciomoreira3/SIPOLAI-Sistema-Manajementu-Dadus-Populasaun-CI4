<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateSekretariaMenus extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Get sekretaria group ID
        $group = $db->table('auth_groups')->where('name', 'sekretaria')->get()->getRow();
        if (!$group) {
            return;
        }

        // 2. Submenus to remove from sekretaria role
        $routesToRemove = [
            'admin/estrutura',         // Membru Suku
            'admin/promosaun',          // Promosaun Membru
            'admin/kargu',              // Jestaun Kargu
            'admin/estrutura/users',    // Jestaun User
            'admin/formatu-relatoriu'   // Formatu Relatoriu
        ];

        $menus = $db->table('menu')->whereIn('route', $routesToRemove)->get()->getResultArray();
        if (!empty($menus)) {
            $menuIds = array_column($menus, 'id');
            $db->table('groups_menu')
                ->where('group_id', $group->id)
                ->whereIn('menu_id', $menuIds)
                ->delete();
        }

        // 3. Add Inventoriu menu (route = admin/inventoriu) to sekretaria role
        $inventoriuMenu = $db->table('menu')->where('route', 'admin/inventoriu')->get()->getRow();
        if ($inventoriuMenu) {
            $exists = $db->table('groups_menu')->where(['group_id' => $group->id, 'menu_id' => $inventoriuMenu->id])->get()->getRow();
            if (!$exists) {
                $db->table('groups_menu')->insert([
                    'group_id' => $group->id,
                    'menu_id'  => $inventoriuMenu->id
                ]);
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $group = $db->table('auth_groups')->where('name', 'sekretaria')->get()->getRow();
        if (!$group) {
            return;
        }

        // Add back the removed submenus
        $routesToRemove = [
            'admin/estrutura',
            'admin/promosaun',
            'admin/kargu',
            'admin/estrutura/users',
            'admin/formatu-relatoriu'
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

        // Remove Inventoriu menu from sekretaria
        $inventoriuMenu = $db->table('menu')->where('route', 'admin/inventoriu')->get()->getRow();
        if ($inventoriuMenu) {
            $db->table('groups_menu')
                ->where('group_id', $group->id)
                ->where('menu_id', $inventoriuMenu->id)
                ->delete();
        }
    }
}
