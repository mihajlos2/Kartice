<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'date',
    'user_id',
])]
class Pack extends Model
{
    public function code()
    {
        return $this->hasMany(Code::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
