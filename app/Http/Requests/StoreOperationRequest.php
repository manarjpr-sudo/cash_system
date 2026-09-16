<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOperationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::in(['income', 'expense']),
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999999999.99',
            ],

            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],

            'operation_date' => [
                'required',
                'date',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->filled('category_id')) {
                return;
            }

            $category = Category::find($this->integer('category_id'));

            if (!$category) {
                return;
            }

            if ($category->type !== $this->input('type')) {
                $validator->errors()->add(
                    'category_id',
                    'The selected category does not match the operation type.'
                );
            }
        });
    }
}