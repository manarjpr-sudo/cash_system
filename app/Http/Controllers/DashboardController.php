<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * صفحة Dashboard الأساسية.
     */
    public function index()
    {
        return redirect('/');
    }

    /**
     * بيانات Dashboard للمستخدم الحالي.
     */
    public function api(Request $request)
    {
        $user = $request->user();

        $baseQuery = Operation::where('user_id', $user->id);

        $totalIncome = (clone $baseQuery)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = (clone $baseQuery)
            ->where('type', 'expense')
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        $operationsCount = (clone $baseQuery)->count();

        $latestOperations = (clone $baseQuery)
            ->with('category:id,name_ar,name_en,type,parent_id')
            ->orderByDesc('operation_date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],

            'stats' => [
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'balance' => $balance,
                'operationsCount' => $operationsCount,
            ],

            'latestOperations' => $latestOperations,
        ]);
    }
}