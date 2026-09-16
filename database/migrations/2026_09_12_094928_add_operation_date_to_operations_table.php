<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('operations', 'operation_date')) {
            Schema::table('operations', function (Blueprint $table) {
                $table->date('operation_date')->nullable()->after('amount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('operations', 'operation_date')) {
            Schema::table('operations', function (Blueprint $table) {
                $table->dropColumn('operation_date');
            });
        }
    }
};