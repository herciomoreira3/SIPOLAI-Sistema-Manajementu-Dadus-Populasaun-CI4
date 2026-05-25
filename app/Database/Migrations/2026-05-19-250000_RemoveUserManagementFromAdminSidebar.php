<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveUserManagementFromAdminSidebar extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Get admin group ID
        $group = $db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        if (!$group) {
            return;
        }

        // 2. Find Menu ID for 'User Management'
        $menu = $db->table('menu')->where('title', 'User Management')->get()->getRow();
        if ($menu) {
            // Delete from groups_menu
            $db->table('groups_menu')
                ->where('group_id', $group->id)
                ->where('menu_id', $menu->id)
                ->delete();
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $group = $db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        if (!$group) {
            return;
        }

        $menu = $db->table('menu')->where('title', 'User Management')->get()->getRow();
        if ($menu) {
            $exists = $db->table('groups_menu')->where(['group_id' => $group->id, 'menu_id' => $menu->id])->get()->getRow();
            if (!$exists) {
                $db->table('groups_menu')->insert([
                    'group_id' => $group->id,
                    'menu_id'  => $menu->id
                ]);
            }
        }
    }
}
