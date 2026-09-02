<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('manage_approvals');
    }

    public function rules(): array
    {
        return [
            'operation_id' => 'required|exists:operations,id',
            'status' => 'required|in:approved,rejected',
            'comment' => 'nullable|string',
        ];
    }
}