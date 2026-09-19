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

        $validated = $request->validate([
            'type' => ['nullable', 'in:income,expense'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'parent_category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = $validated['per_page'] ?? 15;

        $operations = Operation::query()
            ->where('user_id', $user->id)

            ->with([
                'category:id,name_ar,name_en,type,parent_id',
            ])

            // نوع العملية
            ->when(
                !empty($validated['type']),
                function ($query) use ($validated) {
                    $query->where('type', $validated['type']);
                }
            )

            // تصنيف فرعي محدد
            ->when(
                !empty($validated['category_id']),
                function ($query) use ($validated) {
                    $query->where(
                        'category_id',
                        $validated['category_id']
                    );
                }
            )

            // تصنيف رئيسي:
            // يعرض العمليات المسجلة على الرئيسي نفسه
            // + جميع التصنيفات الفرعية التابعة له
            ->when(
                !empty($validated['parent_category_id']),
                function ($query) use ($validated) {
                    $parentId = $validated['parent_category_id'];

                    $query->whereHas('category', function ($categoryQuery) use ($parentId) {
                        $categoryQuery
                            ->where('id', $parentId)
                            ->orWhere('parent_id', $parentId);
                    });
                }
            )

            // من تاريخ
            ->when(
                !empty($validated['date_from']),
                function ($query) use ($validated) {
                    $query->whereDate(
                        'operation_date',
                        '>=',
                        $validated['date_from']
                    );
                }
            )

            // إلى تاريخ
            ->when(
                !empty($validated['date_to']),
                function ($query) use ($validated) {
                    $query->whereDate(
                        'operation_date',
                        '<=',
                        $validated['date_to']
                    );
                }
            )

            // البحث النصي
            ->when(
                !empty($validated['search']),
                function ($query) use ($validated) {
                    $search = trim($validated['search']);

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
                }
            )

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