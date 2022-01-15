<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TwitterAccountRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return preg_match('/^[a-z0-9_]{1,15}$/i', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.twitter_account');
    }
}
