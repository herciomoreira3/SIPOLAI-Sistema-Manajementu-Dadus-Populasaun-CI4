<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNoKbiitLaekAndMenu extends Migration
{
    public function up()
    {
        // 1. Add 'no_kbiit_laek' column to 'tabela_populasaun'
        $this->forge->addColumn('tabela_populasaun', [
            'no_kbiit_laek' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'no_eleitoral', // put it right after no_eleitoral
            ]
        ]);

        $db = \Config\Database::connect();

        // 2. Find parent "Populasaun" menu ID
        $parent = $db->table('menu')->where('title', 'Populasaun')->get()->getRow();
        if ($parent) {
            // Find role dynamic IDs to map
            $admin = $db->table('auth_groups')->where('name', 'admin')->get()->getRow();
            $xefeSuku = $db->table('auth_groups')->where('name', 'xefe-suku')->get()->getRow();
            $xefeAldeia = $db->table('auth_groups')->where('name', 'xefe-aldeia')->get()->getRow();
            $sekretaria = $db->table('auth_groups')->where('name', 'sekretaria')->get()->getRow();
            $groupsToMap = [$admin, $xefeSuku, $xefeAldeia, $sekretaria];

            // Define the new menu
            $menuData = [
                'parent_id'  => $parent->id,
                'title'      => 'Dados Kbiit Laek',
                'icon'       => 'fas fa-hand-holding-heart',
                'route'      => 'admin/kbiit-laek',
                'sequence'   => 18,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Check if already exists
            $checkChild = $db->table('menu')->where('title', $menuData['title'])->get()->getRow();
            if (!$checkChild) {
                $db->table('menu')->insert($menuData);
                $childId = $db->insertID();

                // Map to groups
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

    public function down()
    {
        $db = \Config\Database::connect();

        // 1. Delete menu item and its mapping
        $menuItem = $db->table('menu')->where('title', 'Dados Kbiit Laek')->get()->getRow();
        if ($menuItem) {
            $db->table('groups_menu')->where('menu_id', $menuItem->id)->delete();
            $db->table('menu')->where('id', $menuItem->id)->delete();
        }

        // 2. Drop column from table
        $this->forge->dropColumn('tabela_populasaun', 'no_kbiit_laek');
    }
}
