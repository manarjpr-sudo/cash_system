<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'operation_id' => 'required|exists:operations,id',
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $operation = Operation::findOrFail($request->operation_id);

        if ($operation->status !== 'pending') {
            return response()->json(['message' => 'Only pending operations can be approved/rejected'], 422);
        }

        DB::transaction(function () use ($request, $operation) {
            Approval::create([
                'operation_id' => $operation->id,
                'user_id' => auth()->id(),
                'status' => $request->status,
                'comment' => $request->comment,
            ]);

            $operation->status = $request->status;
            if ($request->status === 'rejected' && $request->has('rejection_reason')) {
                $operation->rejection_reason = $request->rejection_reason;
            }
            $operation->save();
        });

        return response()->json(['message' => 'Operation ' . $request->status . ' successfully']);
    }
}