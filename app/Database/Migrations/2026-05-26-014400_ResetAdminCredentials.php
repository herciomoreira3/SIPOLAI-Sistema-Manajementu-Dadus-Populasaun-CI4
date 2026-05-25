<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;

class ResetAdminCredentials extends Migration
{
    public function up()
    {
        $users = new UserModel();
        
        // Find the admin user by original email
        $admin = $users->where('email', 'admin@admin.com')->first();
        if ($admin) {
            $admin->username = 'admin';
            // Set new secure password
            $admin->password = 'sipolai2026admin';
            $users->save($admin);
        } else {
            // If they deleted or changed email, try by username
            $admin = $users->where('username', 'admin')->first();
            if ($admin) {
                $admin->password = 'sipolai2026admin';
                $users->save($admin);
            }
        }
    }

    public function down()
    {
        $users = new UserModel();
        
        $admin = $users->where('email', 'admin@admin.com')->first();
        if ($admin) {
            $admin->username = 'admin';
            $admin->password = 'super-admin';
            $users->save($admin);
        }
    }
}
