<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::create([
            'name' => 'Admin',
            'description' => 'Full system access',
        ]);

        $manager = Role::create([
            'name' => 'Manager',
            'description' => 'Management access',
        ]);

        $cashier = Role::create([
            'name' => 'Cashier',
            'description' => 'Cash operations access',
        ]);


        $permissions = [
            'users.view',
            'users.create',
            'users.delete',

            'operations.view',
            'operations.create',
            'operations.approve',
            'operations.reject',

            'reports.view',
        ];


        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'description' => $permission,
            ]);
        }


        $admin->permissions()
            ->attach(Permission::all());


        $manager->permissions()
            ->attach([
                Permission::where('name', 'operations.view')->first()->id,
                Permission::where('name', 'operations.approve')->first()->id,
                Permission::where('name', 'reports.view')->first()->id,
            ]);


        $cashier->permissions()
            ->attach([
                Permission::where('name', 'operations.view')->first()->id,
                Permission::where('name', 'operations.create')->first()->id,
            ]);
    }
}