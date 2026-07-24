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

    public function generate_code():string
    {
        $charset = "{ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789,.=+_-?!@#$%^&*<>;:}";
        $code = $charset[rand(0,strlen($charset)-1)];
        for($i = 0;$i<9;$i++) {
            $code .= $charset[rand(0,strlen($charset)-1)];
        }
        return $code;
    }

    public function hashcode()
    {
        return Hash::make($this->code());
    }
}
