<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PopulasaunMenuSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Get the parent "Populasaun" menu ID
        $parent = $db->table('menu')->where('title', 'Populasaun')->get()->getRow();
        if (!$parent) {
            return; // Parent must exist
        }

        // Get roles dynamic IDs to map
        $admin = $db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        $xefeSuku = $db->table('auth_groups')->where('name', 'xefe-suku')->get()->getRow();
        $xefeAldeia = $db->table('auth_groups')->where('name', 'xefe-aldeia')->get()->getRow();
        $sekretaria = $db->table('auth_groups')->where('name', 'sekretaria')->get()->getRow();
        $groupsToMap = [$admin, $xefeSuku, $xefeAldeia, $sekretaria];

        // 2. Define the new sub-menus
        $children = [
            [
                'parent_id'  => $parent->id,
                'title'      => 'Estatutu Populasaun',
                'icon'       => 'fas fa-heartbeat',
                'route'      => 'admin/populasaun?type=estatutu',
                'sequence'   => 16,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => $parent->id,
                'title'      => 'Dados Eleitores',
                'icon'       => 'fas fa-id-card',
                'route'      => 'admin/eleitores',
                'sequence'   => 17,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => $parent->id,
                'title'      => 'Dados Kbiit Laek',
                'icon'       => 'fas fa-hand-holding-heart',
                'route'      => 'admin/kbiit-laek',
                'sequence'   => 18,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($children as $child) {
            $checkChild = $db->table('menu')->where('title', $child['title'])->get()->getRow();
            if (!$checkChild) {
                $db->table('menu')->insert($child);
                $childId = $db->insertID();

                // Map to all groups
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
