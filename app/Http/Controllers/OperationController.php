<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOperationRequest;
use App\Http\Requests\UpdateOperationRequest;
use App\Models\Operation;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    /**
     * عرض عمليات المستخدم الحالي مع البحث والفلترة.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $perPage = max(
            1,
            min(
                (int) $request->input('per_page', 15),
                100
            )
        );

        $operations = Operation::query()
            ->where('user_id', $user->id)

            ->with([
                'category:id,name_ar,name_en,type,parent_id',
            ])

            ->when($request->filled('type'), function ($query) use ($request) {
                $query->whereIn(
                    'type',
                    ['income', 'expense']
                )->where(
                    'type',
                    $request->input('type')
                );
            })

            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where(
                    'category_id',
                    $request->integer('category_id')
                );
            })

            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate(
                    'operation_date',
                    '>=',
                    $request->input('date_from')
                );
            })

            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate(
                    'operation_date',
                    '<=',
                    $request->input('date_to')
                );
            })

            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where(
                        'description',
                        'LIKE',
                        '%' . $search . '%'
                    )
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery
                            ->where('name_ar', 'LIKE', '%' . $search . '%')
                            ->orWhere('name_en', 'LIKE', '%' . $search . '%');
                    });
                });
            })

            ->orderByDesc('operation_date')
            ->orderByDesc('id')

            ->paginate($perPage);

        return response()->json($operations);
    }

    /**
     * إنشاء عملية قبض أو صرف.
     */
    public function store(StoreOperationRequest $request)
    {
        $operation = Operation::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        $operation->load(
            'category:id,name_ar,name_en,type,parent_id'
        );

        return response()->json([
            'message' => 'Operation created successfully',
            'operation' => $operation,
        ], 201);
    }

    /**
     * عرض عملية واحدة للمستخدم الحالي.
     */
    public function show(
        Request $request,
        Operation $operation
    ) {
        $this->ensureOwnership($request, $operation);

        $operation->load(
            'category:id,name_ar,name_en,type,parent_id'
        );

        return response()->json($operation);
    }

    /**
     * تعديل عملية للمستخدم الحالي.
     */
    public function update(
        UpdateOperationRequest $request,
        Operation $operation
    ) {
        $this->ensureOwnership($request, $operation);

        $operation->update($request->validated());

        $operation->load(
            'category:id,name_ar,name_en,type,parent_id'
        );

        return response()->json([
            'message' => 'Operation updated successfully',
            'operation' => $operation,
        ]);
    }

    /**
     * حذف عملية للمستخدم الحالي.
     */
    public function destroy(
        Request $request,
        Operation $operation
    ) {
        $this->ensureOwnership($request, $operation);

        $operation->delete();

        return response()->json([
            'message' => 'Operation deleted successfully',
        ]);
    }

    /**
     * التأكد من أن العملية تخص المستخدم الحالي.
     */
    private function ensureOwnership(
        Request $request,
        Operation $operation
    ): void {
        abort_unless(
            $operation->user_id === $request->user()->id,
            404
        );
    }
}