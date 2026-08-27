<?php

namespace App\Services;

use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class CreateCheckrCandidateRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $firstName, 
        protected string $lastName, 
        protected string $email
    ) {}

    public function resolveEndpoint(): string
    {
        return '/candidates';
    }

    protected function defaultBody(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
        ];
    }
}
