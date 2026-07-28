<?php

namespace App\Models;

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

    public function pack(){
        return $this->belongsTo(Pack::class);
    }
}
