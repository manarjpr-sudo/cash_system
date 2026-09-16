<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Default Categories
        |--------------------------------------------------------------------------
        */

        $this->call(CategorySeeder::class);


        /*
        |--------------------------------------------------------------------------
        | Default Settings
        |--------------------------------------------------------------------------
        */

        Setting::firstOrCreate(
            ['key' => 'currency_symbol'],
            ['value' => '$']
        );

        Setting::firstOrCreate(
            ['key' => 'date_format'],
            ['value' => 'dd/mm/yyyy']
        );

        Setting::firstOrCreate(
            ['key' => 'default_language'],
            ['value' => 'ar']
        );

        Setting::firstOrCreate(
            ['key' => 'timezone'],
            ['value' => 'Asia/Riyadh']
        );
    }
}