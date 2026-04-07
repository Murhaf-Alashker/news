<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required','string','max:50','min:3'],
            'last_name' => ['required','string','max:50','min:3'],
            'country' => ['nullable','string','max:50','min:3'],
            'city' => ['nullable','string','max:50','min:3'],
            'phone' => ['nullable','string','max:50','min:3'],
        ];
    }
}
