<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDashboardAndRemoveKarguFromXefeSuku extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Get group IDs
        $xefeSuku = $db->table('auth_groups')->where('name', 'xefe-suku')->get()->getRow();
        $xefeAldeia = $db->table('auth_groups')->where('name', 'xefe-aldeia')->get()->getRow();
        $sekretaria = $db->table('auth_groups')->where('name', 'sekretaria')->get()->getRow();

        // 2. Get Menu ID for Dashboard (route = 'admin')
        $dashboardMenu = $db->table('menu')->where('route', 'admin')->get()->getRow();
        
        if ($dashboardMenu) {
            // Map Dashboard to xefe-suku
            if ($xefeSuku) {
                $exists = $db->table('groups_menu')->where(['group_id' => $xefeSuku->id, 'menu_id' => $dashboardMenu->id])->get()->getRow();
                if (!$exists) {
                    $db->table('groups_menu')->insert(['group_id' => $xefeSuku->id, 'menu_id' => $dashboardMenu->id]);
                }
            }
            // Map Dashboard to xefe-aldeia
            if ($xefeAldeia) {
                $exists = $db->table('groups_menu')->where(['group_id' => $xefeAldeia->id, 'menu_id' => $dashboardMenu->id])->get()->getRow();
                if (!$exists) {
                    $db->table('groups_menu')->insert(['group_id' => $xefeAldeia->id, 'menu_id' => $dashboardMenu->id]);
                }
            }
            // Map Dashboard to sekretaria
            if ($sekretaria) {
                $exists = $db->table('groups_menu')->where(['group_id' => $sekretaria->id, 'menu_id' => $dashboardMenu->id])->get()->getRow();
                if (!$exists) {
                    $db->table('groups_menu')->insert(['group_id' => $sekretaria->id, 'menu_id' => $dashboardMenu->id]);
                }
            }
        }

        // 3. Remove Jestaun Kargu (route = 'admin/kargu') from xefe-suku
        $karguMenu = $db->table('menu')->where('route', 'admin/kargu')->get()->getRow();
        if ($karguMenu && $xefeSuku) {
            $db->table('groups_menu')
                ->where('group_id', $xefeSuku->id)
                ->where('menu_id', $karguMenu->id)
                ->delete();
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $xefeSuku = $db->table('auth_groups')->where('name', 'xefe-suku')->get()->getRow();
        $xefeAldeia = $db->table('auth_groups')->where('name', 'xefe-aldeia')->get()->getRow();
        $sekretaria = $db->table('auth_groups')->where('name', 'sekretaria')->get()->getRow();
        $dashboardMenu = $db->table('menu')->where('route', 'admin')->get()->getRow();

        if ($dashboardMenu) {
            if ($xefeSuku) {
                $db->table('groups_menu')->where(['group_id' => $xefeSuku->id, 'menu_id' => $dashboardMenu->id])->delete();
            }
            if ($xefeAldeia) {
                $db->table('groups_menu')->where(['group_id' => $xefeAldeia->id, 'menu_id' => $dashboardMenu->id])->delete();
            }
            if ($sekretaria) {
                $db->table('groups_menu')->where(['group_id' => $sekretaria->id, 'menu_id' => $dashboardMenu->id])->delete();
            }
        }

        $karguMenu = $db->table('menu')->where('route', 'admin/kargu')->get()->getRow();
        if ($karguMenu && $xefeSuku) {
            $db->table('groups_menu')->insert(['group_id' => $xefeSuku->id, 'menu_id' => $karguMenu->id]);
        }
    }
}
