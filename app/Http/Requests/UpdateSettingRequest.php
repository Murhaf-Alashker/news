<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('api-admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'facebook' => ['require','string','regex:/^(https?:\/\/)?(www\.)?facebook\.com\/.+$/i'],
            'instagram' => ['require','string','regex:/^(https?:\/\/)?(www\.)?instagram\.com\/.+$/i'],
            'whatsapp' => ['require' , 'string' , 'start_with:+963' , 'size:13'],
            'linkedin' => ['require','string','regex:/^(https?:\/\/)?(www\.)?linkedin\.com\/.+$/i'],
            'response_email' => ['require','email'],
        ];
    }
}
