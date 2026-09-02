<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class AuditLogService
{
    public static function log(
        string $action,
        ?string $targetType = null,
        ?int $targetId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): void {
        try {
            $request = request();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Exception $e) {
            // تسجيل الخطأ في اللوج لكن دون إيقاف العملية
            Log::error('Failed to log audit: ' . $e->getMessage());
        }
    }
}