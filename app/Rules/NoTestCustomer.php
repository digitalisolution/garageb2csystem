<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoTestCustomer implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
     /**
     * List of spam keywords.
     *
     * @var array
     */
    // protected $keywords = ['test', 'dummy', 'example', 'noemail', 'fake','checking', 'testing', 'abc', 'xyz','123', 'test123'];
    protected $keywords = [];
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = strtolower($value);
        foreach ($this->keywords as $word) {
            if (str_contains($value, $word)) {
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
        return 'Your message contains words that look like spam. Please revise and try again.';
    }
}
