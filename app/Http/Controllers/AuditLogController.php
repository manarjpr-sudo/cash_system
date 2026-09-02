<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->action, fn($q, $v) => $q->where('action', $v))
            ->when($request->user_id, fn($q, $v) => $q->where('user_id', $v))
            ->when($request->target_type, fn($q, $v) => $q->where('target_type', $v))
            ->when($request->search, function ($q, $v) {
                $q->where('description', 'LIKE', "%{$v}%")
                  ->orWhereHas('user', fn($sub) => $sub->where('name', 'LIKE', "%{$v}%"));
            })
            ->when($request->from_date, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->to_date, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest('created_at')
            ->paginate(20);

        return response()->json($logs);
    }

    public function actions()
    {
        $actions = AuditLog::distinct()->pluck('action');
        return response()->json($actions);
    }
}