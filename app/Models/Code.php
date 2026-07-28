<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RecipientType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Override;

#[Fillable([

    'pack_id',
    'name',
    'email',
    'amount',
    'date',
    'hashcode',
    'code',
    'recipient_type',
])]
class Code extends Model
{
    #[Override]
    protected function casts(): array
    {
        return [
            'recipient_type' => RecipientType::class,
        ];
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }
}
