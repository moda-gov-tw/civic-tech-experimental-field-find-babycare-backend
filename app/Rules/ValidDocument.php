<?php

namespace App\Rules;

use Closure;
use Illuminate\Validation\Rules\File;
use Illuminate\Contracts\Validation\ValidationRule;
use Validator;

class ValidDocument implements ValidationRule
{
    /**
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validator = Validator::make([$attribute => $value], [
            $attribute => [
                File::types(['jpeg', 'bmp', 'png', 'gif', 'svg', 'pdf'])
                    ->min('1kb')
                    ->max('20mb')
            ]
        ]);

        if ($validator->fails()) {
            $fail($validator->messages()->get($attribute)[0]);
        }
    }
}
