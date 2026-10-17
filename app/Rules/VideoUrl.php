<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class VideoUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! filter_var($value, FILTER_VALIDATE_URL)) {
            $fail('يجب أن يكون :attribute رابطاً صحيحاً.');
            return;
        }

        $host = parse_url($value, PHP_URL_HOST);

        if ($host === null) {
            $fail('يجب أن يكون :attribute رابطاً صحيحاً.');
            return;
        }

        $host = strtolower($host);

        $allowed = [
            'youtube.com',
            'www.youtube.com',
            'youtu.be',
            'vimeo.com',
            'www.vimeo.com',
            'player.vimeo.com',
        ];

        if (! in_array($host, $allowed, true)) {
            $fail('يجب أن يكون :attribute رابط فيديو من YouTube أو Vimeo.');
        }
    }
}
