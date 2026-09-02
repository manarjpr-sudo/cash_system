<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('manage_users');
    }

    public function rules(): array
    {
        return [
            'role_id' => 'required|exists:roles,id',
        ];
    }
}