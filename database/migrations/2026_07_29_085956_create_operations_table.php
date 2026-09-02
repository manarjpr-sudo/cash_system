<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('category_id')->nullable(); // بدون foreign
            $table->enum('type', ['receipt', 'payment']);
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('description')->nullable();
            $table->string('idempotency_key')->unique()->nullable();
            $table->timestamps();

            $table->index(['status', 'type', 'created_at']);
            $table->index('customer_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};