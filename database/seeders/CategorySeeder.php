<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // تصنيفات الدخل (Income)
        $income = Category::create(['name' => 'مبيعات', 'type' => 'income']);
        Category::create(['name' => 'خدمات', 'type' => 'income', 'parent_id' => $income->id]);
        Category::create(['name' => 'منتجات', 'type' => 'income', 'parent_id' => $income->id]);

        $otherIncome = Category::create(['name' => 'إيرادات أخرى', 'type' => 'income']);
        Category::create(['name' => 'عمولات', 'type' => 'income', 'parent_id' => $otherIncome->id]);
        Category::create(['name' => 'إيجارات', 'type' => 'income', 'parent_id' => $otherIncome->id]);

        // تصنيفات المصروفات (Expense)
        $expense = Category::create(['name' => 'مشتريات', 'type' => 'expense']);
        Category::create(['name' => 'مواد خام', 'type' => 'expense', 'parent_id' => $expense->id]);
        Category::create(['name' => 'تجهيزات', 'type' => 'expense', 'parent_id' => $expense->id]);

        $otherExpense = Category::create(['name' => 'مصروفات أخرى', 'type' => 'expense']);
        Category::create(['name' => 'إيجارات', 'type' => 'expense', 'parent_id' => $otherExpense->id]);
        Category::create(['name' => 'رواتب', 'type' => 'expense', 'parent_id' => $otherExpense->id]);
        Category::create(['name' => 'فواتير', 'type' => 'expense', 'parent_id' => $otherExpense->id]);
    }
}