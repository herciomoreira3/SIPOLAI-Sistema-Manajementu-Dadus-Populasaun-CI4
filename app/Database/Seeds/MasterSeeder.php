<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Seed Aldeia
        $db->table('tabela_aldeia')->insertBatch([
            ['naran_aldeia' => 'Aldeia Uaisa', 'id_suku' => 1],
            ['naran_aldeia' => 'Aldeia Bula', 'id_suku' => 1],
            ['naran_aldeia' => 'Aldeia Quelicai Antigo', 'id_suku' => 1],
            ['naran_aldeia' => 'Aldeia Afaca', 'id_suku' => 1],
        ]);

        // 2. Seed Profisaun
        $db->table('tabela_profisaun')->insertBatch([
            ['naran_profisaun' => 'Agrikultór'],
            ['naran_profisaun' => 'Estudante'],
            ['naran_profisaun' => 'Negosiante'],
            ['naran_profisaun' => 'Funsonáriu Publiku'],
            ['naran_profisaun' => 'Desempregadu'],
        ]);

        // 3. Seed Relijiaun
        $db->table('tabela_relijiaun')->insertBatch([
            ['naran_relijiaun' => 'Katólika'],
            ['naran_relijiaun' => 'Protestante'],
            ['naran_relijiaun' => 'Islam'],
            ['naran_relijiaun' => 'Ortodoksu'],
        ]);

        // 4. Seed Literatura
        $db->table('tabela_literatura')->insertBatch([
            ['naran_literatura' => 'Laiha Eskola'],
            ['naran_literatura' => 'Ensinu Báziku'],
            ['naran_literatura' => 'Ensinu Sekundáriu'],
            ['naran_literatura' => 'Lisensiatura'],
            ['naran_literatura' => 'Mestradu'],
        ]);

        // 5. Seed Groups (Roles)
        // Check if group exists before inserting to prevent collision
        $roles = [
            ['name' => 'xefe-suku', 'description' => 'Xefe Suku Laisorolai de Baixo'],
            ['name' => 'xefe-aldeia', 'description' => 'Xefe Aldeia Laisorolai de Baixo'],
            ['name' => 'sekretaria', 'description' => 'Sekretaria Suku Laisorolai de Baixo'],
        ];

        foreach ($roles as $role) {
            $check = $db->table('auth_groups')->where('name', $role['name'])->get()->getRow();
            if (!$check) {
                $db->table('auth_groups')->insert($role);
            }
        }
    }
}
