<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFunctionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'language' => [
                'required',
                'string',
                Rule::in(['PHP 8.4', 'Python', 'Typescript', 'Bash (Ubuntu)', 'Bash (Alpine)']),
            ],

            'route' => [
                'required',
                'string',
                'max:255',
                'regex:/^\/[a-zA-Z0-9\-_\/{}\s]*$/',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\-_]+$/',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'timeout' => [
                'required',
                'integer',
                'min:1',
                'max:900',
            ],

            'method' => [
                'required',
                'string',
                Rule::in(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD']),
            ],

            'entrypoint' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'route.regex' => 'The route must be a valid URL path starting with a forward slash.',
            'name.regex'  => 'The name may only contain letters, numbers, dashes, and underscores.',
            'timeout.min' => 'The timeout must be at least 1 second.',
            'timeout.max' => 'The timeout must not exceed 900 seconds (15 minutes).',
        ];
    }
}
