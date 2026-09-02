<?php

namespace App\Services;

use App\Models\User; // ✅ مساحة الاسم الصحيحة
use Illuminate\Support\Facades\DB;

class UserService
{
    public function approve(User $user, array $data, int $approverId): User
    {
        if ($user->status !== 'pending') {
            throw new \Exception('Only pending users can be approved.');
        }

        DB::transaction(function () use ($user, $data, $approverId) {
            $user->update([
                'role_id' => $data['role_id'],
                'status' => 'active',
                'approved_by' => $approverId,
                'approved_at' => now(),
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);
        });

        return $user->fresh(['role', 'requestedRole', 'approver']);
    }

    public function reject(User $user, string $reason, int $rejectorId): User
    {
        if ($user->status !== 'pending') {
            throw new \Exception('Only pending users can be rejected.');
        }

        $user->update([
            'status' => 'rejected',
            'rejected_by' => $rejectorId,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'role_id' => null,
        ]);

        return $user->fresh(['requestedRole', 'rejector']);
    }

    public function deactivate(User $user, int $currentUserId): void
    {
        if ($user->id === $currentUserId) {
            throw new \Exception('You cannot deactivate your own account.');
        }

        $user->update(['status' => 'inactive']);
        $user->tokens()->delete();
    }

    public function activate(User $user): void
    {
        if ($user->role_id === null) {
            throw new \Exception('User must have an assigned role before activation.');
        }

        $user->update(['status' => 'active']);
    }
}