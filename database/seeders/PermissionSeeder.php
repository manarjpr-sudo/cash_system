<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $managerRole = Role::where('name', 'Manager')->first();

        if (!$managerRole) {
            $this->command->error('❌ دور Manager غير موجود!');
            return;
        }

        // ✅ صلاحية view_operations
        $permissionOps = Permission::firstOrCreate(
            ['name' => 'view_operations'],
            ['description' => 'مشاهدة العمليات المالية']
        );

        if (!$managerRole->permissions()->where('permission_id', $permissionOps->id)->exists()) {
            $managerRole->permissions()->attach($permissionOps->id);
            $this->command->info('✅ تم إضافة view_operations لدور Manager');
        }

        // ✅ صلاحية view_customers
        $permissionCust = Permission::firstOrCreate(
            ['name' => 'view_customers'],
            ['description' => 'مشاهدة العملاء']
        );

        if (!$managerRole->permissions()->where('permission_id', $permissionCust->id)->exists()) {
            $managerRole->permissions()->attach($permissionCust->id);
            $this->command->info('✅ تم إضافة view_customers لدور Manager');
        }
    }
}