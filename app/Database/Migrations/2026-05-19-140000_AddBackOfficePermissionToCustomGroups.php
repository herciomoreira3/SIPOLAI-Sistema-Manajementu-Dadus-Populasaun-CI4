<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBackOfficePermissionToCustomGroups extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Find permission ID for 'back-office'
        $permission = $db->table('auth_permissions')->where('name', 'back-office')->get()->getRow();

        if ($permission) {
            // 2. Find group IDs for the custom roles
            $groups = $db->table('auth_groups')
                ->whereIn('name', ['xefe-suku', 'xefe-aldeia', 'sekretaria'])
                ->get()
                ->getResultArray();

            // 3. Map permission to each group
            foreach ($groups as $group) {
                $exists = $db->table('auth_groups_permissions')
                    ->where('group_id', $group['id'])
                    ->where('permission_id', $permission->id)
                    ->countAllResults();

                if ($exists === 0) {
                    $db->table('auth_groups_permissions')->insert([
                        'group_id'      => $group['id'],
                        'permission_id' => $permission->id
                    ]);
                }
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $permission = $db->table('auth_permissions')->where('name', 'back-office')->get()->getRow();

        if ($permission) {
            $groups = $db->table('auth_groups')
                ->whereIn('name', ['xefe-suku', 'xefe-aldeia', 'sekretaria'])
                ->get()
                ->getResultArray();

            foreach ($groups as $group) {
                $db->table('auth_groups_permissions')
                    ->where('group_id', $group['id'])
                    ->where('permission_id', $permission->id)
                    ->delete();
            }
        }
    }
}
