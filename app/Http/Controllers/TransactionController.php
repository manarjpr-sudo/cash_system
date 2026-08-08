<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with([
                'operation',
                'customer',
                'user'
            ])
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->customer_id, function ($query, $customerId) {
                $query->where('customer_id', $customerId);
            })
            ->when($request->user_id, function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($request->date, function ($query, $date) {
                $query->whereDate('created_at', $date);
            })
            ->latest()
            ->paginate(10);


        return response()->json($transactions);
    }


    public function show(Transaction $transaction)
    {
        return response()->json(
            $transaction->load([
                'operation',
                'customer',
                'user'
            ])
        );
    }
}