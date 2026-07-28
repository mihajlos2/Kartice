<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Code;

class CodeGenerator
{
    public static function generate_code(): string
    {
        $charset = '{ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789,.=+_-?!@#$%^&*<>;:}';
        $code = $charset[random_int(0, strlen($charset) - 1)];
        do {
            for ($i = 0; $i < 9; $i++) {
                $code .= $charset[random_int(0, strlen($charset) - 1)];
            }
        } while (Code::where('code', $code)->exists());

        return $code;
    }
}
