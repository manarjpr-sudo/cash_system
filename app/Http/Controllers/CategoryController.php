<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * عرض التصنيفات.
     *
     * بشكل افتراضي:
     * نعرض التصنيفات الفعالة فقط.
     *
     * include_inactive=1:
     * نعرض الفعالة والمعطلة،
     * وهذا مخصص لإدارة التصنيفات.
     */
    public function index(Request $request)
    {
        $query = Category::query()
            ->select([
                'id',
                'name_ar',
                'name_en',
                'type',
                'parent_id',
                'is_active',
            ]);

        if ($request->filled('type')) {
            $query
                ->whereIn(
                    'type',
                    ['income', 'expense']
                )
                ->where(
                    'type',
                    $request->type
                );
        }

        if ($request->has('parent_id')) {
            if (
                $request->parent_id === null ||
                $request->parent_id === ''
            ) {
                $query->whereNull('parent_id');
            } else {
                $query->where(
                    'parent_id',
                    $request->parent_id
                );
            }
        } else {
            $query->whereNull('parent_id');
        }

        $includeInactive = filter_var(
            $request->input(
                'include_inactive',
                false
            ),
            FILTER_VALIDATE_BOOLEAN
        );

        if (!$includeInactive) {
            $query->where(
                'is_active',
                true
            );
        }

        return response()->json(
            $query
                ->orderBy('name_ar')
                ->get()
        );
    }

    /**
     * إنشاء تصنيف جديد.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'type' => [
                'required',
                Rule::in([
                    'income',
                    'expense',
                ]),
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
            ],
        ]);

        $parent = null;

        if (!empty($data['parent_id'])) {
            $parent = Category::findOrFail(
                $data['parent_id']
            );

            if (
                $parent->type !==
                $data['type']
            ) {
                return response()->json(
                    [
                        'message' =>
                            'The parent category must have the same type.',
                    ],
                    422
                );
            }
        }

        $category = Category::create([
            'name_ar' =>
                $data['name_ar'],

            'name_en' =>
                $data['name_en'],

            'type' =>
                $data['type'],

            'parent_id' =>
                $data['parent_id'] ?? null,

            'is_active' => true,
        ]);

        return response()->json(
            $category,
            201
        );
    }

    /**
     * تحديث تصنيف.
     */
    public function update(
        Request $request,
        Category $category
    ) {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'type' => [
                'required',
                Rule::in([
                    'income',
                    'expense',
                ]),
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
            ],
        ]);

        if ($data['parent_id'] !== null) {
            if (
                (int) $data['parent_id'] ===
                $category->id
            ) {
                return response()->json(
                    [
                        'message' =>
                            'A category cannot be its own parent.',
                    ],
                    422
                );
            }

            $parent = Category::findOrFail(
                $data['parent_id']
            );

            if (
                $parent->type !==
                $data['type']
            ) {
                return response()->json(
                    [
                        'message' =>
                            'The parent category must have the same type.',
                    ],
                    422
                );
            }
        }

        $category->update($data);

        return response()->json(
            $category
        );
    }

    /**
     * تفعيل / تعطيل التصنيف.
     */
    public function toggleStatus(
        Category $category
    ) {
        $category->update([
            'is_active' =>
                !$category->is_active,
        ]);

        return response()->json([
            'message' =>
                $category->is_active
                    ? 'Category enabled successfully.'
                    : 'Category disabled successfully.',

            'category' => $category,
        ]);
    }

    /**
     * الحذف القديم يبقى كما هو
     * للتوافق مع أي كود سابق.
     *
     * واجهة الإعدادات الجديدة لا تستخدمه،
     * بل تستخدم التعطيل لحماية العمليات القديمة.
     */
    public function destroy(
        Category $category
    ) {
        $category->delete();

        return response()->json([
            'message' =>
                'Category deleted successfully.',
        ]);
    }
}