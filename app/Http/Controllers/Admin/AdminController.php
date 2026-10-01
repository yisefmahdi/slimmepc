<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard (cards only, like the old system).
     */
    public function dashboard(Request $request): View
    {
        $stats = [
            // Klanten (old: User::count() — all roles)
            'customers' => \App\Models\User::count(),
            // Bestellingen
            'orders' => \App\Models\Order::count(),
            // Producten
            'products' => \App\Models\Product::count(),
            // Facturen (old: order invoices → new direct equivalent)
            'invoices' => \App\Models\OrderInvoice::count(),
            // Afspraken (old: Appointment → new: AfspraakSubmission)
            'afspraken' => \App\Models\AfspraakSubmission::count(),
            // Berichten-live: only customer messages actually waiting for the employee
            // (actionable conversation + last message is from the customer).
            // The inbox total also counts closed chats and chats the AI already
            // answered, which inflates the number.
            'chat_unread' => \Illuminate\Support\Facades\DB::table('chat_messages as m')
                ->join('chat_conversations as c', 'c.id', '=', 'm.chat_conversation_id')
                ->whereIn('c.status', ['open', 'handed_over', 'offline'])
                ->where('m.sender', 'customer')
                ->where(function ($q) {
                    $q->whereNull('c.admin_read_at')
                        ->orWhereColumn('m.created_at', '>', 'c.admin_read_at');
                })
                ->whereRaw("(SELECT m2.sender FROM chat_messages m2 WHERE m2.chat_conversation_id = c.id ORDER BY m2.created_at DESC, m2.id DESC LIMIT 1) = ?", ['customer'])
                ->count(),
            // Abonnement-Lidworden (total)
            'memberships' => \App\Models\Membership::count(),
            // DeviceReceipt
            'device_receipts' => \App\Models\DeviceReceipt::count(),
            // Actieve abonnementen (old: LicenseCode has no equivalent — active paid memberships instead)
            'memberships_active' => \App\Models\Membership::where('payment_status', 'paid')
                ->where('end_date', '>', now())
                ->count(),
            // Quick-action badges
            'orders_new' => \App\Models\Order::where('order_status', 'pending')->count(),
            'contact_new' => \App\Models\ContactSubmission::where('status', 'new')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}

