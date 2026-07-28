<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Code;
use Str;

class CodeGenerator
{
    public static function generate_code(): string
    {
        do {
            for ($i = 0; $i < 9; $i++) {
                $code = Str::random(10);
            }
        } while (Code::where('code', $code)->exists());

        return $code;
    }
}
