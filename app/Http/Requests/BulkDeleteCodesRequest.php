<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Pack;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkDeleteCodesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Pack $pack */
        $pack = $this->route('pack');

        return [
            'code_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'code_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('codes', 'id')
                    ->where('pack_id', $pack->id),
            ],
        ];
    }
}
