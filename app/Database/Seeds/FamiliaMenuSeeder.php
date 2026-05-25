<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FamiliaMenuSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Get the parent "Populasaun" menu ID
        $parent = $db->table('menu')->where('title', 'Populasaun')->get()->getRow();
        if (!$parent) {
            return;
        }

        // Get roles dynamic IDs to map
        $admin = $db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        $xefeSuku = $db->table('auth_groups')->where('name', 'xefe-suku')->get()->getRow();
        $xefeAldeia = $db->table('auth_groups')->where('name', 'xefe-aldeia')->get()->getRow();
        $sekretaria = $db->table('auth_groups')->where('name', 'sekretaria')->get()->getRow();
        $groupsToMap = [$admin, $xefeSuku, $xefeAldeia, $sekretaria];

        // 2. Insert "Jestaun Familia" sub-menu
        $checkChild = $db->table('menu')->where('title', 'Jestaun Familia')->get()->getRow();
        if (!$checkChild) {
            $db->table('menu')->insert([
                'parent_id'  => $parent->id,
                'title'      => 'Jestaun Familia',
                'icon'       => 'fas fa-id-card',
                'route'      => 'admin/familia',
                'sequence'   => 14, // Put it right next to Jestaun Populasaun
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
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
