<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOperationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'sometimes',
                'required',
                Rule::in(['income', 'expense']),
            ],

            'amount' => [
                'sometimes',
                'required',
                'numeric',
                'min:0.01',
                'max:999999999999.99',
            ],

            'category_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:categories,id',
            ],

            'operation_date' => [
                'sometimes',
                'required',
                'date',
            ],

            'description' => [
                'sometimes',
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

            $operation = $this->route('operation');

            $effectiveType = $this->input(
                'type',
                $operation?->type
            );

            if ($category->type !== $effectiveType) {
                $validator->errors()->add(
                    'category_id',
                    'The selected category does not match the operation type.'
                );
            }
        });
    }
}