<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Models\Operation;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        // توجيه إلى الصفحة الرئيسية (React)
        return redirect('/');
    }


    public function api(Request $request)
    {
        $user = $request->user();

        $data = [
            'user' => [
                'name' => $user->name,
                'role' => $user->role?->name,
            ]
        ];

        if ($user->hasPermission('view_dashboard')) {
            // 🔥 حساب الإحصائيات المالية
            $totalReceipts = Operation::where('type', 'receipt')->where('status', 'approved')->sum('amount');
            $totalPayments = Operation::where('type', 'payment')->where('status', 'approved')->sum('amount');
            $totalAdvances = Operation::where('type', 'advance')->where('status', 'approved')->sum('amount');
            $netCash = $totalReceipts - $totalPayments;

            $data['stats'] = [
                'usersCount' => User::count(),
                'customersCount' => Customer::count(),
                'operationsCount' => Operation::count(),
                'pendingOperations' => Operation::where('status', 'pending')->count(),
                'approvedOperations' => Operation::where('status', 'approved')->count(),
                'rejectedOperations' => Operation::where('status', 'rejected')->count(),
                'totalAmount' => Operation::where('status', 'approved')->sum('amount'),
                'transactionsCount' => Transaction::count(),
                'totalReceipts' => $totalReceipts,
                'totalPayments' => $totalPayments,
                'totalAdvances' => $totalAdvances,
                'netCash' => $netCash,
            ];

            // 🔥 إضافة آخر العمليات (بجميع حالاتها) للمدير
            $data['latestOperations'] = Operation::with(['customer', 'user'])
                ->latest()
                ->take(5)
                ->get();

            $data['latestTransactions'] = Transaction::with(['customer', 'user'])
                ->latest()
                ->take(5)
                ->get();

        } else {
            $data['myOperations'] = Operation::where('user_id', $user->id)
                ->with('customer')
                ->latest()
                ->take(5)
                ->get();

            $data['myStats'] = [
                'total' => Operation::where('user_id', $user->id)->count(),
                'pending' => Operation::where('user_id', $user->id)->where('status', 'pending')->count(),
                'approved' => Operation::where('user_id', $user->id)->where('status', 'approved')->count(),
            ];
        }

        return response()->json($data);
    }
}