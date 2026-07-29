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
    'queued_at',
    'sent_at',
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
            'date' => 'datetime',
            'sent_at' => 'datetime',
            'queued_at' => 'datetime',
            'recipient_type' => RecipientType::class,
        ];
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }
}
