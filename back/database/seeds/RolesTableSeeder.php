<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesTableSeeder extends Seeder
{

    protected PermissionRegistrar $reg;

    public function __construct(PermissionRegistrar $reg)
    {
        $this->reg = $reg;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->reg->forgetCachedPermissions();

        $permissions = [
            "update-avatar",
            "change-password",
            "likes",
            "write-comments",
            "remove-own-comments",
            "update-account",

            "add-herbs",
            "update-herbs",
            "delete-herbs",
            "likes-comments",
            "using-admin-panel",
            "change-roles",
            "period",
            "pickpart"
        ];

        foreach ($permissions as $permission)
        {
            Permission::create(['name' => $permission]);
        }

        Role::create(['name' => 'admin'])->givePermissionTo($permissions);

        Role::create(['name' => 'user'])->givePermissionTo([
            "update-account",
            "update-avatar",
            "change-password",
            "likes-comments",
            "likes",
            "write-comments",
            "remove-own-comments"
        ]);
    }
}
