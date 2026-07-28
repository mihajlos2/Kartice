<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\RecipientType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        return [
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_email' => ['required', 'string', 'email', 'max:255'],
            'amount' => ['required', 'integer', 'max:255'],
            'recipient_type' => ['required', Rule::enum(RecipientType::class)],
            'send_options' => ['required', Rule::in(['send_at', 'instant', 'no_date'])],
            'send_at' => ['nullable', 'required_if:send_options,send_at', 'date'],
        ];
    }
}
