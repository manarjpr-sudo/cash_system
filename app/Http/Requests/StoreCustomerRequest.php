<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('manage_customers');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'identity_number' => 'nullable|string|max:100|unique:customers',
            'room_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'email' => 'nullable|email|max:255',
        ];
    }
}