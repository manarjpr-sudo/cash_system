<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('manage_users');
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => 'required|string|max:1000',
        ];
    }
}