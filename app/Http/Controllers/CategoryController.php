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
     * type:
     * income | expense
     *
     * parent_id:
     * عند عدم إرساله، نعرض التصنيفات الرئيسية فقط.
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
            ]);

        if ($request->filled('type')) {
            $query->whereIn('type', ['income', 'expense'])
                ->where('type', $request->type);
        }

        if ($request->has('parent_id')) {
            if ($request->parent_id === null || $request->parent_id === '') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        } else {
            $query->whereNull('parent_id');
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
                Rule::in(['income', 'expense']),
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
            ],
        ]);

        $parent = null;

        if (!empty($data['parent_id'])) {
            $parent = Category::findOrFail($data['parent_id']);

            if ($parent->type !== $data['type']) {
                return response()->json([
                    'message' => 'The parent category must have the same type.',
                ], 422);
            }
        }

        $category = Category::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'type' => $data['type'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        return response()->json($category, 201);
    }

    /**
     * تحديث تصنيف.
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'type' => [
                'required',
                Rule::in(['income', 'expense']),
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
            ],
        ]);

        if ($data['parent_id'] !== null) {
            if ((int) $data['parent_id'] === $category->id) {
                return response()->json([
                    'message' => 'A category cannot be its own parent.',
                ], 422);
            }

            $parent = Category::findOrFail($data['parent_id']);

            if ($parent->type !== $data['type']) {
                return response()->json([
                    'message' => 'The parent category must have the same type.',
                ], 422);
            }
        }

        $category->update($data);

        return response()->json($category);
    }

    /**
     * حذف تصنيف.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }
}