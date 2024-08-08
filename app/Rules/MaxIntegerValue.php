<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MaxIntegerValue implements Rule
{
    public $max;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($max)
    {
        $this->max = $max;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return is_int($value) && $value <= $this->max;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute must be less than or equal to ' . $this->max . ' .';
    }
}
