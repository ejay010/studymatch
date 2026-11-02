<?php

namespace App\Services;

use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Request to capture an approved PayPal Order
 */
class CapturePayPalOrderRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $orderId)
    {}

    public function resolveEndpoint(): string
    {
        return "/v2/checkout/orders/{$this->orderId}/capture";
    }

    protected function defaultBody(): array
    {
        return [];
    }
}
