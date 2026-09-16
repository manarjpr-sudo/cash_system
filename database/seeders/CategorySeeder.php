<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Income Categories
        |--------------------------------------------------------------------------
        */

        $salary = Category::create([
            'name_ar' => 'راتب',
            'name_en' => 'Salary',
            'type' => 'income',
        ]);

        Category::create([
            'name_ar' => 'راتب أساسي',
            'name_en' => 'Base Salary',
            'type' => 'income',
            'parent_id' => $salary->id,
        ]);

        Category::create([
            'name_ar' => 'مكافآت',
            'name_en' => 'Bonuses',
            'type' => 'income',
            'parent_id' => $salary->id,
        ]);

        $work = Category::create([
            'name_ar' => 'عمل إضافي',
            'name_en' => 'Extra Income',
            'type' => 'income',
        ]);

        Category::create([
            'name_ar' => 'عمل حر',
            'name_en' => 'Freelance',
            'type' => 'income',
            'parent_id' => $work->id,
        ]);

        Category::create([
            'name_ar' => 'خدمات',
            'name_en' => 'Services',
            'type' => 'income',
        ]);

        Category::create([
            'name_ar' => 'مبيعات',
            'name_en' => 'Sales',
            'type' => 'income',
        ]);

        Category::create([
            'name_ar' => 'استثمار',
            'name_en' => 'Investment',
            'type' => 'income',
        ]);

        Category::create([
            'name_ar' => 'هدية',
            'name_en' => 'Gift',
            'type' => 'income',
        ]);

        Category::create([
            'name_ar' => 'استرداد',
            'name_en' => 'Refund',
            'type' => 'income',
        ]);

        Category::create([
            'name_ar' => 'دخل آخر',
            'name_en' => 'Other Income',
            'type' => 'income',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Expense Categories
        |--------------------------------------------------------------------------
        */

        $food = Category::create([
            'name_ar' => 'طعام',
            'name_en' => 'Food',
            'type' => 'expense',
        ]);

        Category::create([
            'name_ar' => 'مطاعم',
            'name_en' => 'Restaurants',
            'type' => 'expense',
            'parent_id' => $food->id,
        ]);

        Category::create([
            'name_ar' => 'بقالة',
            'name_en' => 'Groceries',
            'type' => 'expense',
            'parent_id' => $food->id,
        ]);

        Category::create([
            'name_ar' => 'قهوة ومشروبات',
            'name_en' => 'Coffee & Drinks',
            'type' => 'expense',
            'parent_id' => $food->id,
        ]);

        $transportation = Category::create([
            'name_ar' => 'مواصلات',
            'name_en' => 'Transportation',
            'type' => 'expense',
        ]);

        Category::create([
            'name_ar' => 'وقود',
            'name_en' => 'Fuel',
            'type' => 'expense',
            'parent_id' => $transportation->id,
        ]);

        Category::create([
            'name_ar' => 'تاكسي',
            'name_en' => 'Taxi',
            'type' => 'expense',
            'parent_id' => $transportation->id,
        ]);

        Category::create([
            'name_ar' => 'صيانة',
            'name_en' => 'Maintenance',
            'type' => 'expense',
            'parent_id' => $transportation->id,
        ]);

        Category::create([
            'name_ar' => 'سكن',
            'name_en' => 'Housing',
            'type' => 'expense',
        ]);

        Category::create([
            'name_ar' => 'فواتير',
            'name_en' => 'Bills',
            'type' => 'expense',
        ]);

        Category::create([
            'name_ar' => 'صحة',
            'name_en' => 'Health',
            'type' => 'expense',
        ]);

        Category::create([
            'name_ar' => 'تعليم',
            'name_en' => 'Education',
            'type' => 'expense',
        ]);

        Category::create([
            'name_ar' => 'تسوق',
            'name_en' => 'Shopping',
            'type' => 'expense',
        ]);

        Category::create([
            'name_ar' => 'ترفيه',
            'name_en' => 'Entertainment',
            'type' => 'expense',
        ]);

        Category::create([
            'name_ar' => 'اشتراكات',
            'name_en' => 'Subscriptions',
            'type' => 'expense',
        ]);

        Category::create([
            'name_ar' => 'صحة وعناية شخصية',
            'name_en' => 'Personal Care',
            'type' => 'expense',
        ]);

        Category::create([
            'name_ar' => 'ديون وأقساط',
            'name_en' => 'Debt & Installments',
            'type' => 'expense',
        ]);

        Category::create([
            'name_ar' => 'مصروفات أخرى',
            'name_en' => 'Other Expenses',
            'type' => 'expense',
        ]);
    }
}