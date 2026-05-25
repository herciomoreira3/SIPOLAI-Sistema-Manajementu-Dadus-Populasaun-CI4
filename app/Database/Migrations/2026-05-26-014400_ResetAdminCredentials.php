<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;

class ResetAdminCredentials extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Let's generate the password hash using Myth Auth's expected mechanism (default password_hash with PASSWORD_DEFAULT)
        // Myth\Auth\Entities\User uses password_hash() under the hood when setting the password.
        $password = 'sipolai2026admin';
        $hash = password_hash($password, PASSWORD_DEFAULT);

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
        $db = \Config\Database::connect();
        $hash = password_hash('super-admin', PASSWORD_DEFAULT);
        
        $db->table('users')
            ->where('email', 'admin@admin.com')
            ->update([
                'username'      => 'admin',
                'password_hash' => $hash
            ]);
    }
}
