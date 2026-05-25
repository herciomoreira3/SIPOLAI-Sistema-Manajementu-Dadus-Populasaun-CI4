<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ReconstructPediduAndInventoriuMenus extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Rename "Inventoriu" parent menu to "Inventoriu Deklarasaun"
        $db->table('menu')
           ->where('title', 'Inventoriu')
           ->update([
               'title' => 'Inventoriu Deklarasaun',
               'icon'  => 'fas fa-box-open'
           ]);

        // Find parent IDs
        $inventoriuParent = $db->table('menu')->where('title', 'Inventoriu Deklarasaun')->get()->getRow();
        $pediduParent = $db->table('menu')->where('title', 'Pedidu')->get()->getRow();

        // Get group IDs to map
        $admin = $db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        $xefeSuku = $db->table('auth_groups')->where('name', 'xefe-suku')->get()->getRow();
        $xefeAldeia = $db->table('auth_groups')->where('name', 'xefe-aldeia')->get()->getRow();
        $sekretaria = $db->table('auth_groups')->where('name', 'sekretaria')->get()->getRow();
        $groupsToMap = [$admin, $xefeSuku, $sekretaria];

        // 2. Delete unwanted submenus under "Pedidu" and "Inventoriu Deklarasaun"
        $unwantedSubmenus = [
            'Tipu Pedidu',
            'Karta Tama',
            'Karta Sai',
            'Karta Tama (Inv)',
            'Karta Sai (Inv)'
        ];

        foreach ($unwantedSubmenus as $title) {
            $menuItem = $db->table('menu')->where('title', $title)->get()->getRow();
            if ($menuItem) {
                // Delete mappings in groups_menu
                $db->table('groups_menu')->where('menu_id', $menuItem->id)->delete();
                // Delete menu
                $db->table('menu')->where('id', $menuItem->id)->delete();
            }
        }

        // 3. Rename "Formatu Pedidu" to "Formatu Deklarasaun"
        $db->table('menu')
           ->where('title', 'Formatu Pedidu')
           ->update([
               'title' => 'Formatu Deklarasaun',
               'route' => 'admin/formatu-deklarasaun'
           ]);

        // 4. Create new submenus under "Inventoriu Deklarasaun"
        if ($inventoriuParent) {
            $newChildren = [
                [
                    'parent_id'  => $inventoriuParent->id,
                    'title'      => 'Deklarasaun Eleitoral',
                    'icon'       => 'fas fa-vote-yea',
                    'route'      => 'admin/pedidu?naran_pedidu=Deklarasaun+Eleitoral',
                    'sequence'   => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'parent_id'  => $inventoriuParent->id,
                    'title'      => 'Deklarasaun Nascimentu',
                    'icon'       => 'fas fa-baby',
                    'route'      => 'admin/pedidu?naran_pedidu=Deklarasaun+Nascimentu',
                    'sequence'   => 2,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'parent_id'  => $inventoriuParent->id,
                    'title'      => 'Deklarasaun Mortalidade',
                    'icon'       => 'fas fa-book-dead',
                    'route'      => 'admin/pedidu?naran_pedidu=Deklarasaun+Mortalidade',
                    'sequence'   => 3,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ];

            foreach ($newChildren as $child) {
                // Check if already exists
                $check = $db->table('menu')->where(['title' => $child['title'], 'parent_id' => $child['parent_id']])->get()->getRow();
                if (!$check) {
                    $db->table('menu')->insert($child);
                    $childId = $db->insertID();

                    // Map to specified groups (admin, xefe-suku, sekretaria)
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

        // 5. Seed default types into tabela_tipu_pedidu
        $typesToSeed = [
            ['naran_tipu_pedidu' => 'Deklarasaun Eleitoral'],
            ['naran_tipu_pedidu' => 'Deklarasaun Nascimentu'],
            ['naran_tipu_pedidu' => 'Deklarasaun Mortalidade']
        ];

        foreach ($typesToSeed as $type) {
            $checkType = $db->table('tabela_tipu_pedidu')->where('naran_tipu_pedidu', $type['naran_tipu_pedidu'])->get()->getRow();
            if (!$checkType) {
                $db->table('tabela_tipu_pedidu')->insert([
                    'naran_tipu_pedidu' => $type['naran_tipu_pedidu'],
                    'template_formatu'  => '',
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    public function down()
    {
        // No down needed
    }
}
