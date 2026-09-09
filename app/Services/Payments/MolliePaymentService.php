<?php

namespace App\Services\Payments;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;

/**
 * Single reusable Mollie gateway for the whole platform
 * (webshop now, memberships/technician later).
 */
class MolliePaymentService
{
    protected function client(): MollieApiClient
    {
        $mollie = new MollieApiClient;
        $mollie->setApiKey((string) config('services.mollie.key'));

        return $mollie;
    }

    public function isConfigured(): bool
    {
        return (bool) config('services.mollie.key');
    }

    /**
     * @param  array{order_id?:int,membership_id?:int,form_id?:int}  $metadata
     */
    public function createPayment(float $amount, string $description, string $redirectUrl, ?string $webhookUrl = null, array $metadata = []): Payment
    {
        $payload = [
            'amount' => [
                'currency' => 'EUR',
                'value' => number_format($amount, 2, '.', ''),
            ],
            'description' => $description,
            'redirectUrl' => $redirectUrl,
            'metadata' => $metadata,
        ];

        if ($webhookUrl) {
            $payload['webhookUrl'] = $webhookUrl;
        }

        try {
            return $this->client()->payments->create($payload);
        } catch (ApiException $e) {
            report($e);
            throw $e;
        }
    }

    public function getPayment(string $paymentId): Payment
    {
        return $this->client()->payments->get($paymentId);
    }

    public function isPaid(Payment $payment): bool
    {
        return $payment->isPaid();
    }
}
