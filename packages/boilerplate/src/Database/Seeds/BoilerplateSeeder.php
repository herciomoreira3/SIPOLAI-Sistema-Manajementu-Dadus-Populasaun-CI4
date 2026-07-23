<?php

namespace Boilerplate\Database\Seeds;

use CodeIgniter\Config\Services;
use CodeIgniter\Database\Seeder;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;

/**
 * Class BoilerplateSeeder.
 */
class BoilerplateSeeder extends Seeder
{
    /**
     * @var Authorize
     */
    protected $authorize;

    /**
     * @var Db
     */
    protected $db;

    /**
     * @var Users
     */
    protected $users;

    public function __construct()
    {
        $this->authorize = Services::authorization();
        $this->db = \Config\Database::connect();
        $this->users = new UserModel();
    }

    public function run()
    {
        $adminId = null;
        $memberId = null;
        $adminPassword = env('SIPOLAI_ADMIN_PASSWORD') ?: env('ADMIN_DEFAULT_PASSWORD');
        $memberPassword = env('SIPOLAI_MEMBER_PASSWORD');

        if (is_string($adminPassword) && trim($adminPassword) !== '') {
            $this->users->save(new User([
                'email'    => 'admin@admin.com',
                'username' => 'admin',
                'password' => $adminPassword,
                'active'   => '1',
            ]));
            $admin = $this->users->where('email', 'admin@admin.com')->first();
            $adminId = $admin->id ?? null;
        } else {
            log_message('warning', 'Skip default admin user seed: SIPOLAI_ADMIN_PASSWORD/ADMIN_DEFAULT_PASSWORD not configured.');
        }

        if (is_string($memberPassword) && trim($memberPassword) !== '') {
            $this->users->save(new User([
                'email'    => 'user@user.com',
                'username' => 'user',
                'password' => $memberPassword,
                'active'   => '1',
            ]));
            $member = $this->users->where('email', 'user@user.com')->first();
            $memberId = $member->id ?? null;
        }

        // Role
        $this->authorize->createGroup('admin', 'Administrators. The top of the food chain.');
        $this->authorize->createGroup('member', 'Member everyday member.');

        // Permission
        $this->authorize->createPermission('back-office', 'User can access to the administration panel.');
        $this->authorize->createPermission('manage-user', 'User can create, delete or modify the users.');
        $this->authorize->createPermission('role-permission', 'User can edit and define permissions for a role.');
        $this->authorize->createPermission('menu-permission', 'User cand create, delete or modify the menu.');

        // Assign Permission to role
        $this->authorize->addPermissionToGroup('back-office', 'admin');
        $this->authorize->addPermissionToGroup('manage-user', 'admin');
        $this->authorize->addPermissionToGroup('role-permission', 'admin');
        $this->authorize->addPermissionToGroup('menu-permission', 'admin');
        $this->authorize->addPermissionToGroup('back-office', 'member');

        // Assign Role to user
        if ($adminId !== null) {
            $this->authorize->addUserToGroup((int) $adminId, 'admin');
            $this->authorize->addUserToGroup((int) $adminId, 'member');
        }
        if ($memberId !== null) {
            $this->authorize->addUserToGroup((int) $memberId, 'member');
        }

        // Assign Permission to user
        if ($adminId !== null) {
            $this->authorize->addPermissionToUser('back-office', (int) $adminId);
            $this->authorize->addPermissionToUser('manage-user', (int) $adminId);
            $this->authorize->addPermissionToUser('role-permission', (int) $adminId);
            $this->authorize->addPermissionToUser('menu-permission', (int) $adminId);
        }
        if ($memberId !== null) {
            $this->authorize->addPermissionToUser('back-office', (int) $memberId);
        }

        $this->db->table('menu')->insertBatch([
            [
                'parent_id'  => '0',
                'title'      => 'Dashboard',
                'icon'       => 'fas fa-tachometer-alt',
                'route'      => 'admin',
                'sequence'   => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => '0',
                'title'      => 'User Management',
                'icon'       => 'fas fa-user',
                'route'      => '#',
                'sequence'   => '2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => '2',
                'title'      => 'User Profile',
                'icon'       => 'fas fa-user-edit',
                'route'      => 'admin/user/profile',
                'sequence'   => '3',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => '2',
                'title'      => 'Users',
                'icon'       => 'fas fa-users',
                'route'      => 'admin/user/manage',
                'sequence'   => '4',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => '2',
                'title'      => 'Permissions',
                'icon'       => 'fas fa-user-lock',
                'route'      => 'admin/permission',
                'sequence'   => '5',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => '2',
                'title'      => 'Roles',
                'icon'       => 'fas fa-users-cog',
                'route'      => 'admin/role',
                'sequence'   => '6',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'  => '2',
                'title'      => 'Menu',
                'icon'       => 'fas fa-stream',
                'route'      => 'admin/menu',
                'sequence'   => '7',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);

        $this->db->table('groups_menu')->insertBatch([
            [
                'group_id' => 1,
                'menu_id'  => 1,
            ],
            [
                'group_id' => 1,
                'menu_id'  => 2,
            ],
            [
                'group_id' => 1,
                'menu_id'  => 3,
            ],
            [
                'group_id' => 1,
                'menu_id'  => 4,
            ],
            [
                'group_id' => 1,
                'menu_id'  => 5,
            ],
            [
                'group_id' => 1,
                'menu_id'  => 6,
            ],
            [
                'group_id' => 1,
                'menu_id'  => 7,
            ],
            [
                'group_id' => 2,
                'menu_id'  => 1,
            ],
            [
                'group_id' => 2,
                'menu_id'  => 2,
            ],
            [
                'group_id' => 2,
                'menu_id'  => 3,
            ],
        ]);
    }

    public function down()
    {
        //
    }
}
