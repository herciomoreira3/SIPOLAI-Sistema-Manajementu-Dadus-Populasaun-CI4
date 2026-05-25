<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KartaMenuSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Get the parent menus
        $pediduParent = $db->table('menu')->where('title', 'Pedidu')->get()->getRow();
        
        // Check if parent menu "Inventoriu" already exists, otherwise create it
        $inventoriuParent = $db->table('menu')->where('title', 'Inventoriu')->get()->getRow();
        if (!$inventoriuParent) {
            $db->table('menu')->insert([
                'parent_id'  => 0,
                'title'      => 'Inventoriu',
                'icon'       => 'fas fa-archive',
                'route'      => '#',
                'sequence'   => 20,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $inventoriuParent = $db->table('menu')->where('title', 'Inventoriu')->get()->getRow();
        }

        // Get roles dynamic IDs to map
        $admin = $db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        $xefeSuku = $db->table('auth_groups')->where('name', 'xefe-suku')->get()->getRow();
        $xefeAldeia = $db->table('auth_groups')->where('name', 'xefe-aldeia')->get()->getRow();
        $sekretaria = $db->table('auth_groups')->where('name', 'sekretaria')->get()->getRow();

        // Map Inventoriu parent menu to Admin
        if ($admin && $inventoriuParent) {
            $checkMap = $db->table('groups_menu')->where(['group_id' => $admin->id, 'menu_id' => $inventoriuParent->id])->get()->getRow();
            if (!$checkMap) {
                $db->table('groups_menu')->insert([
                    'group_id' => $admin->id,
                    'menu_id'  => $inventoriuParent->id,
                ]);
            }
        }

        // 2. Define the Pedidu submenus
        if ($pediduParent) {
            $pediduChildren = [
                [
                    'parent_id'  => $pediduParent->id,
                    'title'      => 'Tipu Pedidu',
                    'icon'       => 'fas fa-cog',
                    'route'      => 'admin/tipu-pedidu',
                    'sequence'   => 4,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'roles'      => [$admin, $xefeAldeia] // Only Admin & Xefe Aldeia
                ],
                [
                    'parent_id'  => $pediduParent->id,
                    'title'      => 'Formatu Pedidu',
                    'icon'       => 'fas fa-file-signature',
                    'route'      => 'admin/formatu-pedidu',
                    'sequence'   => 5,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'roles'      => [$admin] // Only Admin
                ],
                [
                    'parent_id'  => $pediduParent->id,
                    'title'      => 'Karta Tama',
                    'icon'       => 'fas fa-envelope-open-text',
                    'route'      => 'admin/karta-tama',
                    'sequence'   => 6,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'roles'      => [$admin, $sekretaria] // Only Admin & Sekretaria
                ],
                [
                    'parent_id'  => $pediduParent->id,
                    'title'      => 'Karta Sai',
                    'icon'       => 'fas fa-paper-plane',
                    'route'      => 'admin/karta-sai',
                    'sequence'   => 7,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'roles'      => [$admin, $sekretaria] // Only Admin & Sekretaria
                ],
            ];

            foreach ($pediduChildren as $child) {
                // Check if submenu exists under this parent
                $checkChild = $db->table('menu')->where(['title' => $child['title'], 'parent_id' => $child['parent_id']])->get()->getRow();
                if (!$checkChild) {
                    $roles = $child['roles'];
                    unset($child['roles']);
                    
                    $db->table('menu')->insert($child);
                    $childId = $db->insertID();

                    // Map to specified roles
                    foreach ($roles as $r) {
                        if ($r) {
                            $db->table('groups_menu')->insert([
                                'group_id' => $r->id,
                                'menu_id'  => $childId,
                            ]);
                        }
                    }
                }
            }
        }

        // 3. Define the Inventoriu submenus (Admin only)
        if ($inventoriuParent) {
            $inventoriuChildren = [
                [
                    'parent_id'  => $inventoriuParent->id,
                    'title'      => 'Karta Tama (Inv)',
                    'icon'       => 'fas fa-envelope-open-text',
                    'route'      => 'admin/karta-tama',
                    'sequence'   => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'parent_id'  => $inventoriuParent->id,
                    'title'      => 'Karta Sai (Inv)',
                    'icon'       => 'fas fa-paper-plane',
                    'route'      => 'admin/karta-sai',
                    'sequence'   => 2,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ];

            foreach ($inventoriuChildren as $child) {
                $checkChild = $db->table('menu')->where(['title' => $child['title'], 'parent_id' => $child['parent_id']])->get()->getRow();
                if (!$checkChild) {
                    $db->table('menu')->insert($child);
                    $childId = $db->insertID();

                    if ($admin) {
                        $db->table('groups_menu')->insert([
                            'group_id' => $admin->id,
                            'menu_id'  => $childId,
                        ]);
                    }
                }
            }
        }
    }
}
