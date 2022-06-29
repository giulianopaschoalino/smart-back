<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Uppercase implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        protected $code
    ){}

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if ($value == 2) {
            if ( $this->code == null){
               return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The client field is required.';
    }
}
