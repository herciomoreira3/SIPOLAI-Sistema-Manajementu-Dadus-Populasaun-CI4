<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ResetAdminCredentialsFix extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        $password = 'sipolai2026admin';
        // Myth\Auth uses PASSWORD_BCRYPT with cost set to 10 by default
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

        // Update username and password directly via query builder
        $db->table('users')
            ->where('email', 'admin@admin.com')
            ->update([
                'username'      => 'admin',
                'password_hash' => $hash,
                'active'        => 1
            ]);
            
        // Also ensure user with username 'admin' has this email and password
        $db->table('users')
            ->where('username', 'admin')
            ->update([
                'email'         => 'admin@admin.com',
                'password_hash' => $hash,
                'active'        => 1
            ]);
    }

    public function down()
    {
        // No down needed for this fix
    }
}
