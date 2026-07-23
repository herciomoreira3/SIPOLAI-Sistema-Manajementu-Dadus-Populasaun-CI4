<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserDummySeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Get Group IDs
        $groupXefeSuku = $db->table('auth_groups')->where('name', 'xefe-suku')->get()->getRow();
        $groupXefeAldeia = $db->table('auth_groups')->where('name', 'xefe-aldeia')->get()->getRow();
        $groupSekretaria = $db->table('auth_groups')->where('name', 'sekretaria')->get()->getRow();

        // 2. Dummy Users configuration
        $users = [
            [
                'username'  => 'xefesuku',
                'email'     => 'suku@sipolai.com',
                'password'  => env('SIPOLAI_XEFE_SUKU_PASSWORD'),
                'id_aldeia' => null,
                'group'     => $groupXefeSuku
            ],
            [
                'username'  => 'xefealdeia',
                'email'     => 'aldeia@sipolai.com',
                'password'  => env('SIPOLAI_XEFE_ALDEIA_PASSWORD'),
                'id_aldeia' => 1, // Aldeia Uaisa
                'group'     => $groupXefeAldeia
            ],
            [
                'username'  => 'sekretaria',
                'email'     => 'sekretaria@sipolai.com',
                'password'  => env('SIPOLAI_SEKRETARIA_PASSWORD'),
                'id_aldeia' => null,
                'group'     => $groupSekretaria
            ]
        ];

        foreach ($users as $u) {
            if (! is_string($u['password']) || trim($u['password']) === '') {
                log_message('warning', 'Skip user seed for ' . $u['username'] . ': password environment variable not configured.');
                continue;
            }

            // Check if user exists
            $check = $db->table('users')->where('username', $u['username'])->get()->getRow();
            if (!$check) {
                $db->table('users')->insert([
                    'username'      => $u['username'],
                    'email'         => $u['email'],
                    'id_aldeia'     => $u['id_aldeia'],
                    'password_hash' => password_hash($u['password'], PASSWORD_DEFAULT),
                    'active'        => 1,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
                $userId = $db->insertID();

                if ($u['group']) {
                    $db->table('auth_groups_users')->insert([
                        'group_id' => $u['group']->id,
                        'user_id'  => $userId
                    ]);
                }
            }
        }
    }
}
