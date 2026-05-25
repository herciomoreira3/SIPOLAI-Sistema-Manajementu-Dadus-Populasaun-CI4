<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RelatoriuMenuSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Check if parent menu "Relatoriu" already exists
        $checkParent = $db->table('menu')->where('title', 'Relatoriu')->get()->getRow();
        
        if (!$checkParent) {
            $db->table('menu')->insert([
                'parent_id'  => 0,
                'title'      => 'Relatoriu',
                'icon'       => 'fas fa-chart-line',
                'route'      => 'admin/relatoriu',
                'sequence'   => 21,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $parentId = $db->insertID();
        } else {
            $parentId = $checkParent->id;
        }

        // Get roles dynamic IDs to map
        $admin = $db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        $xefeSuku = $db->table('auth_groups')->where('name', 'xefe-suku')->get()->getRow();
        $xefeAldeia = $db->table('auth_groups')->where('name', 'xefe-aldeia')->get()->getRow();
        $sekretaria = $db->table('auth_groups')->where('name', 'sekretaria')->get()->getRow();
        $groupsToMap = [$admin, $xefeSuku, $xefeAldeia, $sekretaria];

        // Map parent menu "Relatoriu" to all roles
        foreach ($groupsToMap as $grp) {
            if ($grp) {
                $checkParentMap = $db->table('groups_menu')->where(['group_id' => $grp->id, 'menu_id' => $parentId])->get()->getRow();
                if (!$checkParentMap) {
                    $db->table('groups_menu')->insert([
                        'group_id' => $grp->id,
                        'menu_id'  => $parentId,
                    ]);
                }
            }
        }
    }
}
