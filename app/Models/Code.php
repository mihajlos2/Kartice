<?php

namespace App\Models;

use App\Enums\RecipientType;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    protected $fillable= [

        'pack_id',
        'name',
        'email',
        'amount',
        'date',
        'hashcode',
        'code',
        'recipient_type',
    ];

    protected function casts(): array
    {
        return [
            'recipient_type' => RecipientType::class,
        ];
    }

    public function pack(){
        return $this->belongsTo(Pack::class);
    }
}
