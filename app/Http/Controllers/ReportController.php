<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $dateFrom = $validated['date_from'] ?? null;
        $dateTo = $validated['date_to'] ?? null;

        $query = Operation::query()
            ->where('user_id', $user->id)
            ->with('category:id,name_ar,name_en,type,parent_id');

        if ($dateFrom) {
            $query->whereDate('operation_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('operation_date', '<=', $dateTo);
        }

        $operations = $query
            ->orderByDesc('operation_date')
            ->orderByDesc('id')
            ->get();

        $income = $operations
            ->where('type', 'income')
            ->sum('amount');

        $expense = $operations
            ->where('type', 'expense')
            ->sum('amount');

        $groupByCategory = function ($items) {
            return $items
                ->groupBy('category_id')
                ->map(function ($group) {
                    $category = $group->first()->category;

                    return [
                        'category_id' => $category?->id,
                        'name_ar' => $category?->name_ar,
                        'name_en' => $category?->name_en,
                        'amount' => (float) $group->sum('amount'),
                        'count' => $group->count(),
                    ];
                })
                ->sortByDesc('amount')
                ->values();
        };

        $incomeByCategory = $groupByCategory(
            $operations->where('type', 'income')
        );

        $expenseByCategory = $groupByCategory(
            $operations->where('type', 'expense')
        );

        return response()->json([
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ],

            'summary' => [
                'income' => (float) $income,
                'expense' => (float) $expense,
                'balance' => (float) ($income - $expense),
                'operations_count' => $operations->count(),
            ],

            'income_by_category' => $incomeByCategory,
            'expense_by_category' => $expenseByCategory,
        ]);
    }
}