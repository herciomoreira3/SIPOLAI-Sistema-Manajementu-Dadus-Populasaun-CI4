<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKarguTableAndMenu extends Migration
{
    public function up()
    {
        // 1. Create table 'tabela_kargu'
        $this->forge->addField([
            'id_kargu' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_kargu' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_kargu', true);
        $this->forge->createTable('tabela_kargu');

        $db = \Config\Database::connect();

        // 2. Seed default positions
        $defaultKargus = [
            ['naran_kargu' => 'Xefe Suku', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['naran_kargu' => 'Sekretariu Suku', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['naran_kargu' => 'Xefe Aldeia Uaisa', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['naran_kargu' => 'Xefe Aldeia Bula', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['naran_kargu' => 'Xefe Aldeia Quelicai Antigo', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['naran_kargu' => 'Xefe Aldeia Afaca', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['naran_kargu' => 'Adjuntu Suku', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $db->table('tabela_kargu')->insertBatch($defaultKargus);

        // 3. Find parent "Estrutura Suku" menu ID
        $parent = $db->table('menu')->where('title', 'Estrutura Suku')->get()->getRow();
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
                'title'      => 'Jestaun Kargu',
                'icon'       => 'fas fa-briefcase',
                'route'      => 'admin/kargu',
                'sequence'   => 21,
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
        $menuItem = $db->table('menu')->where('title', 'Jestaun Kargu')->get()->getRow();
        if ($menuItem) {
            $db->table('groups_menu')->where('menu_id', $menuItem->id)->delete();
            $db->table('menu')->where('id', $menuItem->id)->delete();
        }

        // 2. Drop table
        $this->forge->dropTable('tabela_kargu', true);
    }
}
