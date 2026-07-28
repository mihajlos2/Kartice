<?php

namespace App\Helpers;

use App\Models\Code;

class CodeGenerator
{
    public static function generate_code():string
    {
        $charset = "{ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789,.=+_-?!@#$%^&*<>;:}";
        $code = $charset[rand(0,strlen($charset)-1)];
        do{
            for($i = 0;$i<9;$i++) {
                $code .= $charset[rand(0,strlen($charset)-1)];
            }
        }while (Code::where('code', $code)->exists());
        return $code;
    }
}
