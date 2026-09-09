<?php

namespace App\Console\Commands;

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
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestMails extends Command
{
    protected $signature = 'mail:test-all {email : Destination address for all 12 test mails}';

    protected $description = 'Send one rendered copy of each Slimme-PC mail to the given address (design test)';

    public function handle(): int
    {
        $to = $this->argument('email');

        $order = Order::create([
            'customer_email' => $to,
            'customer_phone' => '0612345678',
            'subtotal' => 100,
            'total_price' => 106.95,
            'payment_status' => 'paid',
            'order_status' => 'processing',
        ]);
        $orderInvoice = OrderInvoice::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV-TEST-001',
            'invoice_date' => now()->toDateString(),
            'customer_name' => 'Test Klant',
            'customer_email' => $to,
            'total' => 106.95,
        ]);

        $contact = ContactSubmission::create([
            'name' => 'Test Klant',
            'email' => $to,
            'phone' => '0612345678',
            'subject' => 'Testvraag (design-test, negeren)',
            'request_type' => 'Vraag',
            'message' => 'Dit is een design-test, je kunt dit bericht negeren.',
        ]);

        $repair = RepairSubmission::create([
            'repair_number' => 'SP-TEST-001',
            'device' => 'Laptop',
            'problems' => ['Scherm defect'],
            'description' => 'Design-test, negeren.',
            'brand' => 'HP',
            'model' => 'Pavilion',
            'data_importance' => 'Belangrijk',
            'opened_before' => 'Nee',
            'name' => 'Test Klant',
            'email' => $to,
            'phone' => '0612345678',
            'postcode' => '1234 AB',
            'delivery_method' => 'Brengen',
            'contact_preference' => 'E-mail',
        ]);

        $afspraak = AfspraakSubmission::create([
            'afspraak_number' => 'AF-TEST-001',
            'name' => 'Test Klant',
            'email' => $to,
            'street' => 'Hoofdstraat',
            'phone' => '0612345678',
            'postcode' => '1234 AB',
            'house_number' => '10',
            'city' => 'Apeldoorn',
            'device' => 'Desktop',
            'problem' => 'Design-test, negeren.',
            'preferred_date' => now()->addWeek()->toDateString(),
            'preferred_time' => 'Ochtend',
        ]);

        $receipt = DeviceReceipt::create([
            'customer_name' => 'Test Klant',
            'customer_email' => $to,
            'device_type' => 'Laptop',
            'phone_number' => '0612345678',
            'received_at' => now(),
        ]);

        $manual = ManualInvoice::create([
            'name' => 'Test Klant',
            'email' => $to,
            'invoice_number' => 'SLM-TEST-001',
            'tax_percentage' => 21,
            'total' => 50,
        ]);

        $mails = [
            'OrderInvoiceMail' => new OrderInvoiceMail($orderInvoice),
            'AdminOrderNotificationMail' => new AdminOrderNotificationMail($order),
            'ContactReceived' => new ContactReceived($contact),
            'AdminContactNotification' => new AdminContactNotification($contact),
            'ContactReplyMail' => new ContactReplyMail($contact, 'Dit is een design-test, je kunt dit bericht negeren.', 'Test Admin'),
            'RepairReceived' => new RepairReceived($repair),
            'AdminRepairNotification' => new AdminRepairNotification($repair),
            'AfspraakReceived' => new AfspraakReceived($afspraak),
            'AdminAfspraakNotification' => new AdminAfspraakNotification($afspraak),
            'DeviceReceiptMail' => new DeviceReceiptMail($receipt),
            'DeviceReceiptCompletedMail' => new DeviceReceiptCompletedMail($receipt),
            'ManualInvoiceMail' => new ManualInvoiceMail($manual),
        ];

        foreach ($mails as $name => $mail) {
            Mail::to($to)->send($mail);
            $this->info("Sent: {$name}");
        }

        // Cleanup test rows (mails already rendered/sent)
        $orderInvoice->delete();
        $order->delete();
        $contact->delete();
        $repair->delete();
        $afspraak->delete();
        $receipt->delete();
        $manual->delete();

        $this->info('All 12 test mails sent to '.$to);

        return self::SUCCESS;
    }
}
