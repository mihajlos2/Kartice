<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    protected $fillable = [
        'name',
        'date',
    ];

    public function code()
    {
        return $this->hasMany(Code::class);
    }
}
