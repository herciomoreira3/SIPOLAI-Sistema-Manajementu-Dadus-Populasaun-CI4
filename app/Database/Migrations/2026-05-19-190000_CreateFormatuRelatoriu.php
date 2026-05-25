<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFormatuRelatoriu extends Migration
{
    public function up()
    {
        // 1. Create table tabela_formatu_relatoriu
        $this->forge->addField([
            'id_formatu_relatoriu' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'naran_relatoriu' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'template_cop' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('id_formatu_relatoriu', true);
        $this->forge->createTable('tabela_formatu_relatoriu');

        $db = \Config\Database::connect();

        // 2. Seed Default Premium Letterheads (COPs)
        $defaultCop = '<div class="text-center mb-4" style="font-family: \'Times New Roman\', Times, serif; color: #1e293b;">
    <h4 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 18px;">REPÚBLICA DEMOCRÁTICA DE TIMOR-LESTE</h4>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 16px;">MINISTÉRIO DA ADMINISTRAÇÃO ESTATAL</h5>
    <h5 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 15px;">MUNICÍPIO DE BAUCAU</h5>
    <h6 style="font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-size: 14px;">POSTO ADMINISTRATIVO DE MATEBIAN</h6>
    <h6 style="font-weight: bold; margin-bottom: 12px; text-transform: uppercase; font-size: 14px;">SUCO LAISOROLAI DE BAIXO</h6>
    <div style="border-bottom: 3px double #000000; width: 100%; margin-top: 5px; margin-bottom: 15px;"></div>
</div>';

        $reports = [
            ['naran_relatoriu' => 'Relatoriu Populasaun Suku'],
            ['naran_relatoriu' => 'Relatoriu Fixa Familia'],
            ['naran_relatoriu' => 'Relatoriu Maternidade'],
            ['naran_relatoriu' => 'Relatoriu Mortalidade']
        ];

        foreach ($reports as $rep) {
            $db->table('tabela_formatu_relatoriu')->insert([
                'naran_relatoriu' => $rep['naran_relatoriu'],
                'template_cop'    => $defaultCop,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s')
            ]);
        }

        // 3. Find parent "Relatoriu" menu and update route to '#' to allow treeview dropdown
        $parent = $db->table('menu')->where('title', 'Relatoriu')->get()->getRow();
        if ($parent) {
            $db->table('menu')->where('id', $parent->id)->update(['route' => '#']);

            // Get groups dynamic IDs to map
            $admin = $db->table('auth_groups')->where('name', 'admin')->get()->getRow();
            $xefeSuku = $db->table('auth_groups')->where('name', 'xefe-suku')->get()->getRow();
            $xefeAldeia = $db->table('auth_groups')->where('name', 'xefe-aldeia')->get()->getRow();
            $sekretaria = $db->table('auth_groups')->where('name', 'sekretaria')->get()->getRow();
            $groupsToMap = [$admin, $xefeSuku, $xefeAldeia, $sekretaria];

            // Submenu 1: Lista Relatoriu
            $sub1 = [
                'parent_id'  => $parent->id,
                'title'      => 'Lista Relatoriu',
                'icon'       => 'fas fa-list-alt',
                'route'      => 'admin/relatoriu',
                'sequence'   => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $db->table('menu')->insert($sub1);
            $sub1Id = $db->insertID();

            // Submenu 2: Formatu Relatoriu (custom COPs)
            $sub2 = [
                'parent_id'  => $parent->id,
                'title'      => 'Formatu Relatoriu',
                'icon'       => 'fas fa-heading',
                'route'      => 'admin/formatu-relatoriu',
                'sequence'   => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $db->table('menu')->insert($sub2);
            $sub2Id = $db->insertID();

            // Map submenus to groups
            foreach ($groupsToMap as $grp) {
                if ($grp) {
                    $db->table('groups_menu')->insert(['group_id' => $grp->id, 'menu_id' => $sub1Id]);
                    
                    // Formatu Relatoriu available for Admin, Xefe Suku, and Sekretaria
                    if ($grp->name !== 'xefe-aldeia') {
                        $db->table('groups_menu')->insert(['group_id' => $grp->id, 'menu_id' => $sub2Id]);
                    }
                }
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Restore Relatoriu parent menu route and delete submenus
        $parent = $db->table('menu')->where('title', 'Relatoriu')->get()->getRow();
        if ($parent) {
            $db->table('menu')->where('id', $parent->id)->update(['route' => 'admin/relatoriu']);

            $submenus = $db->table('menu')->where('parent_id', $parent->id)->get()->getResultArray();
            if (!empty($submenus)) {
                $ids = array_column($submenus, 'id');
                $db->table('groups_menu')->whereIn('menu_id', $ids)->delete();
                $db->table('menu')->whereIn('id', $ids)->delete();
            }
        }

        $this->forge->dropTable('tabela_formatu_relatoriu');
    }
}
