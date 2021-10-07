<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {


        // permissions for Admin

        $prsArray = [
            'role-list',
            'user-role-list',
            'role-create',
            'user-role-update',
            'permission-list',
            'role-permission-list',
            'user-permission-list',
            'permission-create',
            'role-permission-update',
            'complaint-list-all',
            'complaint-list',
            'complaint-create',
            'complaint-edit',
            'complaint-delete',
            'setting-list',
            'setting-edit',
            'user-list-all',
            'user-list',
            'user-edit',
            'user-delete',
            'transaction-list',
            'notice-list-all',
            'notice-all',
            'bookings-list-all',
            'bookings-all',
            'shift-list-all',
            'shift-list',
            'nook-list-all',
            'nook-list',
            'notification-list-all',
            'bookings-list',
            'bookings-update',
            'bookings-addSecurity',
            'nook-area',
            'nook-create',
            'nook-delete',
            "notice-list",
            'notice-edit',
            'receipt-list',
            'receipt-generate',
            'receipt-pay',
            'receipt-publish',
            'shift-edit',
            'transaction-edit',
            'visits-list',
            'visits-edit'
        ];

        foreach ($prsArray as $p) {
            $prs[] = Permission::findOrCreate($p);
        }

        $role = Role::create(['name' => env('SUPER_ADMIN_ROLE_NAME',"Admin")]);
        $role->syncPermissions($prs);

        // permissions for manager

        $prs = [];

        $prsArray = [
            'user-permission-list',
            'user-list',
            'complaint-list',
            'transaction-list',
            'notice-all',
            'bookings-all',
            'shift-list',
            'nook-list',
            'notification-list'
        ];

        foreach ($prsArray as $p) {
            $prs[] = Permission::findOrCreate($p);
        }

        $role = Role::create(['name' => env('RESTAURANT_OWNER_ROLE_NAME','Manager')]);

        $role->syncPermissions($prs);

        $prs = [];

        $prsArray = [
            'bookings-list',
            'bookings-update',
            'bookings-addSecurity',
            'complaint-list',
            'complaint-edit',
            'nook-list',
            'nook-area',
            'nook-create',
            'nook-delete',
            "notice-list",
            'notice-edit',
            'receipt-list',
            'receipt-generate',
            'receipt-pay',
            'receipt-publish',
            'shift-list',
            'shift-edit',
            'transaction-list',
            'transaction-edit',
            'visits-list',
            'visits-edit'
        ];

        foreach ($prsArray as $p) {
            $prs[] = Permission::findOrCreate($p);
        }

        $role = Role::create(['name' => env('PARTNER_ROLE_NAME','Partner')]);

        $role->syncPermissions($prs);
    }
}
