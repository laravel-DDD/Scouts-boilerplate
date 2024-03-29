<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class MatchUserPassword implements Rule
{
    /**
     * {@inheritDoc}
     */
    public function passes($attribute, $value)
    {
        return Hash::check($value, auth()->user()->getAuthPassword());
    }

    /**
     * {@inheritDoc}
     */
    public function message(): string
    {
        return __('Het opgegeven wachtwoord komt niet overeen met je huidige wachtwoord.');
    }
}
