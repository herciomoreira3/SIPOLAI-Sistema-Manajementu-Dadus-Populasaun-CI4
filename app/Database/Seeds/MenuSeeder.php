<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Check if parent menu "Dados Principal" already exists
        $checkParent = $db->table('menu')->where('title', 'Dados Principal')->get()->getRow();
        
        if (!$checkParent) {
            $db->table('menu')->insert([
                'parent_id'  => 0,
                'title'      => 'Dados Principal',
                'icon'       => 'fas fa-database',
                'route'      => '#',
                'sequence'   => 8,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $parentId = $db->insertID();
        } else {
            $parentId = $checkParent->id;
        }

        // Insert Children
        $children = [
            [
                'parent_id'  => $parentId,
                'title'      => 'Jestaun Aldeia',
                'icon'       => 'fas fa-map-marked-alt',
                'route'      => 'admin/aldeia',
                'sequence'   => 9,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => $parentId,
                'title'      => 'Jestaun Profisaun',
                'icon'       => 'fas fa-briefcase',
                'route'      => 'admin/profisaun',
                'sequence'   => 10,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => $parentId,
                'title'      => 'Jestaun Relijiaun',
                'icon'       => 'fas fa-hands-helping',
                'route'      => 'admin/relijiaun',
                'sequence'   => 11,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => $parentId,
                'title'      => 'Jestaun Literatura',
                'icon'       => 'fas fa-book',
                'route'      => 'admin/literatura',
                'sequence'   => 12,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($children as $child) {
            $checkChild = $db->table('menu')->where('title', $child['title'])->get()->getRow();
            if (!$checkChild) {
                $db->table('menu')->insert($child);
                $childId = $db->insertID();

                // Map to admin group (group_id = 1)
                $db->table('groups_menu')->insert([
                    'group_id' => 1,
                    'menu_id'  => $childId,
                ]);
            }
        }

        // Map parent menu "Dados Principal" to admin group if not already mapped
        $checkParentMap = $db->table('groups_menu')->where(['group_id' => 1, 'menu_id' => $parentId])->get()->getRow();
        if (!$checkParentMap) {
            $db->table('groups_menu')->insert([
                'group_id' => 1,
                'menu_id'  => $parentId,
            ]);
        }

        // --- POPULASAUN MENU ---
        // Get roles dynamic IDs
        $admin = $db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        $xefeSuku = $db->table('auth_groups')->where('name', 'xefe-suku')->get()->getRow();
        $xefeAldeia = $db->table('auth_groups')->where('name', 'xefe-aldeia')->get()->getRow();
        $sekretaria = $db->table('auth_groups')->where('name', 'sekretaria')->get()->getRow();

        $checkPopParent = $db->table('menu')->where('title', 'Populasaun')->get()->getRow();
        if (!$checkPopParent) {
            $db->table('menu')->insert([
                'parent_id'  => 0,
                'title'      => 'Populasaun',
                'icon'       => 'fas fa-users',
                'route'      => '#',
                'sequence'   => 13,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $popParentId = $db->insertID();
        } else {
            $popParentId = $checkPopParent->id;
        }

        // Map Populasaun parent menu to Admin, Xefe Suku, Xefe Aldeia, Sekretaria
        $groupsToMap = [$admin, $xefeSuku, $xefeAldeia, $sekretaria];
        foreach ($groupsToMap as $grp) {
            if ($grp) {
                $checkMap = $db->table('groups_menu')->where(['group_id' => $grp->id, 'menu_id' => $popParentId])->get()->getRow();
                if (!$checkMap) {
                    $db->table('groups_menu')->insert([
                        'group_id' => $grp->id,
                        'menu_id'  => $popParentId,
                    ]);
                }
            }
        }

        // Insert Child: Jestaun Populasaun
        $checkPopChild = $db->table('menu')->where('title', 'Jestaun Populasaun')->get()->getRow();
        if (!$checkPopChild) {
            $db->table('menu')->insert([
                'parent_id'  => $popParentId,
                'title'      => 'Jestaun Populasaun',
                'icon'       => 'fas fa-user-friends',
                'route'      => 'admin/populasaun',
                'sequence'   => 14,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $popChildId = $db->insertID();
        } else {
            $popChildId = $checkPopChild->id;
        }

        // Map Child to Admin, Xefe Suku, Xefe Aldeia, Sekretaria
        foreach ($groupsToMap as $grp) {
            if ($grp) {
                $checkMap = $db->table('groups_menu')->where(['group_id' => $grp->id, 'menu_id' => $popChildId])->get()->getRow();
                if (!$checkMap) {
                    $db->table('groups_menu')->insert([
                        'group_id' => $grp->id,
                        'menu_id'  => $popChildId,
                    ]);
                }
            }
        }

        // --- PEDIDU MENU ---
        $checkPedParent = $db->table('menu')->where('title', 'Pedidu')->get()->getRow();
        if (!$checkPedParent) {
            $db->table('menu')->insert([
                'parent_id'  => 0,
                'title'      => 'Pedidu',
                'icon'       => 'fas fa-file-signature',
                'route'      => '#',
                'sequence'   => 15,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $pedParentId = $db->insertID();
        } else {
            $pedParentId = $checkPedParent->id;
        }

        // Map Pedidu parent menu to Admin, Xefe Suku, Xefe Aldeia, Sekretaria
        foreach ($groupsToMap as $grp) {
            if ($grp) {
                $checkMap = $db->table('groups_menu')->where(['group_id' => $grp->id, 'menu_id' => $pedParentId])->get()->getRow();
                if (!$checkMap) {
                    $db->table('groups_menu')->insert([
                        'group_id' => $grp->id,
                        'menu_id'  => $pedParentId,
                    ]);
                }
            }
        }

        // Insert Child: Jestaun Pedidu
        $checkPedChild = $db->table('menu')->where('title', 'Jestaun Pedidu')->get()->getRow();
        if (!$checkPedChild) {
            $db->table('menu')->insert([
                'parent_id'  => $pedParentId,
                'title'      => 'Jestaun Pedidu',
                'icon'       => 'fas fa-file-alt',
                'route'      => 'admin/pedidu',
                'sequence'   => 16,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $pedChildId = $db->insertID();
        } else {
            $pedChildId = $checkPedChild->id;
        }

        // Map Child to Admin, Xefe Suku, Xefe Aldeia, Sekretaria
        foreach ($groupsToMap as $grp) {
            if ($grp) {
                $checkMap = $db->table('groups_menu')->where(['group_id' => $grp->id, 'menu_id' => $pedChildId])->get()->getRow();
                if (!$checkMap) {
                    $db->table('groups_menu')->insert([
                        'group_id' => $grp->id,
                        'menu_id'  => $pedChildId,
                    ]);
                }
            }
        }
    }
}
