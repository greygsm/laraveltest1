<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RandomUserRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'fields.*' => 'required|in:name,phone,email,location',
            'user_qty' => 'sometimes|integer',
            'format' => 'required|string|in:json,xml',
            'sort_by' => 'sometimes|string|in:last,phone,email,country',
            'sort_order' => 'sometimes|string|in:asc,desc',
        ];
    }
}
