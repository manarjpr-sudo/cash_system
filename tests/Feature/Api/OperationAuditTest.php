<?php

namespace Tests\Feature\Api;

use App\Models\AuditLog;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_operation_changes_create_audit_logs(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/operations', [
                'type' => 'income',
                'amount' => 500,
                'operation_date' => '2026-09-12',
                'description' => 'Audit test',
            ])
            ->assertCreated();

        $operation = Operation::first();

        $this->assertNotNull($operation);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'operation_created',
            'target_type' => 'operation',
            'target_id' => $operation->id,
        ]);

        $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/operations/{$operation->id}", [
                'type' => 'income',
                'amount' => 750,
                'operation_date' => '2026-09-12',
                'description' => 'Audit test updated',
            ])
            ->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'operation_updated',
            'target_type' => 'operation',
            'target_id' => $operation->id,
        ]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/operations/{$operation->id}")
            ->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'operation_deleted',
            'target_type' => 'operation',
            'target_id' => $operation->id,
        ]);

        $this->assertSame(3, AuditLog::count());
    }
}