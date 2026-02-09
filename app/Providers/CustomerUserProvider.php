<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class CustomerUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(array $credentials)
    {
        // Use 'customer_email' instead of 'email'
        if (empty($credentials) || !isset($credentials['customer_email'])) {
            return null;
        }

        $query = $this->createModel()->newQuery();

        return $query->where('customer_email', $credentials['customer_email'])->first();
    }
}