<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    protected $fillable = [
        'name',
        'date',
        'user_id',
    ];

    public function code()
    {
        return $this->hasMany(Code::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
