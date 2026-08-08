<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\User;

class DashboardController extends Controller
{
    private function getDashboardData()
    {
        return [
            'totalUsers' => User::count(),

            'totalCustomers' => Customer::count(),

            'totalOperations' => Operation::count(),

            'pendingOperations' => Operation::where('status', 'pending')->count(),

            'approvedOperations' => Operation::where('status', 'approved')->count(),

            'rejectedOperations' => Operation::where('status', 'rejected')->count(),


            'totalTransactions' => Transaction::count(),

            'totalAmount' => Transaction::sum('amount'),


            'latestTransactions' => Transaction::with([
                'operation',
                'customer',
                'user'
            ])
            ->latest()
            ->take(5)
            ->get(),


            'latestOperations' => Operation::with([
                'customer',
                'user'
            ])
            ->latest()
            ->take(5)
            ->get(),
        ];
    }


    public function index()
    {
        return view('dashboard', $this->getDashboardData());
    }


    public function api()
    {
        $data = $this->getDashboardData();


        return response()->json([
            'total_users' => $data['totalUsers'],

            'total_customers' => $data['totalCustomers'],

            'total_operations' => $data['totalOperations'],

            'pending_operations' => $data['pendingOperations'],

            'approved_operations' => $data['approvedOperations'],

            'rejected_operations' => $data['rejectedOperations'],

            'total_transactions' => $data['totalTransactions'],

            'total_amount' => $data['totalAmount'],


            'latest_transactions' => $data['latestTransactions'],

            'latest_operations' => $data['latestOperations'],
        ]);
    }
}