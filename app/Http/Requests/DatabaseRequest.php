<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatabaseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'host' => ['required'],
            'port' => ['required', 'integer'],
            'username' => ['required'],
            'password' => ['nullable'],
            'name' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
