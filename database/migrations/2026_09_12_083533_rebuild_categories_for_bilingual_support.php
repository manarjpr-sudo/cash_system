<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * جميع بيانات التصنيفات الحالية تجريبية.
         */
        DB::table('categories')->delete();

        /*
         * حذف الحقول القديمة.
         */
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'type',
            ]);
        });

        /*
         * إنشاء الحقول الجديدة.
         *
         * name_ar = الاسم العربي
         * name_en = الاسم الإنجليزي
         * type    = income / expense
         */
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_ar')->after('id');
            $table->string('name_en')->after('name_ar');

            $table->enum('type', [
                'income',
                'expense',
            ])->after('name_en');
        });
    }

    public function down(): void
    {
        /*
         * حذف البيانات الحالية قبل الرجوع للبنية القديمة.
         */
        DB::table('categories')->delete();

        /*
         * حذف الحقول الجديدة.
         */
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar',
                'name_en',
                'type',
            ]);
        });

        /*
         * إعادة البنية القديمة.
         */
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('type')->default('income')->after('name');
        });
    }
};