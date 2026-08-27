<?php

namespace App\Services;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Http\Auth\BasicAuthenticator;

/**
 * Saloon Connector for PayPal REST API
 */
class PayPalConnector extends Connector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return config('services.paypal.mode', 'sandbox') === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';
    }

    protected function defaultAuth(): \Saloon\Contracts\Authenticator
    {
        return new BasicAuthenticator(
            config('services.paypal.client_id'), 
            config('services.paypal.secret')
        );
    }
}
