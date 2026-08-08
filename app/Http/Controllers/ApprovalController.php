<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Operation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    /**
     * Display a listing of approvals.
     */
    public function index()
    {
        return response()->json(
            Approval::with([
                'operation',
                'user'
            ])
            ->latest()
            ->paginate(10)
        );
    }


    /**
     * Create approval and process operation.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'operation_id' => 'required|exists:operations,id',
            'status' => 'required|in:approved,rejected',
            'comment' => 'nullable|string',
        ]);


        return DB::transaction(function () use ($data) {

            $operation = Operation::findOrFail($data['operation_id']);


            // منع معالجة نفس العملية أكثر من مرة
            if ($operation->status !== 'pending') {

                return response()->json([
                    'message' => 'Operation already processed'
                ], 403);
            }


            // إنشاء سجل الموافقة
            $approval = Approval::create([
                'operation_id' => $operation->id,
                'user_id' => auth()->id(),
                'status' => $data['status'],
                'comment' => $data['comment'] ?? null,
                'approved_at' => now(),
            ]);


            // تحديث حالة العملية
            $operation->update([
                'status' => $data['status'],
            ]);


            $transaction = null;


            // إنشاء Transaction فقط عند الموافقة
            if ($data['status'] === 'approved') {


                $transaction = Transaction::create([
                    'operation_id' => $operation->id,
                    'customer_id' => $operation->customer_id,
                    'user_id' => auth()->id(),
                    'type' => $operation->type,
                    'amount' => $operation->amount,
                    'description' => $operation->description,
                ]);

            }


            return response()->json([

                'message' =>
                    $data['status'] === 'approved'
                    ? 'Operation approved and transaction created successfully'
                    : 'Operation rejected successfully',

                'approval' => $approval,

                'transaction' => $transaction
            ]);
        });
    }


    /**
     * Display specific approval.
     */
    public function show(Approval $approval)
    {
        return response()->json(
            $approval->load([
                'operation',
                'user'
            ])
        );
    }


    /**
     * Update approval comment.
     */
    public function update(Request $request, Approval $approval)
    {
        $data = $request->validate([
            'comment' => 'nullable|string',
        ]);


        $approval->update($data);


        return response()->json([
            'message' => 'Approval updated successfully',
            'approval' => $approval
        ]);
    }


    /**
     * Delete approval.
     */
    public function destroy(Approval $approval)
    {
        $approval->delete();


        return response()->json([
            'message' => 'Approval deleted successfully'
        ]);
    }
}