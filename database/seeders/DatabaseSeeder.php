<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. الأدوار (استخدام firstOrCreate لتجنب التكرار)
        $admin = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['description' => 'Full system access']
        );

        $manager = Role::firstOrCreate(
            ['name' => 'Manager'],
            ['description' => 'Management access']
        );

        $cashier = Role::firstOrCreate(
            ['name' => 'Cashier'],
            ['description' => 'Cash operations access']
        );

        // 2. الصلاحيات
        $permissions = [
            'users.view', 'users.create', 'users.delete',
            'operations.view', 'operations.create', 'operations.approve', 'operations.reject',
            'reports.view',
            'manage_users', 'manage_operations', 'manage_customers', 'manage_approvals',
            'view_transactions', 'view_dashboard', 'view_reports',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm],
                ['description' => $perm]
            );
        }

        $allPermissions = Permission::all();

        // Admin: كل الصلاحيات
        $admin->permissions()->sync($allPermissions);

        // Manager: بعض الصلاحيات
        $managerPermissions = [
            'operations.view', 'operations.approve', 'operations.reject',
            'manage_operations', 'manage_approvals', 'view_transactions',
            'view_dashboard', 'view_reports', 'reports.view',
        ];
        $manager->permissions()->sync(
            Permission::whereIn('name', $managerPermissions)->get()
        );

        // Cashier: صلاحيات محدودة
        $cashierPermissions = [
            'operations.view', 'operations.create',
            'manage_operations', 'view_transactions',
        ];
        $cashier->permissions()->sync(
            Permission::whereIn('name', $cashierPermissions)->get()
        );

        // 3. مستخدم Admin (مع التحقق من الوجود)
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('123456'),
                'role_id' => $admin->id,
                'status' => 'active',
                'approved_by' => 1,
                'approved_at' => now(),
            ]
        );

        // 4. تصنيفات افتراضية (Categories)
        $categories = [
            ['name' => 'مبيعات', 'parent_id' => null],
            ['name' => 'مشتريات', 'parent_id' => null],
            ['name' => 'رواتب', 'parent_id' => null],
            ['name' => 'مصاريف تشغيلية', 'parent_id' => null],
            ['name' => 'إيرادات أخرى', 'parent_id' => null],
            ['name' => 'خدمات', 'parent_id' => null],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                ['parent_id' => $cat['parent_id']]
            );
        }

        // 5. إعدادات افتراضية
        Setting::firstOrCreate(['key' => 'currency_symbol'], ['value' => '$']);
        Setting::firstOrCreate(['key' => 'company_name'], ['value' => 'My Company']);
        Setting::firstOrCreate(['key' => 'date_format'], ['value' => 'dd/mm/yyyy']);
        Setting::firstOrCreate(['key' => 'default_language'], ['value' => 'ar']);
        Setting::firstOrCreate(['key' => 'timezone'], ['value' => 'Asia/Riyadh']);
    }
}