<?php

namespace App\Services;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Http\Auth\TokenAuthenticator;

/**
 * Saloon Connector for Checkr API (Background Checks)
 */
class CheckrConnector extends Connector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://api.checkr.com/v1';
    }

    protected function defaultAuth(): \Saloon\Contracts\Authenticator
    {
        // Checkr uses Bearer tokens or Basic Auth with the API key as the username.
        // We'll use a TokenAuthenticator for simplicity in this example.
        return new TokenAuthenticator(config('services.checkr.api_key'));
    }
}
