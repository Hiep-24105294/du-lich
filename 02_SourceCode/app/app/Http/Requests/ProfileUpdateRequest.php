<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['nullable', 'string', 'regex:/^\+?[0-9]{9,15}$/'],
            'locale' => ['required', Rule::in(['vi', 'en'])],
            'theme' => ['required', Rule::in(['light', 'dark'])],
            'organization' => ['nullable', 'string', 'max:190'],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => __('validation.custom.contact_phone.regex')];
    }
}
