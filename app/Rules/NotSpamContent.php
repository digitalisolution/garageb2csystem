<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotSpamContent implements Rule
{
    /**
     * List of spam keywords.
     *
     * @var array
     */
    protected $spamKeywords = [
        'Aloha',
        'test',
        'testing',
        'checking',
        'writing',
        'writting',
        'write',
        'wrote',
        'about',
        'check',
        'i am writing about the prices',
        'Hello, i am wrote about prices',
        'Hello writing about your the prices',
        'Hello writing about your price',
        'buy now',
        'promotion',
        'Hi, i write about price',
        'click here',
        'visit my website',
        'Aloha, i wrote about your the price for reseller',
        'reseller',
        'Hi, i wrote about your the price',
        'i wrote about your the price',
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = strtolower($value); // Normalize input to lowercase

        foreach ($this->spamKeywords as $keyword) {
            if (str_contains($value, strtolower($keyword))) {
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
