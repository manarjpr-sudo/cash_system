<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('manage_approvals');
    }

    public function rules(): array
    {
        return [
            'comment' => 'nullable|string',
        ];
    }
}