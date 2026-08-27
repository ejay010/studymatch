<?php

namespace App\Services;

use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Request to create a PayPal Order (Checkout)
 */
class CreatePayPalOrderRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected float $amount, protected string $description)
    {}

    public function resolveEndpoint(): string
    {
        return '/v2/checkout/orders';
    }

    protected function defaultBody(): array
    {
        return [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format($this->amount, 2, '.', ''),
                    ],
                    'description' => $this->description,
                ]
            ]
        ];
    }
}
