<?php

/*
 * Sub-agent F (testing audit): pure unit tests (no application boot needed)
 * for helpers, value objects and the Mollie amount guard.
 */

use App\Models\Coupon;
use App\Models\DigitalFile;
use App\Models\LicenseCode;
use App\Models\Order;
use App\Services\Payments\MolliePaymentService;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;

function auditUnitPayment(mixed $amount): Payment
{
    $payment = new Payment(new MollieApiClient);
    $payment->amount = $amount;

    return $payment;
}

it('formats digital file sizes', function () {
    expect((new DigitalFile(['size' => 0]))->humanSize())->toBe('0 B');
    expect((new DigitalFile(['size' => 512]))->humanSize())->toBe('512 B');
    expect((new DigitalFile(['size' => 2048]))->humanSize())->toBe('2 KB');
    expect((new DigitalFile(['size' => 5 * 1024 * 1024]))->humanSize())->toBe('5 MB');
});

it('labels shipping methods', function () {
    expect((new Order(['shipping_method' => 'pickup']))->shippingMethodLabel())->toBe('Afhalen');
    expect((new Order(['shipping_method' => 'digital']))->shippingMethodLabel())->toBe('Digitaal');
    expect((new Order(['shipping_method' => 'delivery']))->shippingMethodLabel())->toBe('Bezorging');
});

it('flags licence availability', function () {
    expect((new LicenseCode(['status' => 'available']))->isAvailable())->toBeTrue();
    expect((new LicenseCode(['status' => 'sold']))->isAvailable())->toBeFalse();
});

it('computes coupon discounts without touching the database', function () {
    expect((new Coupon(['discount_type' => 'percentage', 'discount_value' => 10]))->discountAmount(100.0))->toBe(10.0);
    expect((new Coupon(['discount_type' => 'fixed', 'discount_value' => 7.50]))->discountAmount(100.0))->toBe(7.50);
    // Fixed discounts never exceed the subtotal.
    expect((new Coupon(['discount_type' => 'fixed', 'discount_value' => 7.50]))->discountAmount(5.0))->toBe(5.0);
    expect((new Coupon(['status' => false]))->isMaxedOut())->toBeFalse();
});

it('guards mollie amounts strictly', function () {
    $service = new MolliePaymentService;

    expect($service->amountMatches(auditUnitPayment((object) ['currency' => 'EUR', 'value' => '30.95']), 30.95))->toBeTrue();
    expect($service->amountMatches(auditUnitPayment((object) ['currency' => 'EUR', 'value' => '30.94']), 30.95))->toBeFalse();
    expect($service->amountMatches(auditUnitPayment(['value' => '30.95']), 30.95))->toBeTrue();
    expect($service->amountMatches(auditUnitPayment(30.95), 30.95))->toBeTrue();
    expect($service->amountMatches(auditUnitPayment((object) ['currency' => 'EUR', 'value' => 'diefstal']), 30.95))->toBeFalse();
    expect($service->amountMatches(auditUnitPayment(null), 30.95))->toBeFalse();
    // Mollie sends exactly two decimals; trailing-zero variants still match.
    expect($service->amountMatches(auditUnitPayment((object) ['currency' => 'EUR', 'value' => '30.950']), 30.95))->toBeTrue();
});
