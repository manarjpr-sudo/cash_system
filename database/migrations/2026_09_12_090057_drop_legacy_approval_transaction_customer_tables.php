<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('customers');
    }

    public function down(): void
    {
        // These legacy tables are intentionally not recreated.
    }
};