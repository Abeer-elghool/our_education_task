<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\MasterRequest;
use Illuminate\Foundation\Http\FormRequest;

class UserJsonRequest extends MasterRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:json|max:2048',
        ];
    }
}
