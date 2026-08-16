<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard(Request $request): View
    {
        $stats = [
            'customers' => \App\Models\User::where('role', 'user')->count(),
            'technicians' => \App\Models\User::where('role', 'technician')->count(),
            'orders' => 0,
            'repairs' => 0,
            'contact_new' => \App\Models\ContactSubmission::where('status', 'new')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}

