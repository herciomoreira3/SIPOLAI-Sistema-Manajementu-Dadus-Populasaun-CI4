<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EstruturaMenuSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Check if parent menu "Estrutura Suku" already exists
        $checkParent = $db->table('menu')->where('title', 'Estrutura Suku')->get()->getRow();
        
        if (!$checkParent) {
            $db->table('menu')->insert([
                'parent_id'  => 0,
                'title'      => 'Estrutura Suku',
                'icon'       => 'fas fa-sitemap',
                'route'      => '#',
                'sequence'   => 17,
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

        // Map parent menu "Estrutura Suku" to all roles
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

        // Insert Children
        $children = [
            [
                'parent_id'  => $parentId,
                'title'      => 'Membru Suku',
                'icon'       => 'fas fa-list',
                'route'      => 'admin/estrutura',
                'sequence'   => 18,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => $parentId,
                'title'      => 'Promosaun Membru',
                'icon'       => 'fas fa-level-up-alt',
                'route'      => 'admin/promosaun',
                'sequence'   => 19,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => $parentId,
                'title'      => 'Hirarkia Suku',
                'icon'       => 'fas fa-project-diagram',
                'route'      => 'admin/hirarkia',
                'sequence'   => 20,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => $parentId,
                'title'      => 'Jestaun User',
                'icon'       => 'fas fa-users-cog',
                'route'      => 'admin/estrutura/users',
                'sequence'   => 21,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($children as $child) {
            $checkChild = $db->table('menu')->where('title', $child['title'])->get()->getRow();
            if (!$checkChild) {
                $db->table('menu')->insert($child);
                $childId = $db->insertID();

                // Map to all roles
                foreach ($groupsToMap as $grp) {
                    if ($grp) {
                        $db->table('groups_menu')->insert([
                            'group_id' => $grp->id,
                            'menu_id'  => $childId,
                        ]);
                    }
                }
            }
        }
    }
}
