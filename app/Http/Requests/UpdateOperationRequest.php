<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOperationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('manage_operations');
    }

    public function rules(): array
    {
        return [
            'description' => 'nullable|string',
            'amount' => 'sometimes|numeric|min:0.01',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }
}