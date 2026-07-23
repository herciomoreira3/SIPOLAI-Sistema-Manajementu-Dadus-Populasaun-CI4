<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ResetAdminCredentialsFix extends Migration
{
    public function up()
    {
        $password = $this->configuredAdminPassword();
        if ($password === null) {
            log_message('warning', 'Skip ResetAdminCredentialsFix: SIPOLAI_ADMIN_PASSWORD/ADMIN_DEFAULT_PASSWORD not configured.');
            return;
        }

        $db = \Config\Database::connect();
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

        $db->table('users')
            ->where('email', 'admin@admin.com')
            ->update([
                'username'      => 'admin',
                'password_hash' => $hash,
                'active'        => 1
            ]);

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

    private function configuredAdminPassword(): ?string
    {
        $password = env('SIPOLAI_ADMIN_PASSWORD') ?: env('ADMIN_DEFAULT_PASSWORD');
        if (! is_string($password) || trim($password) === '') {
            return null;
        }

        return $password;
    }
}
