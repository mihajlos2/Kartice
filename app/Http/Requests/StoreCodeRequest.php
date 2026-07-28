<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCodeRequest extends FormRequest
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
        return ([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_email' => ['required', 'string','email', 'max:255'],
            'amount' => ['required', 'integer', 'max:255'],
            'recipient_type' => ['required', 'string', 'in:specific,bulk'],
            'pack_id' => ['required', 'integer', 'exists:packs,id']
        ]);
    }
}
