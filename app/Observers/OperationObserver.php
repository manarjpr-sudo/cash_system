<?php

namespace App\Observers;

use App\Models\Operation;
use App\Services\AuditLogService;

class OperationObserver
{
    public function created(Operation $operation)
    {
        AuditLogService::log(
            action: 'operation_created',
            targetType: 'operation',
            targetId: $operation->id,
            newValues: $operation->toArray(),
            description: 'تم إنشاء عملية جديدة'
        );
    }

    public function updated(Operation $operation)
    {
        AuditLogService::log(
            action: 'operation_updated',
            targetType: 'operation',
            targetId: $operation->id,
            oldValues: $operation->getOriginal(),
            newValues: $operation->toArray(),
            description: 'تم تحديث العملية'
        );
    }

    public function deleted(Operation $operation)
    {
        AuditLogService::log(
            action: 'operation_deleted',
            targetType: 'operation',
            targetId: $operation->id,
            oldValues: $operation->toArray(),
            description: 'تم حذف العملية'
        );
    }
}