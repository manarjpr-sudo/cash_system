<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            // Remove old foreign key and index before dropping customer_id.
            if (Schema::hasColumn('operations', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropIndex('operations_customer_id_index');
            }

            // Remove old composite index before dropping status.
            $table->dropIndex('operations_status_type_created_at_index');
            $table->dropIndex('operations_idempotency_key_unique');
        });

        Schema::table('operations', function (Blueprint $table) {
            $columns = [];

            foreach ([
                'customer_id',
                'status',
                'rejection_reason',
                'idempotency_key',
            ] as $column) {
                if (Schema::hasColumn('operations', $column)) {
                    $columns[] = $column;
                }
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }

    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            if (!Schema::hasColumn('operations', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable();
            }

            if (!Schema::hasColumn('operations', 'status')) {
                $table->enum('status', [
                    'pending',
                    'approved',
                    'rejected',
                ])->default('pending');
            }

            if (!Schema::hasColumn('operations', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }

            if (!Schema::hasColumn('operations', 'idempotency_key')) {
                $table->string('idempotency_key')->nullable()->unique();
            }
        });

        if (
            Schema::hasTable('customers') &&
            Schema::hasColumn('operations', 'customer_id')
        ) {
            Schema::table('operations', function (Blueprint $table) {
                $table->foreign('customer_id')
                    ->references('id')
                    ->on('customers')
                    ->nullOnDelete();
            });
        }
    }
};