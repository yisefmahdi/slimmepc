<?php

use App\Mail\AdminAfspraakNotification;
use App\Mail\AdminContactNotification;
use App\Mail\AdminOrderNotificationMail;
use App\Mail\AdminRepairNotification;
use App\Mail\AfspraakReceived;
use App\Mail\ContactReceived;
use App\Mail\ContactReplyMail;
use App\Mail\DeviceReceiptCompletedMail;
use App\Mail\DeviceReceiptMail;
use App\Mail\ManualInvoiceMail;
use App\Mail\OrderInvoiceMail;
use App\Mail\RepairReceived;
use App\Models\AfspraakSubmission;
use App\Models\ContactSubmission;
use App\Models\DeviceReceipt;
use App\Models\ManualInvoice;
use App\Models\Order;
use App\Models\OrderInvoice;
use App\Models\RepairSubmission;

function makeMailOrder(): Order
{
    return Order::create([
        'customer_email' => 'klant@example.com',
        'customer_phone' => '0612345678',
        'subtotal' => 100,
        'total_price' => 106.95,
        'payment_status' => 'paid',
        'order_status' => 'processing',
    ]);
}

function makeMailContact(): ContactSubmission
{
    return ContactSubmission::create([
        'name' => 'Jan Jansen',
        'email' => 'jan@example.com',
        'phone' => '0612345678',
        'subject' => 'Vraag over laptop',
        'request_type' => 'Vraag',
        'message' => 'Wanneer is mijn laptop klaar?',
    ]);
}

function makeMailRepair(): RepairSubmission
{
    return RepairSubmission::create([
        'repair_number' => 'SP-2026-00001',
        'device' => 'Laptop',
        'problems' => ['Scherm defect'],
        'description' => 'Scherm blijft zwart',
        'brand' => 'HP',
        'model' => 'Pavilion',
        'data_importance' => 'Belangrijk',
        'opened_before' => 'Nee',
        'name' => 'Jan Jansen',
        'email' => 'jan@example.com',
        'phone' => '0612345678',
        'postcode' => '1234 AB',
        'delivery_method' => 'Brengen',
        'contact_preference' => 'E-mail',
    ]);
}

function makeMailAfspraak(): AfspraakSubmission
{
    return AfspraakSubmission::create([
        'afspraak_number' => 'AF-2026-00001',
        'name' => 'Jan Jansen',
        'email' => 'jan@example.com',
        'street' => 'Hoofdstraat',
        'phone' => '0612345678',
        'postcode' => '1234 AB',
        'house_number' => '10',
        'city' => 'Apeldoorn',
        'device' => 'Desktop',
        'problem' => 'Start niet op',
        'preferred_date' => '2026-09-15',
        'preferred_time' => 'Ochtend',
    ]);
}

function makeMailReceipt(): DeviceReceipt
{
    return DeviceReceipt::create([
        'customer_name' => 'Jan Jansen',
        'customer_email' => 'jan@example.com',
        'device_type' => 'Laptop',
        'phone_number' => '0612345678',
        'received_at' => '2026-09-09 10:00:00',
    ]);
}

function assertMailLayout(string $html, string $needle): void
{
    // Shared Slimme-PC email design system markers
    expect($html)->toContain('SLIMME-PC')
        ->toContain('linear-gradient')
        ->toContain('logo.webp')
        ->toContain($needle)
        // No leftover markdown / old components / uncompiled Blade
        ->not->toContain('<x-mail')
        ->not->toContain('**')
        ->not->toContain('<?php')
        ->not->toContain('{{');
}

it('renders the order invoice mail in the new design', function () {
    $order = makeMailOrder();
    $invoice = OrderInvoice::create([
        'order_id' => $order->id,
        'invoice_number' => 'INV-2026-00001',
        'invoice_date' => '2026-09-09',
        'customer_name' => 'Jan Jansen',
        'customer_email' => 'jan@example.com',
        'total' => 106.95,
    ]);

    assertMailLayout((new OrderInvoiceMail($invoice))->render(), 'INV-2026-00001');
});

it('renders the admin order notification in the new design', function () {
    $order = makeMailOrder();

    assertMailLayout((new AdminOrderNotificationMail($order))->render(), $order->order_number);
});

it('renders contact mails in the new design', function () {
    $sub = makeMailContact();

    assertMailLayout((new ContactReceived($sub))->render(), 'Vraag over laptop');
    assertMailLayout((new AdminContactNotification($sub))->render(), 'Nieuwe contactaanvraag');
    assertMailLayout((new ContactReplyMail($sub, 'Dank voor je bericht.', 'Piet'))->render(), 'Dank voor je bericht.');
});

it('renders repair mails in the new design', function () {
    $sub = makeMailRepair();

    assertMailLayout((new RepairReceived($sub))->render(), 'SP-2026-00001');
    assertMailLayout((new AdminRepairNotification($sub))->render(), 'Nieuwe reparatieaanvraag');
});

it('renders afspraak mails in the new design', function () {
    $sub = makeMailAfspraak();

    assertMailLayout((new AfspraakReceived($sub))->render(), 'AF-2026-00001');
    assertMailLayout((new AdminAfspraakNotification($sub))->render(), 'Nieuwe afspraak-aan-huis aanvraag');
});

it('renders device receipt mails in the new design', function () {
    $receipt = makeMailReceipt();

    assertMailLayout((new DeviceReceiptMail($receipt))->render(), 'Bevestiging ontvangst apparaat');
    assertMailLayout((new DeviceReceiptCompletedMail($receipt))->render(), 'Uw apparaat is gerepareerd!');
});

it('renders the manual invoice mail in the new design', function () {
    $invoice = ManualInvoice::create([
        'name' => 'Jan Jansen',
        'email' => 'jan@example.com',
        'invoice_number' => 'SLM-2026-00001',
        'total' => 50,
    ]);

    assertMailLayout((new ManualInvoiceMail($invoice))->render(), 'SLM-2026-00001');
});
